<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;



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
        View::composer('layouts.app', function ($view) {
            $parents = Category::whereNull('parent_id')->get();

            $tree = [];
            foreach ($parents as $parent) {
                $tree[$parent->name] = $parent->children()->pluck('name', 'id')->toArray();
            }
            $view->with('tree', $tree);
        });
        DB::listen(function ($query) {
            if ($query->time > 100) {
                logger()->warning('Slow query', [
                    'sql' => $query->sql,
                    'time' => $query->time,
                ]);
            }
        });
        Model::preventLazyLoading(!app()->isProduction());
    }

}
