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
    const BUILD_OBJECT_KIND = 'build';
    const BUILD_OBJECT_SUCCESS_STATUS = 'success';
    const TARGET_BRANCH = ['dev', 'master'];

    public function __construct(ChatbotService $chatbotService)
    {
        $this->chatbotService = $chatbotService;
    }

    public function listener(Request $request)
    {
        $hookData = $request->all();
        \Log::info('Gitlab hooks data receiver');
        \Log::info($hookData);
        $eventType = \Arr::get($hookData, 'event_type');
        $objectKind = \Arr::get($hookData, 'object_kind');
        switch ($eventType) {
            case self::MERGE_REQUEST_EVENT_TYPE:
                $this->mergeRequestHandle($hookData);
        }

        switch ($objectKind) {
            case self::BUILD_OBJECT_KIND:
                $this->buildEventHandle($hookData);
        }
    }

    public function mergeRequestHandle($hookData)
    {
        $status = $hookData['object_attributes']['state'];
        if ($status !== 'merged') {
            return;
        }
        $target = $hookData['object_attributes']['target_branch'];
        if (!in_array($target, self::TARGET_BRANCH)) {
            return;
        }
        $projectUrl = $hookData['project']['web_url'];
        $sourceBranch = $hookData['object_attributes']['source_branch'];
        $mergeRequestId = $hookData['object_attributes']['iid'];
        $mergeRequestDescription = $hookData['object_attributes']['description'];
        $authorEmail = $hookData['object_attributes']['last_commit']['author']['email'];
        $mergeRequestUrl = "$projectUrl/merge_requests/$mergeRequestId/diffs";
        $conversationId = ProjectGit::where(['repo_name' => $projectUrl])->first()->project->skype_id;
        $author = User::where(['git_account' => $authorEmail])->first();
        $messageObject = new BotMessageObject(
            BotMessageObject::BOT_MESSAGE_TYPE,
            "<at id='$author->skype_live'>@$author->skype_name</at> MERGED **$sourceBranch** INTO **$target** \n $mergeRequestUrl \n $mergeRequestDescription"
        );
        $this->chatbotService->sendMessage($conversationId, $messageObject);
    }

    public function buildEventHandle($hookData)
    {
        $buildStage = \Arr::get($hookData, 'build_stage');
        $status = $hookData['commit']['status'];
        if ($status !== self::BUILD_OBJECT_SUCCESS_STATUS || $buildStage !== 'deploy') {
            return;
        }
        $projectUrl = $hookData['repository']['homepage'];
        $conversationId = ProjectGit::where(['repo_name' => $projectUrl])->first()->project->skype_id;
        $message = $hookData['commit']['message'];
        $dataExplode = explode('!', $message);
        $mergeRequestId = $dataExplode[sizeof($dataExplode) - 1];
        $mergeRequestUrl = "$projectUrl/merge_requests/$mergeRequestId/diffs";
        $messageObject = new BotMessageObject(
            BotMessageObject::BOT_MESSAGE_TYPE,
            "<at id='$conversationId'>@all</at> \n Đã deploy lên test server: http://cms.ntq.solutions/auth \n $message \n $mergeRequestUrl"
        );
        $this->chatbotService->sendMessage($conversationId, $messageObject);
    }
}
