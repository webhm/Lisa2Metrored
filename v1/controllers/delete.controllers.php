<?php

use app\models\lisa2 as Model;

// Eliminar
$app->delete('/empresas/{id}', function ($id) use ($app) {
    $u = new Model\Empresas;
    return $app->json($u->eliminar($id));
});


// Pedidos
$app->delete('/pedidos/{tipo}/{id}', function ($tipo, $id) use ($app) {
    $u = new Model\Pedidos;
    if ($tipo == 1) {
        // Cancelación Total
        return $app->json($u->cancelar($id, true));
    } else {
        // Cancelación Parcial
        return $app->json($u->cancelar($id, false));
    }
});
