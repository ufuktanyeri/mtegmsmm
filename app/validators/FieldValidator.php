<?php

class FieldValidator {
    private $data;
    private $errors = [];
    private static $fields = ['name'];

    public function __construct($post_data) {
        $this->data = $post_data;
    }

    public function validateForm() {
        foreach (self::$fields as $field) {
            if (!array_key_exists($field, $this->data)) {
                trigger_error("$field is not present in data");
                return;
            }
        }

        $this->validateName();

        return $this->errors;
    }

    private function validateName() {
        $val = trim($this->data['name']);
        if (empty($val)) {
            $this->addError('name', 'Alan ismi gerekli');
        }
    }

    private function addError($key, $val) {
        $this->errors[$key] = $val;
    }
}
?>
