<?php

/*
 * Hospital Metropolitano
 *
 */

namespace app\models\lisa;

use Doctrine\DBAL\DriverManager;
use Ocrend\Kernel\Helpers as Helper;
use Ocrend\Kernel\Models\IModels;
use Ocrend\Kernel\Models\Models;
use Ocrend\Kernel\Models\ModelsException;
use Ocrend\Kernel\Router\IRouter;

/**
 * Modelo Listar
 */
class Listar extends Models implements IModels
{

    # Variables de clase
    # Variables de clase
    public $dataPedido = null;
    public $documentoPedido = null;
    public $pedido = null;
    public $codigoPedido = null;
    public $nomeMaquina = null;
    public $usuarioSolicitante = null;
    public $fechaPedido = null;
    public $strSolicitante = null;
    public $fechaTomaMuestra = null;
    public $tipoProceso = null;
    public $statusProcesado = 0;
    public $timestampLog = null;
    public $operacao = null;
    public $dirNuevosPedidos = '../v1/lisa/pedidos/nuevosPedidos/';
    public $dirEnviados = '../v1/lisa/pedidos/enviados/';
    public $dirTomaMuestras = '../v1/lisa/pedidos/tomamuestras/';
    public $dirRecepMuestras = '../v1/lisa/pedidos/recepmuestras/';
    public $dirMuestrasFacturadas = '../v1/lisa/pedidos/muestrasFacturadas/';
    public $dirIngresados = '../v1/lisa/pedidos/ingresados/';
    public $dirRetenidos = '../v1/lisa/pedidos/retenidos/';
    public $dirErrores = '../v1//lisa/pedidos/errores/';
    public $dirUsers = '../v1//lisa/pedidos/users/';
    public $dirTurnero = '../v1/lisa/pedidos/turnero/';
    public $dirTurneroPendientes = '../v1/lisa/pedidos/tomasPendientes/';
    public $dirListadoTurnero = '../v1/lisa/pedidos/listadoToma/';
    private $_conexion = null;


    private $ordenes = array();
    private $documento = array();

    public function listarOrdenes(): array
    {

        try {

            global $config, $http;

            $tipoFiltro = $http->query->get('type');

            if (isset($tipoFiltro)) {

                if ($tipoFiltro == 'generaListado') {
                    $this->ordenes = $this->getGeneraListadoToma();
                }

                if ($tipoFiltro == 'ingresadas') {
                    $this->ordenes = $this->getIngresadas();
                }

                if ($tipoFiltro == 'pendienteMuestras') {
                    $this->ordenes = $this->getMuetrasPendientes();
                }

                if ($tipoFiltro == 'reportes') {
                    $this->ordenes = $this->getReportes();
                }

                if ($tipoFiltro == 'ingresadasFlebotomia') {
                    $this->ordenes = $this->getIngresadasFlebotomista();
                }

                if ($tipoFiltro == 'filtradas') {
                    $this->ordenes = $this->getFiltradas();
                }

                if ($tipoFiltro == 'porenviar') {
                    $this->ordenes = $this->getPorEnviar();
                }

                if ($tipoFiltro == 'enviadas') {
                    $this->ordenes = $this->getEnviadas();
                }

                if ($tipoFiltro == 'errorFiltradas') {
                    $this->ordenes = $this->getErroresFiltradas();
                }

                if ($tipoFiltro == 'errorEnviadas') {
                    $this->ordenes = $this->getErroresEnviadas();
                }

                if ($tipoFiltro == 'reglas') {
                    $this->ordenes = $this->getReglas();
                }

                if ($tipoFiltro == 'reglas') {
                    $this->ordenes = $this->getReglas();
                }

                if ($tipoFiltro == 'rp') {
                    $this->ordenes = $this->getReprocesoFiltradas();
                }
            } else {

                throw new ModelsException('No existe data Envíe un tipo de filtro.');
            }

            # Devolver Información
            return array(
                'status' => true,
                'data' => $this->ordenes,
                'total' => count($this->ordenes),
            );
        } catch (ModelsException $e) {

            return array('status' => false, 'data' => [], 'message' => $e->getMessage(), 'errorCode' => $e->getCode());
        }
    }

    private function conectar_Oracle_MV()
    {
        global $config;

        $_config = new \Doctrine\DBAL\Configuration();
        //..
        # SETEAR LA CONNEXION A LA BASE DE DATOS DE ORACLE GEMA
        $this->_conexion = \Doctrine\DBAL\DriverManager::getConnection($config['database']['drivers']['oracle_mv'], $_config);
    }

    private function conectar_Oracle_MV_SML()
    {
        global $config;

        $_config = new \Doctrine\DBAL\Configuration();
        //..
        # SETEAR LA CONNEXION A LA BASE DE DATOS DE ORACLE GEMA
        $this->_conexion = \Doctrine\DBAL\DriverManager::getConnection($config['database']['drivers']['oracle_mv_sml'], $_config);
    }

    public function listarOrdenesBeta(): array
    {

        try {

            global $config, $http;

            $tipoFiltro = $http->query->get('type');

            if (isset($tipoFiltro)) {

                if ($tipoFiltro == 'ingresadas') {

                    $this->ordenes = $this->getIngresadasBeta();
                }

                if ($tipoFiltro == 'filtradas') {

                    $this->ordenes = $this->getFiltradas();
                }

                if ($tipoFiltro == 'porenviar') {

                    $this->ordenes = $this->getPorEnviar();
                }

                if ($tipoFiltro == 'enviadas') {

                    $this->ordenes = $this->getEnviadas();
                }

                if ($tipoFiltro == 'errorFiltradas') {

                    $this->ordenes = $this->getErroresFiltradas();
                }

                if ($tipoFiltro == 'errorEnviadas') {

                    $this->ordenes = $this->getErroresEnviadas();
                }

                if ($tipoFiltro == 'reglas') {
                    $this->ordenes = $this->getReglas();
                }

                if ($tipoFiltro == 'reglas') {
                    $this->ordenes = $this->getReglas();
                }

                if ($tipoFiltro == 'rp') {
                    $this->ordenes = $this->getReprocesoFiltradas();
                }
            } else {

                throw new ModelsException('No existe data Envíe un tipo de filtro.');
            }

            # Devolver Información
            return array(
                'status' => true,
                'data' => $this->ordenes,
                'total' => count($this->ordenes),
            );
        } catch (ModelsException $e) {

            return array('status' => false, 'data' => [], 'message' => $e->getMessage(), 'errorCode' => $e->getCode());
        }
    }

    public function getReglas()
    {

        global $config, $http;

        $query = $this->db->select('*', 'filtro_notificaciones_lab', null, "statusFiltro='1'");

        if (false !== $query) {

            return $query;
        }

        return array();
    }

    public function getReprocesoFiltradas()
    {

        global $config, $http;

        $list = Helper\Files::get_files_in_dir('../../nss/v1/ordenes/ingresadas/');

        $i = 0;

        $ordenes = array();

        // Extraer ORDENES PARA FILTRAR
        foreach ($list as $key => $val) {

            $content = file_get_contents($val);
            $documento = json_decode($content, true);
            $documento['file'] = $val;
            $documento['_PDF'] = '';

            @unlink($documento['file']);
            unset($documento['file']);
            $documento['reglasFiltrosEnvio'] = array();
            $documento['reglasFiltrosNoEnvio'] = array();
            $documento['correosElectronicos'] = array();
            $documento['statusFiltro'] = 0;
            $documento['ultimoFiltrado'] = '';
            $file = 'ordenes/ingresadas/sc_' . $documento['sc'] . '_' . $documento['fechaExamen'] . '.json';
            $json_string = json_encode($documento);
            file_put_contents($file, $json_string);
            $ordenes[] = $documento;

            /*
            if (count($documento['dataClinica']) !== 0 && count($documento['dataMicro']) !== 0) {
            }
             */

            $ordenes[] = $documento;
        }

        return $ordenes;
    }

    public function extraerExamenesPedido($pedido = array())
    {

        $res = array();

        foreach ($pedido->Exame as $key) {

            $_NM_EXA_LAB = (array) $key->descExame;
            $_CD_EXA_LAB = (array) $key->codigoExame;

            $k['NM_EXA_LAB'] = $_NM_EXA_LAB[0];
            $k['CD_EXA_LAB'] = $_CD_EXA_LAB[0];
            $k['STATUS_TOMA'] = '';
            $k['FECHA_TOMA'] = '';
            $k['USR_TOMA'] = '';
            $k['STATUS_RECEP'] = '';
            $k['FECHA_RECEP'] = '';
            $k['USR_RECEP'] = '';
            $k['customCheked'] = false;
            $res[] = $k;
        }

        return $res;

        # code...
    }

    public function extraerExamenesPedidoFact($pedido = array(), $codigoPedido, $codigoAtendimento, $numeroHistoriaClinica)
    {

        $res = array();

        foreach ($pedido->Exame as $key) {

            $_NM_EXA_LAB = (array) $key->descExame;
            $_CD_EXA_LAB = (array) $key->codigoExame;
            $_CD_PRO_FAT = (array) $key->codigoExameFaturamento;
            $_CD_SETOR = (array) $key->setorExecutante;
            $_CD_PACIENTE = (array) $numeroHistoriaClinica;
            $_CD_PED_LAB = (array) $codigoPedido;
            $_CD_ATENDIMENTO = (array) $codigoAtendimento;

            $k['CD_PACIENTE'] = $_CD_PACIENTE[0];
            $k['CD_PED_LAB'] = $_CD_PED_LAB[0];
            $k['CD_ATENDIMENTO'] = $_CD_ATENDIMENTO[0];
            $k['CD_PRO_FAT'] = $_CD_PRO_FAT[0];
            $k['CD_SETOR'] = $_CD_SETOR[0];
            $k['NM_EXA_LAB'] = $_NM_EXA_LAB[0];
            $k['CD_EXA_LAB'] = $_CD_EXA_LAB[0];
            $k['STATUS_FACT'] = '';
            $k['FECHA_FACT'] = '';
            $k['USR_FACT'] = '';
            $k['customCheked'] = false;
            $res[] = $k;
        }

        return $res;

        # code...
    }

    public function updateRecepMuestraPedido()
    {

        global $http;

        $doc = json_decode($http->request->get('documento'), true);

        $codigoPedido = $doc['pedido']['PedidoExameLab']['codigoPedido'];

        # Registro de Recepción de Muestras
        $stsmuestrasRecibidas = $this->dirRecepMuestras . $codigoPedido . '.json';
        $documentoRecep['dataRecepcion'] = $doc['dataRecepcion'];
        $json_string = json_encode($documentoRecep);
        file_put_contents($stsmuestrasRecibidas, $json_string);

        # Regsitro de envios Parciales
        $stsEnviados = $this->dirEnviados . $codigoPedido . '_parcial_.json';
        $json_string = json_encode($documentoRecep);
        file_put_contents($stsEnviados, $json_string);

        # Verificar status de nevio lisa recepcion
        $recepListo = true;

        foreach ($doc['dataRecepcion']['examenesRecep'] as $k) {
            if ($k['customCheked'] == false) {
                $recepListo = false;
            }
        }

        if ($recepListo) {

            $stsmuestrasRecibidas = $this->dirRecepMuestras . $codigoPedido . '_procesado_.json';
            $documentoRecep['dataRecepcion'] = $doc['dataRecepcion'];
            $json_string = json_encode($documentoRecep);
            file_put_contents($stsmuestrasRecibidas, $json_string);

            /*

            // Eliminar de toma de muestra

            $listado = $this->dirListadoTurnero . '_listado_.json';
            $datos = file_get_contents($listado);
            $ingresos = json_decode($datos, true);

            # Agregar nuevos registros
            foreach ($ingresos as $k => $v) {

                if ($v['codigoPedido'] == $codigoPedido) {
                    unset($ingresos[$k]);
                }
            }

            $json_string = json_encode($ingresos);
            file_put_contents($this->dirListadoTurnero . '_listado_.json', $json_string);

            */
        }

        return array(
            'status' => true,
            'data' => $doc,
            'dataTomaMuestra' => $doc['dataTomaMuestra'],
            'dataRecepcion' => $doc['dataRecepcion'],
            'dataFacturacion' => $doc['dataFacturacion'],
            "dataObservaciones" => array(),
        );
    }

    public function updateFacturacionPedido()
    {

        global $http;

        $doc = json_decode($http->request->get('documento'), true);

        $codigoPedido = $doc['pedido']['PedidoExameLab']['codigoPedido'];

        // Insertar Valores en BDD
        $this->postDataAllFact($doc['dataFacturacion']['examenesFact'], $doc['dataRecepcion']['usuarioRecep']);

        foreach ($doc['dataFacturacion']['examenesFact'] as $k => $val) {
            if ($doc['dataFacturacion']['examenesFact'][$k]['STATUS_FACT'] == '1') {
                $doc['dataFacturacion']['examenesFact'][$k]['STATUS_FACT'] = '2';
            }
        }

        # Registro de Facturacion
        $stsmuestrasFacturadas = $this->dirMuestrasFacturadas . $codigoPedido . '.json';
        $documentoFact['dataFacturacion'] = $doc['dataFacturacion'];
        $json_string = json_encode($documentoFact);
        file_put_contents($stsmuestrasFacturadas, $json_string);

        # Verificar status Facturacion
        $factListo = true;

        foreach ($doc['dataFacturacion']['examenesFact'] as $k) {
            if ($k['customCheked'] == false) {
                $factListo = false;
            }
        }

        if ($factListo) {


            $stsmuestrasFacturadas = $this->dirMuestrasFacturadas . $codigoPedido . '_procesado_.json';
            $documentoFact['dataFacturacion'] = $doc['dataFacturacion'];
            $json_string = json_encode($documentoFact);
            file_put_contents($stsmuestrasFacturadas, $json_string);
        }

        return array(
            'status' => true,
            'data' => $doc,
            'dataTomaMuestra' => $doc['dataTomaMuestra'],
            'dataRecepcion' => $doc['dataRecepcion'],
            'dataFacturacion' => $doc['dataFacturacion'],
            "dataObservaciones" => array(),
        );
    }

    public function actualizarFechaLlamadaToma()
    {

        try {

            global $http;

            $id = $http->request->get('id');

            # Conectar base de datos
            $this->conectar_Oracle_MV();

            $this->setSpanishOracle_Insert();

            # QueryBuilder
            $queryBuilder = $this->_conexion->createQueryBuilder();

            $queryBuilder
                ->update('HMETRO.TURNO_SGAC_MV', 'u')
                ->set('u.FECHA_FLEBO_LLAMA', '?')
                ->where('u.ID_SGAC = ?')
                ->setParameter(0, date('d-m-Y H:i:s'))
                ->setParameter(1, $id);

            # Execute
            $stmt = $queryBuilder->execute();

            $this->_conexion->close();

            return true;
        } catch (\Exception $ex) {
            throw new ModelsException($ex->getMessage());
        }
    }

    public function actualizarFechaFinalizaToma()
    {

        try {

            global $http;

            $id = $http->request->get('id');
            $usr = $http->request->get('usr');

            $dcTriage = $this->buscarTriageAtendimento($id);

            # Conectar base de datos
            $this->conectar_Oracle_MV();

            $this->setSpanishOracle_Insert();

            # QueryBuilder
            $queryBuilder = $this->_conexion->createQueryBuilder();

            $queryBuilder
                ->update('HMETRO.TURNO_SGAC_MV', 'u')
                ->set('u.FECHA_FLEBO_FINALIZA', '?')
                ->set('u.LUGAR_TOMA', '?')
                ->where('u.CD_TRIAGEM_ATENDIMENTO = ?')
                ->setParameter(0, date('d-m-Y H:i:s'))
                ->setParameter(1, $usr)
                ->setParameter(2, $dcTriage);

            # Execute
            $stmt = $queryBuilder->execute();

            $this->_conexion->close();

            return true;
        } catch (\Exception $ex) {
            throw new ModelsException($ex->getMessage());
        }
    }

    public function buscarTriageAtendimento($dcTriage = '')
    {

        try {


            $sql = "  SELECT CD_TRIAGEM_ATENDIMENTO FROM TRIAGEM_ATENDIMENTO WHERE CD_ATENDIMENTO = '$dcTriage' ";

            # Conectar base de datos
            $this->conectar_Oracle_MV();

            # Execute
            $stmt = $this->_conexion->query($sql);

            $this->_conexion->close();

            $data = $stmt->fetch();

            if ($data !== false) {
                return $data['CD_TRIAGEM_ATENDIMENTO'];
            } else {
                return 0;
            }
        } catch (\Exception $ex) {
            return 0;
        }
    }

