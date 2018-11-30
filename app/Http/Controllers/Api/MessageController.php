<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateMessageRequest;
use App\Models\Room;
use App\Services\MessageService;
use Illuminate\Database\Eloquent\Collection;

class MessageController extends Controller
{
    /**
     * @var MessageService
     */
    private $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    /**
     * Get messages list for api
     *
     * @param Room $room
     *
     * @return Collection
     */
    public function messages(Room $room): Collection
    {
        return $this->messageService->getRoomMessages($room);
    }

    /**
     * Create new message and broadcast it to other users
     *
     * @param Room $room
     * @param CreateMessageRequest $request
     *
     * @return array
     */
    public function create(Room $room, CreateMessageRequest $request)
    {
        $this->messageService->createMessage($room, $request);

        return ['status' => 200];
    }
}
