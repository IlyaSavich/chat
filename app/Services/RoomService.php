<?php

declare(strict_types=1);

namespace App\Services;

use App\Events\RoomCreated;
use App\Events\RoomDeleted;
use App\Models\Room;
use App\Models\User;
use App\Repositories\RoomRepository;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Contracts\Broadcasting\Factory as BroadcastFactory;
use Illuminate\Support\Collection;

class RoomService
{
    /**
     * @var RoomRepository
     */
    private $roomRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var \Illuminate\Contracts\Auth\Factory|\Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard
     */
    private $authFactory;

    /**
     * @var BroadcastFactory|\Illuminate\Broadcasting\BroadcastManager
     */
    private $broadcastFactory;

    public function __construct(RoomRepository $roomRepository, UserRepository $userRepository, AuthFactory $authFactory, BroadcastFactory $broadcastFactory)
    {
        $this->roomRepository = $roomRepository;
        $this->userRepository = $userRepository;
        $this->authFactory = $authFactory;
        $this->broadcastFactory = $broadcastFactory;
    }

    /**
     * Get room list data for api
     *
     * @return Collection
     */
    public function getRoomListForApi(): Collection
    {
        /** @var User $user */
        $user = $this->authFactory->user();
        $rooms = $this->roomRepository->getRoomsListForApi($user);

        return $rooms->map(function (Room $room) use ($user) {
            $roomName = $this->getRoomName($room, $user);

            return [
                'id' => $room->id,
                'type' => $room->type,
                'name' => $roomName,
                'owner_id' => $room->owner_id,
                'last_message' => $room->messages->sortByDesc('created_at')->first(),
                'users_count' => $room->users->count() + 1, // + 1 for current user
            ];
        });
    }

    public function createPublicRoom(string $name): Room
    {
        $user = $this->authFactory->user();
        $room = $this->roomRepository->createPublicRoom($name);

        $room->owner()->associate($user);
        $room->save();

        $this->attachAllUsersToRoom($room);

        $this->broadcastFactory->event(new RoomCreated($room))->toOthers();

        return $room;
    }

    public function createDialogWithEachUser(User $user): void
    {
        $otherUsers = $this->userRepository->getOtherUsers($user);

        foreach ($otherUsers as $anotherUser) {
            $room = $this->roomRepository->createDialog();
            $room->users()->attach($user);
            $room->users()->attach($anotherUser);
        }
    }

    /**
     * @param int $roomId
     *
     * @throws \Exception
     */
    public function deleteRoom(int $roomId): void
    {
        Room::destroy([$roomId]);

        $this->broadcastFactory->event(new RoomDeleted($roomId))->toOthers();
    }

    public function joinEachPublicRoom(User $user): void
    {
        $rooms = $this->roomRepository->getAllPublicRooms();

        foreach ($rooms as $room) {
            $room->users()->attach($user);
        }
    }

    /**
     * For dialog get interlocutor name.
     * For public room get joined users names if the name of room is not provided.
     *
     * @param Room $room
     * @param User $user
     *
     * @return string
     */
    private function getRoomName(Room $room, User $user): string
    {
        if ($room->name) {
            return $room->name;
        }

        if ($room->type === Room::TYPE_DIALOG) {
            /** @var User $anotherUser */
            $anotherUser = $room->users->first(function ($roomUser) use ($user) {
                return $roomUser->id !== $user->id;
            });

            return $anotherUser->name;
        }

        return $room->users->implode('name', ', ');
    }

    private function attachAllUsersToRoom(Room $room)
    {
        $users = $this->userRepository->getAllUsers();

        foreach ($users as $user) {
            $room->users()->attach($user);
        }
    }
}