    public function postDataAllFact($datosFacturados, $usuarioRecep)
    {

        try {

            if($usuarioRecep == ''){
                $usuarioRecep = 'METRCONSULTA';
            }

            # Conectar base de datos
            $this->conectar_Oracle_MV();

            $this->setSpanishOracle_Insert();
            // $this->setSpanishOracle();

            foreach ($datosFacturados as $key => $value) {

                if ($value['STATUS_FACT'] == '1') {
                    # QueryBuilder
                    $queryBuilder = $this->_conexion->createQueryBuilder();

                    # Query
                    $queryBuilder
                        ->insert('HMETRO.HM_INTEGRA_PED_LAB')
                        ->values(
                            array(
                                'CD_PACIENTE' => '?',
                                'CD_ATENDIMENTO' => '?',
                                'CD_PED_LAB' => '?',
                                'CD_SETOR' => '?',
                                'CD_PRO_FAT' => '?',
                                'NM_MNEMONICO' => '?',
                                'CD_EXA_LAB' => '?',
                                'CD_SET_EXA' => '?',
                                'QT_LANCAMENTO' => '?',
                                'NM_USUARIO' => '?',
                                'FECHA_LISA' => '?',
                            )
                        )
                        ->setParameter(0, $value['CD_PACIENTE'])
                        ->setParameter(1, $value['CD_ATENDIMENTO'])
                        ->setParameter(2, $value['CD_PED_LAB'])
                        ->setParameter(3, $value['CD_SETOR'])
                        ->setParameter(4, $value['CD_PRO_FAT'])
                        ->setParameter(5, $value['CD_PRO_FAT'])
                        ->setParameter(6, $value['CD_EXA_LAB'])
                        ->setParameter(7, 1)
                        ->setParameter(8, 1)
                        ->setParameter(9, $usuarioRecep)
                        ->setParameter(10, date('d-m-Y H:i:s'));


                    $parameters = $queryBuilder->getParameters();

                    # Execute
                    $result = $queryBuilder->execute();
                }
            }


            $this->_conexion->close();

            if (false === $result) {
                throw new ModelsException('¡Error! No fue posible crear un nuevo registro.');
            }
            // return array('status' => true, 'message' => 'Proceso realizado con éxito.', $parameters);
        } catch (\Exception $ex) {
            throw new ModelsException($ex->getMessage());
        }
    }


    public function listarUsersEtiquetas()
    {

        global $config, $http;

        $stsUsers = $this->dirUsers . 'users-etiquetas.json';

        $content = file_get_contents($stsUsers);
        $documento = json_decode($content, true);

        $etiquetas = array();

        foreach ($documento as $key) {

            if ($key['usuario'] !== '' && $key['impresion'] !== '') {
                $etiquetas[] = $key;
            }
        }

        return array(
            'status' => true,
            'data' => array_reverse(array_unique($etiquetas, SORT_REGULAR)),
        );
    }

    public function changeUser()
    {

        global $config, $http;

        $caja = strtoupper(trim($http->request->get("impresion")));
        $user = strtoupper(trim($http->request->get("usuario")));

        $stsUsers = $this->dirUsers . 'users-etiquetas.json';

        $content = file_get_contents($stsUsers);
        $documento = json_decode($content, true);

        $existeNoUsuario = false;

        foreach ($documento as $k => $v) {

            if ($v['usuario'] == $user) {
                $documento[$k]['usuario'] = $user;
                $documento[$k]['impresion'] = $caja;
                $documento[$k]['timestamp'] = date("d-m-Y H:i:s");
            } else {
                $existeNoUsuario = true;
            }
        }

        if ($existeNoUsuario) {
            $documento[] = array(
                'usuario' => $user,
                'impresion' => $caja,
                'timestamp' => date("d-m-Y H:i:s"),
            );
        }

        $doc = array_reverse(array_unique($documento, SORT_REGULAR));

        $json_string = json_encode($doc);
        file_put_contents($stsUsers, $json_string);

        return array(
            'status' => true,
            'data' => $caja,
            'message' => 'Proceso realizado con éxito.',
        );
    }

    private function setSpanishOracle_Insert()
    {

        $sql = "alter session set NLS_LANGUAGE = 'SPANISH'";
        # Execute
        $stmt = $this->_conexion->query($sql);

        $sql = "alter session set NLS_TERRITORY = 'SPAIN'";
        # Execute
        $stmt = $this->_conexion->query($sql);

        $sql = " alter session set NLS_DATE_FORMAT = 'DD-MM-YYYY HH24:MI:SS' ";
        # Execute
        $stmt = $this->_conexion->query($sql);
    }

    public function updateTomaMuestraPedido()
    {

        global $config, $http;

        $doc = json_decode($http->request->get('documento'), true);

        $codigoPedido = $doc['pedido']['PedidoExameLab']['codigoPedido'];

        $stsmuestrasProcesadas = $this->dirTomaMuestras . $codigoPedido . '.json';
        $documentoMuestras['dataTomaMuestra'] = $doc['dataTomaMuestra'];
        $json_string = json_encode($documentoMuestras);
        file_put_contents($stsmuestrasProcesadas, $json_string);

        # Verificar status de nevio lisa recepcion
        $recepListo = true;

        foreach ($doc['dataTomaMuestra']['examenesToma'] as $k) {

            if ($k['customCheked'] == false) {
                $recepListo = false;
            }
        }

        $stsmuestrasRecibidas = $this->dirTurnero . $codigoPedido . '_procesado_.json';

        if ($recepListo && @file_get_contents($stsmuestrasRecibidas, true) === false) {

            $stsmuestrasRecibidas = $this->dirTurnero . $codigoPedido . '_procesado_.json';
            $documentoRecep['dataTomaMuestra'] = $doc['dataTomaMuestra'];
            $json_string = json_encode($documentoRecep);
            file_put_contents($stsmuestrasRecibidas, $json_string);
        }

        return array(
            'status' => true,
            'data' => $doc,
            'dataTomaMuestra' => $doc['dataTomaMuestra'],
            'dataRecepcion' => $doc['dataRecepcion'],
            "dataObservaciones" => array(),
        );
    }

    public function getDetallePedidoLab()
    {

        try {

            global $config, $http;

            $numeroPedido = $http->request->get('numeroPedido');

            $itr = $http->request->get('idTimeRecord');

            // $list = Helper\Files::get_files_in_dir($this->dirIngresados);

            if ($itr == 0) {

                $files = glob($this->dirIngresados . $numeroPedido . "_*.xml");

                $list = array($files[0]);
            } else {

                $list = array($this->dirIngresados . $numeroPedido . '_' . $itr . '.xml');
            }

            $i = 0;

            $ordenes = array();

            foreach ($list as $key => $val) {

                $xml = file_get_contents($val);
                $data = utf8_encode($xml);
                $dataPedido = simplexml_load_string($data);
                // Extract XML Dcoument

                $pedido = $dataPedido->children('soap', true)->Body->children();
                $listaExamen = $pedido->Mensagem->PedidoExameLab->listaExame;

                $segundoApellido = $pedido->Mensagem->PedidoExameLab->paciente->segundoSobrenome;
                $primerApellido = $pedido->Mensagem->PedidoExameLab->paciente->primeiroSobrenome;
                $primerNombre = $pedido->Mensagem->PedidoExameLab->paciente->primeiroNome;
                $segundoNombre = $pedido->Mensagem->PedidoExameLab->paciente->segundoNome;

                $codigoPedido = $pedido->Mensagem->PedidoExameLab->codigoPedido;
                $idTimeRecord = strtotime($pedido->Mensagem->Cabecalho->dataHora);

                $fechaPedido = $pedido->Mensagem->PedidoExameLab->dataColetaPedido;
                $numeroHistoriaClinica = $pedido->Mensagem->PedidoExameLab->paciente->codigoPaciente;
                $codigoAtendimento = $pedido->Mensagem->PedidoExameLab->atendimento->codigoAtendimento;

                $sector = $pedido->Mensagem->PedidoExameLab->descSetorSolicitante;
                $tipoPedido = $pedido->Mensagem->PedidoExameLab->tipoSolicitacao;
                $descPrestadorSolicitante = $pedido->Mensagem->PedidoExameLab->descPrestadorSolicitante;
                $sexo = $pedido->Mensagem->PedidoExameLab->paciente->sexo;

                $tipoOperacion = $pedido->Mensagem->PedidoExameLab->operacao;

                // Status enviado Infinity
                $stsenviado = $this->dirEnviados . $codigoPedido . '.xml';
                $enviadoInfinity = 1;
                if (@file_get_contents($stsenviado, true) === false) {
                    $enviadoInfinity = 0;
                }

                if ($itr == 0) {

                    # Pedidos de Hoy
                    if ($codigoPedido == $numeroPedido) {

                        $_codigoPedido = (array) $codigoPedido;
                        $_fechaPedido = (array) $fechaPedido;
                        $_codigoAtendimento = (array) $codigoAtendimento;
                        $_numeroHistoriaClinica = (array) $numeroHistoriaClinica;
                        $_sector = (array) $sector;
                        $_descPrestadorSolicitante = (array) $descPrestadorSolicitante;
                        $_tipoPedido = (array) $tipoPedido;
                        $_tipoOperacion = (array) $tipoOperacion;
                        $_sexo = (array) $sexo;

                        $dataMuestras = $this->extraerExamenesPedido($pedido->Mensagem->PedidoExameLab->listaExame);
                        $dataMuestrasFacturadas = $this->extraerExamenesPedidoFact($pedido->Mensagem->PedidoExameLab->listaExame, $codigoPedido, $codigoAtendimento, $numeroHistoriaClinica);


                        # Validación para control de toma de muestras
                        $stsmuestrasProcesadas = $this->dirTomaMuestras . $codigoPedido . '.json';

                        $muestrasProcesadas = 1;
                        $dataTomaMuestras = array();

                        if (@file_get_contents($stsmuestrasProcesadas, true) === false) {
                            $muestrasProcesadas = 0;
                            $documentoMuestras['dataTomaMuestra'] = array(
                                "fechaToma" => "",
                                "usuarioToma" => "",
                                "examenesToma" => $dataMuestras,
                                "insumosToma" => array(),
                            );
                            $json_string = json_encode($documentoMuestras);
                            file_put_contents($stsmuestrasProcesadas, $json_string);
                            $dataTomaMuestras = $documentoMuestras;
                        } else {
                            $content = file_get_contents($stsmuestrasProcesadas);
                            $documentoMuestras = json_decode($content, true);
                            $dataTomaMuestras = $documentoMuestras;
                        }

                        # Validación para control de recepcion de muestras
                        $stsmuestrasRecibidas = $this->dirRecepMuestras . $codigoPedido . '.json';
                        $muestrasRecebidas = 1;
                        $dataRecepMuestras = array();

                        if (@file_get_contents($stsmuestrasRecibidas, true) === false) {
                            $muestrasRecebidas = 0;
                            $documentoRecep['dataRecepcion'] = array(
                                "fechaRecep" => "",
                                "usuarioRecep" => "",
                                "examenesRecep" => $dataMuestras,
                                "insumosRecep" => array(),
                            );
                            $json_string = json_encode($documentoRecep);
                            file_put_contents($stsmuestrasRecibidas, $json_string);
                            $dataRecepMuestras = $documentoRecep;
                        } else {
                            $content = file_get_contents($stsmuestrasRecibidas);
                            $documentoRecep = json_decode($content, true);
                            $dataRecepMuestras = $documentoRecep;
                        }

                        # Validación para control de Facturacion de Muestras
                        $stsmuestrasFacturadas = $this->dirMuestrasFacturadas . $codigoPedido . '.json';
                        $muestrasFacturadas = 1;
                        $dataFactMuestras = array();

                        if (@file_get_contents($stsmuestrasFacturadas, true) === false) {
                            $muestrasFacturadas = 0;
                            $documentoFact['dataFacturacion'] = array(
                                "fechaFact" => "",
                                "usuarioFact" => "",
                                "examenesFact" => $dataMuestrasFacturadas,
                            );
                            $json_string = json_encode($documentoFact);
                            file_put_contents($stsmuestrasFacturadas, $json_string);
                            $dataFactMuestras = $documentoFact;
                        } else {
                            $content = file_get_contents($stsmuestrasFacturadas);
                            $documentoFact = json_decode($content, true);
                            $dataFactMuestras = $documentoFact;
                        }



                        $ordenes[] = array(
                            'idTimeRecord' => $idTimeRecord,
                            'pedido' => $pedido->Mensagem,
                            'dataTomaMuestra' => $dataTomaMuestras['dataTomaMuestra'],
                            'dataRecepcion' => $dataRecepMuestras['dataRecepcion'],
                            'dataFacturacion' => $dataFactMuestras['dataFacturacion'],
                            "dataObservaciones" => array(),

                        );
                    }
                } else {

                    # Pedidos de Hoy
                    if ($codigoPedido == $numeroPedido && $idTimeRecord == $itr) {

                        $_codigoPedido = (array) $codigoPedido;
                        $_fechaPedido = (array) $fechaPedido;
                        $_codigoAtendimento = (array) $codigoAtendimento;
                        $_numeroHistoriaClinica = (array) $numeroHistoriaClinica;
                        $_sector = (array) $sector;
                        $_descPrestadorSolicitante = (array) $descPrestadorSolicitante;
                        $_tipoPedido = (array) $tipoPedido;
                        $_tipoOperacion = (array) $tipoOperacion;
                        $_sexo = (array) $sexo;

                        $dataMuestras = $this->extraerExamenesPedido($pedido->Mensagem->PedidoExameLab->listaExame);
                        $dataMuestrasFacturadas = $this->extraerExamenesPedidoFact($pedido->Mensagem->PedidoExameLab->listaExame, $codigoPedido, $codigoAtendimento, $numeroHistoriaClinica);

                        # Validación para control de toma de muestras
                        $stsmuestrasProcesadas = $this->dirTomaMuestras . $codigoPedido . '.json';

                        $muestrasProcesadas = 1;
                        $dataTomaMuestras = array();

                        if (@file_get_contents($stsmuestrasProcesadas, true) === false) {
                            $muestrasProcesadas = 0;
                            $documentoMuestras['dataTomaMuestra'] = array(
                                "fechaToma" => "",
                                "usuarioToma" => "",
                                "examenesToma" => $dataMuestras,
                                "insumosToma" => array(),
                            );
                            $json_string = json_encode($documentoMuestras);
                            file_put_contents($stsmuestrasProcesadas, $json_string);
                            $dataTomaMuestras = $documentoMuestras;
                        } else {
                            $content = file_get_contents($stsmuestrasProcesadas);
                            $documentoMuestras = json_decode($content, true);
                            $dataTomaMuestras = $documentoMuestras;
                        }

                        # Validación para control de recepcion de muestras
                        $stsmuestrasRecibidas = $this->dirRecepMuestras . $codigoPedido . '.json';
                        $muestrasRecebidas = 1;
                        $dataRecepMuestras = array();

                        if (@file_get_contents($stsmuestrasRecibidas, true) === false) {
                            $muestrasRecebidas = 0;
                            $documentoRecep['dataRecepcion'] = array(
                                "fechaRecep" => "",
                                "usuarioRecep" => "",
                                "examenesRecep" => $dataMuestras,
                                "insumosRecep" => array(),
                            );
                            $json_string = json_encode($documentoRecep);
                            file_put_contents($stsmuestrasRecibidas, $json_string);
                            $dataRecepMuestras = $documentoRecep;
                        } else {
                            $content = file_get_contents($stsmuestrasRecibidas);
                            $documentoRecep = json_decode($content, true);
                            $dataRecepMuestras = $documentoRecep;
                        }

                        # Validación para control de Facturacion de Muestras
                        $stsmuestrasFacturadas = $this->dirMuestrasFacturadas . $codigoPedido . '.json';
                        $muestrasFacturadas = 1;
                        $dataFactMuestras = array();

                        if (@file_get_contents($stsmuestrasFacturadas, true) === false) {
                            $muestrasFacturadas = 0;
                            $documentoFact['dataFacturacion'] = array(
                                "fechaFact" => "",
                                "usuarioFact" => "",
                                "examenesFact" => $dataMuestrasFacturadas,
                            );
                            $json_string = json_encode($documentoFact);
                            file_put_contents($stsmuestrasFacturadas, $json_string);
                            $dataFactMuestras = $documentoFact;
                        } else {
                            $content = file_get_contents($stsmuestrasFacturadas);
                            $documentoFact = json_decode($content, true);
                            $dataFactMuestras = $documentoFact;
                        }



                        $ordenes[] = array(
                            'idTimeRecord' => $idTimeRecord,
                            'pedido' => $pedido->Mensagem,
                            'dataTomaMuestra' => $dataTomaMuestras['dataTomaMuestra'],
                            'dataRecepcion' => $dataRecepMuestras['dataRecepcion'],
                            'dataFacturacion' => $dataFactMuestras['dataFacturacion'],
                            "dataObservaciones" => array(),

                        );
                    }
                }
            }

            return array(
                'status' => true,
                'data' => $ordenes[0],
            );
        } catch (ModelsException $e) {

            return array(
                'status' => false,
                'data' => array(),
            );
        }
    }

