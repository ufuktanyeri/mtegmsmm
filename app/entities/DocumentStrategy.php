<?php

class DocumentStrategy {
    private $id;
    private $strategyDesc;
    private $strategyNo;

    public function __construct($id, $strategyDesc, $strategyNo) {
        $this->id = $id;
        $this->strategyDesc = $strategyDesc;
        $this->strategyNo = $strategyNo;
    }

    public function getId() {
        return $this->id;
    }

    public function getStrategyDesc() {
        return $this->strategyDesc;
    }

    public function getStrategyNo() {
        return $this->strategyNo;
    }
}
?>
