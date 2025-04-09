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
 * Modelo Usuarios
 */
class Usuarios extends Models implements IModels
{
    private $usuarios = [];
    private $contadorId = 0;
    private $jsonFilePath;

    // Reglas de validación
    private $validaciones = [
        'nombre' => ['max_length' => 100, 'type' => 'string', 'required' => true],
        'email' => ['max_length' => 255, 'type' => 'string', 'format' => 'email', 'required' => true],
        'password' => ['max_length' => 255, 'type' => 'string', 'required' => true],
        'rol_id' => ['type' => 'integer', 'required' => true],
        'empresa_id' => ['type' => 'integer', 'required' => true]
    ];

    private function cargarDatosIniciales()
    {
        try {
            global $config;
            $filePath = 'data/usuarios.json';
            $this->jsonFilePath = $filePath;

            $directory = dirname($this->jsonFilePath);
            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
            }

            if (file_exists($this->jsonFilePath)) {
                $jsonData = file_get_contents($this->jsonFilePath);
                $this->usuarios = json_decode($jsonData, true) ?: [];
                if (!empty($this->usuarios)) {
                    $ids = array_column($this->usuarios, 'id');
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

            $validatedParams[$key] = $value;
        }

        if (!empty($errors)) {
            throw new Exception(json_encode($errors));
        }

        return $validatedParams;
    }

    private function guardarEnJson()
    {
        $jsonData = json_encode($this->usuarios, JSON_PRETTY_PRINT);
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
            $empresasModel = new Empresas();
            $empresaValidacion = $empresasModel->validarExistenciaPorId($validatedParams['empresa_id']);
            if (!$empresaValidacion['existe']) {
                throw new Exception("No existe una empresa con ID {$validatedParams['empresa_id']} en el sistema.");
            }

            // Validar existencia del rol y su relación con la empresa
            $rolesModel = new Roles();
            $rolValidacion = $rolesModel->validarExistenciaPorId($validatedParams['rol_id']);
            if (!$rolValidacion['existe']) {
                throw new Exception("No existe un rol con ID {$validatedParams['rol_id']} en el sistema.");
            }

            // Verificar que el rol pertenezca a la misma empresa
            if ($rolValidacion['data']['empresa_id'] !== $validatedParams['empresa_id']) {
                throw new Exception("El rol con ID {$validatedParams['rol_id']} no pertenece a la empresa con ID {$validatedParams['empresa_id']}.");
            }

            // Verificar que el email no esté ya registrado
            foreach ($this->usuarios as $usuario) {
                if (isset($usuario['email']) && strtolower($usuario['email']) === strtolower($validatedParams['email'])) {
                    throw new Exception("Ya existe un usuario con el email {$validatedParams['email']}.");
                }
            }

            $nuevoUsuario = [
                'id' => $this->contadorId
            ] + $validatedParams;

            $this->usuarios[] = $nuevoUsuario;
            $this->contadorId++;
            $this->guardarEnJson();

            return array(
                'status' => true,
                'data' => $nuevoUsuario,
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
            $result = $this->usuarios;
            $params = $http->request->all();

            if (!empty($params)) {
                foreach ($params as $key => $value) {
                    if (array_key_exists($key, $this->validaciones) && $value !== '') {
                        $result = array_filter($result, function ($usuario) use ($key, $value) {
                            if (isset($usuario[$key])) {
                                if (is_string($usuario[$key])) {
                                    return stripos($usuario[$key], $value) !== false;
                                }
                                return $usuario[$key] == $value;
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
            $usuarioIndex = array_search($id, array_column($this->usuarios, 'id'));

            if ($usuarioIndex === false) {
                throw new Exception("Usuario con ID $id no encontrado.");
            }

            $validatedParams = $this->setParameters($params);

            // Validar empresa_id y rol_id si se proporcionan
            if (isset($validatedParams['empresa_id']) || isset($validatedParams['rol_id'])) {
                $empresaId = $validatedParams['empresa_id'] ?? $this->usuarios[$usuarioIndex]['empresa_id'];
                $rolId = $validatedParams['rol_id'] ?? $this->usuarios[$usuarioIndex]['rol_id'];

                $empresasModel = new Empresas();
                $rolesModel = new Roles();

                $empresaValidacion = $empresasModel->validarExistenciaPorId($empresaId);
                if (!$empresaValidacion['existe']) {
                    throw new Exception("No existe una empresa con ID $empresaId en el sistema.");
                }

                $rolValidacion = $rolesModel->validarExistenciaPorId($rolId);
                if (!$rolValidacion['existe']) {
                    throw new Exception("No existe un rol con ID $rolId en el sistema.");
                }

                if ($rolValidacion['data']['empresa_id'] !== $empresaId) {
                    throw new Exception("El rol con ID $rolId no pertenece a la empresa con ID $empresaId.");
                }
            }

            // Validar email único si se proporciona
            if (isset($validatedParams['email'])) {
                foreach ($this->usuarios as $index => $usuario) {
                    if (
                        $index !== $usuarioIndex &&
                        isset($usuario['email']) &&
                        strtolower($usuario['email']) === strtolower($validatedParams['email'])
                    ) {
                        throw new Exception("Ya existe otro usuario con el email {$validatedParams['email']}.");
                    }
                }
            }

            $this->usuarios[$usuarioIndex] = array_merge($this->usuarios[$usuarioIndex], $validatedParams);
            $this->guardarEnJson();

            return array(
                'status' => true,
                'data' => $this->usuarios[$usuarioIndex]
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
            $usuarioIndex = array_search($id, array_column($this->usuarios, 'id'));

            if ($usuarioIndex === false) {
                throw new Exception("Usuario con ID $id no encontrado.");
            }

            array_splice($this->usuarios, $usuarioIndex, 1);
            $this->guardarEnJson();

            return array(
                'status' => true,
                'message' => "Usuario con ID $id eliminado exitosamente"
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
            $usuarioExistente = null;

            foreach ($this->usuarios as $usuario) {
                if (isset($usuario['id']) && $usuario['id'] === $id) {
                    $existe = true;
                    $usuarioExistente = $usuario;
                    break;
                }
            }

            return array(
                'status' => true,
                'existe' => $existe,
                'data' => $existe ? $usuarioExistente : null,
                'message' => $existe ? "El usuario con ID $id existe en el sistema" : "No existe usuario con ID $id en el sistema"
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
