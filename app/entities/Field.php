<?php

class Field {
    private $id;
    private $fieldName;

    public function __construct($id, $fieldName) {
        $this->id = $id;
        $this->fieldName = $fieldName;
    }

    public function getId() {
        return $this->id;
    }

    public function getFieldName() {
        return $this->fieldName;
    }
}
?>
