<?php

namespace App\Policies;

use App\Models\Chronicle;
use App\Models\User;

class ChroniclePolicy
{
    public function delete(User $user, Chronicle $chronicle): bool
    {
        return $user->isAdminOrChef()
            || (int) optional($chronicle->world)->user_id === (int) $user->id;
    }

    public function restore(User $user, Chronicle $chronicle): bool
    {
        return $this->delete($user, $chronicle);
    }
}
