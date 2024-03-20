<?php
/**
 * Controller que da página inicial que gera o botão de 
 * login do google e o formulário para o login regular
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

use App\Library\Authenticate;
use App\Library\GoogleClient;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

/**
 * Controller que da página inicial que gera o botão de 
 * login do google e o formulário para o login regular
 * 
 * @category Controller
 * @package  App\Controllers
 * @author   Kaio Luiz Marques <kaiolmarques@gmail.com>
 * @license  https://opensource.org/license/MIT MIT
 * @link     https://github.com/kaiomarques/
 */
class HomeController extends Controller
{
    /**
     * Exibe a página inicial.
     *
     * @return \Inertia\Response
     */
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