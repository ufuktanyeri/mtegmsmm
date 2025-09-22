<?php

class RegulationValidator {
    private $data;
    private $errors = [];
    private static $fields = ['regulationDesc', 'regulationSource', 'regulationSourceNo'];

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

        $this->validateRegulationDesc();
        $this->validateRegulationSource();
        $this->validateRegulationSourceNo();

        return $this->errors;
    }

    private function validateRegulationDesc() {
        $val = trim($this->data['regulationDesc']);

        if (empty($val)) {
            $this->addError('regulationDesc', 'Regülasyon açıklaması gerekli');
        }
    }

    private function validateRegulationSource() {
        $val = trim($this->data['regulationSource']);

        if (empty($val)) {
            $this->addError('regulationSource', 'Regülasyon kaynağı gerekli');
        }
    }

    private function validateRegulationSourceNo() {
        $val = trim($this->data['regulationSourceNo']);

        if (empty($val)) {
            $this->addError('regulationSourceNo', 'Regülasyon kaynak numarası gerekli');
        }
    }

    private function addError($key, $val) {
        $this->errors[$key] = $val;
    }
}
?>
