{{--
    Ported from src/components/Footer.tsx. In the source app this component's import is
    commented out in LandingPage.tsx and it was never rendered there. Now included at the
    end of welcome.blade.php (past the fullpage scroll-snap sections), wrapped in a white
    background there; the text and icons are dark slate to stay readable on it.

    $socialLinks comes from the composer registered in AppServiceProvider (shared with the
    including 'welcome' view, not fetched here) — see resources/views/components/social-links.blade.php.
--}}
<footer class="text-slate-700 text-center border-t border-slate-200 pt-10">
    <x-social-links :links="$socialLinks" class="mb-6" icon-class="text-slate-600" brand-hover />

    <p>&copy; 2025 The Way to Fluency. All rights reserved.</p>
    <small class="text-slate-800">
        with &hearts; by <b><a href="https://alfeux.coolpage.biz" target="_blank" rel="noopener noreferrer">AlfeuX</a></b>
    </small>
</footer>