    public function getLogsEnvio()
    {

        global $http;

        $numeroPedido = $http->query->get('numeroPedido');

        $list = Helper\Files::get_files_in_dir($this->dirEnviados, '.xml');

        $i = 0;

        $ordenes = array();

        foreach ($list as $key => $val) {

            $filename = basename($val, ".xml");

            if (strpos($filename, 'log_') === false) {

                $filename = basename($val, ".xml");
                $codigoPedido = explode("_", $filename);

                # Pedidos
                if ($codigoPedido[0] == $numeroPedido) {

                    $xml = file_get_contents($val);
                    $data = utf8_encode($xml);
                    $dataPedido = simplexml_load_string($data);
                    // Extract XML Dcoument

                    $pedido = $dataPedido->children('soap', true)->Body->children();
                    $listaExamen = $pedido->Mensagem->PedidoExameLab->listaExame;

                    if ($pedido !== null && $listaExamen !== null) {

                        $segundoApellido = $pedido->Mensagem->PedidoExameLab->paciente->segundoSobrenome;
                        $primerApellido = $pedido->Mensagem->PedidoExameLab->paciente->primeiroSobrenome;
                        $primerNombre = $pedido->Mensagem->PedidoExameLab->paciente->primeiroNome;
                        $segundoNombre = $pedido->Mensagem->PedidoExameLab->paciente->segundoNome;

                        $codigoPedido = $pedido->Mensagem->PedidoExameLab->codigoPedido;
                        $idTimeRecord = strtotime($pedido->Mensagem->Cabecalho->dataHora);

                        $fechaPedido = $pedido->Mensagem->PedidoExameLab->dataColetaPedido;
                        $numeroHistoriaClinica = $pedido->Mensagem->PedidoExameLab->paciente->codigoPaciente;
                        $codigoAtendimento = $pedido->Mensagem->PedidoExameLab->atendimento->codigoAtendimento;

                        $sector = $pedido->Mensagem->PedidoExameLab->descSetorSolicitante;
                        $tipoPedido = $pedido->Mensagem->PedidoExameLab->tipoSolicitacao;
                        $descPrestadorSolicitante = $pedido->Mensagem->PedidoExameLab->descPrestadorSolicitante;
                        $sexo = $pedido->Mensagem->PedidoExameLab->paciente->sexo;

                        $tipoOperacion = $pedido->Mensagem->PedidoExameLab->operacao;

                        $ordenes[] = $pedido->Mensagem;
                    }
                }
            }
        }

        return array(
            'status' => true,
            'data' => $ordenes,
        );
    }



    public function getNuevoPedidoLISA()
    {

        try {

            $list = Helper\Files::get_files_in_dir($this->dirNuevosPedidos, 'xml');

            $i = 0;

            $ordenes = array();

            foreach ($list as $key => $val) {

                $filename = basename($val, ".xml");

                if (strpos($filename, 'log_') === false) {

                    $xml = file_get_contents($val);
                    $data = utf8_encode($xml);
                    $dataPedido = simplexml_load_string($data);
                    // Extract XML Dcoument

                    $pedido = $dataPedido->children('soap', true)->Body->children();

                    if ($pedido !== null) {

                        $listaExamen = $pedido->Mensagem->PedidoExameLab->listaExame;

                        $segundoApellido = $pedido->Mensagem->PedidoExameLab->paciente->segundoSobrenome;
                        $primerApellido = $pedido->Mensagem->PedidoExameLab->paciente->primeiroSobrenome;
                        $primerNombre = $pedido->Mensagem->PedidoExameLab->paciente->primeiroNome;
                        $segundoNombre = $pedido->Mensagem->PedidoExameLab->paciente->segundoNome;

                        $codigoPedido = $pedido->Mensagem->PedidoExameLab->codigoPedido;
                        $idTimeRecord = strtotime($pedido->Mensagem->Cabecalho->dataHora);

                        $fechaPedido = $pedido->Mensagem->PedidoExameLab->dataColetaPedido;
                        $numeroHistoriaClinica = $pedido->Mensagem->PedidoExameLab->paciente->codigoPaciente;
                        $codigoAtendimento = $pedido->Mensagem->PedidoExameLab->atendimento->codigoAtendimento;

                        $sector = $pedido->Mensagem->PedidoExameLab->descSetorSolicitante;
                        $tipoPedido = $pedido->Mensagem->PedidoExameLab->tipoSolicitacao;
                        $descPrestadorSolicitante = $pedido->Mensagem->PedidoExameLab->descPrestadorSolicitante;
                        $sexo = $pedido->Mensagem->PedidoExameLab->paciente->sexo;

                        $tipoOperacion = $pedido->Mensagem->PedidoExameLab->operacao;

                        $ordenes[] = $codigoPedido;
                    }
                }
            }

            if (count($ordenes) == 0) {
                $nuevaOrden = 88000000;
            } else {
                $_ultimaOrden = end($ordenes);
                $_ultimaOrden = (int) $_ultimaOrden;
                $nuevaOrden = ($_ultimaOrden + 1);
            }

            return array(
                'status' => true,
                'nuevaOrden' => $nuevaOrden,
            );
        } catch (\Exception $e) {

            return array(
                'status' => false,
                'message' => $e->getMessage(),
            );
        }
    }

