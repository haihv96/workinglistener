<?php

namespace App\Services\ThirdParty\BotMessage;

class BotMessageObject
{
    const BOT_MESSAGE_TYPE = 'message';

    public $type;

    public $text;

    public function __construct($type, $text)
    {
        $this->type = $type;
        $this->text = $text;
    }
}
