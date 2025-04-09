<?php

/*
 * This file is part of the Ocrend Framewok 3 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use app\models\lisa as LisaModel;
use Symfony\Component\HttpFoundation\Response;

$app->get('/listar', function () use ($app) {
    $m = new LisaModel\Listar;
    return $app->json($m->listarOrdenesBeta());
});

$app->get('/status-pedido-lisa', function () use ($app) {
    $m = new LisaModel\Listar;
    return $app->json($m->getDetallePedidoLab());
});

$app->get('/pedidos/send-pedido', function () use ($app) {

    global $http;

    $sc = $http->query->get('sc');

    $xml = file_get_contents('lisa/pedidos/ingresados/' . $sc . '.xml');

    $webservice_url = "http://172.16.253.11:8184/mv-api-hl7bus/proxySaidaMLLP";

    $headers = array(
        'Content-Type: text/xml; charset=utf-8',
        'Content-Length: ' . strlen($xml),
    );

    $ch = curl_init($webservice_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $data = curl_exec($ch);

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($http_code == 200) {

        $fileIngresadas = 'lisa/pedidos/enviados/' . $sc . '.xml';
        file_put_contents($fileIngresadas, $xml, FILE_APPEND);

        return new Response($data,
            200,
            array('Content-Type' => 'application/xml')
        );

    } else {
        return new Response($data,
            $http_code,
            array('Content-Type' => 'application/xml')
        );
    }

    // ssh -i C:\Users\mchang\Desktop\apikeys\ApiLis2_key.pem azureuser@20.97.208.97

});
