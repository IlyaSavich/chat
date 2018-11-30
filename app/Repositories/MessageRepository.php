<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Message;
use App\Models\Room;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageRepository
{
    /**
     * @param Room $room
     *
     * @return Collection|Message[]
     */
    public function getRoomMessages(Room $room): Collection
    {
        return Message::select('messages.user_id', 'messages.message', 'messages.created_at')
            ->where('messages.room_id', $room->id)
            ->orderBy('messages.created_at')
            ->with(['user' => function (BelongsTo $query) {
                return $query->select('users.id', 'users.name');
            }])
            ->get();
    }
}