    public function getIngresadas()
    {

        try {

            global $config, $http;

            $typeFilter = $http->query->get('idFiltro');
            $fechaDesde = $http->query->get('fechaDesde');
            $fechaHasta = $http->query->get('fechaHasta');

            $_list = Helper\Files::get_files_in_dir($this->dirIngresados);

            $list = array_reverse($_list);

            $i = 0;

            $ordenes = array();

            foreach ($list as $key => $val) {

                if ($typeFilter == 1) {

                    if ($i <= 1000) {

                        $xml = file_get_contents($val);

                        $data = utf8_encode($xml);
                        // Extract XML Dcoument
                        $dataPedido = simplexml_load_string($data);
                        $pedido = $dataPedido->children('soap', true)->Body->children();
                        $listaExamen = $pedido->Mensagem->PedidoExameLab->listaExame;

                        $segundoApellido = $pedido->Mensagem->PedidoExameLab->paciente->segundoSobrenome;
                        $primerApellido = $pedido->Mensagem->PedidoExameLab->paciente->primeiroSobrenome;
                        $primerNombre = $pedido->Mensagem->PedidoExameLab->paciente->primeiroNome;
                        $segundoNombre = $pedido->Mensagem->PedidoExameLab->paciente->segundoNome;

                        $codigoPedido = $pedido->Mensagem->PedidoExameLab->codigoPedido;
                        $idTimeRecord = strtotime($pedido->Mensagem->Cabecalho->dataHora);

                        $fechaPedido = $pedido->Mensagem->PedidoExameLab->dataColetaPedido;
                        $numeroHistoriaClinica = $pedido->Mensagem->PedidoExameLab->paciente->codigoPaciente;
                        $codigoAtendimento = $pedido->Mensagem->PedidoExameLab->atendimento->codigoAtendimento;

                        $sector = $pedido->Mensagem->PedidoExameLab->descSetorSolicitante;
                        $tipoPedido = $pedido->Mensagem->PedidoExameLab->tipoSolicitacao;
                        $descPrestadorSolicitante = $pedido->Mensagem->PedidoExameLab->descPrestadorSolicitante;
                        $tipoOperacion = $pedido->Mensagem->PedidoExameLab->operacao;

                        // Status enviado Infinity
                        $stsenviado = $this->dirEnviados . $codigoPedido . '_' . $idTimeRecord . '.xml';
                        $stsenviado2 = $this->dirEnviados . $codigoPedido . '_parcial_.json';

                        $_tipoOperacion = (array) $tipoOperacion;

                        // Status envpio parciales
                        if ($_tipoOperacion[0] == 'I') {
                            $enviadoInfinity = 0;
                            if (@file_get_contents($stsenviado2, true)) {
                                $enviadoInfinity = 2; // Envío parcial
                            }

                            if (@file_get_contents($stsenviado, true)) {
                                $enviadoInfinity = 1; // Envío Total
                            }
                        } else {
                            $enviadoInfinity = 0;
                            if (@file_get_contents($stsenviado, true)) {
                                $enviadoInfinity = 1; // Envío Total
                            }
                        }

                        // Status muestras procesadas vs pendientes
                        $stsmuestrasProcesadas = $this->dirRecepMuestras . $codigoPedido . '_procesado_.json';
                        $muestrasProcesadas = 1;
                        if (@file_get_contents($stsmuestrasProcesadas, true) === false) {
                            $muestrasProcesadas = 0;
                        }

                        # Pedidos de Hoy
                        if ($typeFilter == 1) {

                            $_codigoPedido = (array) $codigoPedido;
                            $_fechaPedido = (array) $fechaPedido;
                            $_codigoAtendimento = (array) $codigoAtendimento;
                            $_numeroHistoriaClinica = (array) $numeroHistoriaClinica;
                            $_sector = (array) $sector;
                            $_descPrestadorSolicitante = (array) $descPrestadorSolicitante;
                            $_tipoPedido = (array) $tipoPedido;
                            $_tipoOperacion = (array) $tipoOperacion;
                            $_idTimeRecord = (array) $idTimeRecord;

                            if (date('Y-m-d', strtotime($fechaPedido)) == date('Y-m-d')) {

                                $ordenes[] = array(
                                    'codigoPedido' => (isset($_codigoPedido[0]) ? $_codigoPedido[0] : ''),
                                    'fechaPedido' => (isset($_fechaPedido[0]) ? $_fechaPedido[0] : ''),
                                    'at_mv' => (isset($_codigoAtendimento[0]) ? $_codigoAtendimento[0] : ''),
                                    'numeroHistoriaClinica' => (isset($_numeroHistoriaClinica[0]) ? $_numeroHistoriaClinica[0] : ''),
                                    'paciente' => (isset($primerApellido) ? $primerApellido : '') . ' ' . (isset($segundoApellido) ? $segundoApellido : '') . ' ' . (isset($primerNombre) ? $primerNombre : '') . ' ' . (isset($segundoNombre) ? $segundoNombre : ''),
                                    'sector' => (isset($_sector[0]) ? $_sector[0] : ''),
                                    'tipoPedido' => (isset($_tipoPedido[0]) ? $_tipoPedido[0] : ''),
                                    'descPrestadorSolicitante' => (isset($_descPrestadorSolicitante[0]) ? $_descPrestadorSolicitante[0] : ''),
                                    'tipoOperacion' => (isset($_tipoOperacion[0]) ? $_tipoOperacion[0] : ''),
                                    'idTimeRecord' => (isset($_idTimeRecord[0]) ? $_idTimeRecord[0] : ''),
                                    'enviadoInfinity' => $enviadoInfinity,
                                    'muestrasProcesadas' => $muestrasProcesadas,
                                    '_val' => $listaExamen,

                                );
                            }
                        }

                        # Pedidos de Emergencia
                        if ($typeFilter == 2) {

                            $_codigoPedido = (array) $codigoPedido;
                            $_fechaPedido = (array) $fechaPedido;
                            $_codigoAtendimento = (array) $codigoAtendimento;
                            $_numeroHistoriaClinica = (array) $numeroHistoriaClinica;
                            $_sector = (array) $sector;
                            $_descPrestadorSolicitante = (array) $descPrestadorSolicitante;
                            $_tipoPedido = (array) $tipoPedido;
                            $_tipoOperacion = (array) $tipoOperacion;
                            $_idTimeRecord = (array) $idTimeRecord;

                            $timedesde = strtotime(date('Y-m-d', strtotime($fechaDesde)));
                            $timeHasta = strtotime(date('Y-m-d', strtotime($fechaHasta)));

                            if ($sector == 'EMERGENCIA' && (strtotime(date('Y-m-d', strtotime($fechaPedido))) >= $timedesde) && (strtotime(date('Y-m-d', strtotime($fechaPedido))) <= $timeHasta)) {
                                $ordenes[] = array(
                                    'codigoPedido' => (isset($_codigoPedido[0]) ? $_codigoPedido[0] : ''),
                                    'fechaPedido' => (isset($_fechaPedido[0]) ? $_fechaPedido[0] : ''),
                                    'at_mv' => (isset($_codigoAtendimento[0]) ? $_codigoAtendimento[0] : ''),
                                    'numeroHistoriaClinica' => (isset($_numeroHistoriaClinica[0]) ? $_numeroHistoriaClinica[0] : ''),
                                    'paciente' => (isset($primerApellido) ? $primerApellido : '') . ' ' . (isset($segundoApellido) ? $segundoApellido : '') . ' ' . (isset($primerNombre) ? $primerNombre : '') . ' ' . (isset($segundoNombre) ? $segundoNombre : ''),
                                    'sector' => (isset($_sector[0]) ? $_sector[0] : ''),
                                    'tipoPedido' => (isset($_tipoPedido[0]) ? $_tipoPedido[0] : ''),
                                    'idTimeRecord' => (isset($_idTimeRecord[0]) ? $_idTimeRecord[0] : ''),
                                    'descPrestadorSolicitante' => (isset($_descPrestadorSolicitante[0]) ? $_descPrestadorSolicitante[0] : ''),
                                    'enviadoInfinity' => $enviadoInfinity,
                                    'muestrasProcesadas' => $muestrasProcesadas,
                                    'tipoOperacion' => (isset($_tipoOperacion[0]) ? $_tipoOperacion[0] : ''),
                                );
                            }
                        }

                        if ($typeFilter == 3) {

                            $_codigoPedido = (array) $codigoPedido;
                            $_fechaPedido = (array) $fechaPedido;
                            $_codigoAtendimento = (array) $codigoAtendimento;
                            $_numeroHistoriaClinica = (array) $numeroHistoriaClinica;
                            $_sector = (array) $sector;
                            $_descPrestadorSolicitante = (array) $descPrestadorSolicitante;
                            $_tipoPedido = (array) $tipoPedido;
                            $_tipoOperacion = (array) $tipoOperacion;
                            $_idTimeRecord = (array) $idTimeRecord;

                            $timedesde = strtotime(date('Y-m-d', strtotime($fechaDesde)));
                            $timeHasta = strtotime(date('Y-m-d', strtotime($fechaHasta)));


                            if ((strpos($sector, 'HOSPITALIZACION') !== false || strpos($sector, 'UCI') !== false || strpos($sector, 'UNIDAD QUIRURGICA') !== false || strpos($sector, 'NEONATOLOGIA') !== false) && (strtotime(date('Y-m-d', strtotime($fechaPedido))) >= $timedesde) && (strtotime(date('Y-m-d', strtotime($fechaPedido))) <= $timeHasta)) {
                                $ordenes[] = array(
                                    'codigoPedido' => (isset($_codigoPedido[0]) ? $_codigoPedido[0] : ''),
                                    'fechaPedido' => (isset($_fechaPedido[0]) ? $_fechaPedido[0] : ''),
                                    'at_mv' => (isset($_codigoAtendimento[0]) ? $_codigoAtendimento[0] : ''),
                                    'numeroHistoriaClinica' => (isset($_numeroHistoriaClinica[0]) ? $_numeroHistoriaClinica[0] : ''),
                                    'paciente' => (isset($primerApellido) ? $primerApellido : '') . ' ' . (isset($segundoApellido) ? $segundoApellido : '') . ' ' . (isset($primerNombre) ? $primerNombre : '') . ' ' . (isset($segundoNombre) ? $segundoNombre : ''),
                                    'sector' => (isset($_sector[0]) ? $_sector[0] : ''),
                                    'tipoPedido' => (isset($_tipoPedido[0]) ? $_tipoPedido[0] : ''),
                                    'idTimeRecord' => (isset($_idTimeRecord[0]) ? $_idTimeRecord[0] : ''),
                                    'descPrestadorSolicitante' => (isset($_descPrestadorSolicitante[0]) ? $_descPrestadorSolicitante[0] : ''),
                                    'enviadoInfinity' => $enviadoInfinity,
                                    'muestrasProcesadas' => $muestrasProcesadas,
                                    'tipoOperacion' => (isset($_tipoOperacion[0]) ? $_tipoOperacion[0] : ''),

                                );
                            }
                        }

                        if ($typeFilter == 4) {

                            $_codigoPedido = (array) $codigoPedido;
                            $_fechaPedido = (array) $fechaPedido;
                            $_codigoAtendimento = (array) $codigoAtendimento;
                            $_numeroHistoriaClinica = (array) $numeroHistoriaClinica;
                            $_sector = (array) $sector;
                            $_descPrestadorSolicitante = (array) $descPrestadorSolicitante;
                            $_tipoPedido = (array) $tipoPedido;
                            $_tipoOperacion = (array) $tipoOperacion;
                            $_idTimeRecord = (array) $idTimeRecord;

                            $timedesde = strtotime(date('Y-m-d', strtotime($fechaDesde)));
                            $timeHasta = strtotime(date('Y-m-d', strtotime($fechaHasta)));

                            if (strpos($sector, 'SERVICIOS AMBULATORIOS') !== false && (strtotime(date('Y-m-d', strtotime($fechaPedido))) >= $timedesde) && (strtotime(date('Y-m-d', strtotime($fechaPedido))) <= $timeHasta)) {
                                $ordenes[] = array(
                                    'codigoPedido' => (isset($_codigoPedido[0]) ? $_codigoPedido[0] : ''),
                                    'fechaPedido' => (isset($_fechaPedido[0]) ? $_fechaPedido[0] : ''),
                                    'at_mv' => (isset($_codigoAtendimento[0]) ? $_codigoAtendimento[0] : ''),
                                    'numeroHistoriaClinica' => (isset($_numeroHistoriaClinica[0]) ? $_numeroHistoriaClinica[0] : ''),
                                    'paciente' => (isset($primerApellido) ? $primerApellido : '') . ' ' . (isset($segundoApellido) ? $segundoApellido : '') . ' ' . (isset($primerNombre) ? $primerNombre : '') . ' ' . (isset($segundoNombre) ? $segundoNombre : ''),
                                    'sector' => (isset($_sector[0]) ? $_sector[0] : ''),
                                    'tipoPedido' => (isset($_tipoPedido[0]) ? $_tipoPedido[0] : ''),
                                    'idTimeRecord' => (isset($_idTimeRecord[0]) ? $_idTimeRecord[0] : ''),
                                    'descPrestadorSolicitante' => (isset($_descPrestadorSolicitante[0]) ? $_descPrestadorSolicitante[0] : ''),
                                    'enviadoInfinity' => $enviadoInfinity,
                                    'muestrasProcesadas' => $muestrasProcesadas,
                                    'tipoOperacion' => (isset($_tipoOperacion[0]) ? $_tipoOperacion[0] : ''),

                                );
                            }
                        }
                    }

                    $i++;
                } elseif ($typeFilter == 6) {

                    if ($i <= 1000) {

                        $xml = file_get_contents($val);

                        $data = utf8_encode($xml);
                        // Extract XML Dcoument
                        $dataPedido = simplexml_load_string($data);
                        $pedido = $dataPedido->children('soap', true)->Body->children();
                        $listaExamen = $pedido->Mensagem->PedidoExameLab->listaExame;

                        $segundoApellido = $pedido->Mensagem->PedidoExameLab->paciente->segundoSobrenome;
                        $primerApellido = $pedido->Mensagem->PedidoExameLab->paciente->primeiroSobrenome;
                        $primerNombre = $pedido->Mensagem->PedidoExameLab->paciente->primeiroNome;
                        $segundoNombre = $pedido->Mensagem->PedidoExameLab->paciente->segundoNome;

                        $codigoPedido = $pedido->Mensagem->PedidoExameLab->codigoPedido;
                        $idTimeRecord = strtotime($pedido->Mensagem->Cabecalho->dataHora);

                        $fechaPedido = $pedido->Mensagem->PedidoExameLab->dataColetaPedido;
                        $numeroHistoriaClinica = $pedido->Mensagem->PedidoExameLab->paciente->codigoPaciente;
                        $codigoAtendimento = $pedido->Mensagem->PedidoExameLab->atendimento->codigoAtendimento;

                        $sector = $pedido->Mensagem->PedidoExameLab->descSetorSolicitante;
                        $tipoPedido = $pedido->Mensagem->PedidoExameLab->tipoSolicitacao;
                        $descPrestadorSolicitante = $pedido->Mensagem->PedidoExameLab->descPrestadorSolicitante;
                        $tipoOperacion = $pedido->Mensagem->PedidoExameLab->operacao;

                        // Status enviado Infinity
                        $stsenviado = $this->dirEnviados . $codigoPedido . '_' . $idTimeRecord . '.xml';
                        $stsenviado2 = $this->dirEnviados . $codigoPedido . '_parcial_.json';

                        $_tipoOperacion = (array) $tipoOperacion;

                        // Status envpio parciales
                        if ($_tipoOperacion[0] == 'I') {
                            $enviadoInfinity = 0;
                            if (@file_get_contents($stsenviado2, true)) {
                                $enviadoInfinity = 2; // Envío parcial
                            }

                            if (@file_get_contents($stsenviado, true)) {
                                $enviadoInfinity = 1; // Envío Total
                            }
                        } else {
                            $enviadoInfinity = 0;
                            if (@file_get_contents($stsenviado, true)) {
                                $enviadoInfinity = 1; // Envío Total
                            }
                        }

                        // Status muestras procesadas vs pendientes
                        $stsmuestrasProcesadas = $this->dirRecepMuestras . $codigoPedido . '_procesado_.json';
                        $muestrasProcesadas = 1;
                        if (@file_get_contents($stsmuestrasProcesadas, true) === false) {
                            $muestrasProcesadas = 0;
                        }

                        # Pedidos de Hoy de Banco de Sangre
                        if ($typeFilter == 6) {

                            $_codigoPedido = (array) $codigoPedido;
                            $_fechaPedido = (array) $fechaPedido;
                            $_codigoAtendimento = (array) $codigoAtendimento;
                            $_numeroHistoriaClinica = (array) $numeroHistoriaClinica;
                            $_sector = (array) $sector;
                            $_descPrestadorSolicitante = (array) $descPrestadorSolicitante;
                            $_tipoPedido = (array) $tipoPedido;
                            $_tipoOperacion = (array) $tipoOperacion;
                            $_idTimeRecord = (array) $idTimeRecord;

                            if (date('Y-m-d', strtotime($fechaPedido)) == date('Y-m-d') && $sector != 'SERVICIOS AMBULATORIOS' && $this->validarExamenesBCOSANGRE($listaExamen) !== false) {

                                $ordenes[] = array(
                                    'codigoPedido' => (isset($_codigoPedido[0]) ? $_codigoPedido[0] : ''),
                                    'fechaPedido' => (isset($_fechaPedido[0]) ? $_fechaPedido[0] : ''),
                                    'at_mv' => (isset($_codigoAtendimento[0]) ? $_codigoAtendimento[0] : ''),
                                    'numeroHistoriaClinica' => (isset($_numeroHistoriaClinica[0]) ? $_numeroHistoriaClinica[0] : ''),
                                    'paciente' => (isset($primerApellido) ? $primerApellido : '') . ' ' . (isset($segundoApellido) ? $segundoApellido : '') . ' ' . (isset($primerNombre) ? $primerNombre : '') . ' ' . (isset($segundoNombre) ? $segundoNombre : ''),
                                    'sector' => (isset($_sector[0]) ? $_sector[0] : ''),
                                    'tipoPedido' => (isset($_tipoPedido[0]) ? $_tipoPedido[0] : ''),
                                    'descPrestadorSolicitante' => (isset($_descPrestadorSolicitante[0]) ? $_descPrestadorSolicitante[0] : ''),
                                    'tipoOperacion' => (isset($_tipoOperacion[0]) ? $_tipoOperacion[0] : ''),
                                    'idTimeRecord' => (isset($_idTimeRecord[0]) ? $_idTimeRecord[0] : ''),
                                    'enviadoInfinity' => $enviadoInfinity,
                                    'muestrasProcesadas' => $muestrasProcesadas,
                                    '_val' => $listaExamen,

                                );
                            }
                        }
                    }

                    $i++;
                } else {

                    if ($i <= 10000) {

                        $xml = file_get_contents($val);

                        $data = utf8_encode($xml);
                        // Extract XML Dcoument
                        $dataPedido = simplexml_load_string($data);
                        $pedido = $dataPedido->children('soap', true)->Body->children();
                        $listaExamen = $pedido->Mensagem->PedidoExameLab->listaExame;

                        $segundoApellido = $pedido->Mensagem->PedidoExameLab->paciente->segundoSobrenome;
                        $primerApellido = $pedido->Mensagem->PedidoExameLab->paciente->primeiroSobrenome;
                        $primerNombre = $pedido->Mensagem->PedidoExameLab->paciente->primeiroNome;
                        $segundoNombre = $pedido->Mensagem->PedidoExameLab->paciente->segundoNome;

                        $codigoPedido = $pedido->Mensagem->PedidoExameLab->codigoPedido;
                        $idTimeRecord = strtotime($pedido->Mensagem->Cabecalho->dataHora);

                        $fechaPedido = $pedido->Mensagem->PedidoExameLab->dataColetaPedido;
                        $numeroHistoriaClinica = $pedido->Mensagem->PedidoExameLab->paciente->codigoPaciente;
                        $codigoAtendimento = $pedido->Mensagem->PedidoExameLab->atendimento->codigoAtendimento;

                        $sector = $pedido->Mensagem->PedidoExameLab->descSetorSolicitante;
                        $tipoPedido = $pedido->Mensagem->PedidoExameLab->tipoSolicitacao;
                        $descPrestadorSolicitante = $pedido->Mensagem->PedidoExameLab->descPrestadorSolicitante;
                        $tipoOperacion = $pedido->Mensagem->PedidoExameLab->operacao;

                        // Status enviado Infinity
                        $stsenviado = $this->dirEnviados . $codigoPedido . '_' . $idTimeRecord . '.xml';
                        $stsenviado2 = $this->dirEnviados . $codigoPedido . '_parcial_.json';

                        $_tipoOperacion = (array) $tipoOperacion;

                        // Status envpio parciales
                        if ($_tipoOperacion[0] == 'I') {
                            $enviadoInfinity = 0;
                            if (@file_get_contents($stsenviado2, true)) {
                                $enviadoInfinity = 2; // Envío parcial
                            }

                            if (@file_get_contents($stsenviado, true)) {
                                $enviadoInfinity = 1; // Envío Total
                            }
                        } else {
                            $enviadoInfinity = 0;
                            if (@file_get_contents($stsenviado, true)) {
                                $enviadoInfinity = 1; // Envío Total
                            }
                        }

                        // Status muestras procesadas vs pendientes
                        $stsmuestrasProcesadas = $this->dirRecepMuestras . $codigoPedido . '_procesado_.json';
                        $muestrasProcesadas = 1;
                        if (@file_get_contents($stsmuestrasProcesadas, true) === false) {
                            $muestrasProcesadas = 0;
                        }

                        # Pedidos de Hoy
                        if ($typeFilter == 1) {

                            $_codigoPedido = (array) $codigoPedido;
                            $_fechaPedido = (array) $fechaPedido;
                            $_codigoAtendimento = (array) $codigoAtendimento;
                            $_numeroHistoriaClinica = (array) $numeroHistoriaClinica;
                            $_sector = (array) $sector;
                            $_descPrestadorSolicitante = (array) $descPrestadorSolicitante;
                            $_tipoPedido = (array) $tipoPedido;
                            $_tipoOperacion = (array) $tipoOperacion;
                            $_idTimeRecord = (array) $idTimeRecord;

                            if (date('Y-m-d', strtotime($fechaPedido)) == date('Y-m-d')) {

                                $ordenes[] = array(
                                    'codigoPedido' => (isset($_codigoPedido[0]) ? $_codigoPedido[0] : ''),
                                    'fechaPedido' => (isset($_fechaPedido[0]) ? $_fechaPedido[0] : ''),
                                    'at_mv' => (isset($_codigoAtendimento[0]) ? $_codigoAtendimento[0] : ''),
                                    'numeroHistoriaClinica' => (isset($_numeroHistoriaClinica[0]) ? $_numeroHistoriaClinica[0] : ''),
                                    'paciente' => (isset($primerApellido) ? $primerApellido : '') . ' ' . (isset($segundoApellido) ? $segundoApellido : '') . ' ' . (isset($primerNombre) ? $primerNombre : '') . ' ' . (isset($segundoNombre) ? $segundoNombre : ''),
                                    'sector' => (isset($_sector[0]) ? $_sector[0] : ''),
                                    'tipoPedido' => (isset($_tipoPedido[0]) ? $_tipoPedido[0] : ''),
                                    'descPrestadorSolicitante' => (isset($_descPrestadorSolicitante[0]) ? $_descPrestadorSolicitante[0] : ''),
                                    'tipoOperacion' => (isset($_tipoOperacion[0]) ? $_tipoOperacion[0] : ''),
                                    'idTimeRecord' => (isset($_idTimeRecord[0]) ? $_idTimeRecord[0] : ''),
                                    'enviadoInfinity' => $enviadoInfinity,
                                    'muestrasProcesadas' => $muestrasProcesadas,
                                    '_val' => $listaExamen,

                                );
                            }
                        }

                        # Pedidos de Emergencia
                        if ($typeFilter == 2) {

                            $_codigoPedido = (array) $codigoPedido;
                            $_fechaPedido = (array) $fechaPedido;
                            $_codigoAtendimento = (array) $codigoAtendimento;
                            $_numeroHistoriaClinica = (array) $numeroHistoriaClinica;
                            $_sector = (array) $sector;
                            $_descPrestadorSolicitante = (array) $descPrestadorSolicitante;
                            $_tipoPedido = (array) $tipoPedido;
                            $_tipoOperacion = (array) $tipoOperacion;
                            $_idTimeRecord = (array) $idTimeRecord;

                            $timedesde = strtotime(date('Y-m-d', strtotime($fechaDesde)));
                            $timeHasta = strtotime(date('Y-m-d', strtotime($fechaHasta)));

                            if ($sector == 'EMERGENCIA' && (strtotime(date('Y-m-d', strtotime($fechaPedido))) >= $timedesde) && (strtotime(date('Y-m-d', strtotime($fechaPedido))) <= $timeHasta)) {
                                $ordenes[] = array(
                                    'codigoPedido' => (isset($_codigoPedido[0]) ? $_codigoPedido[0] : ''),
                                    'fechaPedido' => (isset($_fechaPedido[0]) ? $_fechaPedido[0] : ''),
                                    'at_mv' => (isset($_codigoAtendimento[0]) ? $_codigoAtendimento[0] : ''),
                                    'numeroHistoriaClinica' => (isset($_numeroHistoriaClinica[0]) ? $_numeroHistoriaClinica[0] : ''),
                                    'paciente' => (isset($primerApellido) ? $primerApellido : '') . ' ' . (isset($segundoApellido) ? $segundoApellido : '') . ' ' . (isset($primerNombre) ? $primerNombre : '') . ' ' . (isset($segundoNombre) ? $segundoNombre : ''),
                                    'sector' => (isset($_sector[0]) ? $_sector[0] : ''),
                                    'tipoPedido' => (isset($_tipoPedido[0]) ? $_tipoPedido[0] : ''),
                                    'idTimeRecord' => (isset($_idTimeRecord[0]) ? $_idTimeRecord[0] : ''),
                                    'descPrestadorSolicitante' => (isset($_descPrestadorSolicitante[0]) ? $_descPrestadorSolicitante[0] : ''),
                                    'enviadoInfinity' => $enviadoInfinity,
                                    'muestrasProcesadas' => $muestrasProcesadas,
                                    'tipoOperacion' => (isset($_tipoOperacion[0]) ? $_tipoOperacion[0] : ''),
                                );
                            }
                        }

                        if ($typeFilter == 3) {

                            $_codigoPedido = (array) $codigoPedido;
                            $_fechaPedido = (array) $fechaPedido;
                            $_codigoAtendimento = (array) $codigoAtendimento;
                            $_numeroHistoriaClinica = (array) $numeroHistoriaClinica;
                            $_sector = (array) $sector;
                            $_descPrestadorSolicitante = (array) $descPrestadorSolicitante;
                            $_tipoPedido = (array) $tipoPedido;
                            $_tipoOperacion = (array) $tipoOperacion;
                            $_idTimeRecord = (array) $idTimeRecord;

                            $timedesde = strtotime(date('Y-m-d', strtotime($fechaDesde)));
                            $timeHasta = strtotime(date('Y-m-d', strtotime($fechaHasta)));

                            if ((strpos($sector, 'HOSPITALIZACION') !== false || strpos($sector, 'UCI') !== false || strpos($sector, 'UNIDAD QUIRURGICA') !== false || strpos($sector, 'NEONATOLOGIA') !== false) && (strtotime(date('Y-m-d', strtotime($fechaPedido))) >= $timedesde) && (strtotime(date('Y-m-d', strtotime($fechaPedido))) <= $timeHasta)) {
                                $ordenes[] = array(
                                    'codigoPedido' => (isset($_codigoPedido[0]) ? $_codigoPedido[0] : ''),
                                    'fechaPedido' => (isset($_fechaPedido[0]) ? $_fechaPedido[0] : ''),
                                    'at_mv' => (isset($_codigoAtendimento[0]) ? $_codigoAtendimento[0] : ''),
                                    'numeroHistoriaClinica' => (isset($_numeroHistoriaClinica[0]) ? $_numeroHistoriaClinica[0] : ''),
                                    'paciente' => (isset($primerApellido) ? $primerApellido : '') . ' ' . (isset($segundoApellido) ? $segundoApellido : '') . ' ' . (isset($primerNombre) ? $primerNombre : '') . ' ' . (isset($segundoNombre) ? $segundoNombre : ''),
                                    'sector' => (isset($_sector[0]) ? $_sector[0] : ''),
                                    'tipoPedido' => (isset($_tipoPedido[0]) ? $_tipoPedido[0] : ''),
                                    'idTimeRecord' => (isset($_idTimeRecord[0]) ? $_idTimeRecord[0] : ''),
                                    'descPrestadorSolicitante' => (isset($_descPrestadorSolicitante[0]) ? $_descPrestadorSolicitante[0] : ''),
                                    'enviadoInfinity' => $enviadoInfinity,
                                    'muestrasProcesadas' => $muestrasProcesadas,
                                    'tipoOperacion' => (isset($_tipoOperacion[0]) ? $_tipoOperacion[0] : ''),

                                );
                            }
                        }

                        if ($typeFilter == 4) {

                            $_codigoPedido = (array) $codigoPedido;
                            $_fechaPedido = (array) $fechaPedido;
                            $_codigoAtendimento = (array) $codigoAtendimento;
                            $_numeroHistoriaClinica = (array) $numeroHistoriaClinica;
                            $_sector = (array) $sector;
                            $_descPrestadorSolicitante = (array) $descPrestadorSolicitante;
                            $_tipoPedido = (array) $tipoPedido;
                            $_tipoOperacion = (array) $tipoOperacion;
                            $_idTimeRecord = (array) $idTimeRecord;

                            $timedesde = strtotime(date('Y-m-d', strtotime($fechaDesde)));
                            $timeHasta = strtotime(date('Y-m-d', strtotime($fechaHasta)));

                            if (strpos($sector, 'SERVICIOS AMBULATORIOS') !== false && (strtotime(date('Y-m-d', strtotime($fechaPedido))) >= $timedesde) && (strtotime(date('Y-m-d', strtotime($fechaPedido))) <= $timeHasta)) {
                                $ordenes[] = array(
                                    'codigoPedido' => (isset($_codigoPedido[0]) ? $_codigoPedido[0] : ''),
                                    'fechaPedido' => (isset($_fechaPedido[0]) ? $_fechaPedido[0] : ''),
                                    'at_mv' => (isset($_codigoAtendimento[0]) ? $_codigoAtendimento[0] : ''),
                                    'numeroHistoriaClinica' => (isset($_numeroHistoriaClinica[0]) ? $_numeroHistoriaClinica[0] : ''),
                                    'paciente' => (isset($primerApellido) ? $primerApellido : '') . ' ' . (isset($segundoApellido) ? $segundoApellido : '') . ' ' . (isset($primerNombre) ? $primerNombre : '') . ' ' . (isset($segundoNombre) ? $segundoNombre : ''),
                                    'sector' => (isset($_sector[0]) ? $_sector[0] : ''),
                                    'tipoPedido' => (isset($_tipoPedido[0]) ? $_tipoPedido[0] : ''),
                                    'idTimeRecord' => (isset($_idTimeRecord[0]) ? $_idTimeRecord[0] : ''),
                                    'descPrestadorSolicitante' => (isset($_descPrestadorSolicitante[0]) ? $_descPrestadorSolicitante[0] : ''),
                                    'enviadoInfinity' => $enviadoInfinity,
                                    'muestrasProcesadas' => $muestrasProcesadas,
                                    'tipoOperacion' => (isset($_tipoOperacion[0]) ? $_tipoOperacion[0] : ''),

                                );
                            }
                        }

                        # Pedidos de Hoy de Banco de Sangre
                        if ($typeFilter == 5) {

                            $_codigoPedido = (array) $codigoPedido;
                            $_fechaPedido = (array) $fechaPedido;
                            $_codigoAtendimento = (array) $codigoAtendimento;
                            $_numeroHistoriaClinica = (array) $numeroHistoriaClinica;
                            $_sector = (array) $sector;
                            $_descPrestadorSolicitante = (array) $descPrestadorSolicitante;
                            $_tipoPedido = (array) $tipoPedido;
                            $_tipoOperacion = (array) $tipoOperacion;
                            $_idTimeRecord = (array) $idTimeRecord;

                            $timedesde = strtotime(date('Y-m-d', strtotime($fechaDesde)));
                            $timeHasta = strtotime(date('Y-m-d', strtotime($fechaHasta)));


                            if ($sector != 'SERVICIOS AMBULATORIOS' && $this->validarExamenesBCOSANGRE($listaExamen) !== false && (strtotime(date('Y-m-d', strtotime($fechaPedido))) >= $timedesde) && (strtotime(date('Y-m-d', strtotime($fechaPedido))) <= $timeHasta)) {

                                $ordenes[] = array(
                                    'codigoPedido' => (isset($_codigoPedido[0]) ? $_codigoPedido[0] : ''),
                                    'fechaPedido' => (isset($_fechaPedido[0]) ? $_fechaPedido[0] : ''),
                                    'at_mv' => (isset($_codigoAtendimento[0]) ? $_codigoAtendimento[0] : ''),
                                    'numeroHistoriaClinica' => (isset($_numeroHistoriaClinica[0]) ? $_numeroHistoriaClinica[0] : ''),
                                    'paciente' => (isset($primerApellido) ? $primerApellido : '') . ' ' . (isset($segundoApellido) ? $segundoApellido : '') . ' ' . (isset($primerNombre) ? $primerNombre : '') . ' ' . (isset($segundoNombre) ? $segundoNombre : ''),
                                    'sector' => (isset($_sector[0]) ? $_sector[0] : ''),
                                    'tipoPedido' => (isset($_tipoPedido[0]) ? $_tipoPedido[0] : ''),
                                    'descPrestadorSolicitante' => (isset($_descPrestadorSolicitante[0]) ? $_descPrestadorSolicitante[0] : ''),
                                    'tipoOperacion' => (isset($_tipoOperacion[0]) ? $_tipoOperacion[0] : ''),
                                    'idTimeRecord' => (isset($_idTimeRecord[0]) ? $_idTimeRecord[0] : ''),
                                    'enviadoInfinity' => $enviadoInfinity,
                                    'muestrasProcesadas' => $muestrasProcesadas,
                                    '_val' => $listaExamen,

                                );
                            }
                        }
                    }

                    $i++;
                }
            }

            return $ordenes;
        } catch (ModelsException $e) {

            return array();
        }
    }

