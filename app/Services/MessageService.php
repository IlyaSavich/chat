<?php

declare(strict_types=1);

namespace App\Services;

use App\Events\MessagePosted;
use App\Http\Requests\CreateMessageRequest;
use App\Models\Message;
use App\Models\Room;
use App\Models\User;
use App\Repositories\MessageRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Contracts\Broadcasting\Factory as BroadcastFactory;

class MessageService
{
    /**
     * @var MessageRepository
     */
    private $messageRepository;

    /**
     * @var AuthFactory|\Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard
     */
    private $authFactory;

    /**
     * @var BroadcastFactory
     */
    private $broadcastFactory;

    public function __construct(MessageRepository $messageRepository, AuthFactory $authFactory, BroadcastFactory $broadcastFactory)
    {
        $this->messageRepository = $messageRepository;
        $this->authFactory = $authFactory;
        $this->broadcastFactory = $broadcastFactory;
    }

    public function getRoomMessages(Room $room): Collection
    {
        return $this->messageRepository->getRoomMessages($room);
    }

    public function createMessage(Room $room, CreateMessageRequest $request): void
    {
        /** @var User $user */
        $user = $this->authFactory->user();

        /** @var \App\Models\Message $message */
        $message = $room->messages()->create([
            'user_id' => $request->user_id,
            'message' => $request->message,
            'created_at' => new Carbon($request->created_at),
        ]);

        $message->load('user');

        $this->broadcastFactory->event(new MessagePosted($room, $user, $message))->toOthers();
    }
}
