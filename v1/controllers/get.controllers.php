<?php

use app\models\lisa2 as Model;

// Empresas
$app->get('/empresas', function () use ($app) {
    $u = new Model\Empresas;
    return $app->json($u->listar());
});

// Centros
$app->get('/centros', function () use ($app) {
    $u = new Model\Centros;
    return $app->json($u->listar());
});

// Roles
$app->get('/roles', function () use ($app) {
    $u = new Model\Roles;
    return $app->json($u->listar());
});

// Informes
$app->get('/informes/historial', function () use ($app) {
    $u = new Model\Informes;
    return $app->json($u->listar());
});

$app->get('/informe', function () use ($app) {
    $u = new Model\Informes;
    return $app->json($u->verInforme());
});

// Config Esquemas Pedido
$app->get('/config/esquemas/pedidos', function () use ($app) {
    $u = new Model\GestorConfigXML;
    return $app->json($u->obtenerTodasVersiones());
});

// Obtener una versión específica
$app->get('/config/esquemas/pedidos/{idVersion}', function ($idVersion) use ($app) {
    $u = new Model\GestorConfigXML;
    return $app->json($u->obtenerVersion($idVersion));
});

// Obtener una seccion/versión específica
$app->get('/config/esquemas/pedidos/{idVersion}/{idSeccion}', function ($idVersion, $idSeccion) use ($app) {
    $u = new Model\GestorConfigXML;
    return $app->json($u->obtenerSeccion($idVersion, $idSeccion));
});

// Obtener una seccion/versión/campo específica
$app->get('/config/esquemas/pedidos/{idVersion}/{idSeccion}/{IdCampo}', function ($idVersion, $idSeccion, $IdCampo) use ($app) {
    $u = new Model\GestorConfigXML;
    return $app->json($u->obtenerCampo($idVersion, $idSeccion, $IdCampo));
});
