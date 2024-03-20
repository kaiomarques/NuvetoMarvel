<?php
/**
 * Classe para cuidados dos detalhes da autenticação
 * 
 * Php version 8.2.0
 *
 * @category Library
 * @package  App\Library
 * @author   Kaio Luiz Marques <kaiolmarques@gmail.com>
 * @license  https://opensource.org/license/MIT MIT
 * @link     https://github.com/kaiomarques/
 */
namespace App\Library;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * Classe para cuidados dos detalhes da autenticação
 * 
 * @category Controller
 * @package  App\Controllers
 * @author   Kaio Luiz Marques <kaiolmarques@gmail.com>
 * @license  https://opensource.org/license/MIT MIT
 * @link     https://github.com/kaiomarques/
 */
class Authenticate
{
    /**
     * Autentica o usuário com email e senha.
     *
     * @param string $email    E-mail do usuário.
     * @param string $password Senha do usuário.
     * 
     * @return \App\Models\User
     * 
     * @throws \Illuminate\Validation\ValidationException 
     * Se as credenciais forem inválidas.
     */    
    public function auth($email, $password)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw ValidationException::withMessages(
                ['email' => 'E-mail inválido']
            );
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            throw ValidationException::withMessages(
                ['email' => 'E-mail não encontrado']
            );
        }

        if (!password_verify(md5($password), $user->password)) {
            throw ValidationException::withMessages(
                ['password' => 'Senha incorreta']
            );
        }

        Auth::loginUsingId($user->id);

        return $user;
    }

    /**
     * Autentica o usuário usando o serviço do Google.
     *
     * @param mixed $data Dados do usuário fornecidos pelo Google.
     * 
     * @return \Illuminate\Http\RedirectResponse
     * 
     * @throws \Exception Se os dados do usuário forem incompletos ou inválidos.
     */    
    public function authGoogle($data)
    {
        if (!isset($data->givenName)  
            || !isset($data->familyName)  
            || !isset($data->email)
        ) {
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

    /**
     * Realiza o logout do usuário.
     *
     * @return void
     */    
    public function logout()
    {
        Auth::logout();
    }
}