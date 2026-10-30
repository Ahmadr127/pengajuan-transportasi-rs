<?php

use App\Models\User;

foreach (['user', 'admin'] as $name) {
    $user = User::where('username', $name)->first();
    $user->tokens()->where('name', 'debug-list')->delete();
    echo $name.'='.$user->createToken('debug-list')->plainTextToken.PHP_EOL;
}
