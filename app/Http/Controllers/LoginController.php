<?php

namespace App\Http\Controllers;

use App\Library\Authenticate;
use App\Library\GoogleClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\AuthenticationException;
use Inertia\Inertia;
use App\Models\User;

class LoginController extends Controller
{
    private $googleClient;
    private $authenticate;

    public function __construct(Authenticate $authenticate, GoogleClient $googleClient)
    {
        $this->authenticate = $authenticate;
        $this->googleClient = $googleClient;

        $this->googleClient->init();   
    }

    public function auth(Request $request)
    {
        try {
            $email = $request->input("email");
            $password = $request->input("password");

            $this->authenticate->auth($email, $password);

            return redirect()->route('index')->with(['success' => 'success']);
        } catch (AuthenticationException $e) {
            return redirect()->route('index')->with(['success' => 'false', 'message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return redirect()->route('index')->with(['success' => 'false', 'message' => $e->getMessage()]);
        }
    }

    public function googleAuth()
    {
        if ($this->googleClient->authenticated()) {
            return $this->authenticate->authGoogle($this->googleClient->getData());
        }
        return Inertia::render('Home', ['authUrl' => $this->googleClient->generateLink()]);
    }

    public function logout()
    {
        $this->authenticate->logout();
        return redirect('/');
    }
}