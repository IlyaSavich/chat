<?php

namespace App\Http\Controllers\Api;

use App\Events\MessagePosted;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateMessageRequest;
use App\Models\Message;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RoomController extends Controller
{
    public function rooms()
    {
        /** @var User $user */
        $user = auth()->user();

        $rooms = Room::select('rooms.*')
            ->join('room_user', 'room_user.room_id', '=', 'rooms.id')
            ->where('room_user.user_id', $user->id)
            ->with(['messages', 'users' => function (BelongsToMany $query) use ($user) {
                return $query->where('users.id', '!=', $user->id);
            }])
            ->get();

        return $rooms->map(function (Room $room) {
            $name = $room->name ? $room->name :
                ($room->type === Room::TYPE_DIALOG ?
                    $room->users->first()->name : $room->users->implode('name', ', '));

            return [
                'id' => $room->id,
                'type' => $room->type,
                'name' => $name,
                'last_message' => optional($room->messages->sortByDesc('created_at')->first())->message,
                'users' => $room->users,
            ];
        });
    }

    public function messages(Room $room)
    {
        $messages = Message::select('messages.message', 'messages.created_at', 'users.id as user_id', 'users.name as user_name')
            ->join('users', 'users.id', '=', 'messages.user_id')
            ->where('messages.room_id', $room->id)
            ->get();

        return $messages;
    }

    public function storeMessage(Room $room, CreateMessageRequest $request)
    {
        /** @var User $user */
        $user = auth()->user();

        /** @var \App\Models\Message $message */
        $message = $room->messages()->create([
            'user_id' => $request->user_id,
            'message' => $request->message,
        ]);

        broadcast(new MessagePosted($room, $user, $message))->toOthers();

        return ['status' => 'OK'];
    }
}
