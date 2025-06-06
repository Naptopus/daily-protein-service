<?php

namespace App\Data;

use Carbon\CarbonInterface;

class ItemData
{
    public function __construct(
        public string $name,
        public float $protein,
        public CarbonInterface $date
    ) {
    }
}
