<?php

/*
 * Hospital Metropolitano
 */

namespace app\models\lisa2;

use Exception;
use Ocrend\Kernel\Helpers as Helper;
use Ocrend\Kernel\Models\IModels;
use Ocrend\Kernel\Models\Models;
use Ocrend\Kernel\Models\ModelsException;
use Ocrend\Kernel\Router\IRouter;

/**
 * Modelo Pedidos
 */
class Pedidos extends Models implements IModels
{
    private $pedidos = [];
    private $contadorId = 5000;
    private $jsonFilePath;

    // Reglas de validación para los pedidos
    private $validaciones = [
        'id' => ['type' => 'integer', 'required' => true],
        'idEmpresa' => ['type' => 'integer', 'required' => true],
        'idCentro' => ['type' => 'integer', 'required' => true],
        'codigoPedido' => ['max_length' => 20, 'type' => 'string', 'required' => true],
        'fechaPedido' => ['max_length' => 20, 'type' => 'string', 'required' => true],
        'timestampCreacion' => ['max_length' => 20, 'type' => 'integer'],
        'timestampActualizacion' => ['max_length' => 20, 'type' => 'integer'],
        'usrCreacion' => ['max_length' => 20, 'type' => 'string', 'required' => false],
        'usrActualizacion' => ['max_length' => 20, 'type' => 'string', 'required' => false],
        'statusPedido' => ['max_length' => 50, 'type' => 'string', 'required' => true, 'allowed_values' => ['Pendiente', 'En Proceso', 'Completado', 'Cancelado']],
        'data' => ['type' => 'array', 'required' => false],
    ];

