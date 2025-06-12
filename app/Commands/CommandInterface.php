<?php

namespace App\Commands;

use App\Models\User;

interface CommandInterface
{
    public function matches(string $message): bool;
    public function execute(User $user, string $message): void;
}
