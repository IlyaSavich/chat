<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoomRepository
{
    public function getRoomsListForApi(User $user)
    {
        return Room::select('rooms.*')
            ->join('room_user', 'room_user.room_id', '=', 'rooms.id')
            ->where('room_user.user_id', $user->id)
            ->with([
                'messages' => function (HasMany $query) {
                    return $query
                        ->select('messages.user_id', 'messages.room_id', 'messages.message')
                        ->with(['user' => function (BelongsTo $query) {
                            return $query->select('users.id', 'users.name');
                        }])
                        ->latest('messages.created_at');
                },
                'users' => function (BelongsToMany $query) use ($user) {
                    return $query->where('users.id', '!=', $user->id);
                }])
            ->get();
    }

    /**
     * @return Collection|Room[]
     */
    public function getAllPublicRooms(): Collection
    {
        return Room::where('type', Room::TYPE_PUBLIC)->get();
    }

    /**
     * Create and store in db new public room by name.
     *
     * @param string|null $name
     *
     * @return Room
     */
    public function createPublicRoom(string $name = null): Room
    {
        return Room::create([
            'name' => $name,
            'type' => Room::TYPE_PUBLIC,
        ]);
    }

    public function createDialog(): Room
    {
        return Room::create(['type' => Room::TYPE_DIALOG]);
    }
}
