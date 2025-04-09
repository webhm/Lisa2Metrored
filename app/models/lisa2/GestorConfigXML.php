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
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'config' => $this->obtenerConfigPredeterminada()
        ];
    }

    private function obtenerConfigPredeterminada(): array
    {
        return [
            'Cabecalho' => [
                'mensagemID' => [
                    'requerido' => true,
                    'predeterminado' => '',
                    'type' => 'string',
                    'max_length' => 50,
                    'format' => null
                ],
                'dataHora' => [
                    'requerido' => true,
                    'predeterminado' => date('Y-m-d H:i:s'),
                    'type' => 'string',
                    'max_length' => 19,
                    'format' => 'datetime' // Formato: Y-m-d H:i:s
                ],
                'servico' => [
                    'requerido' => true,
                    'predeterminado' => '',
                    'type' => 'string',
                    'max_length' => 100,
                    'format' => null
                ],
                'versaoXML' => [
                    'requerido' => false,
                    'predeterminado' => '1.0',
                    'type' => 'string',
                    'max_length' => 10,
                    'format' => null
                ]
            ],
            'PedidoExameLab' => [
                'idIntegracao' => [
                    'requerido' => true,
                    'predeterminado' => '',
                    'type' => 'integer',
                    'max_length' => null,
                    'format' => null
                ],
                'operacao' => [
                    'requerido' => true,
                    'predeterminado' => 'I',
                    'type' => 'string',
                    'max_length' => 1,
                    'format' => null
                ],
                'codigoPedido' => [
                    'requerido' => false,
                    'predeterminado' => '',
                    'type' => 'string',
                    'max_length' => 20,
                    'format' => null
                ]
            ],
            'Paciente' => [
                'codigoPaciente' => [
                    'requerido' => true,
                    'predeterminado' => '',
                    'type' => 'string',
                    'max_length' => 20,
                    'format' => null
                ],
                'nome' => [
                    'requerido' => true,
                    'predeterminado' => '',
                    'type' => 'string',
                    'max_length' => 100,
                    'format' => null
                ],
                'dataNacimiento' => [
                    'requerido' => false,
                    'predeterminado' => '',
                    'type' => 'string',
                    'max_length' => 10,
                    'format' => 'date' // Formato: Y-m-d
                ],
                'email' => [
                    'requerido' => false,
                    'predeterminado' => '',
                    'type' => 'string',
                    'max_length' => 255,
                    'format' => 'email'
                ]
            ]
        ];
    }

    // Obtener todas las versiones
    public function obtenerTodasVersiones(): array
    {
        return $this->configDb['versions'];
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
        $this->validarConfig($config);
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
        $this->validarCampos($campos);
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
        $this->validarCampo($config);
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
            return true;
        }
        $configCampo = $this->configDb['versions'][$versionId]['config'][$seccion][$campo];

        // Validar requerido
        if ($configCampo['requerido'] && empty($valor)) {
            throw new Exception("El campo $campo en $seccion (versión $versionId) es requerido");
        }

        if (!empty($valor)) {
            // Validar tipo
            switch ($configCampo['type']) {
                case 'string':
                    if (!is_string($valor)) {
                        throw new Exception("El campo $campo en $seccion debe ser una cadena");
                    }
                    break;
                case 'integer':
                    if (!is_numeric($valor) || (int)$valor != $valor) {
                        throw new Exception("El campo $campo en $seccion debe ser un entero");
                    }
                    break;
            }

            // Validar longitud máxima
            if ($configCampo['max_length'] !== null && strlen((string)$valor) > $configCampo['max_length']) {
                throw new Exception("El campo $campo en $seccion excede la longitud máxima de {$configCampo['max_length']}");
            }

            // Validar formato
            if ($configCampo['format']) {
                switch ($configCampo['format']) {
                    case 'email':
                        if (!filter_var($valor, FILTER_VALIDATE_EMAIL)) {
                            throw new Exception("El campo $campo en $seccion debe ser un email válido");
                        }
                        break;
                    case 'date':
                        if (!DateTime::createFromFormat('Y-m-d', $valor)) {
                            throw new Exception("El campo $campo en $seccion debe tener formato de fecha Y-m-d");
                        }
                        break;
                    case 'datetime':
                        if (!DateTime::createFromFormat('Y-m-d H:i:s', $valor)) {
                            throw new Exception("El campo $campo en $seccion debe tener formato de fecha y hora Y-m-d H:i:s");
                        }
                        break;
                }
            }
        }

        return true;
    }

    // Obtener valor predeterminado de un campo en una versión
    public function obtenerValorPredeterminado(string $versionId, string $seccion, string $campo)
    {
        return $this->configDb['versions'][$versionId]['config'][$seccion][$campo]['predeterminado'] ?? '';
    }

    // Obtener la última versión creada
    public function obtenerUltimaVersion(): ?array
    {
        if (empty($this->configDb['versions'])) {
            return null;
        }

        $ultimaVersion = null;
        $ultimaFecha = null;

        foreach ($this->configDb['versions'] as $versionId => $version) {
            $fechaCreacion = DateTime::createFromFormat('Y-m-d H:i:s', $version['created_at']);
            if ($ultimaFecha === null || $fechaCreacion > $ultimaFecha) {
                $ultimaFecha = $fechaCreacion;
                $ultimaVersion = ['version_id' => $versionId] + $version;
            }
        }

        return $ultimaVersion;
    }

    // Validar una configuración completa
    private function validarConfig(array $config): void
    {
        foreach ($config as $seccion => $campos) {
            $this->validarCampos($campos);
        }
    }

    // Validar un conjunto de campos
    private function validarCampos(array $campos): void
    {
        foreach ($campos as $campo => $config) {
            $this->validarCampo($config);
        }
    }

    // Validar un campo individual
    private function validarCampo(array $config): void
    {
        if (!isset($config['requerido']) || !isset($config['predeterminado']) || !isset($config['type'])) {
            throw new Exception("El campo debe incluir 'requerido', 'predeterminado' y 'type'");
        }
        if (!in_array($config['type'], ['string', 'integer'])) {
            throw new Exception("El tipo debe ser 'string' o 'integer'");
        }
        if (isset($config['max_length']) && !is_int($config['max_length']) && $config['max_length'] !== null) {
            throw new Exception("'max_length' debe ser un entero o null");
        }
        if (isset($config['format']) && !in_array($config['format'], [null, 'email', 'date', 'datetime'])) {
            throw new Exception("El formato debe ser null, 'email', 'date' o 'datetime'");
        }
        // Validar que predeterminado cumpla con las reglas
        $this->validarValor($config, $config['predeterminado']);
    }

    // Validar un valor según las reglas del campo
    private function validarValor(array $config, $valor): void
    {
        if (!empty($valor)) {
            switch ($config['type']) {
                case 'string':
                    if (!is_string($valor)) throw new Exception("El valor predeterminado debe ser una cadena");
                    break;
                case 'integer':
                    if (!is_numeric($valor) || (int)$valor != $valor) throw new Exception("El valor predeterminado debe ser un entero");
                    break;
            }
            if ($config['max_length'] !== null && strlen((string)$valor) > $config['max_length']) {
                throw new Exception("El valor predeterminado excede la longitud máxima de {$config['max_length']}");
            }
            if ($config['format']) {
                switch ($config['format']) {
                    case 'email':
                        if (!filter_var($valor, FILTER_VALIDATE_EMAIL)) throw new Exception("El valor predeterminado debe ser un email válido");
                        break;
                    case 'date':
                        if (!DateTime::createFromFormat('Y-m-d', $valor)) throw new Exception("El valor predeterminado debe tener formato Y-m-d");
                        break;
                    case 'datetime':
                        if (!DateTime::createFromFormat('Y-m-d H:i:s', $valor)) throw new Exception("El valor predeterminado debe tener formato Y-m-d H:i:s");
                        break;
                }
            }
        }
    }

    public function __construct(IRouter $router = null, string $archivoConfig = '../data/config_db.json')
    {

        $this->archivoConfig = $archivoConfig;
        $this->cargarConfig();
        parent::__construct($router);
    }
}
