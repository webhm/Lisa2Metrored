<?php

/*
 * Hospital Metropolitano
 */

namespace app\models\lisa2;

use Doctrine\DBAL\DriverManager;
use Exception;
use Ocrend\Kernel\Helpers as Helper;
use Ocrend\Kernel\Models\IModels;
use Ocrend\Kernel\Models\Models;
use Ocrend\Kernel\Models\ModelsException;
use Ocrend\Kernel\Router\IRouter;

/**
 * Modelo GestorConfigXML
 */
class GestorConfigXML extends Models implements IModels
{
    private $archivoConfig;
    private $configDb;

    private function cargarConfig(): void
    {
        if (file_exists($this->archivoConfig)) {
            $this->configDb = json_decode(file_get_contents($this->archivoConfig), true);
        } else {
            $this->configDb = ['versions' => []];
            $this->crearVersionInicial();
            $this->guardarConfig();
        }
    }

    private function guardarConfig(): void
    {
        file_put_contents($this->archivoConfig, json_encode($this->configDb, JSON_PRETTY_PRINT));
    }

    private function crearVersionInicial(): void
    {
        $versionId = uniqid('v_');
        $this->configDb['versions'][$versionId] = [
            'idEmpresa' => 1,
            'fechaCreacion' => date('Y-m-d H:i:s'),
            'fechaActualizacion' => date('Y-m-d H:i:s'),
            'config' => $this->obtenerConfigPredeterminada()
        ];
    }

    private function obtenerConfigPredeterminada(): array
    {
        return [
            'Cabecalho' => [
                'mensagemID' => ['requerido' => true, 'predeterminado' => ''],
                'dataHora' => ['requerido' => true, 'predeterminado' => date('Y-m-d H:i:s')],
                'servico' => ['requerido' => true, 'predeterminado' => ''],
                'versaoXML' => ['requerido' => false, 'predeterminado' => '1.0']
            ],
            'PedidoExameLab' => [
                'idIntegracao' => ['requerido' => true, 'predeterminado' => ''],
                'operacao' => ['requerido' => true, 'predeterminado' => 'I'],
                'codigoPedido' => ['requerido' => false, 'predeterminado' => '']
            ],
            'Paciente' => [
                'codigoPaciente' => ['requerido' => true, 'predeterminado' => ''],
                'nome' => ['requerido' => true, 'predeterminado' => ''],
                'dataNacimiento' => ['requerido' => false, 'predeterminado' => '']
            ]
        ];
    }

    // Obtener todas las versiones
    public function obtenerTodasVersiones(): array
    {
        return array('status' => true, 'data' => $this->configDb['versions']);
    }

    // Obtener una versión específica por ID
    public function obtenerVersion(string $versionId): ?array
    {
        return $this->configDb['versions'][$versionId] ?? null;
    }

    // Obtener la configuración de una versión específica
    public function obtenerConfig(string $versionId): ?array
    {
        return $this->configDb['versions'][$versionId]['config'] ?? null;
    }

    // Obtener una sección de una versión específica
    public function obtenerSeccion(string $versionId, string $seccion): ?array
    {
        return $this->configDb['versions'][$versionId]['config'][$seccion] ?? null;
    }

    // Obtener un campo de una sección en una versión específica
    public function obtenerCampo(string $versionId, string $seccion, string $campo): ?array
    {
        return $this->configDb['versions'][$versionId]['config'][$seccion][$campo] ?? null;
    }

    // Crear una nueva versión
    public function crearVersion(array $config = null): string
    {
        $versionId = uniqid('v_');
        $this->configDb['versions'][$versionId] = [
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'config' => $config ?? $this->obtenerConfigPredeterminada()
        ];
        $this->guardarConfig();
        return $versionId;
    }

