<?php

namespace App\Http\Middleware;

use App\Models\Room;
use Closure;

class RoomOwner
{
    /**
     * Only owner of the room can delete it.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $roomId = $request->route('id');

        $isRoomOwner = Room::where('id', $roomId)
            ->where('owner_id', auth()->id())
            ->exists();

        abort_if(!$isRoomOwner, 403);

        return $next($request);
    }
}
