<?php

namespace App\Library;

use Google\Client;
use Google\Service\Oauth2 as ServiceOauth2;
use Google\Service\Oauth2\Userinfo;

class GoogleClient
{
    private Userinfo $data;
    public readonly Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function init()
    {
        $guzzleClient = new \GuzzleHttp\Client(['curl' => [CURLOPT_SSL_VERIFYPEER => false, ]]);
        $this->client->setHttpClient($guzzleClient);
        $this->client->setClientId(config('app.google_client_id'));
        $this->client->setClientSecret(config('app.google_client_secret'));
        $this->client->setRedirectUri(config('app.asset_url').'/googleAuth');
        $this->client->addScope('email');
        $this->client->addScope('profile');
    }

    public function authenticated()
    {
        if (isset($_GET['code'])) {
            $token = $this->client->fetchAccessTokenWithAuthCode($_GET['code']);
            $this->client->setAccessToken($token['access_token']);
            $google_service = new ServiceOauth2($this->client);
            $this->data = $google_service->userinfo->get();

            return true;
        }

        return false;
    }

    public function getData()
    {
        return $this->data;
    }

    public function generateLink()
    {
        return $this->client->createAuthUrl();
    }
}