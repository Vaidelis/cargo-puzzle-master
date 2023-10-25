<?php

namespace Vaida\CargoPuzzleMaster\Models;

class Container
{
    public $name;
    public $width;
    public $height;
    public $length;

    public function __construct($name, $width, $height, $length)
    {
        $this->name = $name;
        $this->width = $width;
        $this->height = $height;
        $this->length = $length;
    }
}
