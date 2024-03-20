<?php
/**
 * Controller que utilizado para o cadastro e registro
 * do usuário do sistema
 * 
 * Php version 8.2.0
 *
 * @category Controller
 * @package  App\Controllers
 * @author   Kaio Luiz Marques <kaiolmarques@gmail.com>
 * @license  https://opensource.org/license/MIT MIT
 * @link     https://github.com/kaiomarques/
 */
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;

/**
 * Controller que utilizado para o cadastro e registro
 * do usuário do sistema
 * 
 * @category Controller
 * @package  App\Controllers
 * @author   Kaio Luiz Marques <kaiolmarques@gmail.com>
 * @license  https://opensource.org/license/MIT MIT
 * @link     https://github.com/kaiomarques/
 */
class UserController extends Controller
{
    /**
     * Exibe o formulário de registro.
     *
     * @return \Inertia\Response
     */    
    public function register()
    {
        return Inertia::render("Register");
    }

    /**
     * Processa o formulário de registro.
     *
     * @param \Illuminate\Http\Request $request Requisição HTTP.
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            $request->validate(
                [
                'firstname' => 'required',
                'lastname' => 'required',
                'email' => 'required|email',
                'password' => 'required'
                ], [
                'firstname.required' => 'O campo "nome" é obrigatório',
                'lastname.required' => 'O campo "sobrenome" é obrigatório',
                'email.required' => 'O campo "email" é obrigatório',
                'email.email' => 'Esse campo tem que ter um email válido',
                'password.required' => 'O campo "password" é obrigatório'
                ]
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            $request->flash();
            return redirect()->route('register')
                ->withErrors($errors)
                ->with(['success' => 'false', 'message' => 'Erros de validação']);
        }

        $user = User::where('email', $request->input('email'))->first();

        if ($user) {
            if ($user->password != '') {
                return redirect()->route('register')
                    ->with(
                        [
                        'success' => 'false', 
                        'message' => 'Esse usuário já existe']
                    );
            }
            $user->update(["password" => md5($request->input('password'))]);
        } else {
            $user = new User;
            $user->firstname  = $request->input('firstname');
            $user->lastname   = $request->input('lastname');
            $user->email      = $request->input('email');
            $user->password   = md5($request->input('password'));

            $user->save();
        }

        return redirect()->route('register')
            ->with(
                [
                    'success' => 'true',
                    'message' => 'Cadastro realizado com sucesso']
            );
    }
}