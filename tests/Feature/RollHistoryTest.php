<?php

use Illuminate\Support\Facades\Cache;

test('roll history is read back from the cache as a plain array', function () {
    $rolls = [['type' => 'generic', 'roll' => 7]];
    Cache::put('rollsTable', $rolls);

    $this->getJson('/api/roll')
        ->assertOk()
        ->assertExactJson($rolls);
});

test('roll history is empty when nothing has been cached', function () {
    $this->getJson('/api/roll')->assertOk()->assertExactJson([]);
});
