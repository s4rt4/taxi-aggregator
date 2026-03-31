<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('operator.{operatorId}', function ($user, $operatorId) {
    return $user->operator && $user->operator->id === (int) $operatorId;
});

Broadcast::channel('passenger.{userId}', function ($user, $userId) {
    return $user->id === (int) $userId;
});
