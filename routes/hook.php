<?php

Route::post('skype', 'SkypeHookController@listener');
Route::get('test', function (){
    echo app('azure_bot_auth')->getAuthToken();
});
