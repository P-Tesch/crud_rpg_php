<?php

namespace App\Entities;

class RollHistory {

    private $rolls;

    public function __construct() {
        $this->rolls = [];
    }

    public function getRolls() {
        return $this->rolls;
    }

    public function addRoll(array $roll) {
        if (count($this->rolls) >= 20) {
            array_shift($this->rolls);
        }
        $this->rolls[] = $roll;
    }
}