    public function getMuetrasPendientes()
    {

        try {

            global $config, $http;

            $typeFilter = $http->query->get('idFiltro');
            $fechaDesde = $http->query->get('fechaDesde');
            $fechaHasta = $http->query->get('fechaHasta');

            $_list = Helper\Files::get_files_in_dir($this->dirIngresados);

            $list = array_reverse($_list);

            $i = 0;

            $ordenes = array();

            foreach ($list as $key => $val) {

                if ($i <= 10000) {

                    $xml = file_get_contents($val);

                    $data = utf8_encode($xml);
                    // Extract XML Dcoument
                    $dataPedido = simplexml_load_string($data);
                    $pedido = $dataPedido->children('soap', true)->Body->children();
                    $listaExamen = $pedido->Mensagem->PedidoExameLab->listaExame;

                    $segundoApellido = $pedido->Mensagem->PedidoExameLab->paciente->segundoSobrenome;
                    $primerApellido = $pedido->Mensagem->PedidoExameLab->paciente->primeiroSobrenome;
                    $primerNombre = $pedido->Mensagem->PedidoExameLab->paciente->primeiroNome;
                    $segundoNombre = $pedido->Mensagem->PedidoExameLab->paciente->segundoNome;

                    $codigoPedido = $pedido->Mensagem->PedidoExameLab->codigoPedido;
                    $idTimeRecord = strtotime($pedido->Mensagem->Cabecalho->dataHora);

                    $fechaPedido = $pedido->Mensagem->PedidoExameLab->dataColetaPedido;
                    $numeroHistoriaClinica = $pedido->Mensagem->PedidoExameLab->paciente->codigoPaciente;
                    $codigoAtendimento = $pedido->Mensagem->PedidoExameLab->atendimento->codigoAtendimento;

                    $sector = $pedido->Mensagem->PedidoExameLab->descSetorSolicitante;
                    $tipoPedido = $pedido->Mensagem->PedidoExameLab->tipoSolicitacao;
                    $descPrestadorSolicitante = $pedido->Mensagem->PedidoExameLab->descPrestadorSolicitante;
                    $tipoOperacion = $pedido->Mensagem->PedidoExameLab->operacao;

                    // Status enviado Infinity
                    $stsenviado = $this->dirEnviados . $codigoPedido . '_' . $idTimeRecord . '.xml';
                    $stsenviado2 = $this->dirEnviados . $codigoPedido . '_parcial_.json';

                    $_tipoOperacion = (array) $tipoOperacion;

                    // Status envpio parciales
                    if ($_tipoOperacion[0] == 'I') {
                        $enviadoInfinity = 0;
                        if (@file_get_contents($stsenviado2, true)) {
                            $enviadoInfinity = 2; // Envío parcial
                        }

                        if (@file_get_contents($stsenviado, true)) {
                            $enviadoInfinity = 1; // Envío Total
                        }
                    } else {
                        $enviadoInfinity = 0;
                        if (@file_get_contents($stsenviado, true)) {
                            $enviadoInfinity = 1; // Envío Total
                        }
                    }

                    // Status muestras procesadas vs pendientes
                    $stsmuestrasProcesadas = $this->dirRecepMuestras . $codigoPedido . '_procesado_.json';
                    $muestrasProcesadas = 1;
                    if (@file_get_contents($stsmuestrasProcesadas, true) === false) {
                        $muestrasProcesadas = 0;
                    }

                    # Pedidos de Hoy
                    if ($muestrasProcesadas == 0) {

                        $_codigoPedido = (array) $codigoPedido;
                        $_fechaPedido = (array) $fechaPedido;
                        $_codigoAtendimento = (array) $codigoAtendimento;
                        $_numeroHistoriaClinica = (array) $numeroHistoriaClinica;
                        $_sector = (array) $sector;
                        $_descPrestadorSolicitante = (array) $descPrestadorSolicitante;
                        $_tipoPedido = (array) $tipoPedido;
                        $_tipoOperacion = (array) $tipoOperacion;
                        $_idTimeRecord = (array) $idTimeRecord;

                        $timedesde = strtotime(date('Y-m-d', strtotime($fechaDesde)));
                        $timeHasta = strtotime(date('Y-m-d', strtotime($fechaHasta)));

                        if ((strpos($sector, 'HOSPITALIZACION') !== false || strpos($sector, 'EMERGENCIA') !== false) && (strtotime(date('Y-m-d', strtotime($fechaPedido))) >= $timedesde) && (strtotime(date('Y-m-d', strtotime($fechaPedido))) <= $timeHasta)) {
                            $ordenes[] = array(
                                'codigoPedido' => (isset($_codigoPedido[0]) ? $_codigoPedido[0] : ''),
                                'fechaPedido' => (isset($_fechaPedido[0]) ? $_fechaPedido[0] : ''),
                                'at_mv' => (isset($_codigoAtendimento[0]) ? $_codigoAtendimento[0] : ''),
                                'numeroHistoriaClinica' => (isset($_numeroHistoriaClinica[0]) ? $_numeroHistoriaClinica[0] : ''),
                                'paciente' => (isset($primerApellido) ? $primerApellido : '') . ' ' . (isset($segundoApellido) ? $segundoApellido : '') . ' ' . (isset($primerNombre) ? $primerNombre : '') . ' ' . (isset($segundoNombre) ? $segundoNombre : ''),
                                'sector' => (isset($_sector[0]) ? $_sector[0] : ''),
                                'tipoPedido' => (isset($_tipoPedido[0]) ? $_tipoPedido[0] : ''),
                                'idTimeRecord' => (isset($_idTimeRecord[0]) ? $_idTimeRecord[0] : ''),
                                'descPrestadorSolicitante' => (isset($_descPrestadorSolicitante[0]) ? $_descPrestadorSolicitante[0] : ''),
                                'enviadoInfinity' => $enviadoInfinity,
                                'muestrasProcesadas' => $muestrasProcesadas,
                                'tipoOperacion' => (isset($_tipoOperacion[0]) ? $_tipoOperacion[0] : ''),

                            );
                        }
                    }
                }

                $i++;
            }

            return $ordenes;
        } catch (ModelsException $e) {

            return array();
        }
    }

    public function getReportes()
    {

        try {

            global $config, $http;

            $typeFilter = $http->query->get('idFiltro');
            $fechaDesde = $http->query->get('fechaDesde');
            $fechaHasta = $http->query->get('fechaHasta');

            $_list = Helper\Files::get_files_in_dir($this->dirIngresados);

            $list = array_reverse($_list);

            $i = 0;

            $ordenes = array();

            foreach ($list as $key => $val) {

                if ($i <= 10000) {

                    $xml = file_get_contents($val);

                    $data = utf8_encode($xml);
                    // Extract XML Dcoument
                    $dataPedido = simplexml_load_string($data);
                    $pedido = $dataPedido->children('soap', true)->Body->children();
                    $listaExamen = $pedido->Mensagem->PedidoExameLab->listaExame;

                    $segundoApellido = $pedido->Mensagem->PedidoExameLab->paciente->segundoSobrenome;
                    $primerApellido = $pedido->Mensagem->PedidoExameLab->paciente->primeiroSobrenome;
                    $primerNombre = $pedido->Mensagem->PedidoExameLab->paciente->primeiroNome;
                    $segundoNombre = $pedido->Mensagem->PedidoExameLab->paciente->segundoNome;

                    $codigoPedido = $pedido->Mensagem->PedidoExameLab->codigoPedido;
                    $idTimeRecord = strtotime($pedido->Mensagem->Cabecalho->dataHora);

                    $fechaPedido = $pedido->Mensagem->PedidoExameLab->dataColetaPedido;
                    $numeroHistoriaClinica = $pedido->Mensagem->PedidoExameLab->paciente->codigoPaciente;
                    $codigoAtendimento = $pedido->Mensagem->PedidoExameLab->atendimento->codigoAtendimento;

                    $sector = $pedido->Mensagem->PedidoExameLab->descSetorSolicitante;
                    $tipoPedido = $pedido->Mensagem->PedidoExameLab->tipoSolicitacao;
                    $descPrestadorSolicitante = $pedido->Mensagem->PedidoExameLab->descPrestadorSolicitante;
                    $tipoOperacion = $pedido->Mensagem->PedidoExameLab->operacao;

                    // Status enviado Infinity
                    $stsenviado = $this->dirEnviados . $codigoPedido . '_' . $idTimeRecord . '.xml';
                    $stsenviado2 = $this->dirEnviados . $codigoPedido . '_parcial_.json';

                    $_tipoOperacion = (array) $tipoOperacion;

                    // Status envpio parciales
                    if ($_tipoOperacion[0] == 'I') {
                        $enviadoInfinity = 0;
                        if (@file_get_contents($stsenviado2, true)) {
                            $enviadoInfinity = 2; // Envío parcial
                        }

                        if (@file_get_contents($stsenviado, true)) {
                            $enviadoInfinity = 1; // Envío Total
                        }
                    } else {
                        $enviadoInfinity = 0;
                        if (@file_get_contents($stsenviado, true)) {
                            $enviadoInfinity = 1; // Envío Total
                        }
                    }

                    // Status muestras procesadas vs pendientes
                    $stsmuestrasProcesadas = $this->dirRecepMuestras . $codigoPedido . '_procesado_.json';
                    $muestrasProcesadas = 1;
                    if (@file_get_contents($stsmuestrasProcesadas, true) === false) {
                        $muestrasProcesadas = 0;
                    }

                    # Pedidos de Hoy
                    if ($muestrasProcesadas == 1) {

                        $_codigoPedido = (array) $codigoPedido;
                        $_fechaPedido = (array) $fechaPedido;
                        $_codigoAtendimento = (array) $codigoAtendimento;
                        $_numeroHistoriaClinica = (array) $numeroHistoriaClinica;
                        $_sector = (array) $sector;
                        $_descPrestadorSolicitante = (array) $descPrestadorSolicitante;
                        $_tipoPedido = (array) $tipoPedido;
                        $_tipoOperacion = (array) $tipoOperacion;
                        $_idTimeRecord = (array) $idTimeRecord;

                        $contents = file_get_contents($stsmuestrasProcesadas);
                        $contents = utf8_encode($contents);
                        $parseData = json_decode($contents);

                        $timedesde = strtotime(date('Y-m-d', strtotime($fechaDesde)));
                        $timeHasta = strtotime(date('Y-m-d', strtotime($fechaHasta)));

                        if ((strpos($sector, 'HOSPITALIZACION') !== false || strpos($sector, 'EMERGENCIA') !== false) && (strtotime(date('Y-m-d', strtotime($fechaPedido))) >= $timedesde) && (strtotime(date('Y-m-d', strtotime($fechaPedido))) <= $timeHasta)) {
                            $ordenes[] = array(
                                'codigoPedido' => (isset($_codigoPedido[0]) ? $_codigoPedido[0] : ''),
                                'fechaPedido' => (isset($_fechaPedido[0]) ? $_fechaPedido[0] : ''),
                                'at_mv' => (isset($_codigoAtendimento[0]) ? $_codigoAtendimento[0] : ''),
                                'numeroHistoriaClinica' => (isset($_numeroHistoriaClinica[0]) ? $_numeroHistoriaClinica[0] : ''),
                                'paciente' => (isset($primerApellido) ? $primerApellido : '') . ' ' . (isset($segundoApellido) ? $segundoApellido : '') . ' ' . (isset($primerNombre) ? $primerNombre : '') . ' ' . (isset($segundoNombre) ? $segundoNombre : ''),
                                'sector' => (isset($_sector[0]) ? $_sector[0] : ''),
                                'tipoPedido' => (isset($_tipoPedido[0]) ? $_tipoPedido[0] : ''),
                                'idTimeRecord' => (isset($_idTimeRecord[0]) ? $_idTimeRecord[0] : ''),
                                'descPrestadorSolicitante' => (isset($_descPrestadorSolicitante[0]) ? $_descPrestadorSolicitante[0] : ''),
                                'enviadoInfinity' => $enviadoInfinity,
                                'muestrasProcesadas' => $muestrasProcesadas,
                                'tipoOperacion' => (isset($_tipoOperacion[0]) ? $_tipoOperacion[0] : ''),
                                'dataRecepcion' => $parseData,

                            );
                        }
                    }
                }

                $i++;
            }

            return $ordenes;
        } catch (ModelsException $e) {

            return array();
        }
    }