    // Actualizar una versión existente
    public function actualizarVersion(string $versionId, array $config): void
    {
        if (!isset($this->configDb['versions'][$versionId])) {
            throw new Exception("Versión $versionId no encontrada");
        }
        $this->configDb['versions'][$versionId]['config'] = $config;
        $this->configDb['versions'][$versionId]['updated_at'] = date('Y-m-d H:i:s');
        $this->guardarConfig();
    }

    // Actualizar una sección en una versión
    public function actualizarSeccion(string $versionId, string $seccion, array $campos): void
    {
        if (!isset($this->configDb['versions'][$versionId])) {
            throw new Exception("Versión $versionId no encontrada");
        }
        $this->configDb['versions'][$versionId]['config'][$seccion] = $campos;
        $this->configDb['versions'][$versionId]['updated_at'] = date('Y-m-d H:i:s');
        $this->guardarConfig();
    }

    // Actualizar un campo en una sección de una versión
    public function actualizarCampo(string $versionId, string $seccion, string $campo, array $config): void
    {
        if (!isset($this->configDb['versions'][$versionId])) {
            throw new Exception("Versión $versionId no encontrada");
        }
        if (!isset($this->configDb['versions'][$versionId]['config'][$seccion])) {
            $this->configDb['versions'][$versionId]['config'][$seccion] = [];
        }
        $this->configDb['versions'][$versionId]['config'][$seccion][$campo] = $config;
        $this->configDb['versions'][$versionId]['updated_at'] = date('Y-m-d H:i:s');
        $this->guardarConfig();
    }

    // Eliminar una versión
    public function eliminarVersion(string $versionId): void
    {
        if (!isset($this->configDb['versions'][$versionId])) {
            throw new Exception("Versión $versionId no encontrada");
        }
        unset($this->configDb['versions'][$versionId]);
        $this->guardarConfig();
    }

    // Eliminar una sección de una versión
    public function eliminarSeccion(string $versionId, string $seccion): void
    {
        if (!isset($this->configDb['versions'][$versionId])) {
            throw new Exception("Versión $versionId no encontrada");
        }
        if (isset($this->configDb['versions'][$versionId]['config'][$seccion])) {
            unset($this->configDb['versions'][$versionId]['config'][$seccion]);
            $this->configDb['versions'][$versionId]['updated_at'] = date('Y-m-d H:i:s');
            $this->guardarConfig();
        }
    }

    // Eliminar un campo de una sección en una versión
    public function eliminarCampo(string $versionId, string $seccion, string $campo): void
    {
        if (!isset($this->configDb['versions'][$versionId])) {
            throw new Exception("Versión $versionId no encontrada");
        }
        if (isset($this->configDb['versions'][$versionId]['config'][$seccion][$campo])) {
            unset($this->configDb['versions'][$versionId]['config'][$seccion][$campo]);
            $this->configDb['versions'][$versionId]['updated_at'] = date('Y-m-d H:i:s');
            $this->guardarConfig();
        }
    }

    // Validar un campo según la configuración de una versión
    public function validar(string $versionId, string $seccion, string $campo, $valor): bool
    {
        if (!isset($this->configDb['versions'][$versionId]['config'][$seccion][$campo])) {
            return true; // Si no está definido, no se valida
        }
        $configCampo = $this->configDb['versions'][$versionId]['config'][$seccion][$campo];
        if ($configCampo['requerido'] && empty($valor)) {
            throw new Exception("El campo $campo en $seccion (versión $versionId) es requerido");
        }
        return true;
    }

    // Obtener valor predeterminado de un campo en una versión
    public function obtenerValorPredeterminado(string $versionId, string $seccion, string $campo)
    {
        return $this->configDb['versions'][$versionId]['config'][$seccion][$campo]['predeterminado'] ?? '';
    }


    public function __construct(IRouter $router = null, string $archivoConfig = '../data/config_db.json')
    {

        $this->archivoConfig = $archivoConfig;
        $this->cargarConfig();
        parent::__construct($router);
    }
}
