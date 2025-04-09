<?php

use app\models\lisa2 as Model;

// Eliminar
$app->delete('/empresas/{id}', function ($id) use ($app) {
    $u = new Model\Empresas;
    return $app->json($u->eliminar($id));
});


// Pedidos
$app->delete('/pedidos/{id}', function ($id) use ($app) {
    global $http;
    $u = new Model\Pedidos;
    return $app->json($u->cancelar($id, true, $http->request->all()));
});
