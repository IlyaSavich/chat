<?php

namespace App\Http\Controllers\Api;

use App\Events\MessagePosted;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateMessageRequest;
use App\Http\Requests\CreateRoomRequest;
use App\Models\Message;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoomController extends Controller
{
    public function rooms()
    {
        /** @var User $user */
        $user = auth()->user();

        $rooms = Room::select('rooms.*')
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

        return $rooms->map(function (Room $room) {
            $name = $room->name ? $room->name :
                ($room->type === Room::TYPE_DIALOG ?
                    $room->users->first()->name : $room->users->implode('name', ', '));

            return [
                'id' => $room->id,
                'type' => $room->type,
                'name' => $name,
                'last_message' => $room->messages->sortByDesc('created_at')->first(),
                'users_count' => $room->users->count() + 1, // + 1 for current user
            ];
        });
    }

    public function createRoom(CreateRoomRequest $request)
    {
        $room = Room::create([
            'name' => $request->name,
            'type' => Room::TYPE_PUBLIC,
        ]);

        $users = User::all();

        foreach ($users as $user) {
            $room->users()->attach($user);
        }

        return ['status' => 200];
    }

    public function messages(Room $room)
    {
        $messages = Message::select('messages.user_id', 'messages.message', 'messages.created_at')
            ->where('messages.room_id', $room->id)
            ->orderBy('messages.created_at')
            ->with(['user' => function (BelongsTo $query) {
                return $query->select('users.id', 'users.name');
            }])
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

        return ['status' => 200];
    }
}
