<?php
/**
 * Controller responsável pelo login
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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\AuthenticationException;
use Inertia\Inertia;
use App\Models\User;

/**
 * Controller responsável pelo login
 * 
 * @category Controller
 * @package  App\Controllers
 * @author   Kaio Luiz Marques <kaiolmarques@gmail.com>
 * @license  https://opensource.org/license/MIT MIT
 * @link     https://github.com/kaiomarques/
 */
class LoginController extends Controller
{
    /**
     * Instância do cliente Google.
     * 
     * @var GoogleClient
     */    
    private $_googleClient;

    /**
     * Instância do serviço de autenticação.
     * 
     * @var Authenticate
     */    
    private $_authenticate;

    /**
     * Construtor da classe.
     * 
     * @param Authenticate $authenticate Instância do serviço de autenticação.
     * @param GoogleClient $googleClient Instância do cliente Google.
     */    
    public function __construct(
        Authenticate $authenticate, 
        GoogleClient $googleClient
    ) {
        $this->_authenticate = $authenticate;
        $this->_googleClient = $googleClient;

        $this->_googleClient->init();   
    }

    /**
     * Método para autenticar um usuário.
     * 
     * @param Request $request Requisição HTTP.
     * 
     * @return \Illuminate\Http\RedirectResponse
     */    
    public function auth(Request $request)
    {
        try {
            $email = $request->input("email");
            $password = $request->input("password");

            $this->_authenticate->auth($email, $password);

            return redirect()->route('index')->with(
                ['success' => 'success']
            );
        } catch (AuthenticationException $e) {
            return redirect()->route('index')->with(
                ['success' => 'false', 'message' => $e->getMessage()]
            );
        } catch (\Exception $e) {
            return redirect()->route('index')->with(
                ['success' => 'false', 'message' => $e->getMessage()]
            );
        }
    }

    /**
     * Método para iniciar o processo de autenticação com o Google.
     * 
     * @return \Illuminate\Http\Response|\Inertia\Response
     */
    public function googleAuth()
    {
        if ($this->_googleClient->authenticated()) {
            return $this->_authenticate->authGoogle($this->googleClient->getData());
        }
        return Inertia::render(
            'Home', 
            ['authUrl' => $this->_googleClient->generateLink()]
        );
    }

    /**
     * Método para fazer logout do usuário.
     * 
     * @return \Illuminate\Http\RedirectResponse
     */    
    public function logout()
    {
        $this->_authenticate->logout();
        return redirect('/');
    }
}