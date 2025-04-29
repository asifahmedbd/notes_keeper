<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Spatie\Permission\Models\Role;

class AppServiceProvider extends ServiceProvider {


    public function register() {

    }


    public function boot() {

        $roles = ['admin', 'editor', 'reader'];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

    }

}
