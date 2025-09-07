<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Auth\GenericUser;

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

// Route::post('/custom/broadcast/auth/route', function () {
//     $user = new GenericUser(['id' => microtime()]);

//     request()->setUserResolver(function () use ($user) {
//         return $user;
//     });

//     return Broadcast::auth(request());
// });

// Change to a public channel
Broadcast::channel('response.{hash}', function ($user, $hash) {
    return true;
});

Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('custom-event.{hash}', function ($user, $hash) {
    return true;
});
