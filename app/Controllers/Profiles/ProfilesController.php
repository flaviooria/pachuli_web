<?php

namespace App\Controllers\Profiles;

class ProfilesController
{
    public function index(): void
    {
        echo 'Profile';
    }

    public function show(): void
    {
        echo 'Show Profile';
    }

    public static function edit(int $id): void
    {
        echo "Edit Profile: $id";
    }
}