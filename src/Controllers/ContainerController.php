<?php

namespace Vaida\CargoPuzzleMaster\Controllers;

use Vaida\CargoPuzzleMaster\Core\Controller;
use Vaida\CargoPuzzleMaster\Models\Container;
use Vaida\CargoPuzzleMaster\Models\Package;
use Vaida\CargoPuzzleMaster\Models\Transport;

class ContainerController extends Controller {
    public function index() {
        $Containers = [
            new Container('40ft Standard Dry Container', 234.8, 238.44, 1203.1),
            new Container('10ft Standard Dry Container', 234.8, 238.44, 279.4)
        ];
        $Transports = [
            new Transport([
                new Package(27, 78, 79, 93),
            ]),
            new Transport([
                new Package(24, 30, 60, 90),
                new Package(33, 75, 100, 200),
            ]),
            new Transport([
                new Package(10, 80, 100, 200),
                new Package(25, 60, 80, 150),
            ]),
        ];

        $this->render('home/index', ['containers' => $Containers, 'transports' => $Transports]);
    }

    public function calculate()
    {
        $transports = $_POST['transports'];
        $containers = $_POST['containers'];

        // Deserialize the data back to arrays
        $transports = json_decode($transports, true);
        $containers = json_decode($containers, true);
        echo 'test';

    }
}
