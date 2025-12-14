<?php

namespace App\Providers;

use App\Models\Goods;
use App\Models\Category;
use App\Models\Order;
use App\Models\Review;
use App\Models\ReviewReply;
use App\Policies\GoodsPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\OrderPolicy;
use App\Policies\ReviewPolicy;
use App\Policies\ReviewReplyPolicy;
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
    Review::class => ReviewPolicy::class,
    ReviewReply::class => ReviewReplyPolicy::class,
    Goods::class    => GoodsPolicy::class,
    Category::class => CategoryPolicy::class,
    Order::class    => OrderPolicy::class,
    ];
}
