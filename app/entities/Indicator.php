<?php

class Indicator {
    private $id;
    private $objectiveId;
    private $indicatorTypeId;
    private $indicatorTitle;
    private $indicatorDesc;
    private $target;
    private $completed;
    private $indicatorStatus;
    private $fieldId;
    private $coveId;
    private $createdAt;
    private $objectiveTitle;
    private $indicatorTypeTitle;
    private $fieldName;
    private $coveName;

    public function __construct($id, $objectiveId, $indicatorTypeId, $indicatorTitle, $indicatorDesc, $target, $completed, $indicatorStatus, $fieldId, $coveId, $createdAt, $objectiveTitle = null, $indicatorTypeTitle = null, $fieldName = null,$coveName = null) {
        $this->id = $id;
        $this->objectiveId = $objectiveId;
        $this->indicatorTypeId = $indicatorTypeId;
        $this->indicatorTitle = $indicatorTitle;
        $this->indicatorDesc = $indicatorDesc;
        $this->target = $target;
        $this->completed = $completed;
        $this->indicatorStatus = $indicatorStatus;
        $this->fieldId = $fieldId;
        $this->coveId = $coveId;
        $this->createdAt = $createdAt;
        $this->objectiveTitle = $objectiveTitle;
        $this->indicatorTypeTitle = $indicatorTypeTitle;
        $this->fieldName = $fieldName;
        $this->coveName = $coveName;
    }

    public function getId() {
        return $this->id;
    }

    public function getObjectiveId() {
        return $this->objectiveId;
    }

    public function getIndicatorTypeId() {
        return $this->indicatorTypeId;
    }

    public function getIndicatorTitle() {
        return $this->indicatorTitle;
    }

    public function getIndicatorDesc() {
        return $this->indicatorDesc;
    }

    public function getTarget() {
        return $this->target;
    }

    public function getCompleted() {
        return $this->completed;
    }

    public function getIndicatorStatus() {
        return $this->indicatorStatus;
    }

    public function getFieldId() {
        return $this->fieldId;
    }

    public function getCoveId() {
        return $this->coveId;
    }

    //return $this->createdAt;
    public function getCreatedAt(){
      return date('d.m.Y H:i', strtotime($this->createdAt));
    }

    public function getObjectiveTitle() {
        return $this->objectiveTitle;
    }

    public function getIndicatorTypeTitle() {
        return $this->indicatorTypeTitle;
    }

    public function getFieldName() {
        return $this->fieldName;
    }
    public function getCoveName() {
        return $this->coveName;
    }
}
?>
