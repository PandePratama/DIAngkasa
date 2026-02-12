<?php

namespace App\Providers;

use App\Models\CartItem;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
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
    public function boot()
    {
        Paginator::useBootstrap();
        // Membagikan variabel 'cart_count' ke semua file blade
        View::composer('*', function ($view) {

            if (config('app.env') === 'production') {
                URL::forceScheme('https');
            }
            if (Auth::check()) {
                // Gunakan count() untuk menghitung jumlah JENIS produk
                $count = CartItem::whereHas('cart', function ($query) {
                    $query->where('id_user', Auth::id());
                })->count();

                $view->with('cart_count', $count);
            } else {
                $view->with('cart_count', 0);
            }
        });
    }
}
