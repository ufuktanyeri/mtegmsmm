<?php

class ObjectiveValidator {
    private $data;
    private $errors = [];
    private static $fields = ['objectiveTitle', 'objectiveDesc'];

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

        $this->validateObjectiveTitle();
        $this->validateObjectiveDesc();
        return $this->errors;
    }

    private function validateObjectiveTitle() {
        $val = trim($this->data['objectiveTitle']);

        if (empty($val)) {
            $this->addError('objectiveTitle', 'Amaç Başlığı gerekli');
        } elseif (mb_strlen($val) > 250) {
            $this->addError('objectiveTitle', 'Amaç Başlığı en fazla 250 karakter olabilir');
        }
    }

    private function validateObjectiveDesc() {
        $val = trim($this->data['objectiveDesc']);

        if (empty($val)) {
            $this->addError('objectiveDesc', 'Amaç Açıklaması gerekli');
        }
    }

    private function addError($key, $val) {
        $this->errors[$key] = $val;
    }
}
?>