    public function getIngresadasFlebotomista()
    {

        try {

            global $config, $http;

            $typeFilter = $http->query->get('idFiltro');
            $fechaDesde = $http->query->get('fechaDesde');
            $fechaHasta = $http->query->get('fechaHasta');
            $toma = $http->query->get('toma');



            $list = Helper\Files::get_files_in_dir($this->dirIngresados);

            $i = 0;

            $ordenes = array();

            if ($typeFilter == 4) {
                return $this->getIngresadasFlebotomistaAllToma();
            } else if ($typeFilter == 5) {
                return $this->getIngresadasFlebotomista_v3($toma);
            } else if ($typeFilter == 6) {
                return $this->getIngresadasFlebotomista_TomasPendientes();
            } else {

                return array();

                foreach ($list as $key => $val) {

                    $xml = file_get_contents($val);

                    $data = utf8_encode($xml);
                    // Extract XML Dcoument
                    $dataPedido = simplexml_load_string($data);
                    $pedido = $dataPedido->children('soap', true)->Body->children();
                    $listaExamen = $pedido->Mensagem->PedidoExameLab->listaExame;

                    $segundoApellido = $pedido->Mensagem->PedidoExameLab->paciente->segundoSobrenome;
                    $primerApellido = $pedido->Mensagem->PedidoExameLab->paciente->primeiroSobrenome;
                    $primerNombre = $pedido->Mensagem->PedidoExameLab->paciente->primeiroNome;
                    $segundoNombre = $pedido->Mensagem->PedidoExameLab->paciente->segundoNome;

                    $codigoPedido = $pedido->Mensagem->PedidoExameLab->codigoPedido;
                    $idTimeRecord = strtotime($pedido->Mensagem->Cabecalho->dataHora);

                    $fechaPedido = $pedido->Mensagem->PedidoExameLab->dataColetaPedido;
                    $numeroHistoriaClinica = $pedido->Mensagem->PedidoExameLab->paciente->codigoPaciente;
                    $codigoAtendimento = $pedido->Mensagem->PedidoExameLab->atendimento->codigoAtendimento;

                    $sector = $pedido->Mensagem->PedidoExameLab->descSetorSolicitante;
                    $tipoPedido = $pedido->Mensagem->PedidoExameLab->tipoSolicitacao;
                    $descPrestadorSolicitante = $pedido->Mensagem->PedidoExameLab->descPrestadorSolicitante;
                    $tipoOperacion = $pedido->Mensagem->PedidoExameLab->operacao;

                    // Status enviado Infinity
                    $stsenviado = $this->dirEnviados . $codigoPedido . '_' . $idTimeRecord . '.xml';
                    $stsenviado2 = $this->dirEnviados . $codigoPedido . '_parcial_.json';

                    $_tipoOperacion = (array) $tipoOperacion;

                    // Status envpio parciales
                    if ($_tipoOperacion[0] == 'I') {
                        $enviadoInfinity = 0;
                        if (@file_get_contents($stsenviado2, true)) {
                            $enviadoInfinity = 2; // Envío parcial
                        }

                        if (@file_get_contents($stsenviado, true)) {
                            $enviadoInfinity = 1; // Envío Total
                        }
                    } else {
                        $enviadoInfinity = 0;
                        if (@file_get_contents($stsenviado, true)) {
                            $enviadoInfinity = 1; // Envío Total
                        }
                    }

                    // Status muestras procesadas vs pendientes
                    $stsmuestrasProcesadas = $this->dirTomaMuestras . $codigoPedido . '_procesado_.json';
                    $muestrasProcesadas = 1;
                    if (@file_get_contents($stsmuestrasProcesadas, true) === false) {
                        $muestrasProcesadas = 0;
                    }

                    # Pedidos de Hoy
                    if ($typeFilter == 1) {

                        $_codigoPedido = (array) $codigoPedido;
                        $_fechaPedido = (array) $fechaPedido;
                        $_codigoAtendimento = (array) $codigoAtendimento;
                        $_numeroHistoriaClinica = (array) $numeroHistoriaClinica;
                        $_sector = (array) $sector;
                        $_descPrestadorSolicitante = (array) $descPrestadorSolicitante;
                        $_tipoPedido = (array) $tipoPedido;
                        $_tipoOperacion = (array) $tipoOperacion;
                        $_idTimeRecord = (array) $idTimeRecord;

                        if (date('Y-m-d', strtotime($fechaPedido)) == date('Y-m-d')) {
                            $ordenes[] = array(
                                'codigoPedido' => $_codigoPedido[0],
                                'fechaPedido' => $_fechaPedido[0],
                                'at_mv' => $_codigoAtendimento[0],
                                'numeroHistoriaClinica' => $_numeroHistoriaClinica[0],
                                'paciente' => $primerApellido . ' ' . $segundoApellido . ' ' . $primerNombre . ' ' . $segundoNombre,
                                'sector' => $_sector[0],
                                'tipoPedido' => $_tipoPedido[0],
                                'descPrestadorSolicitante' => (isset($_descPrestadorSolicitante[0]) ? $_descPrestadorSolicitante[0] : ''),
                                'tipoOperacion' => $_tipoOperacion[0],
                                'idTimeRecord' => $_idTimeRecord[0],
                                'enviadoInfinity' => $enviadoInfinity,
                                'muestrasProcesadas' => $muestrasProcesadas,
                                '_val' => $listaExamen,
                            );
                        }
                    }

                    # Pedidos de Emergencia
                    if ($typeFilter == 2) {

                        $_codigoPedido = (array) $codigoPedido;
                        $_fechaPedido = (array) $fechaPedido;
                        $_codigoAtendimento = (array) $codigoAtendimento;
                        $_numeroHistoriaClinica = (array) $numeroHistoriaClinica;
                        $_sector = (array) $sector;
                        $_descPrestadorSolicitante = (array) $descPrestadorSolicitante;
                        $_tipoPedido = (array) $tipoPedido;
                        $_tipoOperacion = (array) $tipoOperacion;
                        $_idTimeRecord = (array) $idTimeRecord;

                        $timedesde = strtotime(date('Y-m-d', strtotime($fechaDesde)));
                        $timeHasta = strtotime(date('Y-m-d', strtotime($fechaHasta)));

                        if ($sector == 'EMERGENCIA' && (strtotime(date('Y-m-d', strtotime($fechaPedido))) >= $timedesde) && (strtotime(date('Y-m-d', strtotime($fechaPedido))) <= $timeHasta)) {
                            $ordenes[] = array(
                                'codigoPedido' => $_codigoPedido[0],
                                'fechaPedido' => $_fechaPedido[0],
                                'at_mv' => $_codigoAtendimento[0],
                                'numeroHistoriaClinica' => $_numeroHistoriaClinica[0],
                                'paciente' => $primerApellido . ' ' . $segundoApellido . ' ' . $primerNombre . ' ' . $segundoNombre,
                                'sector' => $_sector[0],
                                'tipoPedido' => $_tipoPedido[0],
                                'idTimeRecord' => $_idTimeRecord[0],
                                'descPrestadorSolicitante' => (isset($_descPrestadorSolicitante[0]) ? $_descPrestadorSolicitante[0] : ''),
                                'enviadoInfinity' => $enviadoInfinity,
                                'muestrasProcesadas' => $muestrasProcesadas,
                                'tipoOperacion' => $_tipoOperacion[0],

                            );
                        }
                    }

                    if ($typeFilter == 3) {

                        $_codigoPedido = (array) $codigoPedido;
                        $_fechaPedido = (array) $fechaPedido;
                        $_codigoAtendimento = (array) $codigoAtendimento;
                        $_numeroHistoriaClinica = (array) $numeroHistoriaClinica;
                        $_sector = (array) $sector;
                        $_descPrestadorSolicitante = (array) $descPrestadorSolicitante;
                        $_tipoPedido = (array) $tipoPedido;
                        $_tipoOperacion = (array) $tipoOperacion;
                        $_idTimeRecord = (array) $idTimeRecord;

                        $timedesde = strtotime(date('Y-m-d', strtotime($fechaDesde)));
                        $timeHasta = strtotime(date('Y-m-d', strtotime($fechaHasta)));

                        if ((strpos($sector, 'HOSPITALIZACION') !== false || strpos($sector, 'UCI') !== false || strpos($sector, 'UNIDAD QUIRURGICA') !== false || strpos($sector, 'NEONATOLOGIA') !== false) && (strtotime(date('Y-m-d', strtotime($fechaPedido))) >= $timedesde) && (strtotime(date('Y-m-d', strtotime($fechaPedido))) <= $timeHasta)) {
                            $ordenes[] = array(
                                'codigoPedido' => $_codigoPedido[0],
                                'fechaPedido' => $_fechaPedido[0],
                                'at_mv' => $_codigoAtendimento[0],
                                'numeroHistoriaClinica' => $_numeroHistoriaClinica[0],
                                'paciente' => $primerApellido . ' ' . $segundoApellido . ' ' . $primerNombre . ' ' . $segundoNombre,
                                'sector' => $_sector[0],
                                'tipoPedido' => $_tipoPedido[0],
                                'idTimeRecord' => $_idTimeRecord[0],
                                'descPrestadorSolicitante' => (isset($_descPrestadorSolicitante[0]) ? $_descPrestadorSolicitante[0] : ''),
                                'enviadoInfinity' => $enviadoInfinity,
                                'muestrasProcesadas' => $muestrasProcesadas,
                                'tipoOperacion' => $_tipoOperacion[0],

                            );
                        }
                    }

                    if ($typeFilter == 4) {

                        $_codigoPedido = (array) $codigoPedido;
                        $_fechaPedido = (array) $fechaPedido;
                        $_codigoAtendimento = (array) $codigoAtendimento;
                        $_numeroHistoriaClinica = (array) $numeroHistoriaClinica;
                        $_sector = (array) $sector;
                        $_descPrestadorSolicitante = (array) $descPrestadorSolicitante;
                        $_tipoPedido = (array) $tipoPedido;
                        $_tipoOperacion = (array) $tipoOperacion;
                        $_idTimeRecord = (array) $idTimeRecord;

                        $timedesde = strtotime(date('Y-m-d', strtotime($fechaDesde)));
                        $timeHasta = strtotime(date('Y-m-d', strtotime($fechaHasta)));

                        if (strpos($sector, 'SERVICIOS AMBULATORIOS') !== false && (strtotime(date('Y-m-d', strtotime($fechaPedido))) >= $timedesde) && (strtotime(date('Y-m-d', strtotime($fechaPedido))) <= $timeHasta)) {
                            $ordenes[] = array(
                                'codigoPedido' => $_codigoPedido[0],
                                'fechaPedido' => $_fechaPedido[0],
                                'at_mv' => $_codigoAtendimento[0],
                                'numeroHistoriaClinica' => $_numeroHistoriaClinica[0],
                                'paciente' => $primerApellido . ' ' . $segundoApellido . ' ' . $primerNombre . ' ' . $segundoNombre,
                                'sector' => $_sector[0],
                                'tipoPedido' => $_tipoPedido[0],
                                'idTimeRecord' => $_idTimeRecord[0],
                                'descPrestadorSolicitante' => (isset($_descPrestadorSolicitante[0]) ? $_descPrestadorSolicitante[0] : ''),
                                'enviadoInfinity' => $enviadoInfinity,
                                'muestrasProcesadas' => $muestrasProcesadas,
                                'tipoOperacion' => $_tipoOperacion[0],

                            );
                        }
                    }

                    if ($typeFilter == 5) {

                        $_codigoPedido = (array) $codigoPedido;
                        $_fechaPedido = (array) $fechaPedido;
                        $_codigoAtendimento = (array) $codigoAtendimento;
                        $_numeroHistoriaClinica = (array) $numeroHistoriaClinica;
                        $_sector = (array) $sector;
                        $_descPrestadorSolicitante = (array) $descPrestadorSolicitante;
                        $_tipoPedido = (array) $tipoPedido;
                        $_tipoOperacion = (array) $tipoOperacion;
                        $_idTimeRecord = (array) $idTimeRecord;

                        $timedesde = strtotime(date('Y-m-d', strtotime($fechaDesde)));
                        $timeHasta = strtotime(date('Y-m-d', strtotime($fechaHasta)));

                        if (strpos($sector, 'SERVICIOS AMBULATORIOS') !== false && (strtotime(date('Y-m-d', strtotime($fechaPedido))) >= $timedesde) && (strtotime(date('Y-m-d', strtotime($fechaPedido))) <= $timeHasta)) {
                            $ordenes[] = array(
                                'codigoPedido' => $_codigoPedido[0],
                                'fechaPedido' => $_fechaPedido[0],
                                'at_mv' => $_codigoAtendimento[0],
                                'numeroHistoriaClinica' => $_numeroHistoriaClinica[0],
                                'paciente' => $primerApellido . ' ' . $segundoApellido . ' ' . $primerNombre . ' ' . $segundoNombre,
                                'sector' => $_sector[0],
                                'tipoPedido' => $_tipoPedido[0],
                                'idTimeRecord' => $_idTimeRecord[0],
                                'descPrestadorSolicitante' => $_descPrestadorSolicitante[0],
                                'enviadoInfinity' => $enviadoInfinity,
                                'muestrasProcesadas' => $muestrasProcesadas,
                                'tipoOperacion' => $_tipoOperacion[0],
                            );
                        }
                    }
                }

                return $ordenes;
            }
        } catch (ModelsException $e) {

            return array();
        }
    }

    public function getGeneraListadoToma()
    {

        try {

            global $config, $http;

            $fechaListado = date('d-m-Y');

            $i = 0;

            $ordenes = array();

            $sql = " SELECT pd.cd_ped_lab SC

            , to_char(pd.dt_pedido,  'DD-MM-YYYY') FECHA_PEDIDO

            , TO_CHAR(pd.hr_ped_lab,'HH24:mi:ss') HORA_PEDIDO

            , pt.nm_paciente

            , pt.cd_paciente

            , at.cd_atendimento

            , at.nr_chamada_painel

            , case when at.cd_atendimento IN(

              SELECT ach.cd_atendimento

                FROM atendime_chamada_painel ach

               WHERE ach.cd_atendimento IS NOT NULL) THEN 'S' else 'N' END STATUS
            ,

            trunc((round(sysdate-pd.hr_ped_lab,4) - trunc(round(sysdate-pd.hr_ped_lab,4) ))*24) tiempo


      from ped_lab pd

           ,coleta_material c

           ,atendime at

           ,paciente pt

      where pd.cd_ped_lab = c.cd_ped_lab

      and at.cd_atendimento = pd.cd_atendimento

      and at.cd_paciente = pt.cd_paciente

      and pd.cd_ped_lab not in (select cd_ped_lab from HMETRO.v_Tomademuestra)

      and  at.cd_ori_ate = 1 --origen de laboratorio

      and trunc(pd.dt_coleta) >= trunc(sysdate)

      and at.nr_chamada_painel  is not null

      and trunc((round(sysdate-pd.hr_ped_lab,4) - trunc(round(sysdate-pd.hr_ped_lab,4) ))*24) < 24

      order by 6 ";

            # Conectar base de datos
            $this->conectar_Oracle_MV();

            # Execute
            $stmt = $this->_conexion->query($sql);

            $this->_conexion->close();

            $data = $stmt->fetchAll();

            $ordenes = array();

            foreach ($data as $key) {

                $ordenes[] = array(
                    'cdAtendimento' => $key['CD_ATENDIMENTO'],
                    'codigoPedido' => $key['SC'],
                    'fechaPedido' => $key['FECHA_PEDIDO'] . ' ' . $key['HORA_PEDIDO'],
                    'at_mv' => $key['CD_ATENDIMENTO'],
                    'numeroHistoriaClinica' => $key['CD_PACIENTE'],
                    'paciente' => $key['NM_PACIENTE'],
                    'sector' => 'SERVICIOS AMBULATORIOS',
                    'tipoPedido' => 0,
                    'idTimeRecord' => 0,
                    'descPrestadorSolicitante' => 0,
                    'enviadoInfinity' => 0,
                    'muestrasProcesadas' => 0,
                    'tipoOperacion' => 0,
                    'callToma' => 0,
                );
            }

            if (count($ordenes) !== 0) {
                $json_string = json_encode($ordenes);
                file_put_contents($this->dirListadoTurnero . '_listado_.json', $json_string);
            } else {
                $json_string = json_encode([]);
                file_put_contents($this->dirListadoTurnero . '_listado_.json', $json_string);
            }

            return array(0);
        } catch (ModelsException $e) {

            return array(1);
        }
    }

    public function getIngresadasFlebotomista_v2()
    {

        try {

            global $config, $http;

            $fechaListado = date('d-m-Y');

            $i = 0;

            $ordenes = array();

            $listado = $this->dirListadoTurnero . '_listado_.json';
            $datos = file_get_contents($listado);
            $ingresos = json_decode($datos, true);

            if ($ingresos !== null) {

                foreach ($ingresos as $key) {

                    // Status muestras procesadas vs pendientes
                    $stsPteToma = $this->dirTurnero . $key['cdAtendimento'] . '_procesado_.json';
                    $stsCall = 1;
                    if (@file_get_contents($stsPteToma, true) === false) {
                        $stsCall = 0;
                    }

                    $stsPteTomaPendientes = $this->dirTurneroPendientes . $key['codigoPedido'] . '_pendiente_.json';
                    $stsPen = 1;
                    if (@file_get_contents($stsPteTomaPendientes, true) === false) {
                        $stsPen = 0;
                    }

                    if ($stsPen == 0) {
                        $ordenes[] = array(
                            'cdAtendimento' => $key['cdAtendimento'],
                            'codigoPedido' => $key['codigoPedido'],
                            'fechaPedido' => $key['fechaPedido'],
                            'at_mv' => $key['at_mv'],
                            'numeroHistoriaClinica' => $key['numeroHistoriaClinica'],
                            'paciente' => $key['paciente'],
                            'sector' => 'SERVICIOS AMBULATORIOS',
                            'tipoPedido' => 0,
                            'idTimeRecord' => 0,
                            'descPrestadorSolicitante' => 0,
                            'enviadoInfinity' => 0,
                            'muestrasProcesadas' => 0,
                            'tipoOperacion' => 0,
                            'callToma' => $stsCall,
                        );
                    }
                }

                return $ordenes;
            } else {

                return array();
            }
        } catch (ModelsException $e) {

            return array();
        }
    }

