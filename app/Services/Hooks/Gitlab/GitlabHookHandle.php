<?php

namespace App\Services\Hooks\Gitlab;

use App\Models\Project;
use App\Models\ProjectGit;
use App\Models\User;
use App\Services\BaseService;
use App\Services\ThirdParty\Azure\ChatbotService;
use App\Services\ThirdParty\BotMessage\BotMessageObject;
use Illuminate\Http\Request;

class GitlabHookHandle extends BaseService
{
    protected $chatbotService;

    const MERGE_REQUEST_EVENT_TYPE = 'merge_request';

    public function __construct(ChatbotService $chatbotService)
    {
        $this->chatbotService = $chatbotService;
    }

    public function listener(Request $request)
    {
        $hookData = $request->all();
        \Log::info('Gitlab hooks data receiver');
        \Log::info($hookData);
        $eventType = $hookData['event_type'];
        switch ($eventType) {
            case self::MERGE_REQUEST_EVENT_TYPE:
                $this->mergeRequestHandle($hookData);
        }
    }

    public function mergeRequestHandle($hookData)
    {
        $status = $hookData['object_attributes']['state'];
        if ($status !== 'merged') {
            return;
        }
        $projectUrl = $hookData['project']['web_url'];
        $sourceBranch = $hookData['object_attributes']['source_branch'];
        $target = $hookData['object_attributes']['target_branch'];
        $mergeRequestId = $hookData['object_attributes']['iid'];
        $authorEmail = $hookData['object_attributes']['last_commit']['author']['email'];
        $mergeRequestUrl = "$projectUrl/merge_requests/$mergeRequestId/diffs";
        $conversationId = ProjectGit::where(['repo_name' => $projectUrl])->first()->project->skype_id;
        $author = User::where(['git_account' => $authorEmail])->first();
        $messageObject = new BotMessageObject(
            BotMessageObject::BOT_MESSAGE_TYPE,
            "<at id='$author->skype_live'>@$author->skype_name</at> MERGED **$sourceBranch** INTO **$target** \n $mergeRequestUrl"
        );
        $this->chatbotService->sendMessage($conversationId, $messageObject);
    }
}
