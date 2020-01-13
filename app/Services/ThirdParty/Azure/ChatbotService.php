<?php

namespace App\Services\ThirdParty\Azure;

use App\Services\ApiBaseService;
use App\Services\ThirdParty\BotMessage\BotMessageObject;

class ChatbotService extends ApiBaseService
{
    public function __construct()
    {
        parent::__construct('https://smba.trafficmanager.net/apis/v3/');
    }

    public function sendMessage($conversationId, BotMessageObject $message)
    {
        $path = "conversations/$conversationId/activities";
        $authToken = app('azure_bot_auth')->getAuthToken();
        $this->request('POST', $path, [
            'headers' => [
                'Authorization' => "Bearer $authToken"
            ],
            'json' => [
                'type' => $message->type,
                'text' => $message->text
            ]
        ]);
    }
}
