<?php

namespace App\Http\Controllers;

use App\Library\Authenticate;
use App\Library\GoogleClient;
use Session;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        if(config('app.api_public_key') && config('app.api_private_key')) {
            $googleClient = new GoogleClient;
            $googleClient->init();    
        }
        
        $auth = (Auth::user())?Auth::user():null;

        return Inertia('Home', ['authUrl' => $googleClient->generateLink(), "auth" => $auth]);
    }
}