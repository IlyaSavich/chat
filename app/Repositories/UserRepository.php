<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository
{
    /**
     * @return Collection|User[]
     */
    public function getAllUsers(): Collection
    {
        return User::all();
    }

    /**
     * @param User $user
     *
     * @return Collection|User[]
     */
    public function getOtherUsers(User $user): Collection
    {
        return User::where('id', '!=', $user->id)->get();
    }

    public function createUser(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => \Hash::make($data['password']),
        ]);
    }
}
