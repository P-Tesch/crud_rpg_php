<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LiveController extends Controller {

    /**
     * Registers a heartbeat signal
     * @param Request $request
     * @return void
     */
    public function heartbeat(Request $request) : void {
        $cached = Cache::get("alive", []);

        $heartbeat = json_decode($request->getContent(), true);

        foreach ($cached as $key => $value) {
            if (($heartbeat["timestamp"] - $value["timestamp"]) > 120000) {
                unset($cached[$key]);
            }
        }

        $cached[$heartbeat["sheet_id"]] = $heartbeat;

        Cache::put("alive", $cached);
    }

    /**
     * Returns the live connections
     * @param Request $request
     * @return array<int, object>
     */
    public function alive(Request $request) : array {
        return Cache::get("alive", []);
    }

}
