@props(['title' => ''])

{{--
    Facebook + WhatsApp only — both have simple URL-based share intents and the project
    already has their brand icons in public/images/ (reused here, same bare /images/... path
    convention as welcome.blade.php). Twitter/X isn't one of the client's platforms
    (SocialPlatform enum: Facebook/YouTube/Instagram/TikTok/WhatsApp); Instagram/TikTok/YouTube
    don't have a simple "share this link" URL intent, so they're skipped too.
--}}
<div x-data="{ copied: false }" class="flex items-center gap-3">
    <span class="text-sm text-gray-500">Share:</span>
    <a
        href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}"
        target="_blank"
        rel="noopener noreferrer"
        aria-label="Share on Facebook"
    >
        <img src="/images/facebook.png" alt="Facebook" class="h-6 hover:scale-110 transition-transform">
    </a>
    <a
        href="https://wa.me/?text={{ urlencode($title.' '.request()->url()) }}"
        target="_blank"
        rel="noopener noreferrer"
        aria-label="Share on WhatsApp"
    >
        <img src="/images/whatsapp.png" alt="WhatsApp" class="h-6 hover:scale-110 transition-transform">
    </a>
    <button
        type="button"
        @click="navigator.clipboard.writeText('{{ request()->url() }}'); copied = true; setTimeout(() => copied = false, 2000)"
        class="text-sm text-gray-500 hover:text-blue-600 transition"
    >
        <span x-show="!copied">Copy Link</span>
        <span x-show="copied">Copied!</span>
    </button>
</div>
