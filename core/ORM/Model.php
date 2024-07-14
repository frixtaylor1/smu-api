<?php

namespace SMU\Core\ORM;

use SMU\Core\Constants\DBTypes;
use SMU\Core\DataBaseHandler;

abstract class Model
{
    protected $db;
    protected $table;
    protected $conn;
    protected $fillable;

    public function __construct()
    {
    	$this->db = DataBaseHandler::getInstance();
    }

    protected function query($sql, $params = []): array
    {
        return $this->db->executeStoreProcedure($sql, $params);
    }

    public function find($id): ?self
    {
        $sql    = "SELECT * FROM {$this->table} WHERE id = ?";
        $result = $this->query($sql, [[DBTypes::INT => $id]]);

        return $result ? $this->mapResult($result[0]) : null;
    }

    public function create($data)
    {
        $data         = $this->filterFillable($data);
        $columns      = implode(", ", array_keys($data));
        $placeHolders = implode(", ", array_fill(0, count($data), "?")); 
        $types        = $this->getParamTypes($data);

        $sql          = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeHolders})";
        $stmt         = $this->db->getConnection()->prepare($sql);
        $stmt->bind_param($types, ...array_values($data));
        $stmt->execute();

        return $stmt->insert_id;
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
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->getConnection()->prepare($sql);

        $stmt->bind_param('i', $id);
        $stmt->execute();

        return $stmt->affected_rows;
    }

    public function all(): array
    {
        $sql     = "SELECT * FROM {$this->table}";
        $result  = $this->db->getConnection()->query($sql);

        return array_map([$this, 'mapResult'], $result->fetch_all(MYSQLI_ASSOC));
    }

    protected function getParamTypes($data)
    {
        $types = '';

        foreach ($data as $value) {
            if (is_int($value)) {
                $types .= DBTypes::INT;
            } else if (is_float($value)) {
                $types .= DBTypes::FLOAT;
            } else if (is_string($value)) {
                $types .= DBTypes::VARCHAR;
            } else {
                $types .= DBTypes::BLOB;
            }
        }
        return $types;
    }

    protected function mapResult($result): self
    {
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
        return array_filter($data, function ($key) {
            return in_array($key, $this->fillable);
        }, ARRAY_FILTER_USE_KEY);
    }
}