    public function getIngresadasFlebotomista_v3($toma = '')
    {

        try {


            $i = 0;

            $ordenes = array();

            $sql = " SELECT e.cd_ped_lab as SC, c.cd_paciente, c.nm_paciente, c.cd_atendimento,
            d.id_sgac AS id, a.strcodigo, b.strnombre AS NombreTramite, a.intSecuenciaModulo AS numero, 
            b.intcalifica As califica, a.strLetra As Letra, b.emite_alarma, a.strIdentificacion As Identificacion, 
            a.strNombreSocio As NombreSocio, a.NivelPrioridad, a.Especialidad, a.intVip, a.strobservacion AS Observacion, d.turno_completo,
            to_char(d.fecha_a_toma, 'DD-MM-YYYY HH24:MI') as FECHATURNOATENCION  
            From tblturnos a, tbltramite b, turno_sgac_mv d, triagem_atendimento c, ped_lab e
            Where a.strempresa = '1' And a.strsucursal = '1' And 
            a.strestado In ( Select strestado  
                           From tblmoduloestado 
                           Where strempresa = '1'  And 
                                 strsucursal = '1'  And 
                                 Especialidad = '0'  And 
                                 strtipo = 'L' ) AND 
                                 TO_CHAR(a.fechaactual,'YYYY-MM-DD') = TO_CHAR(CURRENT_DATE,'YYYY-MM-DD') And 
                                 a.Especialidad = '0' And 
                                 a.strcodigo In ( Select strtramite 
                                                  FROM tbltramitesmodulos  
                                                  WHERE strempresa = '1' AND  
                                                        strsucursal = '1' AND  
                                                        EmiteTurno = 'S' ) AND  
                                 b.strempresa = a.strempresa And 
                                 b.strsucursal = a.strsucursal And 
                                 b.Especialidad = a.Especialidad And 
                                 b.strcodigo = a.strcodigo AND
            a.origen = d.id_sgac AND                     
            d.turno_completo = c.ds_senha AND
            TRUNC(c.dh_pre_atendimento) = TRUNC(SYSDATE) AND
            c.cd_atendimento = e.cd_atendimento ";

            # Conectar base de datos
            $this->conectar_Oracle_MV();

            # Execute
            $stmt = $this->_conexion->query($sql);

            $this->_conexion->close();

            $data = $stmt->fetchAll();


            # Validación de Turneros
            $listado = $this->dirListadoTurnero . '_listado_.json';
            $datos = file_get_contents($listado);
            $ingresos = json_decode($datos, true);

            $indexAntPtes = array();
            foreach ($ingresos as $k => $v) {
                $indexAntPtes[] = $v['SC'];
            }

            # Verificar los nuevos turnos
            $nuevosPtes = array();
            foreach ($data as $k => $v) {
                $nuevosPtes[] = $v;
            }

            # Compara y Agregar nuevos registros
            foreach ($nuevosPtes as $k => $v) {
                if (in_array($v['SC'], $indexAntPtes) == false) {
                    $ingresos[] = $v;
                }
            }

            # Actualiza la lista
            $lista = $this->dirListadoTurnero . '_listado_.json';
            $json_string = json_encode($ingresos);
            file_put_contents($lista, $json_string);

            # Nueva Data
            $listado = $this->dirListadoTurnero . '_listado_.json';
            $datos = file_get_contents($listado);
            $data = json_decode($datos, true);

            $ordenes = array();

            foreach ($data as $key) {

                // Status muestras procesadas vs pendientes
                $stsPteToma = $this->dirTurnero .  $key['SC']  . '_procesado_.json';
                $stsCall = 1;
                if (@file_get_contents($stsPteToma, true) === false) {
                    $stsCall = 0;
                }

                $stsLlamadoFile = $this->dirTurnero .  $key['ID']  . '_llamado_.json';
                $stsLlamado = 1;
                $jsonLlamado = null;
                if (@file_get_contents($stsLlamadoFile, true) === false) {
                    $stsLlamado = 0;
                } else {
                    $jsonLlamado = json_decode(@file_get_contents($stsLlamadoFile, true), true);
                }

                $stsPteTomaPendientes = $this->dirTurneroPendientes . $key['SC'] . '_pendiente_.json';
                $stsPen = 1;
                if (@file_get_contents($stsPteTomaPendientes, true) === false) {
                    $stsPen = 0;
                }

                if ($stsCall == 0 && $stsPen == 0 && isset($jsonLlamado['toma']) && $jsonLlamado['toma'] == $toma && strtotime($key['FECHATURNOATENCION']) > strtotime(date('16-08-2024 00:00'))) {
                    $ordenes[] = array(
                        'cdAtendimento' => $key['CD_ATENDIMENTO'],
                        'codigoPedido' => $key['SC'],
                        'numeroModulo' => $key['NUMERO'],
                        'wid' => $key['ID'],
                        'fechaPedido' => '',
                        'at_mv' => $key['CD_ATENDIMENTO'],
                        'numeroHistoriaClinica' => $key['CD_PACIENTE'],
                        'paciente' => $key['NM_PACIENTE'],
                        'turno_completo' => (isset($key['TURNO_COMPLETO']) ? $key['TURNO_COMPLETO'] : ''),
                        'fechaTurnoAtencion' => (isset($key['FECHATURNOATENCION']) ? $key['FECHATURNOATENCION'] : ''),
                        'sector' => 'SERVICIOS AMBULATORIOS',
                        'tipoPedido' => 0,
                        'idTimeRecord' => 0,
                        'descPrestadorSolicitante' => 0,
                        'enviadoInfinity' => 0,
                        'muestrasProcesadas' => 0,
                        'tipoOperacion' => 0,
                        'callToma' => $stsCall,
                        'call' => $stsLlamado,
                        'k' => @file_get_contents($stsPteTomaPendientes, true)
                    );
                }
            }

            return $ordenes;
        } catch (ModelsException $e) {

            return array();
        }
    }

    public function getIngresadasFlebotomistaAllToma()
    {

        try {


            $i = 0;

            $ordenes = array();

            $sql = " SELECT e.cd_ped_lab as SC, c.cd_paciente, c.nm_paciente, c.cd_atendimento,
            d.id_sgac AS id, a.strcodigo, b.strnombre AS NombreTramite, a.intSecuenciaModulo AS numero, 
            b.intcalifica As califica, a.strLetra As Letra, b.emite_alarma, a.strIdentificacion As Identificacion, 
            a.strNombreSocio As NombreSocio, a.NivelPrioridad, a.Especialidad, a.intVip, a.strobservacion AS Observacion, d.turno_completo,
            to_char(d.fecha_a_toma, 'DD-MM-YYYY HH24:MI') as FECHATURNOATENCION  
            From tblturnos a, tbltramite b, turno_sgac_mv d, triagem_atendimento c, ped_lab e
            Where a.strempresa = '1' And a.strsucursal = '1' And 
            a.strestado In ( Select strestado  
                           From tblmoduloestado 
                           Where strempresa = '1'  And 
                                 strsucursal = '1'  And 
                                 Especialidad = '0'  And 
                                 strtipo = 'L' ) AND 
                                 TO_CHAR(a.fechaactual,'YYYY-MM-DD') = TO_CHAR(CURRENT_DATE,'YYYY-MM-DD') And 
                                 a.Especialidad = '0' And 
                                 a.strcodigo In ( Select strtramite 
                                                  FROM tbltramitesmodulos  
                                                  WHERE strempresa = '1' AND  
                                                        strsucursal = '1' AND  
                                                        EmiteTurno = 'S' ) AND  
                                 b.strempresa = a.strempresa And 
                                 b.strsucursal = a.strsucursal And 
                                 b.Especialidad = a.Especialidad And 
                                 b.strcodigo = a.strcodigo AND
            a.origen = d.id_sgac AND                     
            d.turno_completo = c.ds_senha AND
            TRUNC(c.dh_pre_atendimento) = TRUNC(SYSDATE) AND
            c.cd_atendimento = e.cd_atendimento ";

            # Conectar base de datos
            $this->conectar_Oracle_MV();

            # Execute
            $stmt = $this->_conexion->query($sql);

            $this->_conexion->close();

            $data = $stmt->fetchAll();


            # Validación de Turneros
            $listado = $this->dirListadoTurnero . '_listado_.json';
            $datos = file_get_contents($listado);
            $ingresos = json_decode($datos, true);

            $indexAntPtes = array();
            foreach ($ingresos as $k => $v) {
                $indexAntPtes[] = $v['SC'];
            }

            # Verificar los nuevos turnos
            $nuevosPtes = array();
            foreach ($data as $k => $v) {
                $nuevosPtes[] = $v;
            }

            # Compara y Agregar nuevos registros
            foreach ($nuevosPtes as $k => $v) {
                if (in_array($v['SC'], $indexAntPtes) == false) {
                    $ingresos[] = $v;
                }
            }

            # Actualiza la lista
            $lista = $this->dirListadoTurnero . '_listado_.json';
            $json_string = json_encode($ingresos);
            file_put_contents($lista, $json_string);

            # Nueva Data
            $listado = $this->dirListadoTurnero . '_listado_.json';
            $datos = file_get_contents($listado);
            $data = json_decode($datos, true);

            $ordenes = array();

            foreach ($data as $key) {

                // Status muestras procesadas vs pendientes
                $stsPteToma = $this->dirTurnero .  $key['SC']  . '_procesado_.json';
                $stsCall = 1;
                if (@file_get_contents($stsPteToma, true) === false) {
                    $stsCall = 0;
                }

                $stsLlamadoFile = $this->dirTurnero .  $key['ID']  . '_llamado_.json';
                $stsLlamado = 1;
                $jsonLlamado = null;
                if (@file_get_contents($stsLlamadoFile, true) === false) {
                    $stsLlamado = 0;
                }

                $stsPteTomaPendientes = $this->dirTurneroPendientes . $key['SC'] . '_pendiente_.json';
                $stsPen = 1;
                if (@file_get_contents($stsPteTomaPendientes, true) === false) {
                    $stsPen = 0;
                }

                if ($stsCall == 0 && $stsLlamado == 0 && $stsPen == 0) {
                    $ordenes[] = array(
                        'cdAtendimento' => $key['CD_ATENDIMENTO'],
                        'codigoPedido' => $key['SC'],
                        'numeroModulo' => $key['NUMERO'],
                        'wid' => $key['ID'],
                        'fechaPedido' => '',
                        'at_mv' => $key['CD_ATENDIMENTO'],
                        'numeroHistoriaClinica' => $key['CD_PACIENTE'],
                        'paciente' => $key['NM_PACIENTE'],
                        'sector' => 'SERVICIOS AMBULATORIOS',
                        'fechaTurnoAtencion' => (isset($key['FECHATURNOATENCION']) ? $key['FECHATURNOATENCION'] : ''),
                        'turno_completo' => (isset($key['TURNO_COMPLETO'])  ? $key['TURNO_COMPLETO'] : ''),
                        'tipoPedido' => 0,
                        'idTimeRecord' => 0,
                        'descPrestadorSolicitante' => 0,
                        'enviadoInfinity' => 0,
                        'muestrasProcesadas' => 0,
                        'tipoOperacion' => 0,
                        'callToma' => $stsCall,
                        'call' => $stsLlamado,
                        '_log' => $key


                    );
                }
            }

            return $ordenes;
        } catch (ModelsException $e) {

            return array();
        }
    }

    public function getIngresadasFlebotomista_TomasPendientes()
    {

        try {


            $i = 0;

            $ordenes = array();

            $sql = "SELECT 
            e.cd_ped_lab as SC, 
            c.cd_paciente, 
            c.nm_paciente, 
            c.cd_atendimento, 
            d.id_sgac AS id, 
            a.strcodigo, 
            b.strnombre AS NombreTramite, 
            a.intSecuenciaModulo AS numero, 
            b.intcalifica As califica, 
            a.strLetra As Letra, 
            b.emite_alarma, 
            a.strIdentificacion As Identificacion, 
            a.strNombreSocio As NombreSocio, 
            a.NivelPrioridad, 
            a.Especialidad, 
            a.intVip, 
            a.strobservacion AS Observacion, 
            d.turno_completo,
            to_char(d.fecha_a_toma, 'DD-MM-YYYY HH24:MI') as FECHATURNOATENCION 
          From 
            tblturnos a, 
            tbltramite b, 
            turno_sgac_mv d, 
            triagem_atendimento c, 
            ped_lab e 
          Where 
            a.strempresa = '1' 
            And a.strsucursal = '1' 
            And a.strestado In (
              Select 
                strestado 
              From 
                tblmoduloestado 
              Where 
                strempresa = '1' 
                And strsucursal = '1' 
                And Especialidad = '0' 
                And strtipo = 'L'
            ) 
            AND TO_CHAR(a.fechaactual, 'YYYY-MM-DD') = TO_CHAR(CURRENT_DATE, 'YYYY-MM-DD') 
            And a.Especialidad = '0' 
            And a.strcodigo In (
              Select 
                strtramite 
              FROM 
                tbltramitesmodulos 
              WHERE 
                strempresa = '1' 
                AND strsucursal = '1' 
                AND EmiteTurno = 'S'
            ) 
            AND b.strempresa = a.strempresa 
            And b.strsucursal = a.strsucursal 
            And b.Especialidad = a.Especialidad 
            And b.strcodigo = a.strcodigo 
            AND a.origen = d.id_sgac 
            AND d.turno_completo = c.ds_senha 
            AND TRUNC(c.dh_pre_atendimento) = TRUNC(SYSDATE) 
            AND c.cd_atendimento = e.cd_atendimento ";

            # Conectar base de datos
            $this->conectar_Oracle_MV();

            # Execute
            $stmt = $this->_conexion->query($sql);

            $this->_conexion->close();

            $data = $stmt->fetchAll();


            # Validación de Turneros
            $listado = $this->dirListadoTurnero . '_listado_.json';
            $datos = file_get_contents($listado);
            $ingresos = json_decode($datos, true);

            $indexAntPtes = array();
            foreach ($ingresos as $k => $v) {
                $indexAntPtes[] = $v['SC'];
            }

            # Verificar los nuevos turnos
            $nuevosPtes = array();
            foreach ($data as $k => $v) {
                $nuevosPtes[] = $v;
            }

            # Compara y Agregar nuevos registros
            foreach ($nuevosPtes as $k => $v) {
                if (in_array($v['SC'], $indexAntPtes) == false) {
                    $ingresos[] = $v;
                }
            }

            # Actualiza la lista
            $lista = $this->dirListadoTurnero . '_listado_.json';
            $json_string = json_encode($ingresos);
            file_put_contents($lista, $json_string);

            # Nueva Data
            $listado = $this->dirListadoTurnero . '_listado_.json';
            $datos = file_get_contents($listado);
            $data = json_decode($datos, true);

            $ordenes = array();

            foreach ($data as $key) {


                $stsPteTomaPendientes = $this->dirTurneroPendientes . $key['SC'] . '_pendiente_.json';
                $stsPen = 1;
                if (@file_get_contents($stsPteTomaPendientes, true) === false) {
                    $stsPen = 0;
                }

                if ($stsPen == 1 && strtotime($key['FECHATURNOATENCION']) > strtotime(date('16-08-2024 00:00'))) {
                    $ordenes[] = array(
                        'cdAtendimento' => $key['CD_ATENDIMENTO'],
                        'codigoPedido' => $key['SC'],
                        'numeroModulo' => $key['NUMERO'],
                        'wid' => $key['ID'],
                        'fechaPedido' => '',
                        'at_mv' => $key['CD_ATENDIMENTO'],
                        'numeroHistoriaClinica' => $key['CD_PACIENTE'],
                        'paciente' => $key['NM_PACIENTE'],
                        'turno_completo' => (isset($key['TURNO_COMPLETO']) ? $key['TURNO_COMPLETO'] : ''),
                        'sector' => 'SERVICIOS AMBULATORIOS',
                        'fechaTurnoAtencion' => (isset($key['FECHATURNOATENCION']) ? $key['FECHATURNOATENCION'] : ''),
                        'tipoPedido' => 0,
                        'idTimeRecord' => 0,
                        'descPrestadorSolicitante' => 0,
                        'enviadoInfinity' => 0,
                        'muestrasProcesadas' => 0,
                        'tipoOperacion' => 0,
                        'callToma' => 0,
                        'call' => 0,


                    );
                }
            }

            return $ordenes;
        } catch (ModelsException $e) {

            return array();
        }
    }


