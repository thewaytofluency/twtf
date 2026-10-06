import Alpine from 'alpinejs';

window.Alpine = Alpine;

const slugify = (text) =>
    text
        .normalize('NFD')
        .replace(/[̀-ͯ]/g, '')
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');

/**
 * Admin blog post form: title/slug, the WYSIWYG editor, publish box and cover preview all share
 * one component so the Publish button label, dirty tracking and word count stay in sync.
 * The editor itself (TipTap) is lazy-loaded so it never weighs down the other pages.
 */
Alpine.data('postEditor', (config) => {
    // Kept outside the reactive object: Alpine's proxy breaks ProseMirror's internals.
    let editor = null;

    return {
        title: config.title,
        slug: config.slug,
        slugTouched: config.slugTouched,
        excerpt: config.excerpt,
        html: config.content,
        status: config.status,
        publishedAt: config.publishedAt,
        exists: config.exists,
        wasPublished: config.wasPublished,

        words: 0,
        tick: 0, // bumped on every editor transaction so toolbar state re-evaluates
        dirty: false,
        uploading: false,
        error: '',
        linkOpen: false,
        linkUrl: '',

        coverPreview: config.coverUrl,
        removeCover: false,

        init() {
            import('./post-editor.js').then(({ createPostEditor }) => {
                editor = createPostEditor(this.$refs.surface, {
                    content: this.html,
                    placeholder: 'Start writing your post…',
                    onChange: (instance) => {
                        this.html = instance.getHTML();
                        this.words = instance.storage.characterCount.words();
                        this.dirty = true;
                    },
                    onTransaction: () => this.tick++,
                    onImageFiles: (files) => files.forEach((file) => this.uploadImage(file)),
                });
                this.words = editor.storage.characterCount.words();
            });

            // A real submit (Save/Publish) is not "leaving with unsaved changes".
            this.$el.closest('form')?.addEventListener('submit', () => (this.dirty = false));

            window.addEventListener('beforeunload', (event) => {
                if (this.dirty) {
                    event.preventDefault();
                    event.returnValue = '';
                }
            });
        },

        // ---- title / slug ----
        onTitleInput() {
            this.dirty = true;
            if (!this.slugTouched) this.slug = slugify(this.title);
        },

        onSlugInput() {
            this.slugTouched = true;
            this.slug = slugify(this.slug);
            this.dirty = true;
        },

        // ---- toolbar ----
        is(name, attrs = {}) {
            this.tick; // subscribe to editor changes
            return editor ? editor.isActive(name, attrs) : false;
        },

        isAlign(alignment) {
            this.tick;
            return editor ? editor.isActive({ textAlign: alignment }) : false;
        },

        blockType() {
            this.tick;
            if (!editor) return 'p';
            for (const level of [2, 3, 4]) if (editor.isActive('heading', { level })) return `h${level}`;
            return 'p';
        },

        setBlock(value) {
            const chain = editor.chain().focus();
            value === 'p' ? chain.setParagraph().run() : chain.setHeading({ level: Number(value[1]) }).run();
        },

        cmd(name, ...args) {
            editor.chain().focus()[name](...args).run();
        },

        setAlign(alignment) {
            editor.chain().focus().setTextAlign(this.isAlign(alignment) ? null : alignment).run();
        },

        clearFormat() {
            editor.chain().focus().unsetAllMarks().clearNodes().run();
        },

        // ---- links ----
        openLink() {
            this.linkUrl = editor.getAttributes('link').href ?? '';
            this.linkOpen = true;
            this.$nextTick(() => this.$refs.linkInput?.focus());
        },

        applyLink() {
            let url = this.linkUrl.trim();
            if (url === '') {
                editor.chain().focus().extendMarkRange('link').unsetLink().run();
            } else {
                if (!/^(https?:|mailto:|\/|#)/i.test(url)) url = `https://${url}`;
                editor.chain().focus().extendMarkRange('link').setLink({ href: url }).run();
            }
            this.linkOpen = false;
        },

        removeLink() {
            editor.chain().focus().extendMarkRange('link').unsetLink().run();
            this.linkOpen = false;
        },

        // ---- images ----
        pickImage() {
            this.$refs.imageInput.click();
        },

        onImagePicked(event) {
            Array.from(event.target.files).forEach((file) => this.uploadImage(file));
            event.target.value = '';
        },

        async uploadImage(file) {
            this.error = '';
            this.uploading = true;

            try {
                const body = new FormData();
                body.append('image', file);

                const response = await fetch(config.uploadUrl, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': config.csrf, Accept: 'application/json' },
                    body,
                });

                if (!response.ok) {
                    const data = await response.json().catch(() => ({}));
                    throw new Error(data.errors?.image?.[0] ?? data.message ?? 'Upload failed.');
                }

                const { url } = await response.json();
                editor
                    .chain()
                    .focus()
                    .setImage({ src: url, alt: file.name.replace(/\.[^.]+$/, '').replace(/[-_]+/g, ' ') })
                    .run();
            } catch (e) {
                this.error = e.message;
            } finally {
                this.uploading = false;
            }
        },

        // ---- cover image ----
        onCoverPicked(event) {
            const file = event.target.files[0];
            if (!file) return;
            this.coverPreview = URL.createObjectURL(file);
            this.removeCover = false;
            this.dirty = true;
        },

        clearCover() {
            this.coverPreview = null;
            this.removeCover = true;
            this.$refs.coverInput.value = '';
            this.dirty = true;
        },

        // ---- publish box ----
        isFuture() {
            return this.publishedAt !== '' && new Date(this.publishedAt) > new Date();
        },

        publishLabel() {
            if (this.isFuture()) return 'Schedule';
            return this.wasPublished ? 'Update' : 'Publish';
        },

        readTime() {
            return Math.max(1, Math.ceil(this.words / 200));
        },
    };
});

Alpine.start();
