<?php

require_once 'BaseModel.php';
require_once INCLUDES_PATH . 'Database.php';
require_once APP_PATH . 'entities/IndicatorType.php';

class IndicatorTypeModel extends BaseModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAllIndicatorTypes() {
        $this->db->query("SELECT * FROM indicator_types ORDER BY orderNo ASC, indicatorTitle ASC");
        $data = $this->db->resultSet();
        $indicatorTypes = [];
        foreach ($data as $row) {
            $indicatorTypes[] = new IndicatorType($row['id'], $row['indicatorTitle']);
        }
        return $indicatorTypes;
    }
}
?>
