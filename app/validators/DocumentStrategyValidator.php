<?php

class DocumentStrategyValidator {
    private $data;
    private $errors = [];
    private static $fields = ['strategyDesc', 'strategyNo'];

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

        $this->validateStrategyDesc();
        $this->validateStrategyNo();

        return $this->errors;
    }

    private function validateStrategyDesc() {
        $val = trim($this->data['strategyDesc']);

        if (empty($val)) {
            $this->addError('strategyDesc', 'Strategy description is required');
        }
    }

    private function validateStrategyNo() {
        $val = trim($this->data['strategyNo']);

        if (empty($val)) {
            $this->addError('strategyNo', 'Strategy number is required');
        }
    }

    private function addError($key, $val) {
        $this->errors[$key] = $val;
    }
}
?>