    public function getIngresadasBeta()
    {

        try {

            global $config, $http;

            $typeFilter = $http->query->get('idFiltro');
            $fechaDesde = $http->query->get('fechaDesde');
            $fechaHasta = $http->query->get('fechaHasta');

            $list = Helper\Files::get_files_in_dir('../beta/v1//lisa/pedidos/ingresados/');

            $i = 0;

            $ordenes = array();

            foreach ($list as $key => $val) {

                $xml = file_get_contents($val);

                $data = utf8_encode($xml);

                // Extract XML Dcoument
                $dataPedido = simplexml_load_string($data);
                $pedido = $dataPedido->children('soap', true)->Body->children();
                $listaExamen = $pedido->Mensagem->PedidoExameLab->listaExame;

                $segundoApellido = $pedido->Mensagem->PedidoExameLab->paciente->segundoSobrenome;
                $primerApellido = $pedido->Mensagem->PedidoExameLab->paciente->primeiroSobrenome;
                $primerNombre = $pedido->Mensagem->PedidoExameLab->paciente->primeiroNome;
                $segundoNombre = $pedido->Mensagem->PedidoExameLab->paciente->segundoNome;

                $codigoPedido = $pedido->Mensagem->PedidoExameLab->codigoPedido;
                $idTimeRecord = strtotime($pedido->Mensagem->Cabecalho->dataHora);

                $fechaPedido = $pedido->Mensagem->PedidoExameLab->dataColetaPedido;
                $numeroHistoriaClinica = $pedido->Mensagem->PedidoExameLab->paciente->codigoPaciente;
                $codigoAtendimento = $pedido->Mensagem->PedidoExameLab->atendimento->codigoAtendimento;

                $sector = $pedido->Mensagem->PedidoExameLab->descSetorSolicitante;
                $tipoPedido = $pedido->Mensagem->PedidoExameLab->tipoSolicitacao;
                $descPrestadorSolicitante = $pedido->Mensagem->PedidoExameLab->descPrestadorSolicitante;
                $tipoOperacion = $pedido->Mensagem->PedidoExameLab->operacao;

                // Status enviado Infinity
                $stsenviado = $this->dirEnviados . $codigoPedido . '_' . $idTimeRecord . '.xml';
                $stsenviado2 = $this->dirEnviados . $codigoPedido . '.xml';

                $enviadoInfinity = 1;
                if (@file_get_contents($stsenviado, true) === false) {
                    $enviadoInfinity = 0;
                }

                // Status muestras procesadas vs pendientes
                $stsmuestrasProcesadas = $this->dirTomaMuestras . $codigoPedido . '_procesado_.json';
                $muestrasProcesadas = 1;
                if (@file_get_contents($stsmuestrasProcesadas, true) === false) {
                    $muestrasProcesadas = 0;
                }

                # Pedidos de Hoy
                if ($typeFilter == 1) {

                    $_codigoPedido = (array) $codigoPedido;
                    $_fechaPedido = (array) $fechaPedido;
                    $_codigoAtendimento = (array) $codigoAtendimento;
                    $_numeroHistoriaClinica = (array) $numeroHistoriaClinica;
                    $_sector = (array) $sector;
                    $_descPrestadorSolicitante = (array) $descPrestadorSolicitante;
                    $_tipoPedido = (array) $tipoPedido;
                    $_tipoOperacion = (array) $tipoOperacion;
                    $_idTimeRecord = (array) $idTimeRecord;

                    if (date('Y-m-d', strtotime($fechaPedido)) == date('Y-m-d')) {
                        $ordenes[] = array(
                            'codigoPedido' => $_codigoPedido[0],
                            'fechaPedido' => $_fechaPedido[0],
                            'at_mv' => $_codigoAtendimento[0],
                            'numeroHistoriaClinica' => $_numeroHistoriaClinica[0],
                            'paciente' => $primerApellido . ' ' . $segundoApellido . ' ' . $primerNombre . ' ' . $segundoNombre,
                            'sector' => $_sector[0],
                            'tipoPedido' => $_tipoPedido[0],
                            'descPrestadorSolicitante' => $_descPrestadorSolicitante[0],
                            'tipoOperacion' => $_tipoOperacion[0],
                            'idTimeRecord' => $_idTimeRecord[0],
                            'enviadoInfinity' => $enviadoInfinity,
                            'muestrasProcesadas' => $muestrasProcesadas,
                            '_val' => $listaExamen,
                        );
                    }
                }

                # Pedidos de Emergencia
                if ($typeFilter == 2) {

                    $_codigoPedido = (array) $codigoPedido;
                    $_fechaPedido = (array) $fechaPedido;
                    $_codigoAtendimento = (array) $codigoAtendimento;
                    $_numeroHistoriaClinica = (array) $numeroHistoriaClinica;
                    $_sector = (array) $sector;
                    $_descPrestadorSolicitante = (array) $descPrestadorSolicitante;
                    $_tipoPedido = (array) $tipoPedido;
                    $_tipoOperacion = (array) $tipoOperacion;
                    $_idTimeRecord = (array) $idTimeRecord;

                    $timedesde = strtotime(date('Y-m-d', strtotime($fechaDesde)));
                    $timeHasta = strtotime(date('Y-m-d', strtotime($fechaHasta)));

                    if ($sector == 'EMERGENCIA' && (strtotime(date('Y-m-d', strtotime($fechaPedido))) >= $timedesde) && (strtotime(date('Y-m-d', strtotime($fechaPedido))) <= $timeHasta)) {
                        $ordenes[] = array(
                            'codigoPedido' => $_codigoPedido[0],
                            'fechaPedido' => $_fechaPedido[0],
                            'at_mv' => $_codigoAtendimento[0],
                            'numeroHistoriaClinica' => $_numeroHistoriaClinica[0],
                            'paciente' => $primerApellido . ' ' . $segundoApellido . ' ' . $primerNombre . ' ' . $segundoNombre,
                            'sector' => $_sector[0],
                            'tipoPedido' => $_tipoPedido[0],
                            'idTimeRecord' => $_idTimeRecord[0],
                            'descPrestadorSolicitante' => $_descPrestadorSolicitante[0],
                            'enviadoInfinity' => $enviadoInfinity,
                            'muestrasProcesadas' => $muestrasProcesadas,
                            'tipoOperacion' => $_tipoOperacion[0],

                        );
                    }
                }

                if ($typeFilter == 3) {

                    $_codigoPedido = (array) $codigoPedido;
                    $_fechaPedido = (array) $fechaPedido;
                    $_codigoAtendimento = (array) $codigoAtendimento;
                    $_numeroHistoriaClinica = (array) $numeroHistoriaClinica;
                    $_sector = (array) $sector;
                    $_descPrestadorSolicitante = (array) $descPrestadorSolicitante;
                    $_tipoPedido = (array) $tipoPedido;
                    $_tipoOperacion = (array) $tipoOperacion;
                    $_idTimeRecord = (array) $idTimeRecord;

                    $timedesde = strtotime(date('Y-m-d', strtotime($fechaDesde)));
                    $timeHasta = strtotime(date('Y-m-d', strtotime($fechaHasta)));

                    if ((strpos($sector, 'HOSPITALIZACION') !== false || strpos($sector, 'UCI') !== false || strpos($sector, 'UNIDAD QUIRURGICA') !== false || strpos($sector, 'NEONATOLOGIA') !== false) && (strtotime(date('Y-m-d', strtotime($fechaPedido))) >= $timedesde) && (strtotime(date('Y-m-d', strtotime($fechaPedido))) <= $timeHasta)) {
                        $ordenes[] = array(
                            'codigoPedido' => $_codigoPedido[0],
                            'fechaPedido' => $_fechaPedido[0],
                            'at_mv' => $_codigoAtendimento[0],
                            'numeroHistoriaClinica' => $_numeroHistoriaClinica[0],
                            'paciente' => $primerApellido . ' ' . $segundoApellido . ' ' . $primerNombre . ' ' . $segundoNombre,
                            'sector' => $_sector[0],
                            'tipoPedido' => $_tipoPedido[0],
                            'idTimeRecord' => $_idTimeRecord[0],
                            'descPrestadorSolicitante' => $_descPrestadorSolicitante[0],
                            'enviadoInfinity' => $enviadoInfinity,
                            'muestrasProcesadas' => $muestrasProcesadas,
                            'tipoOperacion' => $_tipoOperacion[0],

                        );
                    }
                }

                if ($typeFilter == 4) {

                    $_codigoPedido = (array) $codigoPedido;
                    $_fechaPedido = (array) $fechaPedido;
                    $_codigoAtendimento = (array) $codigoAtendimento;
                    $_numeroHistoriaClinica = (array) $numeroHistoriaClinica;
                    $_sector = (array) $sector;
                    $_descPrestadorSolicitante = (array) $descPrestadorSolicitante;
                    $_tipoPedido = (array) $tipoPedido;
                    $_tipoOperacion = (array) $tipoOperacion;
                    $_idTimeRecord = (array) $idTimeRecord;

                    $timedesde = strtotime(date('Y-m-d', strtotime($fechaDesde)));
                    $timeHasta = strtotime(date('Y-m-d', strtotime($fechaHasta)));

                    if (strpos($sector, 'SERVICIOS AMBULATORIOS') !== false && (strtotime(date('Y-m-d', strtotime($fechaPedido))) >= $timedesde) && (strtotime(date('Y-m-d', strtotime($fechaPedido))) <= $timeHasta)) {
                        $ordenes[] = array(
                            'codigoPedido' => $_codigoPedido[0],
                            'fechaPedido' => $_fechaPedido[0],
                            'at_mv' => $_codigoAtendimento[0],
                            'numeroHistoriaClinica' => $_numeroHistoriaClinica[0],
                            'paciente' => $primerApellido . ' ' . $segundoApellido . ' ' . $primerNombre . ' ' . $segundoNombre,
                            'sector' => $_sector[0],
                            'tipoPedido' => $_tipoPedido[0],
                            'idTimeRecord' => $_idTimeRecord[0],
                            'descPrestadorSolicitante' => $_descPrestadorSolicitante[0],
                            'enviadoInfinity' => $enviadoInfinity,
                            'muestrasProcesadas' => $muestrasProcesadas,
                            'tipoOperacion' => $_tipoOperacion[0],

                        );
                    }
                }
            }

            return $ordenes;
        } catch (ModelsException $e) {

            return array();
        }
    }

    public function validarExamenesBCOSANGRE($pedido)
    {


        $siContiene = false;

        foreach ($pedido->Exame as $key) {

            $_NM_EXA_LAB = (array) $key->descExame;
            $_CD_EXA_LAB = (array) $key->codigoExame;
            $_CD_PRO_FAT = (array) $key->codigoExameFaturamento;
            $_CD_SETOR = (array) $key->setorExecutante;

            if ($_CD_EXA_LAB[0] == '542' || $_CD_EXA_LAB[0] == '543' || $_CD_EXA_LAB[0] == '544' || $_CD_EXA_LAB[0] == '545') {
                $siContiene = true;
            }
        }

        return $siContiene;
    }

    public function getFiltradas(): array
    {

        try {

            global $config, $http;

            $list = Helper\Files::get_files_in_dir('../../nss/v1/ordenes/filtradas/');

            $i = 0;

            $ordenes = array();

            // Extraer ORDENES PARA FILTRAR
            foreach ($list as $key => $val) {

                $content = file_get_contents($val);
                $documento = json_decode($content, true);
                $ordenes[] = $documento;
            }

            $list = Helper\Files::get_files_in_dir('../../nss/v1/ordenes/ingresadas/');

            // Extraer ORDENES PARA FILTRAR
            foreach ($list as $key => $val) {

                $content = file_get_contents($val);
                $documento = json_decode($content, true);

                $ordenes[] = $documento;
            }

            return $ordenes;
        } catch (ModelsException $e) {

            return array();
        }
    }

    public function getPorEnviar(): array
    {

        try {

            global $config, $http;

            $list = Helper\Files::get_files_in_dir('../../nss/v1/ordenes/porenviar/');

            $i = 0;

            $ordenes = array();

            // Extraer ORDENES PARA FILTRAR
            foreach ($list as $key => $val) {

                $content = file_get_contents($val);
                $documento = json_decode($content, true);
                $ordenes[] = $documento;
            }

            return $ordenes;
        } catch (ModelsException $e) {

            return array();
        }
    }

    public function getEnviadas(): array
    {

        try {

            global $config, $http;

            $list = Helper\Files::get_files_in_dir('../../nss/v1/ordenes/enviadas/');

            $i = 0;

            $ordenes = array();

            // Extraer ORDENES PARA FILTRAR
            foreach ($list as $key => $val) {

                $content = file_get_contents($val);
                $documento = json_decode($content, true);

                if ($documento['fechaExamen'] == date('Y-m-d')) {
                    $documento['_PDF'] = '';
                    $documento['file'] = $val;
                    $ordenes[] = $documento;
                }
            }

            return $ordenes;
        } catch (ModelsException $e) {

            return array();
        }
    }

    public function getErroresFiltradas(): array
    {

        try {

            global $config, $http;

            $list = Helper\Files::get_files_in_dir('../../nss/v1/ordenes/errores/filtradas/');

            $i = 0;

            $ordenes = array();

            // Extraer ORDENES PARA FILTRAR
            foreach ($list as $key => $val) {

                $content = file_get_contents($val);
                $documento = json_decode($content, true);
                $documento['_PDF'] = '';
                $documento['file'] = $val;
                $ordenes[] = $documento;
            }

            return $ordenes;
        } catch (ModelsException $e) {

            return array();
        }
    }

    public function getErroresEnviadas(): array
    {

        try {

            global $config, $http;

            $list = Helper\Files::get_files_in_dir('../../nss/v1/ordenes/errores/enviadas/');

            $i = 0;

            $ordenes = array();

            // Extraer ORDENES PARA FILTRAR
            foreach ($list as $key => $val) {

                $content = file_get_contents($val);
                $documento = json_decode($content, true);
                $documento['_PDF'] = '';
                $documento['file'] = $val;
                $ordenes[] = $documento;
            }

            return $ordenes;
        } catch (ModelsException $e) {

            return array();
        }
    }

    private function quitar_tildes($cadena)
    {
        $no_permitidas = array("%", "é", "í", "ó", "ú", "É", "Í", "Ó", "Ú", "ñ", "À", "Ã", "Ì", "Ò", "Ù", "Ã™", "Ã ", "Ã¨", "Ã¬", "Ã²", "Ã¹", "ç", "Ç", "Ã¢", "ê", "Ã®", "Ã´", "Ã»", "Ã‚", "ÃŠ", "ÃŽ", "Ã”", "Ã›", "ü", "Ã¶", "Ã–", "Ã¯", "Ã¤", "«", "Ò", "Ã", "Ã„", "Ã‹");
        $permitidas = array("", "e", "i", "o", "u", "E", "I", "O", "U", "n", "N", "A", "E", "I", "O", "U", "a", "e", "i", "o", "u", "c", "C", "a", "e", "i", "o", "u", "A", "E", "I", "O", "U", "u", "o", "O", "i", "a", "e", "U", "I", "A", "E");
        $texto = str_replace($no_permitidas, $permitidas, $cadena);
        return $texto;
    }

    private function sanear_string($string)
    {

        $string = trim($string);

        //Esta parte se encarga de eliminar cualquier caracter extraño
        $string = str_replace(
            array(">", "< ", ";", ",", ":", "%", "|", "-", "/"),
            ' ',
            $string
        );

        return trim($string);
    }

    # Ordenar array por campo
    public function orderMultiDimensionalArray($toOrderArray, $field, $inverse = 'desc')
    {
        $position = array();
        $newRow = array();
        foreach ($toOrderArray as $key => $row) {
            $position[$key] = $row[$field];
            $newRow[$key] = $row;
        }
        if ($inverse == 'desc') {
            arsort($position);
        } else {
            asort($position);
        }
        $returnArray = array();
        foreach ($position as $key => $pos) {
            $returnArray[] = $newRow[$key];
        }
        return $returnArray;
    }

    private function get_Order_Pagination(array $arr_input)
    {
        # SI ES DESCENDENTE

        $arr = array();
        $NUM = 1;

        if ($this->sortType == 'desc') {

            $NUM = count($arr_input);
            foreach ($arr_input as $key) {
                $key['NUM'] = $NUM;
                $arr[] = $key;
                $NUM--;
            }

            return $arr;
        }

        # SI ES ASCENDENTE

        foreach ($arr_input as $key) {
            $key['NUM'] = $NUM;
            $arr[] = $key;
            $NUM++;
        }

        return $arr; // 1000302446
    }

    private function get_page(array $input, $pageNum, $perPage)
    {
        $start = ($pageNum - 1) * $perPage;
        $end = $start + $perPage;
        $count = count($input);

        // Conditionally return results
        if ($start < 0 || $count <= $start) {
            // Page is out of range
            return array();
        } else if ($count <= $end) {
            // Partially-filled page
            return array_slice($input, $start);
        } else {
            // Full page
            return array_slice($input, $start, $end - $start);
        }
    }

    private function notResults(array $data)
    {
        if (count($data) == 0) {
            throw new ModelsException('No existe más resultados.', 4080);
        }
    }

    public function __construct(IRouter $router = null)
    {
        parent::__construct($router);
    }
}
