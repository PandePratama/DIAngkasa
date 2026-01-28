<?php

namespace App\Providers;

use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;
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
        // Membagikan variabel 'cart_count' ke semua file blade
        View::composer('*', function ($view) {
            if (Auth::check()) {
                // Gunakan count() untuk menghitung jumlah JENIS produk
                $count = CartItem::whereHas('cart', function ($query) {
                    $query->where('user_id', Auth::id());
                })->count();

                $view->with('cart_count', $count);
            } else {
                $view->with('cart_count', 0);
            }
        });
    }
}
