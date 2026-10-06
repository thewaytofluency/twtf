@props(['links'])

{{-- Floating site-wide chat widget (Phase 6). Renders nothing if the admin has no visible
     WhatsApp link configured (or has hidden it via the visibility toggle). --}}
@if ($links->has('whatsapp'))
    <a
        href="{{ $links['whatsapp']->url }}"
        target="_blank"
        rel="noopener noreferrer"
        aria-label="Chat with us on WhatsApp"
        class="fixed bottom-6 right-6 z-50 flex items-center justify-center w-14 h-14 rounded-full bg-green-500 text-white shadow-lg hover:bg-green-600 hover:scale-110 transition-all"
    >
        <i class="fa-brands fa-whatsapp text-3xl"></i>
    </a>
@endif
