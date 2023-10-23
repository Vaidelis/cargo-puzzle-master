<?php
namespace Vaida\CargoPuzzleMaster\Models;
class Transport {
    public $packages;

    public function __construct($packages) {
        $this->packages = $packages;
    }
}