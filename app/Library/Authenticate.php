<?php

namespace App\Library;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    public function authGoogle($data)
    {
        $user = new User;
        $userFound = $user->where('email', $data->email)->first();
        if (!$userFound) {
            $user->insert([
                'firstName' => $data->givenName,
                'lastName' => $data->familyName,
                'email' => $data->email,
                'foto' => $data->picture,
            ]);
        }

        Auth::loginUsingId($userFound->id);

        return redirect()->to('/');
    }

    public function logout()
    {
        Auth::logout();
    }
}