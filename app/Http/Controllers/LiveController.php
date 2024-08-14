<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Broadcast;

class LiveController extends Controller {

    public function heartbeat(Request $request) {
        $cached = Cache::get("alive", []);

        $heartbeat = json_decode($request->getContent(), true);

        foreach ($cached as $key => $value) {
            if (($heartbeat["timestamp"] - $value["timestamp"]) > 20000) {
                unset($cached[$key]);
            }
        }

        $cached[$heartbeat["sheet_id"]] = $heartbeat;

        Cache::put("alive", $cached);
    }

    public function alive(Request $request) {
        return Cache::get("alive", []);
    }

}
