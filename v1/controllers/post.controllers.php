<?php


use app\models\lisa2 as Model;

// Empresas
$app->post('/empresas', function () use ($app) {
    $u = new Model\Empresas;
    return $app->json($u->crear());
});

// Centros
$app->post('/centros', function () use ($app) {
    $u = new Model\Centros;
    return $app->json($u->crear());
});

// Roles
$app->post('/roles', function () use ($app) {
    $u = new Model\Roles;
    return $app->json($u->crear());
});

// Usuarios
$app->post('/usuarios', function () use ($app) {
    $u = new Model\Usuarios;
    return $app->json($u->crear());
});


// Pedidos
$app->post('/pedidos', function () use ($app) {
    $u = new Model\Pedidos;
    return $app->json($u->crear());
});

// Config Esquemas Pedido
$app->post('/config/esquemas/pedidos', function () use ($app) {
    $u = new Model\GestorConfigXML;
    return $app->json($u->obtenerTodasVersiones());
});
