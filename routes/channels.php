<?php

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel("rolls", function (User $user) {return true;});

Broadcast::channel("heartbeat", function (User $user) {return true;});
