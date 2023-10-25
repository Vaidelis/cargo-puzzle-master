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

        // Deserialize the data back to arrays
        $transports = json_decode($transports, true);
        $containers = json_decode($containers, true);

        $filled_containers = $this->calculateFilledContainers($transports, $containers);

        if(!empty($filled_containers))
        {
            $this->displayCalculatedResults($filled_containers, $containers);
        }
        else
        {
            $_SESSION['error'] = 'Containers are not filled with products';
            $this->redirect('/');
        }
    }

    private function calculateFilledContainers($transports, $containers)
    {
        $containers_for_products = [];
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

                    if($amount['container_place_left'] > 0)
                        $containers_for_products[$key][$count]['place_filled'] = round((1 - $amount['container_place_left'] / $amount['container_place'])  * 100);
                    else
                        $containers_for_products[$key][$count]['place_filled'] = 100;

                    $containers_for_products[$key][$count]['container_key'] = $amount['container_key'];

                    if($amount['amount_left'] > 0) //if still place left in container we should fill rest container
                    {
                        continue;
                    }
                    elseif($amount['amount_left'] < 0) //this case show that container is filled but not all packages is added in container
                    {
                        $count++;
                        $amount = '';
                    }
                    else //this case if amount left is 0
                    {
                        $count++;
                    }
                }
            }
        }

        return $containers_for_products;
    }

    private function calculatePackageInContainer($containers, &$package, $amount)
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

            //calculate max amount of packages to fill in container
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

    private function lowestAmount($amount_left, &$package)
    {
        $lowest_amount = min($amount_left);

        //If packages amount is too much for each container
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

        foreach ($amount_left as $key => $value)
        {
            if ($value['amount_left'] === $lowest_amount['amount_left'])
            {
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

    private function fillRestContainer($amount, &$package)
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

    public function displayCalculatedResults($filled_containers, $containers)
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
