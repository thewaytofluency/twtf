<?php

namespace App\Support;

use App\Models\Course;
use App\Models\Doc;
use App\Models\ImpactItem;
use App\Models\Plan;
use App\Models\SocialMediaLink;
use App\Models\User;
use App\Models\Video;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\CourseSeeder;
use Database\Seeders\DemoContentSeeder;
use Database\Seeders\ImpactItemSeeder;
use Database\Seeders\PlanSeeder;
use Database\Seeders\SocialMediaLinkSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Throwable;

/**
 * Everything a release needs that would normally be typed into a shell: migrations, first-run
 * data, writable storage folders and a public media location that doesn't depend on a
 * `storage:link` symlink. Every step is safe to repeat.
 */
class Deployer
{
    /** @var array<int, string> */
    private array $log = [];

    /** @return array<int, string> a line per step, for the command output / endpoint response */
    public function run(bool $withDemo = false): array
    {
        $this->log = [];

        $this->step('Preparing storage folders', fn () => $this->prepareStorage());
        $this->step('Running migrations', fn () => $this->migrate());
        $this->step('Seeding essential data', fn () => $this->seedEssentials());

        if ($withDemo) {
            $this->step('Loading demo content', fn () => $this->seedDemo());
        }

        return $this->log;
    }

    /**
     * Folders Laravel writes to, plus the two media disks. Created with group-writable
     * permissions so uploaded files can be written and read back by the web server user.
     */
    private function prepareStorage(): string
    {
        $folders = [
            storage_path('framework/cache/data'),
            storage_path('framework/sessions'),
            storage_path('framework/views'),
            storage_path('logs'),
            base_path('bootstrap/cache'),
            config('filesystems.disks.public.root'),
            config('filesystems.disks.local.root'),
        ];

        $created = [];
        foreach (array_unique($folders) as $folder) {
            if (! File::isDirectory($folder)) {
                File::makeDirectory($folder, 0775, true);
                $created[] = basename($folder);
            }

            @chmod($folder, 0775);
        }

        // Media is served by the /storage/{path} route straight from the public disk, so no
        // symlink is needed - but if one is wanted (and possible), `php artisan storage:link` still works.
        return $created ? 'created '.implode(', ', $created) : 'ok';
    }

    private function migrate(): string
    {
        Artisan::call('migrate', ['--force' => true]);

        return trim(Artisan::output()) ?: 'ok';
    }

    /**
     * What the site needs to be usable. Each seeder only fills what's missing, so an admin's
     * edits (plans, courses, links...) are never overwritten.
     */
    private function seedEssentials(): string
    {
        $done = [];

        if (Plan::count() === 0) {
            (new PlanSeeder)->run();
            $done[] = 'plans';
        }

        if (SocialMediaLink::count() === 0) {
            (new SocialMediaLinkSeeder)->run();
            $done[] = 'social links';
        }

        (new CourseSeeder)->run();
        (new ImpactItemSeeder)->run();
        if (Course::count() || ImpactItem::count()) {
            $done[] = 'landing content';
        }

        if (! User::where('role', 'admin')->exists()) {
            $done[] = $this->createAdmin() ? 'admin account' : 'admin account SKIPPED (set ADMIN_PASSWORD)';
        }

        return $done ? implode(', ', $done) : 'ok';
    }

    private function createAdmin(): bool
    {
        // Never fall back to a well-known password on a deployed site.
        if (! config('deploy.admin.password') && ! app()->environment('local', 'testing')) {
            return false;
        }

        (new AdminUserSeeder)->run();

        return true;
    }

    private function seedDemo(): string
    {
        // Demo content is loaded once; after that it's the admin's to edit or delete.
        if (Video::exists() || Doc::exists()) {
            return 'already loaded';
        }

        if (! User::where('role', 'admin')->exists()) {
            return 'skipped (no admin account yet)';
        }

        (new DemoContentSeeder)->run();

        return 'loaded';
    }

    private function step(string $label, callable $callback): void
    {
        try {
            $this->log[] = "{$label}: ".$callback();
        } catch (Throwable $e) {
            $this->log[] = "{$label}: FAILED - ".$e->getMessage();
            report($e);

            throw $e;
        }
    }
}
