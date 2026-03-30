<?php
use Kreait\Laravel\Firebase\Facades\Firebase;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/chat-test', function () {
    return view('chat');
});



 