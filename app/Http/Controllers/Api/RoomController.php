<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateRoomRequest;
use App\Models\Room;
use App\Services\RoomService;
use Illuminate\Support\Collection;

class RoomController extends Controller
{
    /**
     * @var RoomService
     */
    private $roomService;

    public function __construct(RoomService $roomService)
    {
        $this->roomService = $roomService;
    }

    /**
     * Get room list for api
     *
     * @return Collection
     */
    public function rooms(): Collection
    {
        return $this->roomService->getRoomListForApi();
    }

    /**
     * Create public room
     *
     * @param CreateRoomRequest $request
     *
     * @return array
     */
    public function create(CreateRoomRequest $request)
    {
        $room = $this->roomService->createPublicRoom($request->name);

        return ['status' => 200, 'room' => $room];
    }

    /**
     * Delete public room
     *
     * @param int $roomId
     *
     * @return array
     * @throws \Exception
     */
    public function delete(int $roomId): array
    {
        $this->roomService->deleteRoom($roomId);

        return ['status' => 200];
    }
}
