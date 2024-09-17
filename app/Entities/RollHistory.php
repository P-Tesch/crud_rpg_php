<?php

namespace App\Entities;

class RollHistory {

    /** @var array<string, mixed> */
    private $rolls;

    public function __construct() {
        $this->rolls = [];
    }

    /**
     * @return array<string, mixed>
     */
    public function getRolls() : array {
        return $this->rolls;
    }

    /**
     * @param array<string, mixed> $roll
     * @return void
     */
    public function addRoll(array $roll) : void {
        if (count($this->rolls) >= 20) {
            array_shift($this->rolls);
        }
        $this->rolls[] = $roll;
    }
}
