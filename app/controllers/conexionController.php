<?php

/*
 *
 * This file is part of the Ocrend Framewok 3 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace app\controllers;

use app\models as Model;
use Ocrend\Kernel\Controllers\Controllers;
use Ocrend\Kernel\Controllers\IControllers;
use Ocrend\Kernel\Router\IRouter;

/**
 * Controlador conexionController/
 *
 */

class conexionController extends Controllers implements IControllers
{

    public function __construct(IRouter $router)
    {
        parent::__construct($router);

        $conn = new Model\Login;

        header('Content-Type: application/json');
        # echo json_encode($conn->query_Oracle());

        # Helper\Functions::redir('/web');

    }
}
