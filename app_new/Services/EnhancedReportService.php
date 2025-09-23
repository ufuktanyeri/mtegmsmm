<?php

namespace AppNew\Services;

use App\Services\ReportService;
use App\Models\UserModel;
use App\Models\CoveModel;

/**
 * Enhanced Report Service for SuperAdmin features
 * Extends the existing ReportService with new capabilities
 */
class EnhancedReportService extends ReportService
{
    private UserModel $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new UserModel();
    }

    /**
     * Generate executive summary for SuperAdmin
     *
     * @return array
     */
    public function generateExecutiveSummary(): array
    {
        $db = \Database::getInstance();

        // Get overall statistics
        $query = "SELECT
            COUNT(DISTINCT c.id) as total_coves,
            COUNT(DISTINCT a.id) as total_aims,
            COUNT(DISTINCT o.id) as total_objectives,
            COUNT(DISTINCT ac.id) as total_actions,
            COUNT(DISTINCT i.id) as total_indicators,
            SUM(CASE WHEN ac.actionStatus = 'completed' THEN 1 ELSE 0 END) as completed_actions,
            AVG(i.completed / NULLIF(i.target, 0) * 100) as avg_completion_rate
        FROM coves c
        LEFT JOIN aims a ON c.id = a.coveId
        LEFT JOIN objectives o ON a.id = o.aimId
        LEFT JOIN actions ac ON o.id = ac.objectiveId
        LEFT JOIN indicators i ON o.id = i.objectiveId";

        $stats = $db->query($query)->fetch();

        // Get performance by cove
        $covePerformance = $this->getCovePerformanceComparison();

        // Get recent assignments
        $recentAssignments = $this->getRecentAssignments();

        // Get trending indicators
        $trendingIndicators = $this->getTrendingIndicators();

        return [
            'generated_at' => date('Y-m-d H:i:s'),
            'overall_statistics' => $stats,
            'cove_performance' => $covePerformance,
            'recent_assignments' => $recentAssignments,
            'trending_indicators' => $trendingIndicators,
            'alerts' => $this->getSystemAlerts()
        ];
    }

    /**
     * Get comparative performance across all SMM centers
     *
     * @return array
     */
    public function getCovePerformanceComparison(): array
    {
        $db = \Database::getInstance();

        $query = "SELECT
            c.id,
            c.name as cove_name,
            c.city,
            COUNT(DISTINCT a.id) as aim_count,
            COUNT(DISTINCT o.id) as objective_count,
            COUNT(DISTINCT ac.id) as action_count,
            SUM(CASE WHEN ac.actionStatus = 'completed' THEN 1 ELSE 0 END) as completed_actions,
            AVG(o.completion_percentage) as avg_objective_completion,
            SUM(CASE WHEN ac.dateEnd < CURDATE() AND ac.actionStatus != 'completed' THEN 1 ELSE 0 END) as overdue_actions
        FROM coves c
        LEFT JOIN aims a ON c.id = a.coveId
        LEFT JOIN objectives o ON a.id = o.aimId
        LEFT JOIN actions ac ON o.id = ac.objectiveId
        GROUP BY c.id, c.name, c.city
        ORDER BY avg_objective_completion DESC";

        return $db->query($query)->fetchAll();
    }

    /**
     * Get recent task assignments
     *
     * @param int $limit
     * @return array
     */
    public function getRecentAssignments(int $limit = 10): array
    {
        $db = \Database::getInstance();

        $query = "SELECT
            a.id,
            a.aimTitle,
            a.assignment_status,
            a.assignment_date,
            a.assignment_notes,
            u1.realname as assigned_by_name,
            u2.realname as assigned_to_name,
            c.name as cove_name
        FROM aims a
        LEFT JOIN users u1 ON a.assigned_by = u1.id
        LEFT JOIN users u2 ON a.assigned_to = u2.id
        LEFT JOIN coves c ON a.coveId = c.id
        WHERE a.assigned_by IS NOT NULL
        ORDER BY a.assignment_date DESC
        LIMIT :limit";

        $stmt = $db->prepare($query);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get trending indicators (best and worst performing)
     *
     * @return array
     */
    public function getTrendingIndicators(): array
    {
        $db = \Database::getInstance();

        // Best performing indicators
        $bestQuery = "SELECT
            i.indicatorTitle,
            i.completed,
            i.target,
            (i.completed / NULLIF(i.target, 0) * 100) as completion_rate,
            o.objectiveTitle,
            c.name as cove_name
        FROM indicators i
        INNER JOIN objectives o ON i.objectiveId = o.id
        INNER JOIN aims a ON o.aimId = a.id
        INNER JOIN coves c ON a.coveId = c.id
        WHERE i.target > 0
        ORDER BY completion_rate DESC
        LIMIT 5";

        // Worst performing indicators
        $worstQuery = "SELECT
            i.indicatorTitle,
            i.completed,
            i.target,
            (i.completed / NULLIF(i.target, 0) * 100) as completion_rate,
            o.objectiveTitle,
            c.name as cove_name
        FROM indicators i
        INNER JOIN objectives o ON i.objectiveId = o.id
        INNER JOIN aims a ON o.aimId = a.id
        INNER JOIN coves c ON a.coveId = c.id
        WHERE i.target > 0
        ORDER BY completion_rate ASC
        LIMIT 5";

        return [
            'best_performing' => $db->query($bestQuery)->fetchAll(),
            'worst_performing' => $db->query($worstQuery)->fetchAll()
        ];
    }

    /**
     * Get system alerts (overdue tasks, low performance, etc.)
     *
     * @return array
     */
    public function getSystemAlerts(): array
    {
        $alerts = [];
        $db = \Database::getInstance();

        // Check for overdue actions
        $overdueQuery = "SELECT COUNT(*) as count
                        FROM actions
                        WHERE dateEnd < CURDATE()
                        AND actionStatus != 'completed'";
        $overdueCount = $db->query($overdueQuery)->fetchColumn();

        if ($overdueCount > 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "$overdueCount adet gecikmiş faaliyet bulunmaktadır.",
                'priority' => 'high'
            ];
        }

        // Check for low performing objectives
        $lowPerformanceQuery = "SELECT COUNT(*) as count
                                FROM objectives
                                WHERE completion_percentage < 30
                                AND deadline < DATE_ADD(CURDATE(), INTERVAL 30 DAY)";
        $lowPerformanceCount = $db->query($lowPerformanceQuery)->fetchColumn();

        if ($lowPerformanceCount > 0) {
            $alerts[] = [
                'type' => 'danger',
                'message' => "$lowPerformanceCount adet düşük performanslı hedef yaklaşan son tarihe sahip.",
                'priority' => 'critical'
            ];
        }

        // Check for unassigned aims
        $unassignedQuery = "SELECT COUNT(*) as count
                           FROM aims
                           WHERE assigned_to IS NULL
                           AND createdAt < DATE_SUB(NOW(), INTERVAL 7 DAY)";
        $unassignedCount = $db->query($unassignedQuery)->fetchColumn();

        if ($unassignedCount > 0) {
            $alerts[] = [
                'type' => 'info',
                'message' => "$unassignedCount adet amaç henüz atanmamış.",
                'priority' => 'medium'
            ];
        }

        return $alerts;
    }

    /**
     * Generate performance trend analysis
     *
     * @param int $coveId Optional specific cove
     * @param int $months Number of months to analyze
     * @return array
     */
    public function getPerformanceTrend(?int $coveId = null, int $months = 6): array
    {
        $db = \Database::getInstance();

        $query = "SELECT
            DATE_FORMAT(ac.dateEnd, '%Y-%m') as month,
            COUNT(DISTINCT ac.id) as total_actions,
            SUM(CASE WHEN ac.actionStatus = 'completed' THEN 1 ELSE 0 END) as completed_actions,
            AVG(i.completed / NULLIF(i.target, 0) * 100) as avg_indicator_completion
        FROM actions ac
        LEFT JOIN objectives o ON ac.objectiveId = o.id
        LEFT JOIN aims a ON o.aimId = a.id
        LEFT JOIN indicators i ON o.id = i.objectiveId
        WHERE ac.dateEnd >= DATE_SUB(CURDATE(), INTERVAL :months MONTH)";

        if ($coveId) {
            $query .= " AND a.coveId = :coveId";
        }

        $query .= " GROUP BY DATE_FORMAT(ac.dateEnd, '%Y-%m')
                   ORDER BY month ASC";

        $stmt = $db->prepare($query);
        $stmt->bindValue(':months', $months, \PDO::PARAM_INT);
        if ($coveId) {
            $stmt->bindValue(':coveId', $coveId, \PDO::PARAM_INT);
        }
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Export report data in various formats
     *
     * @param string $reportType
     * @param string $format (pdf, excel, word)
     * @param array $params
     * @return mixed
     */
    public function exportReport(string $reportType, string $format, array $params = [])
    {
        $reportData = match($reportType) {
            'executive' => $this->generateExecutiveSummary(),
            'performance' => $this->getCovePerformanceComparison(),
            'detailed' => $this->generateSummaryReport(),
            default => []
        };

        return match($format) {
            'pdf' => $this->exportToPdf($reportData, $reportType),
            'excel' => $this->exportToExcel($reportData, $reportType),
            'word' => $this->exportToWord($reportData, $reportType),
            default => $reportData
        };
    }

    /**
     * Export to PDF using existing dompdf library
     */
    private function exportToPdf(array $data, string $reportType): string
    {
        // Use existing dompdf setup in app/lib/dompdf
        require_once __DIR__ . '/../../app/lib/dompdf/autoload.inc.php';

        $dompdf = new \Dompdf\Dompdf();
        $html = $this->generateReportHtml($data, $reportType);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }

    /**
     * Export to Word using existing PHPWord library
     */
    private function exportToWord(array $data, string $reportType): string
    {
        // Use existing PHPWord setup in app/lib/PHPWord
        require_once __DIR__ . '/../../app/lib/PHPWord/src/PhpWord/Autoloader.php';
        \PhpOffice\PhpWord\Autoloader::register();

        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();

        // Add report title
        $section->addTitle($this->getReportTitle($reportType), 1);

        // Add report content based on type
        $this->addWordContent($section, $data, $reportType);

        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $tempFile = tempnam(sys_get_temp_dir(), 'report');
        $objWriter->save($tempFile);

        return file_get_contents($tempFile);
    }

    /**
     * Export to Excel
     */
    private function exportToExcel(array $data, string $reportType): string
    {
        // Simple CSV export for now
        $output = fopen('php://temp', 'r+');

        // Add headers based on report type
        $headers = $this->getExcelHeaders($reportType);
        fputcsv($output, $headers);

        // Add data rows
        foreach ($data as $row) {
            fputcsv($output, array_values($row));
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    private function generateReportHtml(array $data, string $reportType): string
    {
        ob_start();
        include __DIR__ . '/../Views/reports/' . $reportType . '_template.php';
        return ob_get_clean();
    }

    private function getReportTitle(string $reportType): string
    {
        return match($reportType) {
            'executive' => 'Yönetici Özet Raporu',
            'performance' => 'Performans Karşılaştırma Raporu',
            'detailed' => 'Detaylı Faaliyet Raporu',
            default => 'MTEGM SMM Raporu'
        };
    }
}