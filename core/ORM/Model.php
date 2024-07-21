<?php

namespace SMU\Core\ORM;

use SMU\Core\Constants\DBTypes;
use SMU\Core\DataBaseHandler;

abstract class Model
{
    protected $db;
    protected $conn;
    protected $table;
    private   $fillable;

    public function __construct()
    {
    	$this->db = DataBaseHandler::getInstance();
        $this->initialize();
    }

    /**
     * - Must return the `tableName` and `fillable` members of the 
     * table in an array for the configuration of the model.
     * 
     * Example:
     * 
     * return [
     *    `'tableName'` => 'users',
     *    `'fillable'`  => [
     *        'name',
     *        'email',
     *        'password',
     *    ]
     * ] 
     */
    protected abstract function getTableConfig(): array;

    /**
     * Is mandatory to have a id in a model.
     */
    public abstract function getId(): mixed;

    private function initialize()
    {
        $config         = $this->getTableConfig();
        $this->table    = $config['tableName'];
        $this->fillable = $config['fillable'];
    }

    protected function query($sql, $params = []): array
    {
        return $this->db->executeStoreProcedure($sql, $params);
    }

    public function find($id): ?self
    {
        $sql    = "SELECT * FROM {$this->table} WHERE id = ?";
        $result = $this->query($sql, [[DBTypes::INT => $id]]);
        return !empty($result) ? $this->mapResult($result[0]) : null;
    }

    public function create($data): self
    {
        $data = $this->filterFillable($data);
        
        $columns        = implode(", ", array_keys($data));
        $placeHolders   = implode(", ", array_fill(0, count($data), "?"));
        $types          = $this->getParamTypes($data);
        
        // Depuración
        error_log("Consulta SQL: INSERT INTO {$this->table} ({$columns}) VALUES ({$placeHolders})");
        error_log("Datos para insertar: " . print_r(array_values($data), true));
    
        $sql    = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeHolders})";
        $stmt   = $this->db->getConnection()->prepare($sql);
        
        if (!$stmt) {
            error_log("Error en prepare: " . $this->db->getConnection()->error);
        }
    
        $stmt->bind_param($types, ...array_values($data));
        $stmt->execute();
    
        if ($stmt->error) {
            error_log("Error en execute: " . $stmt->error);
        }
    
        $this->id = $stmt->insert_id;
    
        return $this;
    }

    public function update($id, $data)
    {
        $data    = $this->filterFillable($data);
        $columns = implode(" = ?, ", array_keys($data)) . " = ?";
        $types   = $this->getParamTypes($data) . "i"; 
        $sql     = "UPDATE {$this->table} SET {$columns} WHERE id = ?";
        $stmt    = $this->db->getConnection()->prepare($sql);

        $stmt->bind_param($types, ...array_merge(array_values($data), [$id]));
        $stmt->execute();

        return $stmt->affected_rows;
    }

    public function delete($id)
    {
        $sql  = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->getConnection()->prepare($sql);

        $stmt->bind_param('i', $id);
        $stmt->execute();

        return $stmt->affected_rows;
    }

    public function all(): array
    {
        $sql     = "SELECT * FROM {$this->table}";
        $result  = $this->db->getConnection()->query($sql);

        $objects = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $objects[] = $this->mapResult($row);
            }
        }
        return $objects;
    }

    protected function getParamTypes($data)
    {
        $types = '';
    
        foreach ($data as $value) {
            $types .= match (true) {
                is_int($value)    => DBTypes::INT,
                is_float($value)  => DBTypes::FLOAT,
                is_string($value) => DBTypes::VARCHAR,
                default           => DBTypes::BLOB
            };
        }
    
        // Depuración
        error_log("Tipos de parámetros: " . $types);
    
        return $types;
    }

    protected function mapResult($result): ?self
    {
        if (empty($result)) {
            return null;
        }

        $model = clone $this;
        foreach ($result as $key => $value) {
            $method = "set" . ucfirst($key);
            if (method_exists($model, $method)) {
                $model->$method($value);
            } else {
                $model->$key = $value;
            }
        }
        return $model;
    }

    protected function filterFillable($data)
    {
        $filteredData = array_filter($data, function ($key) {
            return in_array($key, $this->fillable);
        }, ARRAY_FILTER_USE_KEY);
    
        // Depuración
        error_log("Datos filtrados: " . print_r($filteredData, true));
    
        return $filteredData;
    }

    public function save(): self|int
    {
        $data = get_object_vars($this);
        $data = $this->filterFillable($data);
        
        // Si el id está definido, actualiza el registro existente
        if (!empty($this->id)) {
            return $this->update($this->id, $data);
        }
        
        // Si el id no está definido, crea un nuevo registro
        $newModel = $this->create($data);
        $this->id = $newModel->getId();
        return $this;
    }
}
