<?php

declare(strict_types=1);

namespace App\Services;

use App\Events\UserRegistered;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Broadcasting\Factory as BroadcastFactory;

class RegistrationService
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var RoomService
     */
    private $roomService;

    /**
     * @var BroadcastFactory|\Illuminate\Broadcasting\BroadcastManager
     */
    private $broadcastFactory;

    public function __construct(UserRepository $userRepository, RoomService $roomService, BroadcastFactory $broadcastFactory)
    {
        $this->userRepository = $userRepository;
        $this->roomService = $roomService;
        $this->broadcastFactory = $broadcastFactory;
    }

    /**
     * Create user on registration step
     *
     * @param array $data
     *
     * @return User
     */
    public function createUser(array $data): User
    {
        $user = $this->userRepository->createUser($data);

        //In chat each user has dialog with each user
        $this->roomService->createDialogWithEachUser($user);
        // In chat each user can access all public rooms
        $this->roomService->joinEachPublicRoom($user);

        $this->broadcastFactory->event(new UserRegistered($user))->toOthers();

        return $user;
    }
}
