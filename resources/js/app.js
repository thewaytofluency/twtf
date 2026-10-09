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

// ---------------------------------------------------------------------------------------------
// Lesson flow (videos & documents): completion state, the playlist counter and video autoplay.
// ---------------------------------------------------------------------------------------------

/** Completion state shared by the video player and the document reader. */
const progressState = (cfg) => ({
    completed: cfg.completed,
    doneCount: cfg.doneCount,
    busy: false,

    async setCompleted(value) {
        if (this.busy || value === this.completed) return;
        this.busy = true;

        try {
            const response = await fetch(cfg.url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': cfg.csrf,
                },
                body: JSON.stringify({ type: cfg.type, id: cfg.id, completed: value }),
            });

            if (!response.ok) throw new Error('Could not save progress');

            this.completed = value;
            this.doneCount += value ? 1 : -1;
        } catch (e) {
            console.error(e);
        } finally {
            this.busy = false;
        }
    },

    toggle() {
        return this.setCompleted(!this.completed);
    },
});

Alpine.data('lessonProgress', (cfg) => progressState(cfg));

let youTubeApi = null;
const loadYouTubeApi = () => {
    youTubeApi ??= new Promise((resolve) => {
        if (window.YT?.Player) return resolve(window.YT);

        const previous = window.onYouTubeIframeAPIReady;
        window.onYouTubeIframeAPIReady = () => {
            previous?.();
            resolve(window.YT);
        };

        const script = document.createElement('script');
        script.src = 'https://www.youtube.com/iframe_api';
        document.head.appendChild(script);
    });

    return youTubeApi;
};

const AUTOPLAY_KEY = 'twtf.autoplay';
const COUNTDOWN_SECONDS = 8;

Alpine.data('lessonPlayer', (cfg) => {
    // Kept outside the reactive object (Alpine's proxy and the YouTube player don't mix).
    let player = null;
    let timer = null;

    return {
        ...progressState(cfg),

        nextUrl: cfg.nextUrl,
        nextTitle: cfg.nextTitle,
        autoplay: true,
        upNext: false,
        secondsLeft: COUNTDOWN_SECONDS,

        init() {
            try {
                this.autoplay = localStorage.getItem(AUTOPLAY_KEY) !== 'off';
            } catch {
                // storage blocked: keep the default
            }

            if (!cfg.hasEmbed) return;

            loadYouTubeApi().then((YT) => {
                player = new YT.Player(this.$refs.frame, {
                    events: {
                        onStateChange: (event) => {
                            if (event.data === YT.PlayerState.ENDED) this.onEnded();
                        },
                    },
                });
            });
        },

        saveAutoplay() {
            try {
                localStorage.setItem(AUTOPLAY_KEY, this.autoplay ? 'on' : 'off');
            } catch {
                // storage blocked: the choice just won't persist
            }
        },

        async onEnded() {
            await this.setCompleted(true);

            // Finishing a lesson always shows what's next; it only moves on by itself if autoplay is on.
            this.upNext = true;
            if (this.nextUrl && this.autoplay) this.startCountdown();
        },

        startCountdown() {
            this.stopCountdown();
            this.secondsLeft = COUNTDOWN_SECONDS;

            timer = setInterval(() => {
                this.secondsLeft -= 1;
                if (this.secondsLeft <= 0) {
                    this.stopCountdown();
                    window.location.href = this.nextUrl;
                }
            }, 1000);
        },

        stopCountdown() {
            clearInterval(timer);
            timer = null;
        },

        cancelUpNext() {
            this.stopCountdown();
            this.upNext = false;
        },
    };
});

Alpine.start();
