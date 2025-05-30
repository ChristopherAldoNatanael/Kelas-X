<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $this->app['auth']->viaRequest('api', function ($request) {
            // Memeriksa token dari header Authorization
            if ($request->header('Authorization')) {
                $key = explode(' ', $request->header('Authorization'));
                if (count($key) > 1) {
                    $token = $key[1];
                    return User::where('api_token', $token)->first();
                }
            }
            
            // Memeriksa token dari parameter URL atau form
            if ($request->input('api_token')) {
                return User::where('api_token', $request->input('api_token'))->first();
            }
            
            return null;
        });
    }
}
