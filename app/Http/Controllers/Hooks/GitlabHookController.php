<?php

namespace App\Http\Controllers\Hooks;

use App\Services\Hooks\Gitlab\GitlabHookHandle;
use Illuminate\Http\Request;

class GitlabHookController extends HookBaseController
{
    protected $gitlabHookHandle;

    public function __construct(GitlabHookHandle $gitlabHookHandle)
    {
        $this->gitlabHookHandle = $gitlabHookHandle;
    }

    public function listener(Request $request)
    {
        $this->gitlabHookHandle->listener($request);
    }
}
