<?php
use Kreait\Laravel\Firebase\Facades\Firebase;

use Illuminate\Support\Facades\Route;

 use Illuminate\Support\Facades\Broadcast;

Broadcast::routes([
    'middleware' => ['auth:user-api'],
]);

Route::get('/', function () {
    return view('welcome');
});

Route::get('/', function () {
    return view('welcome');
});
Route::get('/chat-test', function () {
    return view('chat');
});



 