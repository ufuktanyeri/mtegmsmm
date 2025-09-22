<?php

class IndicatorType {
    private $id;
    private $indicatorTitle;

    public function __construct($id, $indicatorTitle) {
        $this->id = $id;
        $this->indicatorTitle = $indicatorTitle;
    }

    public function getId() {
        return $this->id;
    }

    public function getIndicatorTitle() {
        return $this->indicatorTitle;
    }
}
?>
