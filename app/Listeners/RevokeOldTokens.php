<?php

namespace App\Listeners;

use Laravel\Passport\Events\AccessTokenCreated;
use Laravel\Passport\Client;
use Carbon\Carbon;
use Laravel\Passport\Passport;
use Laravel\Passport\Token;

class RevokeOldTokens {
    public function __construct() {
        //
    }
    public function handle(AccessTokenCreated $event) {
        Token::where([
            ['user_id', $event->userId],
            ['id', '<>', $event->tokenId]
        ])->delete();
    }
}