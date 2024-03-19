<?php

namespace App\Library;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class Authenticate
{
    public function auth($email, $password)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw ValidationException::withMessages(['email' => 'E-mail inválido']);
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            throw ValidationException::withMessages(['email' => 'E-mail não encontrado']);
        }

        if (!password_verify(md5($password), $user->password)) {
            throw ValidationException::withMessages(['password' => 'Senha incorreta']);
        }

        Auth::loginUsingId($user->id);

        return $user;
    }

    public function authGoogle($data)
    {
        if (!isset($data->givenName) || !isset($data->familyName) || !isset($data->email)) {
            throw new \Exception('Dados do usuário do Google incompletos');
        }

        if (!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('E-mail inválido fornecido pelo Google');
        }

        $user = User::where('email', $data->email)->first();
        
        if (!$user) {
            $user = new User;
            $user->firstName = $data->givenName;
            $user->lastName = $data->familyName;
            $user->email = $data->email;
            $user->foto = $data->picture;
            $user->save();

            if (!$user->id) {
                throw new \Exception('Erro ao salvar o usuário');
            }
        }

        Auth::loginUsingId($user->id);

        return redirect()->to('/');
    }

    public function logout()
    {
        Auth::logout();
    }
}