<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
Use Session;

class UserController extends Controller
{
    public function register() {
      //print_r(session()->get("request"));die;
      return Inertia::render("Register");
    }

    public function store(Request $request)
    {

        try {
            $request->validate([
                'firstname'  => 'required',
                'lastname'  => 'required',
                'email' => 'required|email',
                'password' => 'required'
            ], [
                'firstname.required' => 'O campo "nome" é obrigatório',
                'lastname.required' => 'O campo "sobrenome" é obrigatório',
                'email.required' => 'O campo "email" é obrigatório',
                'email.email' => 'Esse campo tem que ter um email válido',
                'password.required' => 'O campo "password" é obrigatório'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            $request->flash();
            return redirect()->route('register')->withErrors($errors);
        }
    
        $user = User::where('email', $request->input('email'))->first();
    
        if ($user && $user->password != '') {
            return redirect()->route('register')->withErrors(['error' => 'Esse usuário já existe']);
        }
    
        if ($user && !password_verify($request->input('password'), $user->password)) {
            return redirect()->route('register')->withErrors(['error' => 'Email or password invalid']);
        }
    
        $user = new User;
        $user->firstname  = $request->input('firstname');
        $user->lastname   = $request->input('lastname');
        $user->email      = $request->input('email');
        $user->password   = md5($request->input('password'));
    
        $user->save();
    
        return redirect()->route('register')->withSuccess('Cadastro realizado com sucesso');
    }
}
