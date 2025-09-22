<?php

class CoveValidator {
    private $data;
    private $errors = [];
    private static $fields = ['name', 'city', 'district', 'address'];

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
        $this->validateCity();
        $this->validateDistrict();
        $this->validateAddress();

        return $this->errors;
    }

    private function validateName() {
        $val = trim($this->data['name']);

        if (empty($val)) {
            $this->addError('name', 'Name is required');
        }
    }

    private function validateCity() {
        $val = trim($this->data['city']);

        if (empty($val)) {
            $this->addError('city', 'City is required');
        }
    }

    private function validateDistrict() {
        $val = trim($this->data['district']);

        if (empty($val)) {
            $this->addError('district', 'District is required');
        }
    }

    private function validateAddress() {
        $val = trim($this->data['address']);

        if (empty($val)) {
            $this->addError('address', 'Address is required');
        }
    }

    private function addError($key, $val) {
        $this->errors[$key] = $val;
    }
}
?>
