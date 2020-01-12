<?php

namespace App\Http\Controllers\Hooks;

use App\Services\Hooks\Skype\SkypeHookHandle;
use Illuminate\Http\Request;

class SkypeHookController extends HookBaseController
{
    protected $skypeHookHandle;

    public function __construct(SkypeHookHandle $skypeHookHandle)
    {
        $this->skypeHookHandle = $skypeHookHandle;
    }

    public function listener(Request $request)
    {
        $this->skypeHookHandle->listener($request);
    }
}
