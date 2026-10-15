<?php
namespace App\Policies;

use App\Models\Place;
use App\Models\User;

class PlacePolicy
{
    public function viewAny(User $user): bool { return $user->worlds()->whereKey((int) session('selected_world_id'))->exists(); }
    public function view(User $user, Place $place): bool { return $this->ownsActiveWorld($user, $place); }
    public function create(User $user): bool { return $this->viewAny($user); }
    public function update(User $user, Place $place): bool { return $this->ownsActiveWorld($user, $place); }
    public function delete(User $user, Place $place): bool { return $this->ownsActiveWorld($user, $place); }

    private function ownsActiveWorld(User $user, Place $place): bool
    {
        return (int) $place->world_id === (int) session('selected_world_id')
            && $user->worlds()->whereKey((int) $place->world_id)->exists();
    }
}
