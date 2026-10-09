---
name: twtf-project
description: Laravel port of C:\Users\administrator\Documents\coding\react\twtf (React/Vite) into this Blade+Breeze app; key decisions and deviations from the source.
metadata:
  type: project
---

This Laravel app (twtf) is a content-faithful port of a React/Vite marketing+course site at
`C:\Users\administrator\Documents\coding\react\twtf`. It has no real backend beyond auth -
courses/impact/pricing/dashboard stats are all hardcoded arrays, ported verbatim.

**Why:** User explicitly wants a structure/content port, not a feature expansion. Two
architecture decisions were pre-made: Breeze Blade+Alpine stack for auth/UI, and Alpine.js
(not vanilla JS) for interactivity (nav drawer, dashboard drawer).

**How to apply:** When asked to touch this app again, treat the React source as the source of
truth for content/copy/markup - don't "fix" or improve hardcoded data (e.g. broken
`/impact1.jpg` etc. image paths, the reused `/logo.jpg` as course art, MZN pricing) unless
asked.

Key deviations made during the initial port (2026-08-04), each intentional - don't silently
"fix" these without user request:
- Repointed Breeze's default `/dashboard` route to `/home` everywhere (controllers + tests)
  since the source app's private landing route is `/home`, not `/dashboard`. Deleted the
  unused `dashboard.blade.php` / `layouts/navigation.blade.php` / original `layouts/app.blade.php`
  and rewrote `layouts/app.blade.php` to use the ported `partials.navbar` instead of Breeze's nav.
- `/profile` is Breeze's real account-management page (name/email/password/delete), NOT the
  source's placeholder `<div>Profile Page</div>` - keeping working Breeze auth scaffolding took
  priority over literal placeholder parity for this one route.
- Footer (`partials/footer.blade.php`) and Herosection (`partials/hero-section.blade.php`) exist
  as available partials but are NOT included on any page - matches source where their imports
  are present but usage is commented out / unused.
- "Continue with Google" buttons on login/register are visual-only (`disabled`), no Socialite/
  real OAuth - real Google auth was explicitly out of scope.
- Source's dashboard sidebar `/study-guide` (hyphenated) href was a pre-existing dead link in
  the React app itself (router registers `/studyguide`, no hyphen) - the Laravel port uses
  `/studyguide` consistently rather than reproducing the dead link.
- Source's dashboard "Logout" was a dead `<a href="/logout">` (Firebase never actually wired a
  logout handler there). Laravel port uses a real `POST` logout form via Breeze's `logout` route
  since we now have real session auth - needed for `/home` to actually be usable end to end.
- Tailwind: Breeze's `blade` installer standardized the project on Tailwind 3 + PostCSS
  (`tailwind.config.js`, `postcss.config.js`, `@tailwind base/components/utilities`), removing
  the previously-half-set-up Tailwind 4 Vite plugin (`@tailwindcss/vite`) that predated Breeze
  install and was never wired to working auth views.
- `.bg-hero` (wave-blue.svg background, ported from `src/assets/css/main.css`) lives in
  `resources/css/app.css`. `wave-orange.svg` and several `src/assets/*.png/jpg` orphans
  (hero.png, hh*.png, IMG-20250216-WA0009.jpg) were intentionally not ported - unused in source.
