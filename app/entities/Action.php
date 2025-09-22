<?php

class Action {
    private $id;
    private $actionTitle;
    private $actionDesc;
    private $actionResponsible;
    private $actionStatus;
    private $dateStart;
    private $dateEnd;
    private $periodic;
    private $periodType;
    private $periodTime;
    private $createdAt;
    private $objectiveId;
    private $aimId;
    private $coveId;

    public function __construct($id, $actionTitle, $actionDesc, $actionResponsible, $actionStatus, $dateStart, $dateEnd, $periodic, $periodType, $periodTime, $createdAt, $objectiveId, $coveId, $aimId=0) {
        $this->id = $id;
        $this->actionTitle = $actionTitle;
        $this->actionDesc = $actionDesc;
        $this->actionResponsible = $actionResponsible;
        $this->actionStatus = $actionStatus;
        $this->dateStart = $dateStart;
        $this->dateEnd = $dateEnd;
        $this->periodic = $periodic;
        $this->periodType = $periodType;
        $this->periodTime = $periodTime;
        $this->createdAt = $createdAt;
        $this->objectiveId = $objectiveId;
        $this->aimId = $aimId;
        $this->coveId = $coveId;
    }

    public function getId() {
        return $this->id;
    }

    public function getActionTitle() {
        return $this->actionTitle;
    }

    public function getActionDesc() {
        return $this->actionDesc;
    }

    public function getActionResponsible() {
        return $this->actionResponsible;
    }

    public function getActionStatus() {
        return $this->actionStatus;
    }

    public function getDateStart() {
        return $this->dateStart;
    }

    public function getDateEnd() {
        return $this->dateEnd;
    }

    public function getPeriodic() {
        return $this->periodic;
    }

    public function getPeriodType() {
        return $this->periodType;
    }

    public function getPeriodTime() {
        return $this->periodTime;
    }

    public function getCreatedAt() {
    return date('d.m.Y H:i', strtotime($this->createdAt));
    //return $this->createdAt;
  }

    public function getObjectiveId() {
        return $this->objectiveId;
    }

    public function getCoveId() {
        return $this->coveId;
    }

    public function getAimId() {
        return $this->aimId;
    }
}
?>
