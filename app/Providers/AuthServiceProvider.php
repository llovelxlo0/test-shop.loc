<?php

namespace App\Providers;

use App\Models\Goods;
use App\Models\Category;
use App\Models\Review;
use App\Policies\GoodsPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\ReviewPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
    protected $policies = [
    \App\Models\Review::class => \App\Policies\ReviewPolicy::class,
    Goods::class    => GoodsPolicy::class,
    //Category::class => CategoryPolicy::class,
    Review::class   => ReviewPolicy::class,
    ];

}
