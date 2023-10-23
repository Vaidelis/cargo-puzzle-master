<?php

namespace Vaida\CargoPuzzleMaster\Models;

class Package
{
    public $amount;
    public $width;
    public $height;
    public $length;

    public function __construct($amount, $width, $height, $length)
    {
        $this->amount = $amount;
        $this->width = $width;
        $this->height = $height;
        $this->length = $length;
    }
}
