<?php

namespace Vaida\CargoPuzzleMaster\Controllers;

use Vaida\CargoPuzzleMaster\Core\Controller;
use Vaida\CargoPuzzleMaster\Models\Container;
use Vaida\CargoPuzzleMaster\Models\Package;
use Vaida\CargoPuzzleMaster\Models\Transport;

session_start();

class ContainerController extends Controller {
    public function index() {

        if (isset($_SESSION['error']))
        {
            $error = $_SESSION['error'];
            unset($_SESSION['error']); // Clear the error message after displaying it
        }
        else
        {
            $error = '';
        }

        $containers = [
            new Container('40ft Standard Dry Container', 234.8, 238.44, 1203.1),
            new Container('10ft Standard Dry Container', 234.8, 238.44, 279.4)
        ];
        $transports = [
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

        $this->render('home/index', ['containers' => $containers, 'transports' => $transports, 'error' => $error]);
    }

    public function calculate()
    {
        $transports = $_POST['transports'];
        $containers = $_POST['containers'];
        $containers_for_products = [];

        // Deserialize the data back to arrays
        $transports = json_decode($transports, true);
        $containers = json_decode($containers, true);

        foreach ($transports as $key => $transport)
        {
            $amount = '';
            $count = 0;
            foreach ($transport['packages'] as &$package)
            {
                while($package['amount'] > 0)
                {
                    $amount = $this->calculatePackageInContainer($containers, $package, $amount);
                    $amount['container_place'] = $containers[$amount['container_key']]['width'] * $containers[$amount['container_key']]['height'] * $containers[$amount['container_key']]['length'];
                    $containers_for_products[$key][$count]['place_filled'] = round((1 - $amount['container_place_left'] / $amount['container_place'])  * 100);
                    $containers_for_products[$key][$count]['container_key'] = $amount['container_key'];
                    if($amount['amount_left'] > 0)
                    {
                        continue;
                    }
                    elseif($package['amount'] == 0)
                    {
                        $count++;
                    }
                    elseif($amount['amount_left'] < 0)
                    {
                        $count++;
                        $amount = '';
                    }
                    else
                    {
                        $count++;
                    }
                }
            }
        }

        if(!empty($containers_for_products))
        {
            $this->displayCalculatedResults($containers_for_products, $transports, $containers);
        }
        else
        {
            $_SESSION['error'] = 'Containers are not filled with products';
            $this->redirect('/');
        }
    }

    public function calculatePackageInContainer($containers, &$package, $amount)
    {
        $amount_left = [];
        if(!empty($amount) && $amount['amount_left'] > 0)
        {
            $amount_left = $this->fillRestContainer($amount, $package);
            $lowest_amount = $this->lowestAmount($amount_left, $package);
            return $lowest_amount;
        }
        foreach ($containers as $key => $container)
        {
            $fill_length = floor($container['length'] / $package['length']);
            $fill_height = floor($container['height'] / $package['height']);
            $fill_width = floor($container['width'] / $package['width']);
            $max_amount = $fill_length * $fill_height * $fill_width;
            $amount_left[$key]['amount_left'] = $max_amount - $package['amount'];
            $amount_left[$key]['max_amount'] = $max_amount;
            if(isset($amount_left[$key]['container_place_left']))
                $amount_left[$key]['container_place_left'] = $amount_left[$key]['container_place_left'] - ($package['length'] * $package['height'] * $package['width'] * $package['amount']);
            else
                $amount_left[$key]['container_place_left'] = ($container['width'] * $container['height'] * $container['length']) - ($package['length'] * $package['height'] * $package['width'] * $package['amount']);
        }

        $lowest_amount = $this->lowestAmount($amount_left, $package);

        return $lowest_amount;
    }

    public function lowestAmount($amount_left, &$package)
    {
        $lowest_amount = min($amount_left);
        if($lowest_amount['amount_left'] < 0)
        {
            foreach ($amount_left as $left)
            {
                if($left['amount_left'] > 0 && $left['amount_left'] < $lowest_amount['amount_left'])
                {
                    $lowest_amount = $left;
                }
            }

        }
        if($lowest_amount['amount_left'] < 0)
        {
            foreach ($amount_left as $left)
            {
                if($left['amount_left'] > $lowest_amount['amount_left'])
                {
                    $lowest_amount = $left;
                }
            }
        }

        foreach ($amount_left as $key => $value) {
            if ($value['amount_left'] === $lowest_amount['amount_left']) {
                $lowest_amount['container_key'] = $key;
                if($value['max_amount'] < $package['amount'])
                {
                    $package['amount'] =  $package['amount'] - $value['max_amount'];
                }
                else
                {
                    $package['amount'] = 0;
                }
                break;
            }
        }

        return $lowest_amount;
    }

    public function fillRestContainer($amount, &$package)
    {
        $max_amount = floor($amount['container_place_left'] / $package['length'] / $package['height'] / $package['width']);
        if($max_amount < $package['amount'])
        {
            $amount_left[$amount['container_key']]['amount_left'] = 0;
            $amount_left[$amount['container_key']]['container_place_left'] = 0;
        }
        else
        {
            $amount_left[$amount['container_key']]['container_place_left'] = $amount['container_place_left'] - ($package['length'] * $package['height'] * $package['width'] * $package['amount']);
            $amount_left[$amount['container_key']]['amount_left'] = $max_amount - $package['amount'];
        }
        $amount_left[$amount['container_key']]['max_amount'] = $max_amount;

        return $amount_left;
    }

    public function displayCalculatedResults($filled_containers, $transports, $containers)
    {
        $containers_list = [];
        foreach ($filled_containers as $key => $filled)
        {
            foreach ($filled as $key2 => $filled_key)
            {
                $containers_list[$key][$key2] = $containers[$filled_key['container_key']];
                $containers_list[$key][$key2]['place_filled'] = $filled_key['place_filled'];
            }
        }
        $this->render('home/CalculationResults', ['containers_list' => $containers_list]);
    }
}
