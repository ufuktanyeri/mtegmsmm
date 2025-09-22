<?php

class AimValidator {
    private $data;
    private $errors = [];
    private static $fields = ['aimTitle', 'aimDesc'];

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

        $this->validateAimTitle();
        $this->validateAimDesc();

        return $this->errors;
    }

    private function validateAimTitle() {
        $val = trim($this->data['aimTitle']);

        if (empty($val)) {
            $this->addError('aimTitle', 'Amaç başlığı gerekli');
        } elseif (mb_strlen($val) > 250) {
            $this->addError('aimTitle', 'Amaç başlığı en fazla 250 karakter olabilir');
        }
    }

    private function validateAimDesc() {
        $val = trim($this->data['aimDesc']);

        if (empty($val)) {
            $this->addError('aimDesc', 'Amaç açıklaması gerekli');
        }
    }

    private function addError($key, $val) {
        $this->errors[$key] = $val;
    }
}
?>
