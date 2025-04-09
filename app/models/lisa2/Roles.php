<?php

/*
 * Hospital Metropolitano
 */

namespace app\models\lisa2;

use Doctrine\DBAL\DriverManager;
use Exception;
use app\models\lisa2 as Model;
use Ocrend\Kernel\Helpers as Helper;
use Ocrend\Kernel\Models\IModels;
use Ocrend\Kernel\Models\Models;
use Ocrend\Kernel\Models\ModelsException;
use Ocrend\Kernel\Router\IRouter;

/**
 * Modelo Roles
 */
class Roles extends Models implements IModels
{
    private $roles = [];
    private $contadorId = 1;
    private $jsonFilePath;

    // Roles permitidos
    private $rolesPermitidos = [
        'SuperAdministrador',
        'Administrador',
        'Gestionador',
        'Operador',
        'Flebotomista'
    ];

    // Reglas de validación
    private $validaciones = [
        'nombre' => ['max_length' => 50, 'type' => 'string', 'required' => true],
        'descripcion' => ['max_length' => 255, 'type' => 'string'],
        'perfiles' => ['type' => 'array'],
        'idEmpresa' => ['type' => 'integer', 'required' => true]
    ];

    private function cargarDatosIniciales()
    {
        try {
            global $config;
            $filePath = 'data/roles.json';
            $this->jsonFilePath = $filePath;

            $directory = dirname($this->jsonFilePath);
            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
            }

            if (file_exists($this->jsonFilePath)) {
                $jsonData = file_get_contents($this->jsonFilePath);
                $this->roles = json_decode($jsonData, true) ?: [];
                if (!empty($this->roles)) {
                    $ids = array_column($this->roles, 'id');
                    $this->contadorId = max($ids) + 1;
                }
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 0);
        }
    }

    private function setParameters($params)
    {
        $validatedParams = [];
        $errors = [];

        foreach ($params as $key => $value) {
            if (!isset($this->validaciones[$key])) {
                continue;
            }

            $rules = $this->validaciones[$key];

            // Validar campo requerido
            if (isset($rules['required']) && $rules['required'] && (empty($value) && $value !== '0')) {
                $errors[$key] = "Este campo es requerido";
                continue;
            }

            if (empty($value) && !$rules['required']) {
                continue;
            }

            switch ($rules['type']) {
                case 'string':
                    if (!is_string($value)) {
                        $errors[$key] = "Debe ser una cadena de texto";
                        continue 2;
                    }
                    break;
                case 'array':
                    if (!is_array($value)) {
                        $errors[$key] = "Debe ser un arreglo";
                        continue 2;
                    }
                    break;
                case 'integer':
                    if (!is_numeric($value) || (int)$value != $value) {
                        $errors[$key] = "Debe ser un número entero";
                        continue 2;
                    }
                    $value = (int)$value;
                    break;
            }

            if (isset($rules['max_length']) && strlen($value) > $rules['max_length']) {
                $errors[$key] = "Longitud máxima permitida es {$rules['max_length']} caracteres";
                continue;
            }

            // Validar que el nombre esté en los roles permitidos
            if ($key === 'nombre' && !in_array($value, $this->rolesPermitidos)) {
                $errors[$key] = "El rol debe ser uno de: " . implode(', ', $this->rolesPermitidos);
                continue;
            }

            $validatedParams[$key] = $value;
        }

        if (!empty($errors)) {
            throw new Exception(json_encode($errors));
        }

        return $validatedParams;
    }

    private function guardarEnJson()
    {
        $jsonData = json_encode($this->roles, JSON_PRETTY_PRINT);
        if (file_put_contents($this->jsonFilePath, $jsonData) === false) {
            throw new Exception("Error al guardar los datos en el archivo JSON");
        }
    }

    public function crear(): array
    {
        try {
            global $http;

            $params = $http->request->all();

            if ($params == null) {
                throw new Exception("No existe información.");
            }


            $validatedParams = $this->setParameters($params);
            $this->cargarDatosIniciales();

            // Validar existencia de la empresa
            $empresasModel = new Model\Empresas;
            $empresaValidacion = $empresasModel->validarExistenciaPorId($validatedParams['idEmpresa']);

            if (!$empresaValidacion['existe']) {
                throw new Exception("No existe una empresa con ID {$validatedParams['idEmpresa']} en el sistema.");
            }

            $nuevoRol = [
                'id' => $this->contadorId
            ] + $validatedParams;

            $this->roles[] = $nuevoRol;
            $this->contadorId++;
            $this->guardarEnJson();

            return array(
                'status' => true,
                'data' => $nuevoRol,
            );
        } catch (Exception $e) {
            return array('status' => false, 'data' => $http->request->all(), 'message' => $e->getMessage(), 'errorCode' => $e->getCode());
        }
    }

    public function listar(): array
    {
        try {
            global $http;
            $this->cargarDatosIniciales();
            $result = $this->roles;
            $params = $http->request->all();

            if (!empty($params)) {
                foreach ($params as $key => $value) {
                    if (array_key_exists($key, $this->validaciones) && $value !== '') {
                        $result = array_filter($result, function ($rol) use ($key, $value) {
                            if (isset($rol[$key])) {
                                if (is_string($rol[$key])) {
                                    return stripos($rol[$key], $value) !== false;
                                }
                                return $rol[$key] == $value;
                            }
                            return false;
                        });
                    }
                }
                $result = array_values($result);
            }

            if (isset($params['sort_by']) && isset($params['sort_order'])) {
                $sortBy = $params['sort_by'];
                $sortOrder = strtoupper($params['sort_order']) === 'DESC' ? SORT_DESC : SORT_ASC;

                if (array_key_exists($sortBy, $this->validaciones)) {
                    usort($result, function ($a, $b) use ($sortBy, $sortOrder) {
                        if (!isset($a[$sortBy]) || !isset($b[$sortBy])) {
                            return 0;
                        }
                        $comparison = is_string($a[$sortBy]) ? strcmp($a[$sortBy], $b[$sortBy]) : $a[$sortBy] - $b[$sortBy];
                        return $sortOrder === SORT_ASC ? $comparison : -$comparison;
                    });
                }
            }

            return array(
                'status' => true,
                'data' => $result,
                'total' => count($result),
                'filters_applied' => !empty($params) ? $params : null
            );
        } catch (Exception $e) {
            return array(
                'status' => false,
                'message' => $e->getMessage(),
                'errorCode' => $e->getCode()
            );
        }
    }

    public function editar(int $id): array
    {
        try {
            global $http;
            $params = $http->request->all();

            if ($params == null) {
                throw new Exception("No existe información para actualizar.");
            }

            $this->cargarDatosIniciales();
            $rolIndex = array_search($id, array_column($this->roles, 'id'));

            if ($rolIndex === false) {
                throw new Exception("Rol con ID $id no encontrado.");
            }

            $validatedParams = $this->setParameters($params);

            // Validar empresa_id si se proporciona
            if (isset($validatedParams['empresa_id'])) {
                $empresasModel = new Empresas();
                $empresaValidacion = $empresasModel->validarExistenciaPorId($validatedParams['empresa_id']);
                if (!$empresaValidacion['existe']) {
                    throw new Exception("No existe una empresa con ID {$validatedParams['empresa_id']} en el sistema.");
                }
            }

            $this->roles[$rolIndex] = array_merge($this->roles[$rolIndex], $validatedParams);
            $this->guardarEnJson();

            return array(
                'status' => true,
                'data' => $this->roles[$rolIndex]
            );
        } catch (Exception $e) {
            return array(
                'status' => false,
                'data' => $http->request->all(),
                'message' => $e->getMessage(),
                'errorCode' => $e->getCode()
            );
        }
    }

    public function eliminar(int $id): array
    {
        try {
            $this->cargarDatosIniciales();
            $rolIndex = array_search($id, array_column($this->roles, 'id'));

            if ($rolIndex === false) {
                throw new Exception("Rol con ID $id no encontrado.");
            }

            array_splice($this->roles, $rolIndex, 1);
            $this->guardarEnJson();

            return array(
                'status' => true,
                'message' => "Rol con ID $id eliminado exitosamente"
            );
        } catch (Exception $e) {
            return array(
                'status' => false,
                'message' => $e->getMessage(),
                'errorCode' => $e->getCode()
            );
        }
    }

    public function validarExistenciaPorId(int $id): array
    {
        try {
            $this->cargarDatosIniciales();
            $existe = false;
            $rolExistente = null;

            foreach ($this->roles as $rol) {
                if (isset($rol['id']) && $rol['id'] === $id) {
                    $existe = true;
                    $rolExistente = $rol;
                    break;
                }
            }

            return array(
                'status' => true,
                'existe' => $existe,
                'data' => $existe ? $rolExistente : null,
                'message' => $existe ? "El rol con ID $id existe en el sistema" : "No existe rol con ID $id en el sistema"
            );
        } catch (Exception $e) {
            return array(
                'status' => false,
                'message' => $e->getMessage(),
                'errorCode' => $e->getCode()
            );
        }
    }

    public function __construct(IRouter $router = null)
    {
        parent::__construct($router);
    }
}
