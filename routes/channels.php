<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

use App\Models\{User, Room};

Broadcast::channel('user.registration', function (User $user) {
    return $user;
});

Broadcast::channel('rooms', function ($user) {
    return $user;
});
