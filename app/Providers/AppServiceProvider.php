<?php

namespace App\Providers;

use App\Models\BlogPost;
use App\Models\Comment;
use App\Models\SocialMediaLink;
use App\Models\Video;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::enforceMorphMap([
            'video' => Video::class,
            'blog_post' => BlogPost::class,
            'comment' => Comment::class,
        ]);

        // Shares admin-editable social links (Phase 2's /admin/social-media-links) with the
        // views that render them (icon row + WhatsApp widget), so those views don't depend on
        // every controller that renders them (welcome, guest auth pages, the whole student
        // dashboard shell) individually fetching and passing the data down.
        View::composer(['welcome', 'layouts.guest', 'components.layouts.student'], function ($view) {
            $view->with(
                'socialLinks',
                SocialMediaLink::visible()->get()->keyBy(fn (SocialMediaLink $link) => $link->platform->value)
            );
        });
    }
}
