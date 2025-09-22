<?php

class Regulation {
    private $id;
    private $regulationDesc;
    private $regulationSource;
    private $regulationSourceNo;
    private $ownerCoveId;
    private $ownerCoveName;

    public function __construct($id, $regulationDesc, $regulationSource, $regulationSourceNo, $ownerCoveId, $ownerCoveName=null) {
        $this->id = $id;
        $this->regulationDesc = $regulationDesc;
        $this->regulationSource = $regulationSource;
        $this->regulationSourceNo = $regulationSourceNo;
        $this->ownerCoveId = $ownerCoveId;
        $this->ownerCoveName = $ownerCoveName;
    }

  



    public function getId() {
        return $this->id;
    }

    public function getRegulationDesc() {
        return $this->regulationDesc;
    }

    public function getRegulationSource() {
        return $this->regulationSource;
    }

    public function getRegulationSourceNo() {
        return $this->regulationSourceNo;
    }

    public function getOwnerCoveId() {
        return $this->ownerCoveId;
    }
    public function getOwnerCoveName() {
        return $this->ownerCoveName;
    }
}
?>
