<?php

namespace App\Http\Controllers;

use App\Library\Authenticate;
use App\Library\GoogleCLient;
use Session;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $googleClient = new GoogleClient;
        $googleClient->init();
        
        $auth = (Auth::user())?Auth::user():null;

        return Inertia('Home', ['authUrl' => $googleClient->generateLink(), "auth" => $auth]);
    }
}