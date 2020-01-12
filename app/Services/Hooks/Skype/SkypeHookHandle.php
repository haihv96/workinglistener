<?php

namespace App\Services\Hooks\Skype;

use App\Models\User;
use App\Services\BaseService;
use App\Services\ThirdParty\Azure\ChatbotService;
use App\Services\ThirdParty\BotMessage\BotMessageObject;
use Illuminate\Http\Request;

class SkypeHookHandle extends BaseService
{
    const MESSAGE_TYPE = 'message';
    const CONTACT_RELATION_UPDATE_TYPE = 'contactRelationUpdate';

    protected $chatbotService;

    public function __construct(ChatbotService $chatbotService)
    {
        $this->chatbotService = $chatbotService;
    }

    public function listener(Request $request)
    {
        $hookData = $request->all();
        \Log::info('Skype hooks data receiver');
        \Log::info($hookData);
        $hookType = $hookData['type'];
        switch ($hookType) {
            case self::CONTACT_RELATION_UPDATE_TYPE:
                $this->handleOnContactRelationUpdateHook();
            case self::MESSAGE_TYPE:
                $this->handleOnMessageHook($hookData);
        }
    }

    public function handleOnContactRelationUpdateHook()
    {

    }

    /**
     * @param $hookData
     */
    public function handleOnMessageHook($hookData)
    {
        try {
            \DB::beginTransaction();
            $text = $hookData['text'];
            $text = trim(\Str::after($text, SKYPE_BOT_NAME));
            $skypeId = $hookData['from']['id'];
            $skypeName = $hookData['from']['name'];
            $skypeGroupId = $hookData['conversation']['id'];
            $user = User::where(['skype_id' => $skypeId])->first();
            if (!$user) {
                $user = User::create(['skype_id' => $skypeId]);
            }
            $user->update(['skype_name' => $skypeName]);
            if (strpos($text, 'register')) {
                $dataRegisterString = trim(\Str::after($text, 'register'));
                $dataRegisterArr = explode(',', $dataRegisterString);
                foreach ($dataRegisterArr as $dataRegister) {
                    \Log::info($dataRegister);
                    list($key, $value) = explode('=', $dataRegister);
                    $user->update([
                        $key => $value
                    ]);
                }
            }
//            $messageObject = new BotMessageObject(BotMessageObject::BOT_MESSAGE_TYPE, 'Update profile successful!');
//            $this->chatbotService->sendMessage($user->skype_id, $messageObject);
            \DB::commit();
        } catch (\Exception $e) {
            \Log::error('SkypeHookHandle::handleOnMessageHook');
            \Log::error($e);
            \Log::info('SkypeHookHandle::handleOnMessageHook($hookData)');
            \Log::info($hookData);
            \DB::rollback();
        }
    }
}
