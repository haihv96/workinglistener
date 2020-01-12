<?php

namespace App\Services\ThirdParty\Azure;

use App\Services\ApiBaseService;
use Illuminate\Support\Facades\Redis;

class BotAuthService extends ApiBaseService
{
    private $clientId;
    private $clientSecret;
    private $expireTime = 50; // minute

    public function __construct($baseUri, $clientId, $clientSecret)
    {
        parent::__construct($baseUri);
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    public function getAuthToken()
    {
        $token = Redis::get('azure_bot_auth_token');
        if (!$token) {
            $token = $this->requestAuthToken();
            Redis::set('azure_bot_auth_token', $token);
            Redis::expire('azure_bot_auth_token', $this->expireTime);
        }
        return $token;
    }

    public function requestAuthToken()
    {
        $response = $this->request('POST', '/botframework.com/oauth2/v2.0/token', [
            'form_params' => [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'scope' => 'https://api.botframework.com/.default',
                'grant_type' => 'client_credentials'
            ]
        ]);
        return $response['access_token'];
    }
}
