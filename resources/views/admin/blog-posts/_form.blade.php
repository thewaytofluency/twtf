@php
    $inputClass = 'w-full px-3 py-2 border border-gray-300 rounded-md text-gray-900 text-sm focus:outline-none focus:ring-blue-600 focus:border-blue-600';

    $editorConfig = [
        'title' => old('title', $blogPost->title ?? ''),
        'slug' => old('slug', $blogPost->slug ?? ''),
        // On an existing post the slug is a permalink: never rewrite it just because the title changed.
        'slugTouched' => $blogPost->exists || old('slug') !== null,
        'excerpt' => old('excerpt', $blogPost->getRawOriginal('excerpt') ?? ''),
        'content' => old('content', $blogPost->content ?? ''),
        'status' => old('status', $blogPost->status),
        'publishedAt' => old('published_at', $blogPost->published_at?->format('Y-m-d\TH:i') ?? ''),
        'exists' => $blogPost->exists,
        'wasPublished' => $blogPost->exists && $blogPost->status === 'published',
        'coverUrl' => $blogPost->cover_url,
        'uploadUrl' => route('admin.blog-posts.images'),
        'csrf' => csrf_token(),
    ];

    // [icon, label, click expression, active expression|null]
    $toolbar = [
        [
            ['undo-2', 'Undo', "cmd('undo')", null],
            ['redo-2', 'Redo', "cmd('redo')", null],
        ],
        [
            ['bold', 'Bold', "cmd('toggleBold')", "is('bold')"],
            ['italic', 'Italic', "cmd('toggleItalic')", "is('italic')"],
            ['underline', 'Underline', "cmd('toggleUnderline')", "is('underline')"],
            ['strikethrough', 'Strikethrough', "cmd('toggleStrike')", "is('strike')"],
            ['code', 'Inline code', "cmd('toggleCode')", "is('code')"],
        ],
        [
            ['list', 'Bulleted list', "cmd('toggleBulletList')", "is('bulletList')"],
            ['list-ordered', 'Numbered list', "cmd('toggleOrderedList')", "is('orderedList')"],
            ['quote', 'Quote', "cmd('toggleBlockquote')", "is('blockquote')"],
            ['square-code', 'Code block', "cmd('toggleCodeBlock')", "is('codeBlock')"],
        ],
        [
            ['align-left', 'Align left', "setAlign('left')", "isAlign('left')"],
            ['align-center', 'Align center', "setAlign('center')", "isAlign('center')"],
            ['align-right', 'Align right', "setAlign('right')", "isAlign('right')"],
        ],
        [
            ['link', 'Insert link', 'openLink()', "is('link')"],
            ['unlink', 'Remove link', 'removeLink()', null],
            ['image', 'Insert image', 'pickImage()', null],
            ['minus', 'Horizontal rule', "cmd('setHorizontalRule')", null],
            ['remove-formatting', 'Clear formatting', 'clearFormat()', null],
        ],
    ];
@endphp

