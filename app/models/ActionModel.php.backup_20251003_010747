<?php

require_once __DIR__ . '/../../includes/Database.php';

class ActionModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // ==================== TEMEL CRUD İŞLEMLERİ ====================

    public function createAction($data) {
        $query = "INSERT INTO actions (actionTitle, actionDesc, actionResponsible, actionStatus, dateStart, dateEnd, periodic, periodType, periodTime, objectiveId, coveId, createdAt) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        // Database::query already executes and returns boolean
        return $this->db->query($query, [
            $data['actionTitle'],
            $data['actionDesc'],
            $data['actionResponsible'],
            $data['actionStatus'],
            $data['dateStart'],
            $data['dateEnd'],
            $data['periodic'],
            $data['periodType'],
            $data['periodTime'],
            $data['objectiveId'],
            $data['coveId']
        ]);
    }

    public function updateAction($id, $data) {
        $query = "UPDATE actions SET 
                  actionTitle = ?, actionDesc = ?, actionResponsible = ?, actionStatus = ?, 
                  dateStart = ?, dateEnd = ?, periodic = ?, periodType = ?, periodTime = ?, 
                  objectiveId = ?
                  WHERE id = ? AND coveId = ?";
        
        return $this->db->query($query, [
            $data['actionTitle'],
            $data['actionDesc'],
            $data['actionResponsible'],
            $data['actionStatus'],
            $data['dateStart'],
            $data['dateEnd'],
            $data['periodic'],
            $data['periodType'],
            $data['periodTime'],
            $data['objectiveId'],
            $id,
            $data['coveId']
        ]);
    }

    public function deleteAction($id, $coveId) {
        $query = "DELETE FROM actions WHERE id = ? AND coveId = ?";
    return $this->db->query($query, [$id, $coveId]);
    }

    public function getActionByIdAndCoveId($id, $coveId) {
        $query = "SELECT * FROM actions WHERE id = ? AND coveId = ?";
    $this->db->query($query, [$id, $coveId]);
    $result = $this->db->single();
    return $result ? (object)$result : null; // preserve legacy object access in controllers
    }

    // ==================== KULLANICI VE MERKEZ İŞLEMLERİ ====================

    public function getCoveIdByUserId($userId) {
        try {
            // Use correct column names for cove_users table
            $query = "SELECT coveId FROM cove_users WHERE userId = ?";
            $this->db->query($query, [$userId]);
            $result = $this->db->single();
            
            if (is_array($result) && isset($result['coveId'])) {
                return $result['coveId'];
            } elseif (is_object($result) && isset($result->coveId)) {
                return $result->coveId;
            }
            
        } catch (Exception $e) {
            error_log("getCoveIdByUserId error: " . $e->getMessage());
        }
        
        return null;
    }

    // ==================== TAKVİM İŞLEMLERİ ====================

    // Normal kullanıcı takvimi (tek merkez)
    public function getActionsForCalendar($coveId) {
    $query = "SELECT ac.*, o.objectiveTitle, aim.aimTitle,
                         CASE 
                 WHEN CAST(ac.actionStatus AS UNSIGNED) = 1 THEN 0
                             WHEN ac.dateEnd < CURDATE() THEN DATEDIFF(CURDATE(), ac.dateEnd)
                             ELSE DATEDIFF(ac.dateEnd, CURDATE())
                         END as days_remaining,
                         CASE 
                 WHEN CAST(ac.actionStatus AS UNSIGNED) = 1 THEN 'completed'
                             WHEN ac.dateEnd < CURDATE() THEN 'overdue'
                             WHEN DATEDIFF(ac.dateEnd, CURDATE()) <= 3 THEN 'urgent'
                             WHEN DATEDIFF(ac.dateEnd, CURDATE()) <= 7 THEN 'warning'
                             ELSE 'normal'
                         END as alert_type,
                         ac.actionStatus as status
                  FROM actions ac 
                  LEFT JOIN objectives o ON ac.objectiveId = o.id 
                  LEFT JOIN aims aim ON o.aimId = aim.id 
                  WHERE ac.coveId = ? 
                  ORDER BY ac.dateStart ASC";
        
    $this->db->query($query, [$coveId]);
    return array_map(fn($r)=>(object)$r, $this->db->resultSet());
    }

    // Admin takvimi (belirli merkez)
    public function getActionsForCalendarWithCoveInfo($coveId) {
    $query = "SELECT ac.*, o.objectiveTitle, aim.aimTitle,
                         c.name as cove_name, c.district as city_district,
                         CASE 
                 WHEN CAST(ac.actionStatus AS UNSIGNED) = 1 THEN 0
                             WHEN ac.dateEnd < CURDATE() THEN DATEDIFF(CURDATE(), ac.dateEnd)
                             ELSE DATEDIFF(ac.dateEnd, CURDATE())
                         END as days_remaining,
                         CASE 
                 WHEN CAST(ac.actionStatus AS UNSIGNED) = 1 THEN 'completed'
                             WHEN ac.dateEnd < CURDATE() THEN 'overdue'
                             WHEN DATEDIFF(ac.dateEnd, CURDATE()) <= 3 THEN 'urgent'
                             WHEN DATEDIFF(ac.dateEnd, CURDATE()) <= 7 THEN 'warning'
                             ELSE 'normal'
                         END as alert_type,
                         ac.actionStatus as status
                  FROM actions ac 
                  LEFT JOIN objectives o ON ac.objectiveId = o.id 
                  LEFT JOIN aims aim ON o.aimId = aim.id 
                  LEFT JOIN coves c ON ac.coveId = c.id
                  WHERE ac.coveId = ? 
                  ORDER BY ac.dateStart ASC";
        
    $this->db->query($query, [$coveId]);
    return $this->db->resultSet();
    }

    // Admin takvimi (tüm merkezler)
    public function getAllActionsForCalendar() {
    $query = "SELECT ac.*, o.objectiveTitle, aim.aimTitle,
                         c.name as cove_name, c.district as city_district,
                         CASE 
                 WHEN CAST(ac.actionStatus AS UNSIGNED) = 1 THEN 0
                             WHEN ac.dateEnd < CURDATE() THEN DATEDIFF(CURDATE(), ac.dateEnd)
                             ELSE DATEDIFF(ac.dateEnd, CURDATE())
                         END as days_remaining,
                         CASE 
                 WHEN CAST(ac.actionStatus AS UNSIGNED) = 1 THEN 'completed'
                             WHEN ac.dateEnd < CURDATE() THEN 'overdue'
                             WHEN DATEDIFF(ac.dateEnd, CURDATE()) <= 3 THEN 'urgent'
                             WHEN DATEDIFF(ac.dateEnd, CURDATE()) <= 7 THEN 'warning'
                             ELSE 'normal'
                         END as alert_type,
                         ac.actionStatus as status
                  FROM actions ac 
                  LEFT JOIN objectives o ON ac.objectiveId = o.id 
                  LEFT JOIN aims aim ON o.aimId = aim.id 
                  LEFT JOIN coves c ON ac.coveId = c.id
                  ORDER BY ac.dateStart ASC";
        
    $this->db->query($query);
    return array_map(fn($r)=>(object)$r, $this->db->resultSet());
    }

    // ==================== GÖREV LİSTELERİ ====================

    // Geciken görevler (tek merkez)
    public function getOverdueTasks($coveId) {
        $query = "SELECT ac.*, o.objectiveTitle, aim.aimTitle,
                         DATEDIFF(CURDATE(), ac.dateEnd) as days_overdue,
                         'overdue' as task_type,
                         'overdue' as alert_type,
                         ac.actionStatus as status
                  FROM actions ac 
                  LEFT JOIN objectives o ON ac.objectiveId = o.id 
                  LEFT JOIN aims aim ON o.aimId = aim.id 
                  WHERE ac.coveId = ? 
                  AND CAST(ac.actionStatus AS UNSIGNED) = 0 
                  AND ac.dateEnd < CURDATE()
                  ORDER BY ac.dateEnd ASC";
        
    $this->db->query($query, [$coveId]);
    return array_map(fn($r)=>(object)$r, $this->db->resultSet());
    }

    // Yaklaşan görevler (tek merkez)
    public function getUpcomingTasks($coveId, $days) {
        // days <=3 -> urgent, >3 and <=days -> upcoming (warning). Exclude urgent when $days >3
        if($days > 3) {
            $query = "SELECT ac.*, o.objectiveTitle, aim.aimTitle,
                             DATEDIFF(ac.dateEnd, CURDATE()) as days_remaining,
                             'upcoming' as task_type,
                             'warning' as alert_type,
                             ac.actionStatus as status
                      FROM actions ac 
                      LEFT JOIN objectives o ON ac.objectiveId = o.id 
                      LEFT JOIN aims aim ON o.aimId = aim.id 
                      WHERE ac.coveId = ? 
                      AND CAST(ac.actionStatus AS UNSIGNED) = 0 
                      AND ac.dateEnd > CURDATE() 
                      AND DATEDIFF(ac.dateEnd, CURDATE()) > 3
                      AND ac.dateEnd <= DATE_ADD(CURDATE(), INTERVAL ? DAY)
                      ORDER BY ac.dateEnd ASC";
            $this->db->query($query, [$coveId, $days]);
        } else {
            $query = "SELECT ac.*, o.objectiveTitle, aim.aimTitle,
                             DATEDIFF(ac.dateEnd, CURDATE()) as days_remaining,
                             'urgent' as task_type,
                             'urgent' as alert_type,
                             ac.actionStatus as status
                      FROM actions ac 
                      LEFT JOIN objectives o ON ac.objectiveId = o.id 
                      LEFT JOIN aims aim ON o.aimId = aim.id 
                      WHERE ac.coveId = ? 
                      AND CAST(ac.actionStatus AS UNSIGNED) = 0 
                      AND ac.dateEnd >= CURDATE() 
                      AND ac.dateEnd <= DATE_ADD(CURDATE(), INTERVAL ? DAY)
                      ORDER BY ac.dateEnd ASC";
            $this->db->query($query, [$coveId, $days]);
        }
        return $this->db->resultSet();
    }

    // Tamamlanan görevler (tek merkez)
    public function getCompletedTasks($coveId) {
    $query = "SELECT ac.*, o.objectiveTitle, aim.aimTitle,
                         0 as days_remaining,
                         'completed' as task_type,
                         'completed' as alert_type,
                         ac.actionStatus as status
                  FROM actions ac 
                  LEFT JOIN objectives o ON ac.objectiveId = o.id 
                  LEFT JOIN aims aim ON o.aimId = aim.id 
                  WHERE ac.coveId = ? 
          AND CAST(ac.actionStatus AS UNSIGNED) = 1
                  ORDER BY ac.dateEnd DESC";
        
    $this->db->query($query, [$coveId]);
    return array_map(fn($r)=>(object)$r, $this->db->resultSet());
    }

    // ==================== ADMİN LİSTELERİ (TÜM MERKEZLER) ====================

    // Tüm merkezlerin geciken görevleri
    public function getAllOverdueTasks() {
    $query = "SELECT ac.*, o.objectiveTitle, aim.aimTitle,
                         c.name as cove_name, c.district as city_district,
                         DATEDIFF(CURDATE(), ac.dateEnd) as days_overdue,
                         'overdue' as task_type,
                         'overdue' as alert_type,
                         ac.actionStatus as status
                  FROM actions ac 
                  LEFT JOIN objectives o ON ac.objectiveId = o.id 
                  LEFT JOIN aims aim ON o.aimId = aim.id 
                  LEFT JOIN coves c ON ac.coveId = c.id
          WHERE CAST(ac.actionStatus AS UNSIGNED) = 0
                  AND ac.dateEnd < CURDATE()
                  ORDER BY ac.dateEnd ASC";
        
    $this->db->query($query);
    return array_map(fn($r)=>(object)$r, $this->db->resultSet());
    }

    // Tüm merkezlerin yaklaşan görevleri
    public function getAllUpcomingTasks($days) {
        if($days > 3) {
            $query = "SELECT ac.*, o.objectiveTitle, aim.aimTitle,
                             c.name as cove_name, c.district as city_district,
                             DATEDIFF(ac.dateEnd, CURDATE()) as days_remaining,
                             'upcoming' as task_type,
                             'warning' as alert_type,
                             ac.actionStatus as status
                      FROM actions ac 
                      LEFT JOIN objectives o ON ac.objectiveId = o.id 
                      LEFT JOIN aims aim ON o.aimId = aim.id 
                      LEFT JOIN coves c ON ac.coveId = c.id
                      WHERE CAST(ac.actionStatus AS UNSIGNED) = 0 
                      AND ac.dateEnd > CURDATE() 
                      AND DATEDIFF(ac.dateEnd, CURDATE()) > 3
                      AND ac.dateEnd <= DATE_ADD(CURDATE(), INTERVAL ? DAY)
                      ORDER BY ac.dateEnd ASC";
            $this->db->query($query, [$days]);
        } else {
            $query = "SELECT ac.*, o.objectiveTitle, aim.aimTitle,
                             c.name as cove_name, c.district as city_district,
                             DATEDIFF(ac.dateEnd, CURDATE()) as days_remaining,
                             'urgent' as task_type,
                             'urgent' as alert_type,
                             ac.actionStatus as status
                      FROM actions ac 
                      LEFT JOIN objectives o ON ac.objectiveId = o.id 
                      LEFT JOIN aims aim ON o.aimId = aim.id 
                      LEFT JOIN coves c ON ac.coveId = c.id
                      WHERE CAST(ac.actionStatus AS UNSIGNED) = 0 
                      AND ac.dateEnd >= CURDATE() 
                      AND ac.dateEnd <= DATE_ADD(CURDATE(), INTERVAL ? DAY)
                      ORDER BY ac.dateEnd ASC";
            $this->db->query($query, [$days]);
        }
        return $this->db->resultSet();
    }

    // Tüm merkezlerin tamamlanan görevleri
    public function getAllCompletedTasks() {
    $query = "SELECT ac.*, o.objectiveTitle, aim.aimTitle,
                         c.name as cove_name, c.district as city_district,
                         0 as days_remaining,
                         'completed' as task_type,
                         'completed' as alert_type,
                         ac.actionStatus as status
                  FROM actions ac 
                  LEFT JOIN objectives o ON ac.objectiveId = o.id 
                  LEFT JOIN aims aim ON o.aimId = aim.id 
                  LEFT JOIN coves c ON ac.coveId = c.id
          WHERE CAST(ac.actionStatus AS UNSIGNED) = 1
                  ORDER BY ac.dateEnd DESC";
        
    $this->db->query($query);
    return array_map(fn($r)=>(object)$r, $this->db->resultSet());
    }

    // ==================== İSTATİSTİK COUNT METODLARİ ====================

    // Tek merkez sayıları
    public function getOverdueTasksCount($coveId) {
    $query = "SELECT COUNT(*) as count FROM actions WHERE coveId = ? AND CAST(actionStatus AS UNSIGNED) = 0 AND dateEnd < CURDATE()";
        $this->db->query($query, [$coveId]);
    $result = $this->db->single();
    return (int)($result['count'] ?? 0);
    }

    public function getUrgentTasksCount($coveId) {
    $query = "SELECT COUNT(*) as count FROM actions 
          WHERE coveId = ? AND CAST(actionStatus AS UNSIGNED) = 0 
                  AND dateEnd >= CURDATE() 
                  AND dateEnd <= DATE_ADD(CURDATE(), INTERVAL 3 DAY)";
        $this->db->query($query, [$coveId]);
    $result = $this->db->single();
    return (int)($result['count'] ?? 0);
    }

    public function getUpcomingTasksCount($coveId, $days) {
        if($days > 3) {
            $query = "SELECT COUNT(*) as count FROM actions 
                      WHERE coveId = ? AND CAST(actionStatus AS UNSIGNED) = 0 
                      AND dateEnd > CURDATE() 
                      AND DATEDIFF(dateEnd, CURDATE()) > 3
                      AND dateEnd <= DATE_ADD(CURDATE(), INTERVAL ? DAY)";
            $this->db->query($query, [$coveId, $days]);
        } else {
            $query = "SELECT COUNT(*) as count FROM actions 
                      WHERE coveId = ? AND CAST(actionStatus AS UNSIGNED) = 0 
                      AND dateEnd >= CURDATE() 
                      AND dateEnd <= DATE_ADD(CURDATE(), INTERVAL ? DAY)";
            $this->db->query($query, [$coveId, $days]);
        }
    $result = $this->db->single();
    return (int)($result['count'] ?? 0);
    }

    public function getCompletedTasksCount($coveId) {
    $query = "SELECT COUNT(*) as count FROM actions WHERE coveId = ? AND CAST(actionStatus AS UNSIGNED) = 1";
        $this->db->query($query, [$coveId]);
    $result = $this->db->single();
    return (int)($result['count'] ?? 0);
    }

    // Tüm merkezler sayıları
    public function getAllOverdueTasksCount() {
    $query = "SELECT COUNT(*) as count FROM actions WHERE CAST(actionStatus AS UNSIGNED) = 0 AND dateEnd < CURDATE()";
        $this->db->query($query);
    $result = $this->db->single();
    return (int)($result['count'] ?? 0);
    }

    public function getAllUrgentTasksCount() {
    $query = "SELECT COUNT(*) as count FROM actions 
          WHERE CAST(actionStatus AS UNSIGNED) = 0 
                  AND dateEnd >= CURDATE() 
                  AND dateEnd <= DATE_ADD(CURDATE(), INTERVAL 3 DAY)";
        $this->db->query($query);
    $result = $this->db->single();
    return (int)($result['count'] ?? 0);
    }

    public function getAllUpcomingTasksCount($days) {
        if($days > 3) {
            $query = "SELECT COUNT(*) as count FROM actions 
                      WHERE CAST(actionStatus AS UNSIGNED) = 0 
                      AND dateEnd > CURDATE() 
                      AND DATEDIFF(dateEnd, CURDATE()) > 3
                      AND dateEnd <= DATE_ADD(CURDATE(), INTERVAL ? DAY)";
            $this->db->query($query, [$days]);
        } else {
            $query = "SELECT COUNT(*) as count FROM actions 
                      WHERE CAST(actionStatus AS UNSIGNED) = 0 
                      AND dateEnd >= CURDATE() 
                      AND dateEnd <= DATE_ADD(CURDATE(), INTERVAL ? DAY)";
            $this->db->query($query, [$days]);
        }
    $result = $this->db->single();
    return (int)($result['count'] ?? 0);
    }

    public function getAllCompletedTasksCount() {
    $query = "SELECT COUNT(*) as count FROM actions WHERE CAST(actionStatus AS UNSIGNED) = 1";
        $this->db->query($query);
    $result = $this->db->single();
    return (int)($result['count'] ?? 0);
    }

    // ==================== ADMİN GÖREV FİLTRELEME ====================

    // Admin için filtrelenmiş görevler
    public function getAdminTasksByFilter($filter, $coveId = null) {
        $whereClause = "";
        $params = [];
        
        if ($coveId && $coveId > 0) {
            $whereClause = "AND ac.coveId = ?";
            $params[] = $coveId;
        }
        
        switch($filter) {
            case 'overdue':
                $query = "SELECT ac.*, o.objectiveTitle, aim.aimTitle,
                                 c.name as cove_name, c.district as city_district,
                                 DATEDIFF(CURDATE(), ac.dateEnd) as days_overdue,
                                 DATEDIFF(CURDATE(), ac.dateEnd) as days_remaining,
                                 'overdue' as task_type,
                                 'overdue' as alert_type,
                                 ac.actionStatus as status
                          FROM actions ac 
                          LEFT JOIN objectives o ON ac.objectiveId = o.id 
                          LEFT JOIN aims aim ON o.aimId = aim.id 
                          LEFT JOIN coves c ON ac.coveId = c.id
                          WHERE CAST(ac.actionStatus AS UNSIGNED) = 0 
                          AND ac.dateEnd < CURDATE() 
                          $whereClause
                          ORDER BY ac.dateEnd ASC";
                break;
                
            case 'urgent':
                $query = "SELECT ac.*, o.objectiveTitle, aim.aimTitle,
                                 c.name as cove_name, c.district as city_district,
                                 DATEDIFF(ac.dateEnd, CURDATE()) as days_remaining,
                                 'urgent' as task_type,
                                 'urgent' as alert_type,
                                 ac.actionStatus as status
                          FROM actions ac 
                          LEFT JOIN objectives o ON ac.objectiveId = o.id 
                          LEFT JOIN aims aim ON o.aimId = aim.id 
                          LEFT JOIN coves c ON ac.coveId = c.id
                          WHERE CAST(ac.actionStatus AS UNSIGNED) = 0 
                          AND ac.dateEnd >= CURDATE() 
                          AND ac.dateEnd <= DATE_ADD(CURDATE(), INTERVAL 3 DAY)
                          $whereClause
                          ORDER BY ac.dateEnd ASC";
                break;
                
            case 'upcoming':
                $query = "SELECT ac.*, o.objectiveTitle, aim.aimTitle,
                                 c.name as cove_name, c.district as city_district,
                                 DATEDIFF(ac.dateEnd, CURDATE()) as days_remaining,
                                 'upcoming' as task_type,
                                 'warning' as alert_type,
                                 ac.actionStatus as status
                          FROM actions ac 
                          LEFT JOIN objectives o ON ac.objectiveId = o.id 
                          LEFT JOIN aims aim ON o.aimId = aim.id 
                          LEFT JOIN coves c ON ac.coveId = c.id
                          WHERE CAST(ac.actionStatus AS UNSIGNED) = 0 
                          /* Upcoming (warning) kapsamı: >3 gün ve <=7 gün */
                          AND ac.dateEnd > CURDATE() 
                          AND DATEDIFF(ac.dateEnd, CURDATE()) > 3
                          AND ac.dateEnd <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                          $whereClause
                          ORDER BY ac.dateEnd ASC";
                break;
                
            case 'completed':
                $query = "SELECT ac.*, o.objectiveTitle, aim.aimTitle,
                                 c.name as cove_name, c.district as city_district,
                                 0 as days_remaining,
                                 'completed' as task_type,
                                 'completed' as alert_type,
                                 ac.actionStatus as status
                          FROM actions ac 
                          LEFT JOIN objectives o ON ac.objectiveId = o.id 
                          LEFT JOIN aims aim ON o.aimId = aim.id 
                          LEFT JOIN coves c ON ac.coveId = c.id
                          WHERE CAST(ac.actionStatus AS UNSIGNED) = 1
                          $whereClause
                          ORDER BY ac.dateEnd DESC";
                break;
                
            default: // 'all'
                $query = "SELECT ac.*, o.objectiveTitle, aim.aimTitle,
                                 c.name as cove_name, c.district as city_district,
                                 CASE 
                                     WHEN CAST(ac.actionStatus AS UNSIGNED) = 1 THEN 0
                                     WHEN ac.dateEnd < CURDATE() THEN DATEDIFF(CURDATE(), ac.dateEnd)
                                     ELSE DATEDIFF(ac.dateEnd, CURDATE())
                                 END as days_remaining,
                                 CASE 
                                     WHEN CAST(ac.actionStatus AS UNSIGNED) = 1 THEN 'completed'
                                     WHEN ac.dateEnd < CURDATE() THEN 'overdue'
                                     WHEN DATEDIFF(ac.dateEnd, CURDATE()) <= 3 THEN 'urgent'
                                     WHEN DATEDIFF(ac.dateEnd, CURDATE()) > 3 AND DATEDIFF(ac.dateEnd, CURDATE()) <= 7 THEN 'upcoming'
                                     ELSE 'normal'
                                 END as task_type,
                                 CASE 
                                     WHEN CAST(ac.actionStatus AS UNSIGNED) = 1 THEN 'completed'
                                     WHEN ac.dateEnd < CURDATE() THEN 'overdue'
                                     WHEN DATEDIFF(ac.dateEnd, CURDATE()) <= 3 THEN 'urgent'
                                     WHEN DATEDIFF(ac.dateEnd, CURDATE()) > 3 AND DATEDIFF(ac.dateEnd, CURDATE()) <= 7 THEN 'warning'
                                     ELSE 'normal'
                                 END as alert_type,
                                 ac.actionStatus as status
                          FROM actions ac 
                          LEFT JOIN objectives o ON ac.objectiveId = o.id 
                          LEFT JOIN aims aim ON o.aimId = aim.id 
                          LEFT JOIN coves c ON ac.coveId = c.id
                          WHERE 1=1 
                          $whereClause
                          ORDER BY ac.dateStart ASC";
                break;
        }
        
    $this->db->query($query, $params);
    return array_map(fn($r)=>(object)$r, $this->db->resultSet());
    }

    // ==================== NORMAL KULLANICI GÖREV FİLTRELEME ====================

    // Normal kullanıcı için filtrelenmiş görevler
    public function getTasksByFilter($type, $coveId) {
        switch($type) {
            case 'overdue':
                return $this->getOverdueTasks($coveId);
                
            case 'urgent':
                return $this->getUpcomingTasks($coveId, 3);
                
            case 'upcoming':
                return $this->getUpcomingTasks($coveId, 7);
                
            case 'completed':
                return $this->getCompletedTasks($coveId);
                
            default: // 'all'
                $query = "SELECT ac.*, o.objectiveTitle, aim.aimTitle,
                                 CASE 
                                     WHEN CAST(ac.actionStatus AS UNSIGNED) = 1 THEN 0
                                     WHEN ac.dateEnd < CURDATE() THEN DATEDIFF(CURDATE(), ac.dateEnd)
                                     ELSE DATEDIFF(ac.dateEnd, CURDATE())
                                 END as days_remaining,
                                 CASE 
                                     WHEN CAST(ac.actionStatus AS UNSIGNED) = 1 THEN 'completed'
                                     WHEN ac.dateEnd < CURDATE() THEN 'overdue'
                                     WHEN DATEDIFF(ac.dateEnd, CURDATE()) <= 3 THEN 'urgent'
                                     WHEN DATEDIFF(ac.dateEnd, CURDATE()) > 3 AND DATEDIFF(ac.dateEnd, CURDATE()) <= 7 THEN 'upcoming'
                                     ELSE 'normal'
                                 END as task_type,
                                 CASE 
                                     WHEN CAST(ac.actionStatus AS UNSIGNED) = 1 THEN 'completed'
                                     WHEN ac.dateEnd < CURDATE() THEN 'overdue'
                                     WHEN DATEDIFF(ac.dateEnd, CURDATE()) <= 3 THEN 'urgent'
                                     WHEN DATEDIFF(ac.dateEnd, CURDATE()) > 3 AND DATEDIFF(ac.dateEnd, CURDATE()) <= 7 THEN 'warning'
                                     ELSE 'normal'
                                 END as alert_type,
                                 ac.actionStatus as status
                          FROM actions ac 
                          LEFT JOIN objectives o ON ac.objectiveId = o.id 
                          LEFT JOIN aims aim ON o.aimId = aim.id 
                          WHERE ac.coveId = ?
                          ORDER BY ac.dateStart ASC";
                
                $this->db->query($query, [$coveId]);
                return array_map(fn($r)=>(object)$r, $this->db->resultSet());
        }
    }

    // ==================== DİĞER YARDIMCI METODLARİ ====================

    public function getActionsByCoveIdByAimId($aimId, $coveId, $objectiveId = null) {
        if ($objectiveId) {
            $query = "SELECT ac.*, o.objectiveTitle, aim.aimTitle 
                      FROM actions ac 
                      LEFT JOIN objectives o ON ac.objectiveId = o.id 
                      LEFT JOIN aims aim ON o.aimId = aim.id 
                      WHERE ac.coveId = ? AND aim.id = ? AND o.id = ? 
                      ORDER BY ac.dateStart DESC";
            $this->db->query($query, [$coveId, $aimId, $objectiveId]);
        } else {
            $query = "SELECT ac.*, o.objectiveTitle, aim.aimTitle 
                      FROM actions ac 
                      LEFT JOIN objectives o ON ac.objectiveId = o.id 
                      LEFT JOIN aims aim ON o.aimId = aim.id 
                      WHERE ac.coveId = ? AND aim.id = ? 
                      ORDER BY ac.dateStart DESC";
            $this->db->query($query, [$coveId, $aimId]);
        }
        
        return $this->db->resultSet();
    }

    public function getActionsByCoveIdJson($coveId) {
        $query = "SELECT ac.*, o.objectiveTitle, aim.aimTitle 
                  FROM actions ac 
                  LEFT JOIN objectives o ON ac.objectiveId = o.id 
                  LEFT JOIN aims aim ON o.aimId = aim.id 
                  WHERE ac.coveId = ?";
        
    $this->db->query($query, [$coveId]);
    return array_map(fn($r)=>(object)$r, $this->db->resultSet());
    }

    // ==================== ADMİN YETKİ KONTROL METODLARİ ====================

    public function isUserAdmin($userId) {
        $query = "SELECT isAdmin FROM users WHERE id = ?";
        $this->db->query($query, [$userId]);
        $result = $this->db->single();
        
        if (is_object($result)) {
            return (int)$result->isAdmin === 1;
        } elseif (is_array($result)) {
            return (int)$result['isAdmin'] === 1;
        }
        
        return false;
    }

    public function getAllCoves() {
        $query = "SELECT * FROM coves ORDER BY district ASC, name ASC";
        $this->db->query($query);
        return $this->db->resultSet();
    }

    // ==================== PERFORMANS METODLARİ ====================

    public function getTasksStatistics($coveId = null) {
        $whereClause = $coveId ? "WHERE coveId = ?" : "";
        $params = $coveId ? [$coveId] : [];
        
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN CAST(actionStatus AS UNSIGNED) = 1 THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN CAST(actionStatus AS UNSIGNED) = 0 AND dateEnd < CURDATE() THEN 1 ELSE 0 END) as overdue,
                    SUM(CASE WHEN CAST(actionStatus AS UNSIGNED) = 0 AND dateEnd >= CURDATE() AND dateEnd <= DATE_ADD(CURDATE(), INTERVAL 3 DAY) THEN 1 ELSE 0 END) as urgent,
                    /* Upcoming sayısı urgent (<=3 gün) hariç tutulur */
                    SUM(CASE WHEN CAST(actionStatus AS UNSIGNED) = 0 AND dateEnd > CURDATE() AND DATEDIFF(dateEnd, CURDATE()) > 3 AND dateEnd <= DATE_ADD(CURDATE(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as upcoming
                  FROM actions 
                  $whereClause";
        
        $this->db->query($query, $params);
        $result = $this->db->single();
        
        return [
            'total' => $result->total ?? 0,
            'completed' => $result->completed ?? 0,
            'overdue' => $result->overdue ?? 0,
            'urgent' => $result->urgent ?? 0,
            'upcoming' => $result->upcoming ?? 0
        ];
    }
}
?>
