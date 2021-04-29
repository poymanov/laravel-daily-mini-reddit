<?php

namespace App\Providers;

use App\Models\Community;
use App\Models\Post;
use App\Models\PostComment;
use App\Policies\CommunityPolicy;
use App\Policies\PostCommentPolicy;
use App\Policies\PostPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Community::class   => CommunityPolicy::class,
        Post::class        => PostPolicy::class,
        PostComment::class => PostCommentPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
