<?php

namespace Vaida\CargoPuzzleMaster\Core;

class Controller {
    protected function render($view, $data = []) {
        extract($data);

        $viewsPath = __DIR__ . '/../Views/';
        include $viewsPath . $view . '.php';
    }
}