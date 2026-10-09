# Deploying without a shell (Wasmer Edge + GitHub)

Wasmer builds the app straight from GitHub and gives you no SSH, so `php artisan migrate`,
`db:seed` and `storage:link` can't be typed anywhere. The app does those jobs itself instead.

## What happens on each release

1. Wasmer builds and starts the new version from your GitHub branch.
2. The **first web request** (or Wasmer's optional post-deployment job, see below) runs
   `App\Support\Deployer`, which:
   - creates the storage folders, group-writable (`775`), so uploads can be written and read back;
   - runs the migrations;
   - fills what is missing: plans, social links, landing courses/impact, and the admin account;
   - with `DEPLOY_DEMO=true`, loads the demo content once.
3. Media is served by a `/storage/{path}` route straight from the public disk, so **no
   `storage:link` symlink is needed** (and nothing breaks if the host can't create one).

Every step is idempotent: running it again changes nothing, never resets the admin's password and
never brings back things you deleted.

## Setup (all in the Wasmer dashboard, no commands)

Set these as the app's environment variables / secrets:

| Variable | Value | Notes |
|---|---|---|
| `APP_ENV` | `production` | |
| `APP_DEBUG` | `false` | |
| `APP_KEY` | `base64:...` | Generate once on your PC: `php artisan key:generate --show` |
| `APP_URL` | `https://your-app.wasmer.app` | `https://` makes every generated URL https |
| `TRUST_PROXIES` | `true` | The host terminates TLS; needed for correct redirects/links |
| `DB_CONNECTION` `DB_HOST` `DB_PORT` `DB_DATABASE` `DB_USERNAME` `DB_PASSWORD` | your Wasmer MySQL | Use MySQL, not SQLite (see Storage) |
| `SESSION_DRIVER` / `CACHE_STORE` / `QUEUE_CONNECTION` | `database` | Already the defaults |
| `LOG_CHANNEL` | `stderr` | Logs show in Wasmer's log viewer |
| `AUTO_DEPLOY` | `true` | Runs the bootstrap on the first request after each release |
| `ADMIN_EMAIL` | you@example.com | Created on first run |
| `ADMIN_PASSWORD` | a strong password | **Required** - without it no admin is created |
| `DEPLOY_DEMO` | `true` | Optional: load demo students, videos, documents, posts, courses |
| `DEMO_PASSWORD` | something private | Password of the demo student accounts (default `password`!) |

After the first visit, log in at `/login` with `ADMIN_EMAIL` / `ADMIN_PASSWORD`.

### Forcing it to run again

- Visit nothing: it runs by itself after every release that adds migrations.
- To run it on demand, or with no code change: `curl -X POST https://your-app.wasmer.app/__deploy`
  (returns the steps it ran as JSON). Changing `APP_RELEASE` to any new value also makes the next
  request re-run it.
- To protect that endpoint, set `DEPLOY_TOKEN` and send it as `Authorization: Bearer <token>`
  (or `X-Deploy-Token`). It is safe to leave open - it only repeats the idempotent steps above and is
  rate limited (6 requests/minute).

### Optional: run it as a Wasmer post-deployment job

Copy `deploy/wasmer/app.yaml.example` to `app.yaml` in the repo root. It adds a job that calls
`POST /__deploy` right after every deploy (so the first visitor never waits for it) and mounts a
persistent volume for uploads. Check Wasmer's
[app.yaml docs](https://docs.wasmer.io/edge/configuration/) first: the file is merged with the
settings Wasmer already holds for your app, and an invalid `app.yaml` can break the deploy.
The automatic bootstrap works fine without it.

## Storage: where uploaded files live

Wasmer replaces the app's own folder on every deploy, so anything written there (uploaded
PDFs, payment proofs, cover images, avatars) disappears with the next release. To keep uploads, mount
a **volume** (see the example `app.yaml`) and point the disks at it:

```
PUBLIC_DISK_ROOT=/data/public     # cover images, avatars, landing photos  (served at /storage/...)
PRIVATE_DISK_ROOT=/data/private   # documents, payment proofs (never public)
```

Wasmer's docs say volumes are single-region and not suited to databases - which is why the database
should be MySQL, not a SQLite file.

If you skip the volume, it still works for testing: with `DEPLOY_DEMO=true` the demo seeders recreate
the sample PDFs and images on every release. Anything uploaded by hand is lost on the next deploy.

## Media permissions

The `public` disk is configured (`config/filesystems.php`) to create directories as `775` and files as
`664`, and the bootstrap makes the folders group-writable, so the web server can always read what
the app wrote. Media is only ever served from the public disk: the private disk (documents, payment
proofs) is never reachable through `/storage`, and `..` path tricks are refused.

## Before you push: assets

`public/build` (the compiled CSS/JS from `npm run build`) is git-ignored. If a deployed page has no
styling, Wasmer's build isn't running the Vite build; run `npm run build` locally and either remove
`/public/build` from `.gitignore` and commit it, or ask Wasmer's build to run it. Never deploy
`public/hot` (a stale file there makes pages request assets from a dev server that doesn't exist).

## PHP version

The project requires PHP 8.3+. Pick PHP 8.3 or newer in Wasmer's PHP settings.
