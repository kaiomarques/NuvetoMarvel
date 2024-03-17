<?php

namespace App\Http\Controllers;

use App\Library\Authenticate;
use App\Library\GoogleClient;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
Use Session;
use App\Model\User;

class LoginController extends Controller
{
    private $googleClient;
    private $authenticade;

    function __construct(Authenticate $authenticade, GoogleClient $googleClient) {
        $this->googleClient = $googleClient;
        $this->googleClient->init();   

        $this->authenticade = $authenticade;
    }

    public function googleAuth()
    {
        if ($this->googleClient->authenticated()) {
            return $this->authenticade->authGoogle($this->googleClient->getData());
        }
        return Inertia::render('Home', ['authUrl' => $this->googleClient->generateLink()]);
    }

    public function logout() {
        $this->authenticade->logout();
        return redirect('/');
    }

}