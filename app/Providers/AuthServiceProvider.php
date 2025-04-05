<?php

namespace App\Providers;

use App\Models\Assay;
use App\Policies\AssayPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Assay::class => AssayPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
