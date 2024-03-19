<?php

namespace App\Http\Controllers;

use App\Library\Authenticate;
use App\Library\GoogleClient;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $googleClient = null;
        
        if (config('app.api_public_key') && config('app.api_private_key')) {
            $googleClient = new GoogleClient;
            $googleClient->init();    
        }
        
        $auth = Auth::user();

        return Inertia::render(
            'Home', [
            'authUrl' => optional($googleClient)->generateLink(),
            'auth' => $auth
            ]
        );
    }
}