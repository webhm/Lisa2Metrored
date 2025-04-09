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

/**
 *
 * @return json
 */

$app->post('/pedidos/nuevo-pedido', function () use ($app) {

    $xml = file_get_contents('php://input');

    $data = utf8_encode($xml);
    $dataPedido = simplexml_load_string($data);

    $nuevoPedido = new LisaModel\Pedidos;
    $nuevoPedido->dataPedido = $dataPedido;
    $nuevoPedido->documentoPedido = $xml;
    $nuevoPedido->in_nuevoPedido();

    $output = '<?xml version="1.0"?><Mensagem><sucesso><descricao>OPERACAO REALIZADA COM SUCESSO</descricao></sucesso></Mensagem>';

    return new Response($output,
        200,
        array('Content-Type' => 'application/xml')
    );

    // http://172.16.253.11:8184/mv-api-hl7bus/proxySaidaMLLP

    // ssh -i C:\Users\mchang\Desktop\apikeys\ApiLis2_key.pem azureuser@20.97.208.97

});

$app->post('/pdd', function () use ($app) {

    $xml = file_get_contents('php://input');

    $data = utf8_encode($xml);
    // Extract XML Dcoument
    $dataPedido = simplexml_load_string($data);
    $pedido = $dataPedido->children('soap', true)->Body->children();
    $codigoPedido = $pedido->Mensagem->PedidoExameLab->codigoPedido;
    $nomeMaquina = $pedido->Mensagem->PedidoExameLab->atendimento->nomeMaquina;
    $usuarioSolicitante = $pedido->Mensagem->PedidoExameLab->atendimento->usuarioSolicitante;
    $strSolicitante = $pedido->Mensagem->PedidoExameLab->descSetorSolicitante;

    $fechaTomaMuestra = $pedido->Mensagem->PedidoExameLab->atendimento->dataColetaPedido;
    $fechaPedido = date("Y-m-d", strtotime($fechaTomaMuestra));

    $fileIngresadas = 'lisa/pedidos/ingresados/' . $codigoPedido . '.xml';

    if ($usuarioSolicitante == 'KLAVERDE') {

        $new_xml = str_replace($nomeMaquina, 'CAJA1', $xml);

        file_put_contents($fileIngresadas, $new_xml, FILE_APPEND);
        chmod($fileIngresadas, 0777);

    } else if ($usuarioSolicitante == 'DDELGADO') {

        $new_xml = str_replace($nomeMaquina, 'CAJA2', $xml);

        file_put_contents($fileIngresadas, $new_xml, FILE_APPEND);
        chmod($fileIngresadas, 0777);

    } else {

        $new_xml = $xml;

        file_put_contents($fileIngresadas, $new_xml, FILE_APPEND);
        chmod($fileIngresadas, 0777);

    }

    if ($strSolicitante == 'EMERGENCIA') {

        $fileRetenidos = 'lisa/pedidos/retenidos/sector/emergencia/' . $codigoPedido . '.xml';
        file_put_contents($fileRetenidos, $new_xml, FILE_APPEND);

    } else {

        # Registro envpio de log
        if ($fechaPedido == date('Y-m-d')) {

            $webservice_url = "http://172.16.253.17:8084/mv-api-hl7bus/proxySaidaMLLP";

            $headers = array(
                'Content-Type: text/xml; charset=utf-8',
                'Content-Length: ' . strlen($new_xml),
            );

            $ch = curl_init($webservice_url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $new_xml);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $data = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_code == 200) {

                $fileEnviados = 'lisa/pedidos/enviados/' . $codigoPedido . '.xml';
                file_put_contents($fileEnviados, $new_xml, FILE_APPEND);
                $logsfileEnviados = 'lisa/pedidos/enviados/log_success_' . $codigoPedido . '.xml';
                file_put_contents($logsfileEnviados, $data, FILE_APPEND);

            } else {

                $logsfileEnviados = 'lisa/pedidos/enviados/log_error_' . $codigoPedido . '.xml';
                file_put_contents($logsfileEnviados, $data, FILE_APPEND);

            }

        } else {

            $fileRetenidos = 'lisa/pedidos/retenidos/' . $codigoPedido . '.xml';
            file_put_contents($fileRetenidos, $new_xml, FILE_APPEND);

        }

    }

    $output = '<?xml version="1.0"?><Mensagem><sucesso><descricao>OPERACAO REALIZADA COM SUCESSO</descricao></sucesso></Mensagem>';

    return new Response($output,
        200,
        array('Content-Type' => 'application/xml')
    );

});
