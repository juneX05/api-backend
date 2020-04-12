<?php

namespace App\Listeners;

use Illuminate\Support\Facades\DB;
use Laravel\Passport\Client;
use Carbon\Carbon;
use Laravel\Passport\Events\RefreshTokenCreated;
use Laravel\Passport\Passport;
use Laravel\Passport\Token;

class PruneOldTokens {
    public function __construct() {
        //
    }
    public function handle(RefreshTokenCreated $event) {
        DB::table('oauth_refresh_tokens')
            ->whereDate('expires_at', '<', now()->addDays(1))
            ->delete();
    }
}