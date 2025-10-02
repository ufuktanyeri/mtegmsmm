<?php

require_once 'BaseModel.php';
require_once INCLUDES_PATH . 'Database.php';
require_once APP_PATH . 'entities/Field.php';

class FieldModel extends BaseModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAllFields() {
        $this->db->query("SELECT * FROM fields");
        $fieldData = $this->db->resultSet();
        $fields = [];
        foreach ($fieldData as $data) {
            $fields[] = new Field($data['id'], $data['name']);
        }
        return $fields;
    }

    public function getFieldById($id) {
        $this->db->query("SELECT * FROM fields WHERE id = ?", [$id]);
        $data = $this->db->single();
        if ($data) {
            return new Field($data['id'], $data['name']);
        }
        return null;
    }

    public function createField($name) {
        $this->db->query("INSERT INTO fields (name) VALUES (?)", [$name]);
    }

    public function updateField($id, $name) {
        $this->db->query("UPDATE fields SET name = ? WHERE id = ?", [$name, $id]);
    }

    public function deleteField($id) {
        $this->db->query("DELETE FROM fields WHERE id = ?", [$id]);
    }

    public function getFieldsByCoveId($coveId) {
        $this->db->query("SELECT f.id, f.name FROM fields f INNER JOIN cove_fields cf ON f.id = cf.fieldId WHERE cf.coveId = ?", [$coveId]);
        $data = $this->db->resultSet();
        $fields = [];
        foreach ($data as $row) {
            $fields[] = new Field($row['id'], $row['name']);
        }
        return $fields;
    }
}
?>
