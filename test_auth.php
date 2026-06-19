<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;

$user = User::first();
if (!$user) {
    echo "No users found in the database.\n";
    exit;
}

echo "Found user: {$user->email}\n";

// We don't know the password, but we can verify the model loads correctly
echo "User ID: {$user->id}\n";
echo "User Role: {$user->role}\n";

// Test if Auth can retrieve the user by ID
Auth::loginUsingId($user->id);
if (Auth::check()) {
    echo "Auth::check() passed. Authentication integration works.\n";
} else {
    echo "Auth::check() failed.\n";
}
