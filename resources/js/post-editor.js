import { Editor, Extension } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Image from '@tiptap/extension-image';
import { CharacterCount, Placeholder } from '@tiptap/extensions';

/**
 * Text alignment stored as a `data-align` attribute rather than an inline style. The server
 * sanitizer (App\Support\PostHtml) strips `style` attributes entirely, and the stylesheet
 * (resources/css/app.css) maps `[data-align]` back to `text-align`.
 */
const TextAlign = Extension.create({
    name: 'textAlign',

    addGlobalAttributes() {
        return [
            {
                types: ['paragraph', 'heading'],
                attributes: {
                    textAlign: {
                        default: null,
                        parseHTML: (element) => element.getAttribute('data-align') || element.style.textAlign || null,
                        renderHTML: (attributes) => (attributes.textAlign ? { 'data-align': attributes.textAlign } : {}),
                    },
                },
            },
        ];
    },

    addCommands() {
        return {
            setTextAlign:
                (alignment) =>
                ({ commands }) =>
                    ['paragraph', 'heading'].map((type) => commands.updateAttributes(type, { textAlign: alignment })).some(Boolean),
        };
    },
});

/**
 * Mounts the WYSIWYG editor. Image files (toolbar, paste, drag-and-drop) are handed to
 * `onImageFiles`, which uploads them and inserts the result.
 */
export function createPostEditor(element, { content, placeholder, onChange, onTransaction, onImageFiles }) {
    const imageFiles = (dataTransfer) =>
        Array.from(dataTransfer?.files ?? []).filter((file) => file.type.startsWith('image/'));

    return new Editor({
        element,
        content,
        extensions: [
            StarterKit.configure({
                heading: { levels: [2, 3, 4] },
                link: {
                    openOnClick: false,
                    autolink: true,
                    HTMLAttributes: { rel: 'noopener noreferrer nofollow', target: '_blank' },
                },
            }),
            Image.configure({ HTMLAttributes: { loading: 'lazy' } }),
            TextAlign,
            Placeholder.configure({ placeholder }),
            CharacterCount,
        ],
        editorProps: {
            attributes: {
                class: 'post-content prose prose-gray max-w-none min-h-[26rem] px-6 py-5 focus:outline-none',
            },
            handlePaste: (_view, event) => {
                const files = imageFiles(event.clipboardData);
                if (!files.length) return false;
                onImageFiles(files);
                return true;
            },
            handleDrop: (_view, event) => {
                const files = imageFiles(event.dataTransfer);
                if (!files.length) return false;
                event.preventDefault();
                onImageFiles(files);
                return true;
            },
        },
        onUpdate: ({ editor }) => onChange(editor),
        onTransaction: () => onTransaction(),
    });
}
