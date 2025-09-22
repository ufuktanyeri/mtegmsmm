<?php

namespace App\Services;

use App\Models\AimModel;
use App\Models\CoveModel;
use App\Models\ActionModel;
use App\Models\IndicatorModel;

class ReportService
{
    private AimModel $aimModel;
    private CoveModel $coveModel;
    private ActionModel $actionModel;
    private IndicatorModel $indicatorModel;

    public function __construct()
    {
        $this->aimModel = new AimModel();
        $this->coveModel = new CoveModel();
        $this->actionModel = new ActionModel();
        $this->indicatorModel = new IndicatorModel();
    }

    /**
     * Prepares the data structure for a Cove report.
     *
     * @param int $coveId
     * @return array
     */
    public function prepareCoveReportData(int $coveId): array
    {
        $cove = $this->coveModel->getCoveById($coveId);
        if (!$cove) {
            return [];
        }

        $aims = $this->aimModel->getAimsForReport($coveId);

        $reportData = [
            'coveName' => $cove->getName(),
            'aims' => [],
        ];

        foreach ($aims as $aim) {
            $aimData = [
                'title' => $aim->getAimTitle(),
                'description' => $aim->getAimDesc(),
                'result' => $aim->getAimResult(),
                'objectives' => [],
            ];

            foreach ($aim->getObjectives() as $objective) {
                $objectiveData = [
                    'title' => $objective->getObjectiveTitle(),
                    'description' => $objective->getObjectiveDesc(),
                    'result' => $objective->getObjectiveResult(),
                    'actions' => [],
                    'indicators' => [],
                ];

                $actions = $this->actionModel->getActionsByCoveIdByAimId($aim->getId(), $coveId, $objective->getId());
                foreach ($actions as $action) {
                    $objectiveData['actions'][] = [
                        'title' => $action->getActionTitle(),
                        'description' => $action->getActionDesc(),
                        'responsible' => $action->getActionResponsible(),
                        'status' => $action->getActionStatus() ? 'Tamamlandı' : 'Devam Ediyor',
                        'startDate' => $action->getDateStart(),
                        'endDate' => $action->getDateEnd(),
                        'periodic' => $action->getPeriodic() == 1 ? 'Haftalık' : '-',
                    ];
                }

                $indicators = $this->indicatorModel->getIndicatorsByCoveId($aim->getId(), $coveId, $objective->getId());
                foreach ($indicators as $indicator) {
                    $objectiveData['indicators'][] = [
                        'title' => $indicator->getIndicatorTitle(),
                        'description' => $indicator->getIndicatorDesc(),
                        'target' => $indicator->getTarget(),
                        'completed' => $indicator->getCompleted(),
                        'status' => $indicator->getIndicatorStatus(),
                        'indicatorType' => $indicator->getIndicatorTypeTitle(),
                        'fieldName' => $indicator->getFieldName(),
                    ];
                }

                $aimData['objectives'][] = $objectiveData;
            }

            $reportData['aims'][] = $aimData;
        }

        return $reportData;
    }

    /**
     * Generates a summary report for all coves
     * 
     * @return array
     */
    public function generateSummaryReport(): array
    {
        $coves = $this->coveModel->getAllCoves();
        $summary = [];

        foreach ($coves as $cove) {
            $summary[] = [
                'coveId' => $cove->getId(),
                'coveName' => $cove->getName(),
                'reportData' => $this->prepareCoveReportData($cove->getId())
            ];
        }

        return $summary;
    }

    /**
     * Get report statistics for a specific cove
     * 
     * @param int $coveId
     * @return array
     */
    public function getCoveStatistics(int $coveId): array
    {
        $reportData = $this->prepareCoveReportData($coveId);
        
        if (empty($reportData)) {
            return [];
        }

        $totalAims = count($reportData['aims']);
        $totalObjectives = 0;
        $totalActions = 0;
        $totalIndicators = 0;
        $completedActions = 0;
        $completedIndicators = 0;

        foreach ($reportData['aims'] as $aim) {
            $totalObjectives += count($aim['objectives']);
            foreach ($aim['objectives'] as $objective) {
                $totalActions += count($objective['actions']);
                $totalIndicators += count($objective['indicators']);
                
                foreach ($objective['actions'] as $action) {
                    if ($action['status'] === 'Tamamlandı') {
                        $completedActions++;
                    }
                }
                
                foreach ($objective['indicators'] as $indicator) {
                    if ($indicator['completed'] > 0) {
                        $completedIndicators++;
                    }
                }
            }
        }

        return [
            'coveName' => $reportData['coveName'],
            'totalAims' => $totalAims,
            'totalObjectives' => $totalObjectives,
            'totalActions' => $totalActions,
            'totalIndicators' => $totalIndicators,
            'completedActions' => $completedActions,
            'completedIndicators' => $completedIndicators,
            'actionCompletionRate' => $totalActions > 0 ? round(($completedActions / $totalActions) * 100, 2) : 0,
            'indicatorCompletionRate' => $totalIndicators > 0 ? round(($completedIndicators / $totalIndicators) * 100, 2) : 0,
        ];
    }
}
