<?php



use app\models\lisa2 as Model;

// Empresas
$app->put('/empresas/{id}', function ($id) use ($app) {
    $u = new Model\Empresas;
    return $app->json($u->editar($id));
});


// Pedidos
$app->put('/pedidos/{id}', function ($id) use ($app) {
    $u = new Model\Pedidos;
    return $app->json($u->editar($id));
});
