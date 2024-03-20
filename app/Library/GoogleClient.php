<?php
/**
 * Classe que se conecta com a API OAuth do Google
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

use Google\Client;
use Google\Service\Oauth2 as ServiceOauth2;
use Google\Service\Oauth2\Userinfo;

/**
 * Classe para cuidados dos detalhes da autenticação
 * 
 * @category Controller
 * @package  App\Controllers
 * @author   Kaio Luiz Marques <kaiolmarques@gmail.com>
 * @license  https://opensource.org/license/MIT MIT
 * @link     https://github.com/kaiomarques/
 */
class GoogleClient
{
    /**
     * Dados do usuário obtidos após autenticação.
     * 
     * @var \Google\Service\Oauth2\Userinfo
     */    
    private Userinfo $_data;

    /**
     * Cliente Google.
     * 
     * @var \Google\Client
     */    
    public readonly Client $client;


    /**
     * Construtor da classe.
     */    
    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * Inicializa o cliente Google com as configurações necessárias.
     * 
     * @return void
     */    
    public function init()
    {
        $guzzleClient = new \GuzzleHttp\Client(
            ['curl' => [CURLOPT_SSL_VERIFYPEER => false, ]]
        );
        $this->client->setHttpClient($guzzleClient);
        $this->client->setClientId(config('app.google_client_id'));
        $this->client->setClientSecret(config('app.google_client_secret'));
        $this->client->setRedirectUri(config('app.asset_url').'googleAuth');
        $this->client->addScope('email');
        $this->client->addScope('profile');
    }

    /**
     * Verifica se o usuário está autenticado com o Google.
     * 
     * @return bool Retorna true se o usuário estiver autenticado, 
     * caso contrário retorna false.
     */    
    public function authenticated()
    {
        if (isset($_GET['code'])) {
            $token = $this->client->fetchAccessTokenWithAuthCode($_GET['code']);
            $this->client->setAccessToken($token['access_token']);
            $google_service = new ServiceOauth2($this->client);
            $this->_data = $google_service->userinfo->get();

            return true;
        }

        return false;
    }

    /**
     * Obtém os dados do usuário após a autenticação.
     * 
     * @return \Google\Service\Oauth2\Userinfo
     */    
    public function getData()
    {
        return $this->_data;
    }

    /**
     * Gera o link de autenticação com o Google.
     * 
     * @return string Retorna o link de autenticação com o Google.
     */   
    public function generateLink()
    {
        return $this->client->createAuthUrl();
    }
}