<div x-data="postEditor({{ Illuminate\Support\Js::from($editorConfig) }})">
    <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_20rem] gap-6 max-w-7xl">
        {{-- ───────────── Main column ───────────── --}}
        <div class="space-y-4 min-w-0">
            <div>
                <label for="title" class="sr-only">Title</label>
                <input
                    id="title" name="title" type="text" required autocomplete="off"
                    x-model="title" @input="onTitleInput()" @keydown.enter.prevent
                    placeholder="Add title"
                    class="w-full px-4 py-3 text-2xl font-bold border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-blue-600 focus:border-blue-600"
                >
                <div class="mt-2 flex items-center gap-1 text-sm text-gray-500">
                    <span class="flex-shrink-0">Permalink: {{ url('/blog') }}/</span>
                    <input
                        name="slug" type="text" x-model="slug" @input="onSlugInput()" @keydown.enter.prevent
                        placeholder="auto-generated-from-title" aria-label="Permalink slug"
                        class="flex-1 min-w-0 px-2 py-0.5 text-sm border border-transparent hover:border-gray-300 rounded focus:border-blue-600 focus:ring-0 text-gray-700"
                    >
                </div>
            </div>

            <div class="bg-white border border-gray-300 rounded-lg overflow-hidden">
                {{-- Toolbar --}}
                <div class="sticky top-0 z-10 bg-gray-50 border-b border-gray-200 px-2 py-1.5 flex flex-wrap items-center gap-x-1 gap-y-1">
                    <select
                        aria-label="Text style"
                        :value="blockType()" @change="setBlock($event.target.value)"
                        class="text-sm py-1 pl-2 pr-8 border border-gray-300 rounded-md bg-white text-gray-700 focus:ring-blue-600 focus:border-blue-600"
                    >
                        <option value="p">Paragraph</option>
                        <option value="h2">Heading 2</option>
                        <option value="h3">Heading 3</option>
                        <option value="h4">Heading 4</option>
                    </select>

                    @foreach ($toolbar as $group)
                        <span class="w-px h-5 bg-gray-300 mx-1"></span>
                        @foreach ($group as [$icon, $label, $click, $active])
                            <button
                                type="button" title="{{ $label }}" aria-label="{{ $label }}"
                                @mousedown.prevent
                                @click="{{ $click }}"
                                :class="{{ $active ?? 'false' }} ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-200'"
                                class="p-1.5 rounded transition-colors"
                            >
                                <x-dynamic-component :component="'lucide-'.$icon" class="w-4 h-4" />
                            </button>
                        @endforeach
                    @endforeach

                    <input type="file" x-ref="imageInput" accept="image/png,image/jpeg,image/webp,image/gif" class="hidden" @change="onImagePicked($event)">
                </div>

                {{-- Link popover --}}
                <div x-show="linkOpen" x-cloak class="px-3 py-2 bg-blue-50 border-b border-blue-100 flex items-center gap-2">
                    <x-lucide-link class="w-4 h-4 text-blue-600 flex-shrink-0" />
                    <input
                        x-ref="linkInput" x-model="linkUrl" type="text" placeholder="Paste or type a link, e.g. https://example.com"
                        @keydown.enter.prevent="applyLink()" @keydown.escape="linkOpen = false"
                        class="flex-1 text-sm py-1 border border-gray-300 rounded-md focus:ring-blue-600 focus:border-blue-600"
                    >
                    <button type="button" @click="applyLink()" class="text-sm bg-blue-600 text-white font-semibold px-3 py-1 rounded-md hover:bg-blue-700">Apply</button>
                    <button type="button" @click="linkOpen = false" class="text-sm text-gray-600 hover:text-gray-900">Cancel</button>
                </div>

                <div x-show="error" x-cloak class="px-4 py-2 bg-red-50 text-red-700 text-sm border-b border-red-100" x-text="error"></div>

                <div x-ref="surface" class="bg-white"></div>

                <div class="px-4 py-2 bg-gray-50 border-t border-gray-200 text-xs text-gray-500 flex items-center justify-between">
                    <span><span x-text="words"></span> words · ~<span x-text="readTime()"></span> min read</span>
                    <span x-show="uploading" x-cloak class="inline-flex items-center gap-1 text-blue-600">
                        <x-lucide-loader-circle class="w-3.5 h-3.5 animate-spin" /> Uploading image…
                    </span>
                    <span x-show="!uploading" class="hidden sm:inline">Tip: paste or drop images straight into the editor</span>
                </div>
            </div>

            <input type="hidden" name="content" :value="html">
            @error('content')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- ───────────── Sidebar ───────────── --}}
        <aside class="space-y-4">
            {{-- Publish --}}
            <section class="bg-white border border-gray-200 rounded-lg">
                <h2 class="px-4 py-3 border-b border-gray-200 text-sm font-semibold text-gray-800">Publish</h2>
                <div class="p-4 space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Status</span>
                        @if ($blogPost->exists)
                            <span class="font-medium
                                {{ $blogPost->status === 'draft' ? 'text-gray-700' : ($blogPost->isScheduled() ? 'text-amber-600' : 'text-green-600') }}">
                                {{ $blogPost->status === 'draft' ? 'Draft' : ($blogPost->isScheduled() ? 'Scheduled' : 'Published') }}
                            </span>
                        @else
                            <span class="font-medium text-gray-700">New</span>
                        @endif
                    </div>

                    <div>
                        <label for="published_at" class="block text-gray-600 mb-1">Publish date</label>
                        <input id="published_at" name="published_at" type="datetime-local" x-model="publishedAt" class="{{ $inputClass }}">
                        <p class="mt-1 text-xs text-gray-500">Leave empty to publish immediately. A future date schedules the post.</p>
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <button type="submit" name="status" value="draft" class="flex-1 border border-gray-300 text-gray-700 font-semibold px-3 py-2 rounded-md hover:bg-gray-50 transition">
                            {{ $blogPost->exists && $blogPost->status === 'published' ? 'Unpublish' : 'Save Draft' }}
                        </button>
                        <button type="submit" name="status" value="published" class="flex-1 bg-blue-600 text-white font-semibold px-3 py-2 rounded-md hover:bg-blue-700 transition" x-text="publishLabel()">
                            Publish
                        </button>
                    </div>

                    @if ($blogPost->exists)
                        <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                            <a href="{{ route('blog.show', $blogPost) }}" target="_blank" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-700">
                                <x-lucide-external-link class="w-4 h-4" /> {{ $blogPost->status === 'draft' ? 'Preview' : 'View post' }}
                            </a>
                            <button type="submit" form="delete-post-form" class="inline-flex items-center gap-1 text-red-600 hover:text-red-700">
                                <x-lucide-trash-2 class="w-4 h-4" /> Delete
                            </button>
                        </div>
                    @endif
                </div>
            </section>

            {{-- Cover image --}}
            <section class="bg-white border border-gray-200 rounded-lg">
                <h2 class="px-4 py-3 border-b border-gray-200 text-sm font-semibold text-gray-800">Cover image</h2>
                <div class="p-4 space-y-3">
                    <template x-if="coverPreview">
                        <img :src="coverPreview" alt="Cover preview" class="w-full aspect-video object-cover rounded-md border border-gray-200">
                    </template>

                    <input type="hidden" name="remove_cover" :value="removeCover ? 1 : 0">
                    <input
                        x-ref="coverInput" type="file" name="cover" accept="image/png,image/jpeg,image/webp,image/gif"
                        @change="onCoverPicked($event)"
                        class="block w-full text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:bg-blue-50 file:text-blue-700 file:font-semibold hover:file:bg-blue-100"
                    >
                    <button type="button" x-show="coverPreview" @click="clearCover()" class="text-sm text-red-600 hover:text-red-700">Remove cover image</button>
                    <p class="text-xs text-gray-500">JPG, PNG, WebP or GIF, up to 4 MB. Shown on the blog list and above the article.</p>
                    @error('cover')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </section>

            {{-- Excerpt --}}
            <section class="bg-white border border-gray-200 rounded-lg">
                <h2 class="px-4 py-3 border-b border-gray-200 text-sm font-semibold text-gray-800">Excerpt</h2>
                <div class="p-4">
                    <textarea name="excerpt" rows="4" maxlength="500" x-model="excerpt" @input="dirty = true" class="{{ $inputClass }}" placeholder="A short summary for the blog list…"></textarea>
                    <p class="mt-1 text-xs text-gray-500"><span x-text="excerpt.length"></span>/500 · Leave empty to use the start of the article.</p>
                </div>
            </section>

            <a href="{{ route('admin.blog-posts.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-600 hover:text-gray-900">
                <x-lucide-arrow-left class="w-4 h-4" /> Back to all posts
            </a>
        </aside>
    </div>
</div>
