<?php

/*
 * This file is part of the Ocrend Framewok 3 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace app\models\lisa\usp;

use app\models\lisa as Model;
use Exception;
use Ocrend\Kernel\Models\IModels;
use Ocrend\Kernel\Models\Models;
use Ocrend\Kernel\Models\ModelsException;
use Ocrend\Kernel\Router\IRouter;

/**
 * Modelo Odbc GEMA -> Historia clínica
 */

class Procesos extends Models implements IModels
{

    # Variables de clase
    private $historiaClinica = null;
    private $motivoConsulta;
    private $revisionOrganos;
    private $antecedentesFamiliares;
    private $signosVitales;
    private $examenFisico;
    private $diagnosticos;
    private $evoluciones;
    private $prescripciones;
    private $conexion;
    private $numeroHistoriaClinica;
    private $numeroAdmision;
    private $codigoInstitucion = 1;
    private $numeroCompania = '01';
    private $recomendacionesNoFarmacologicas;
    private $tamanioCodigoExamen = 9;
    private $tamanioDescripcionExamen = 120;
    private $pedidosLaboratorio;
    private $pedidosImagen;
    private $start = 0;
    private $length = 10;
    public $dirTurnero = '../v1/lisa/pedidos/turnero/';
    public $dirTurneroPendiente = '../v1/lisa/pedidos/tomasPendientes/';

    /**
     * Get Auth
     *
     * @var
     */

    private function getAuthorization()
    {

        try {

            global $http;

            $token = $http->headers->get("Authorization");

            $auth = new Model\Auth;
            $data = $auth->GetData($token);

            $this->id_user = $data;
        } catch (ModelsException $e) {
            return array('status' => false, 'message' => $e->getMessage());
        }
    }

    /**
     * Asigna los parámetros de entrada
     */
    private function setParameters()
    {
        global $http;

        foreach ($http->request->all() as $key => $value) {
            $this->$key = strtoupper($value);
        }

        foreach ($http->query->all() as $key => $value) {
            $this->$key = $value;
        }
    }

    private function setSpanishOracle($stid)
    {

        $sql = "alter session set NLS_LANGUAGE = 'SPANISH'";
        # Execute
        $stid = oci_parse($this->conexion->getConexion(), $sql);
        oci_execute($stid);

        $sql = "alter session set NLS_TERRITORY = 'SPAIN'";
        # Execute
        $stid = oci_parse($this->conexion->getConexion(), $sql);
        oci_execute($stid);

        $sql = " alter session set NLS_DATE_FORMAT = 'DD/MM/YYYY HH24:MI'";
        # Execute
        $stid = oci_parse($this->conexion->getConexion(), $sql);
        oci_execute($stid);
    }