    private function cargarDatosIniciales()
    {
        try {
            global $config;
            $filePath = 'data/pedidos.json';
            $this->jsonFilePath = $filePath;

            $directory = dirname($this->jsonFilePath);
            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
            }

            if (file_exists($this->jsonFilePath)) {
                $jsonData = file_get_contents($this->jsonFilePath);
                $this->pedidos = json_decode($jsonData, true) ?: [];
                if (!empty($this->pedidos)) {
                    $ids = array_column($this->pedidos, 'id');
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

            if (isset($rules['required']) && $rules['required'] && (empty($value) && $value !== '0')) {
                $errors[$key] = "Este campo es requerido";
                continue;
            }

            if (empty($value) && !$rules['required']) {
                $validatedParams[$key] = null;
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
                case 'array':
                    if (!is_array($value)) {
                        $errors[$key] = "Debe ser un arreglo";
                        continue 2;
                    }
                    break;
            }

            if (isset($rules['max_length']) && strlen($value) > $rules['max_length']) {
                $errors[$key] = "Longitud máxima permitida es {$rules['max_length']} caracteres";
                continue;
            }

            if (isset($rules['allowed_values']) && !in_array($value, $rules['allowed_values'])) {
                $errors[$key] = "El valor debe ser uno de: " . implode(', ', $rules['allowed_values']);
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
        $jsonData = json_encode($this->pedidos, JSON_PRETTY_PRINT);
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

            $empresasModel = new Empresas();
            $empresaValidacion = $empresasModel->validarExistenciaPorId(1);
            if (!$empresaValidacion['existe']) {
                throw new Exception("No existe una empresa con ID {$validatedParams['idEmpresa']} en el sistema.");
            }

            foreach ($this->pedidos as $pedido) {
                if (
                    isset($pedido['codigoPedido']) &&
                    $pedido['codigoPedido'] === $validatedParams['codigoPedido'] &&
                    $pedido['idEmpresa'] === $validatedParams['idEmpresa']
                ) {
                    throw new Exception("Ya existe un pedido con el código {$validatedParams['codigoPedido']} para la empresa con ID {$validatedParams['empresa_id']}.");
                }
            }


            $nuevoPedido = [
                'id' => $this->contadorId,
                'idEmpresa' => 1,
                'idCentro' => 1,
                'codigoPedido' => date('ymd') . $this->contadorId,
                'fechaPedido' => date('Y-m-d H:i:s'),
                'timestampCreacion' => time(),
                'timestampActualizacion' => null,
                'statusPedido' => 'Pendiente',
                'usrCreacion' => 'mchang',
                'usrActualizacion' => null,
                'data' => $params,
            ] + $validatedParams;

            $this->pedidos[] = $nuevoPedido;
            $this->contadorId++;
            $this->guardarEnJson();

            return array(
                'status' => true,
                'message' => 'Pedido registrado.',
                'data' => $nuevoPedido,
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
            $result = $this->pedidos;
            $params = $http->request->all();

            if (!empty($params)) {
                foreach ($params as $key => $value) {
                    if (array_key_exists($key, $this->validaciones) && $value !== '') {
                        $result = array_filter($result, function ($pedido) use ($key, $value) {
                            if (isset($pedido[$key])) {
                                if (is_string($pedido[$key])) {
                                    return stripos($pedido[$key], $value) !== false;
                                }
                                return $pedido[$key] == $value;
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

                if (array_key_exists($sortBy, $this->validaciones) || $sortBy === 'fecha_creacion') {
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
            $pedidoIndex = array_search($id, array_column($this->pedidos, 'id'));

            if ($pedidoIndex === false) {
                throw new Exception("Pedido con ID $id no encontrado.");
            }

            $validatedParams = $this->setParameters($params);

            if (isset($validatedParams['idEmpresa'])) {
                $empresasModel = new Empresas();
                $empresaValidacion = $empresasModel->validarExistenciaPorId($validatedParams['idEmpresa']);
                if (!$empresaValidacion['existe']) {
                    throw new Exception("No existe una empresa con ID {$validatedParams['idEmpresa']} en el sistema.");
                }
            }

            if (isset($validatedParams['codigoPedido'])) {
                $empresaId = $validatedParams['idEmpresa'] ?? $this->pedidos[$pedidoIndex]['idEmpresa'];
                foreach ($this->pedidos as $index => $pedido) {
                    if (
                        $index !== $pedidoIndex &&
                        $pedido['codigoPedido'] === $validatedParams['codigoPedido'] &&
                        $pedido['idEmpresa'] === $empresaId
                    ) {
                        throw new Exception("Ya existe un pedido con el código {$validatedParams['codigoPedido']} para la empresa con ID $empresaId.");
                    }
                }
            }

            $this->pedidos[$pedidoIndex] = array_merge($this->pedidos[$pedidoIndex], $validatedParams);
            $this->guardarEnJson();

            return array(
                'status' => true,
                'data' => $this->pedidos[$pedidoIndex]
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
            $pedidoIndex = array_search($id, array_column($this->pedidos, 'id'));

            if ($pedidoIndex === false) {
                throw new Exception("Pedido con ID $id no encontrado.");
            }

            array_splice($this->pedidos, $pedidoIndex, 1);
            $this->guardarEnJson();

            return array(
                'status' => true,
                'message' => "Pedido con ID $id eliminado exitosamente"
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
            $pedidoExistente = null;

            foreach ($this->pedidos as $pedido) {
                if (isset($pedido['id']) && $pedido['id'] === $id) {
                    $existe = true;
                    $pedidoExistente = $pedido;
                    break;
                }
            }

            return array(
                'status' => true,
                'existe' => $existe,
                'data' => $existe ? $pedidoExistente : null,
                'message' => $existe ? "El pedido con ID $id existe en el sistema" : "No existe pedido con ID $id en el sistema"
            );
        } catch (Exception $e) {
            return array(
                'status' => false,
                'message' => $e->getMessage(),
                'errorCode' => $e->getCode()
            );
        }
    }

    public function cancelar(int $id, bool $total = true, array $itemsCancelados = []): array
    {
        try {
            global $http;
            $this->cargarDatosIniciales();
            $pedidoIndex = array_search($id, array_column($this->pedidos, 'id'));

            if ($pedidoIndex === false) {
                throw new Exception("Pedido con ID $id no encontrado.");
            }

            $pedido = &$this->pedidos[$pedidoIndex];

            // Verificar si el pedido ya está cancelado o completado
            if ($pedido['statusPedido'] === 'Cancelado') {
                throw new Exception("El pedido con ID $id ya está cancelado.");
            }
            if ($pedido['statusPedido'] === 'Completado') {
                throw new Exception("No se puede cancelar un pedido completado con ID $id.");
            }

            if ($total) {
                // Cancelación total
                $pedido['statusPedido'] = 'Cancelado';
            } else {
                // Cancelación parcial
                if (empty($pedido['detalles'])) {
                    throw new Exception("El pedido con ID $id no tiene detalles para cancelar parcialmente.");
                }

                if (empty($itemsCancelados)) {
                    throw new Exception("Debe especificar los ítems a cancelar para una cancelación parcial.");
                }

                $detallesOriginales = $pedido['detalles'];
                $detallesRestantes = array_diff($detallesOriginales, $itemsCancelados);

                if (count($detallesRestantes) === count($detallesOriginales)) {
                    throw new Exception("Ningún ítem especificado para cancelar coincide con los detalles del pedido.");
                }

                $pedido['detalles'] = array_values($detallesRestantes);
                $pedido['observaciones'] = isset($pedido['observaciones'])
                    ? $pedido['observaciones'] . " | Cancelación parcial de " . implode(', ', $itemsCancelados) . " el " . date('Y-m-d H:i:s')
                    : "Cancelación parcial de " . implode(', ', $itemsCancelados) . " el " . date('Y-m-d H:i:s');

                // Si no quedan detalles, cambiar estado a Cancelado
                if (empty($pedido['detalles'])) {
                    $pedido['estado'] = 'Cancelado';
                }
            }

            $this->guardarEnJson();

            return array(
                'status' => true,
                'data' => $pedido,
                'message' => $total ? "Pedido con ID $id cancelado totalmente." : "Pedido con ID $id cancelado parcialmente."
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
