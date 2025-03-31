<?php

namespace App\Providers;

use App\Models\Assay;
use App\Policies\AssayPolicy;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    protected $policies = [
        Assay::class => AssayPolicy::class,
    ];

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
    }
}