    /**
     * Permite registrar un nuevo paciente
     */
    public function callToma()
    {
        global $config, $http;

        //Inicialización de variables
        $stmt = null;
        $codigoRetorno = null;
        $mensajeRetorno = null;
        $r = false;
        $rCommit = false;
        $codigoError = -1;
        $mensajeError = null;

        try {

            $sonido = '';

            if ($http->request->get('toma') == 'TOMA1') {
                $sonido = 'MODULO1';
            } else if ($http->request->get('toma') == 'TOMA2') {
                $sonido = 'MODULO2';
            } else if ($http->request->get('toma') == 'TOMA3') {
                $sonido = 'MODULO3';
            } else if ($http->request->get('toma') == 'TOMA4') {
                $sonido = 'MODULO4';
            } else if ($http->request->get('toma') == 'TOMA5') {
                $sonido = 'MODULO5';
            } else {
                $sonido = 'MODULO';
            }

            // $sonido = 'MODULO';

            //$atencion = $http->request->get('atencion');
            $wempresa = '1';
            $wsucursal = '1';
            $wid =  $http->request->get('wid');
            $wmodulo =  $http->request->get('toma');
            $wusuario = 'MCHANG';
            $westado = 'Llamado';
            $wabreviado = $http->request->get('toma');
            $wobservacion = '';
            $wrespuesta1 = 0;
            $wrespuesta2 = 0;
            $wrespuesta3 = 0;
            $wrespuesta4 = 0;
            $wrespuesta5 = 0;
            $wtipocierre = '';
            $wtramitetransferido = '';
            $wnumeromodulo = $http->request->get('modulo');
            $wtipo = '1';
            $worigen = '0';
            $wsonido = $sonido;

            // Conectar a la BDD
            $this->conexion->conectar();

            // Setear idioma y formatos en español para Oracle
            $this->setSpanishOracle($stmt);

            $stmt = oci_parse($this->conexion->getConexion(), "BEGIN
                SP_CambiaEstado(
                    :wempresa,
                    :wsucursal,
                    :wid,
                    :wmodulo,
                    :wusuario,
                    :westado,
                    :wabreviado,
                    :wobservacion,
                    :wrespuesta1,
                    :wrespuesta2,
                    :wrespuesta3,
                    :wrespuesta4,
                    :wrespuesta5,
                    :wtipocierre,
                    :wtramitetransferido,
                    :worigen,
                    :wsonido,
                    :wnumeromodulo,
                    :wtipo
            ); END;");

            // Bind the input parameter
            oci_bind_by_name($stmt, ':wempresa', $wempresa, 32);
            oci_bind_by_name($stmt, ':wsucursal', $wsucursal, 32);
            oci_bind_by_name($stmt, ':wid', $wid, 32);
            oci_bind_by_name($stmt, ':wmodulo', $wmodulo, 32);
            oci_bind_by_name($stmt, ':wusuario', $wusuario, 32);
            oci_bind_by_name($stmt, ':westado', $westado, 32);
            oci_bind_by_name($stmt, ':wabreviado', $wabreviado, 32);
            oci_bind_by_name($stmt, ':wobservacion', $wobservacion, 32);
            oci_bind_by_name($stmt, ':wrespuesta1', $wrespuesta1, 32);
            oci_bind_by_name($stmt, ':wrespuesta2', $wrespuesta2, 32);
            oci_bind_by_name($stmt, ':wrespuesta3', $wrespuesta3, 32);
            oci_bind_by_name($stmt, ':wrespuesta4', $wrespuesta4, 32);
            oci_bind_by_name($stmt, ':wrespuesta5', $wrespuesta5, 32);
            oci_bind_by_name($stmt, ':wtipocierre', $wtipocierre, 32);
            oci_bind_by_name($stmt, ':wtramitetransferido', $wtramitetransferido, 32);
            oci_bind_by_name($stmt, ':worigen', $worigen, 32);
            oci_bind_by_name($stmt, ':wsonido', $wsonido, 32);
            oci_bind_by_name($stmt, ':wnumeromodulo', $wnumeromodulo, 32);
            oci_bind_by_name($stmt, ':wtipo', $wtipo, 32);

            //Ejecuta el SP
            oci_execute($stmt);

            $rCommit = oci_commit($this->conexion->getConexion());

            //Error al insertar en la tabla principal
            if (!$rCommit) {
                $e = oci_error($stmt);
                $mensajeError = "Error, consulte con el Administrador del Sistema. " . $e['message'];
            }

            // Crea Registro de Llamada
            $stsmuestrasRecibidas = $this->dirTurnero . $wid . '_llamado_.json';
            $json_string = json_encode($http->request->all());
            file_put_contents($stsmuestrasRecibidas, $json_string);

            $mensajeRetorno = 'Proceso ejecutado con éxito.';

            return array(
                'status' => true,
                'data' => [],
                'message' => $mensajeRetorno
            );
        } catch (ModelsException $e) {

            return array(
                'status' => false,
                'data' => [],
                'message' => $e->getMessage(),
                'errorCode' => $e->getCode()
            );
        } catch (Exception $ex) {
            //
            $mensajeError = $ex->getMessage();

            return array(
                'status' => false,
                'data' => [],
                'message' => $mensajeError,
                'errorCode' => $codigoError
            );
        } finally {
            //Libera recursos de conexión
            if ($stmt != null) {
                oci_free_statement($stmt);
            }

            //Cierra la conexión
            $this->conexion->cerrar();
        }
    }

    public function tomaPendiente()
    {
        global $config, $http;

        //Inicialización de variables
        $stmt = null;
        $codigoRetorno = null;
        $mensajeRetorno = null;
        $r = false;
        $rCommit = false;
        $codigoError = -1;

        try {

            $sc = $http->request->get('sc');

            // Crea Registro de Llamada
            $stsmuestrasRecibidas = $this->dirTurneroPendiente . $sc . '_pendiente_.json';
            $json_string = json_encode(array());
            file_put_contents($stsmuestrasRecibidas, $json_string);

            $mensajeRetorno = 'Proceso ejecutado con éxito.';

            return array(
                'status' => true,
                'data' => [],
                'message' => $mensajeRetorno
            );
        } catch (ModelsException $e) {

            return array(
                'status' => false,
                'data' => [],
                'message' => $e->getMessage(),
                'errorCode' => $e->getCode()
            );
        }
    }

    public function deshacerTomaPendiente()
    {
        global $config, $http;

        //Inicialización de variables
        $stmt = null;
        $codigoRetorno = null;
        $mensajeRetorno = null;
        $r = false;
        $rCommit = false;
        $codigoError = -1;

        try {

            $sc = $http->request->get('sc');
            $wid = $http->request->get('wid');


            // Crea Registro de Llamada
            $stsmuestrasRecibidas = $this->dirTurneroPendiente . $sc . '_pendiente_.json';
            @unlink($stsmuestrasRecibidas);

            $stsmuestrasLlamado = $this->dirTurnero . $wid . '_llamado_.json';
            @unlink($stsmuestrasLlamado);

            $stsmuestrasProcesado = $this->dirTurnero . $sc . '_procesado_.json';
            @unlink($stsmuestrasProcesado);

            $mensajeRetorno = 'Proceso ejecutado con éxito.';

            return array(
                'status' => true,
                'data' => [],
                'message' => $mensajeRetorno
            );
        } catch (ModelsException $e) {

            return array(
                'status' => false,
                'data' => [],
                'message' => $e->getMessage(),
                'errorCode' => $e->getCode()
            );
        }
    }

    /**
     * __construct()
     */
    public function __construct(IRouter $router = null)
    {
        parent::__construct($router);

        //Instancia la clase conexión a la base de datos
        $this->conexion = new Conexion();
    }
}
