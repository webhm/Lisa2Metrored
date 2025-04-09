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
 * Modelo Empresas
 */
class Empresas extends Models implements IModels
{

    private $empresas = [];
    private $contadorId = 1;
    private $jsonFilePath;

    // Reglas de validación
    private $validaciones = [
        'id' => ['type' => 'integer', 'min' => 1],
        'nombre' => ['max_length' => 255, 'type' => 'string'],
        'direccion' => ['max_length' => 255, 'type' => 'string'],
        'telefono' => ['max_length' => 50, 'type' => 'string'],
        'email' => ['max_length' => 255, 'type' => 'string', 'format' => 'email'],
        'sector' => ['max_length' => 100, 'type' => 'string'],
        'empleados' => ['type' => 'integer', 'min' => 0]
    ];

    private function cargarDatosIniciales()
    {

        try {

            global $config;

            $filePath = 'data/empresas.json';

            $this->jsonFilePath = $filePath;
            // Crear directorio si no existe
            $directory = dirname($this->jsonFilePath);
            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
            }

            if (file_exists($this->jsonFilePath)) {
                $jsonData = file_get_contents($this->jsonFilePath);
                $this->empresas = json_decode($jsonData, true) ?: [];

                if (!empty($this->empresas)) {
                    $ids = array_column($this->empresas, 'id');
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

            switch ($rules['type']) {
                case 'string':
                    if (!is_string($value)) {
                        $errors[$key] = "Debe ser una cadena de texto";
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

            if (isset($rules['format']) && $rules['format'] === 'email') {
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$key] = "Formato de email inválido";
                    continue;
                }
            }

            if (isset($rules['min']) && $value < $rules['min']) {
                $errors[$key] = "El valor mínimo permitido es {$rules['min']}";
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
        $jsonData = json_encode($this->empresas, JSON_PRETTY_PRINT);
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

            $this->setParameters($params);

            $this->cargarDatosIniciales();

            $nuevaEmpresa = [
                'id' => $this->contadorId
            ] + $params;

            $this->empresas[] = $nuevaEmpresa;
            $this->contadorId++;
            $this->guardarEnJson();

            # Devolver Información
            return array(
                'status' => true,
                'data' => $nuevaEmpresa,
            );
        } catch (Exception $e) {

            return array('status' => false, 'data' => $http->request->all(), 'message' => $e->getMessage(), 'errorCode' => $e->getCode());
        }
    }

    // Método para listar todas las empresas con filtros y ordenamiento
    public function listar(): array
    {
        try {
            global $http;

            $this->cargarDatosIniciales();

            $result = $this->empresas;

            // Obtener parámetros de la URL
            $params = $http->query->all();

            // Aplicar filtros
            if (!empty($params)) {
                foreach ($params as $key => $value) {
                    // Solo procesar campos que existen en las validaciones
                    if (array_key_exists($key, $this->validaciones) && $value !== '') {
                        $result = array_filter($result, function ($empresa) use ($key, $value) {
                            if (isset($empresa[$key])) {
                                // Para strings, búsqueda parcial insensible a mayúsculas
                                if (is_string($empresa[$key])) {
                                    return stripos($empresa[$key], $value) !== false;
                                }
                                // Para números, comparación exacta
                                return $empresa[$key] == $value;
                            }
                            return false;
                        });
                    }
                }
                // Reindexar el array después de filtrar
                $result = array_values($result);
            }

            // Ordenamiento
            if (isset($params['sort_by']) && isset($params['sort_order'])) {
                $sortBy = $params['sort_by'];
                $sortOrder = strtoupper($params['sort_order']) === 'DESC' ? SORT_DESC : SORT_ASC;

                // Verificar que el campo de ordenamiento sea válido
                if (array_key_exists($sortBy, $this->validaciones)) {
                    usort($result, function ($a, $b) use ($sortBy, $sortOrder) {
                        if (!isset($a[$sortBy]) || !isset($b[$sortBy])) {
                            return 0;
                        }

                        if (is_string($a[$sortBy])) {
                            $comparison = strcmp($a[$sortBy], $b[$sortBy]);
                        } else {
                            $comparison = $a[$sortBy] - $b[$sortBy];
                        }

                        return $sortOrder === SORT_ASC ? $comparison : -$comparison;
                    });
                }
            }

            return array(
                'status' => true,
                'data' => $result,
                'total' => count($result),
                // 'filters_applied' => !empty($params) ? $params : null
            );
        } catch (Exception $e) {
            return array(
                'status' => false,
                'message' => $e->getMessage(),
                'errorCode' => $e->getCode()
            );
        }
    }

    // Método para validar si una empresa existe por ID
    public function validarExistenciaPorId(int $id): array
    {
        try {
            $this->cargarDatosIniciales();

            $existe = false;
            $empresaExistente = null;

            // Buscar empresa por ID
            foreach ($this->empresas as $empresa) {
                if (isset($empresa['id']) && $empresa['id'] === $id) {
                    $existe = true;
                    $empresaExistente = $empresa;
                    break;
                }
            }

            return array(
                'status' => true,
                'existe' => $existe,
                'data' => $existe ? $empresaExistente : null,
                'message' => $existe ? "La empresa con ID $id existe en el sistema" : "No existe empresa con ID $id en el sistema"
            );
        } catch (Exception $e) {
            return array(
                'status' => false,
                'message' => $e->getMessage(),
                'errorCode' => $e->getCode()
            );
        }
    }

    // Método para editar una empresa
    public function editar(int $id): array
    {
        try {
            global $http;
            $params = $http->request->all();

            if ($params == null) {
                throw new Exception("No existe información para actualizar.");
            }

            $this->cargarDatosIniciales();

            $empresaIndex = array_search($id, array_column($this->empresas, 'id'));

            if ($empresaIndex === false) {
                throw new Exception("Empresa con ID $id no encontrada.");
            }

            $validatedParams = $this->setParameters($params);

            // Actualizar solo los campos proporcionados
            $this->empresas[$empresaIndex] = array_merge(
                $this->empresas[$empresaIndex],
                $validatedParams
            );

            $this->guardarEnJson();

            return array(
                'status' => true,
                'data' => $this->empresas[$empresaIndex]
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

    // Método para eliminar una empresa
    public function eliminar(int $id): array
    {
        try {
            $this->cargarDatosIniciales();

            $empresaIndex = array_search($id, array_column($this->empresas, 'id'));

            if ($empresaIndex === false) {
                throw new Exception("Empresa con ID $id no encontrada.");
            }

            // Eliminar la empresa del array
            array_splice($this->empresas, $empresaIndex, 1);
            $this->guardarEnJson();

            return array(
                'status' => true,
                'message' => "Empresa con ID $id eliminada exitosamente"
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
