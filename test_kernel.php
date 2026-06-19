<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Mock an authenticated user
Auth::loginUsingId(1);

$request = Request::create('/dashboard', 'GET');
$response = $kernel->handle($request);

echo $response->getContent();
