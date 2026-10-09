{{-- Optional image upload with live preview and remove. $currentUrl: existing image URL or null. --}}
<div x-data="{ preview: @js($currentUrl), removed: false }">
    <label class="block text-sm font-medium text-gray-700 mb-1">Image</label>

    <template x-if="preview">
        <img :src="preview" alt="Image preview" class="mb-3 w-full max-w-xs aspect-video object-cover rounded-md border border-gray-200">
    </template>

    <input type="hidden" name="remove_image" :value="removed ? 1 : 0">
    <input
        x-ref="file" type="file" name="image" accept="image/png,image/jpeg,image/webp,image/gif"
        @change="const f = $event.target.files[0]; if (f) { preview = URL.createObjectURL(f); removed = false }"
        class="block w-full text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:bg-blue-50 file:text-blue-700 file:font-semibold hover:file:bg-blue-100"
    >
    <button type="button" x-show="preview" x-cloak @click="preview = null; removed = true; $refs.file.value = ''" class="mt-2 text-sm text-red-600 hover:text-red-700">Remove image</button>
    <p class="mt-1 text-sm text-gray-500">JPG, PNG, WebP or GIF, up to 4 MB. Shown at the top of the landing page card.</p>
</div>
