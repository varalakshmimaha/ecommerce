<?php

namespace App\Policies;

use App\Models\Address;
use App\Models\User;

class AddressPolicy
{
    /**
     * Determine if the given address can be updated by the user.
     */
    public function update(User $user, Address $address)
    {
        return $user->id === $address->user_id;
    }

    /**
     * Determine if the given address can be deleted by the user.
     */
    public function delete(User $user, Address $address)
    {
        return $user->id === $address->user_id;
    }
}
