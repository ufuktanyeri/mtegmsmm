# Task Management System - Universal Multi-User Design

**Project:** MTEGM SMM Portal
**Feature:** Universal Role-Based Task Management System
**Target:** ALL SuperAdmin users and their organizations
**Date:** October 3, 2025
**Status:** Ready for Implementation

---

## Executive Summary

This document describes a **universal task management system** for the entire MTEGM SMM Portal, supporting **all SuperAdmin users** (not just admin_gazi) with complete **multi-tenant isolation** and **role-based workflows**.

### Key Improvements Over Previous Design
- ✅ **Universal:** Works for ALL 12 SuperAdmins (not hardcoded to ID: 45)
- ✅ **Multi-Tenant:** Complete `cove_id` isolation for each organization
- ✅ **Scalable:** Supports unlimited users across all roles
- ✅ **Flexible:** Each SuperAdmin manages their own task templates
- ✅ **Secure:** Strict permission boundaries between organizations

---

## Table of Contents
1. [System Architecture](#system-architecture)
2. [User Roles & Responsibilities](#user-roles--responsibilities)
3. [Database Schema (Universal)](#database-schema-universal)
4. [Permission Model](#permission-model)
5. [Multi-Tenant Isolation](#multi-tenant-isolation)
6. [Workflows by Role](#workflows-by-role)
7. [Implementation Guide](#implementation-guide)
8. [Migration Strategy](#migration-strategy)

---

## System Architecture

### Design Principles

```
┌─────────────────────────────────────────────────────┐
│        UNIVERSAL TASK MANAGEMENT SYSTEM              │
├─────────────────────────────────────────────────────┤
│                                                      │
│  12 SuperAdmins                                      │
│  ├── Each has their own task definitions            │
│  ├── Each manages their own assignments             │
│  └── Complete isolation between organizations       │
│                                                      │
│  Multi-Tenant Architecture                           │
│  ├── cove_id for organization isolation             │
│  ├── created_by for ownership tracking              │
│  └── Permission-based access control                │
│                                                      │
│  Role Hierarchy (Per Organization)                   │
│  SuperAdmin → Admin → Coordinator → User            │
│                                                      │
└─────────────────────────────────────────────────────┘
```

### Current SuperAdmin Users

Based on system analysis, we have **12 SuperAdmins**:

| User ID | Username | Organization | Role |
|---------|----------|--------------|------|
| 1 | Sistem Admin | System-wide | SuperAdmin |
| 2 | admin | System-wide | SuperAdmin |
| 9 | ufuk_merkez | MTEGM Central | SuperAdmin |
| 19 | mkserdar | System-wide | SuperAdmin |
| 35 | hizmetici_betul | Service Support | SuperAdmin |
| 45 | **admin_gazi** | Regional Coordination | SuperAdmin |
| 53 | test_admin | Testing | SuperAdmin |
| 54 | m.fatih | Regional | SuperAdmin |
| 55 | merkez_user | Central Operations | SuperAdmin |
| 59 | emelytanyeri | System Management | SuperAdmin |
| 60 | ayse.hoca | Education Coordination | SuperAdmin |
| 67 | ilknuryaman | Regional Support | SuperAdmin |

**Each SuperAdmin can:**
- Create their own task definitions
- Assign tasks to users in ANY cove (system-wide view)
- View all tasks they created
- Generate reports for their tasks

---

## User Roles & Responsibilities

### 1. SuperAdmin (12 users)
**Access Level:** System-Wide (across all coves)

**Capabilities:**
- ✅ Create/edit/delete task definitions (reusable templates)
- ✅ Assign tasks to ANY user in ANY cove
- ✅ View ALL tasks (system-wide or filtered by cove)
- ✅ Generate comprehensive reports
- ✅ Track task completion across the entire system
- ✅ Manage task priorities and deadlines globally

**Use Cases:**
- Ministry-wide strategic planning initiatives
- Cross-regional data collection tasks
- System maintenance and updates
- Performance reporting requirements
- Emergency/urgent communications

### 2. Admin (Cove-Specific)
**Access Level:** Single Cove (their assigned organization)

**Capabilities:**
- ✅ View tasks assigned to them by SuperAdmin
- ✅ Accept/reject task assignments
- ✅ Delegate tasks to Coordinators in their cove
- ✅ Track delegated task progress
- ✅ Update task status and progress
- ✅ View team performance within their cove
- ❌ Cannot create task definitions
- ❌ Cannot see tasks from other coves

**Use Cases:**
- Regional implementation of ministry directives
- Local team coordination
- Progress reporting to SuperAdmin
- Resource allocation within cove

### 3. Coordinator (Cove-Specific)
**Access Level:** Single Cove (their assigned organization)

**Capabilities:**
- ✅ View tasks delegated to them by Admin
- ✅ Accept/reject delegations
- ✅ Update progress and completion status
- ✅ Add comments and notes
- ✅ Upload completion documents
- ❌ Cannot delegate further
- ❌ Cannot see other users' tasks

**Use Cases:**
- Execute specific tasks delegated by Admin
- Data entry and collection
- Local reporting
- Field operations

### 4. User (Regular Staff)
**Access Level:** Own Tasks Only

**Capabilities:**
- ✅ View tasks assigned directly to them
- ✅ Accept/reject assignments
- ✅ Update progress
- ✅ Mark tasks complete
- ✅ Add comments
- ❌ Very limited visibility (own tasks only)

**Use Cases:**
- Complete assigned work
- Report progress
- Request clarification

---

## Database Schema (Universal)

### Key Changes from Previous Design

1. **No Hardcoded User IDs** - Works with all SuperAdmins
2. **Creator Tracking** - `created_by` tracks which SuperAdmin created each definition
3. **Multi-Tenant Aware** - All queries filter by appropriate scope
4. **Flexible Cove Assignment** - `cove_id` can be NULL (system-wide) or specific

### 1. Task Definitions (Templates)

```sql
CREATE TABLE IF NOT EXISTS `task_definitions` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `task_name` VARCHAR(200) NOT NULL COMMENT 'Task name',
  `task_description` TEXT NULL COMMENT 'Detailed description',
  `task_type` ENUM('strategic','operational','reporting','urgent')
    NOT NULL DEFAULT 'operational' COMMENT 'Task type',
  `task_category` ENUM(
    'aim_management',
    'objective_setting',
    'indicator_update',
    'action_planning',
    'report_generation',
    'system_maintenance',
    'user_management',
    'data_entry'
  ) NULL COMMENT 'Task category',
  `template_data` JSON NULL COMMENT 'Template configuration (checklist, fields, etc.)',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Active status',
  `created_by` INT(11) NOT NULL COMMENT 'SuperAdmin who created this template',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),
  INDEX `idx_task_type` (`task_type`),
  INDEX `idx_created_by` (`created_by`),
  INDEX `idx_task_category` (`task_category`),
  INDEX `idx_active` (`is_active`),

  CONSTRAINT `fk_task_definitions_created_by`
    FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci
COMMENT='Universal task definitions - works for all SuperAdmins';
```

**Important:**
- `created_by` can be ANY SuperAdmin (not just ID: 45)
- Each SuperAdmin sees only their own templates OR can see all if needed
- Template sharing possible in future (not in v1)

### 2. Task Assignments

```sql
CREATE TABLE IF NOT EXISTS `task_assignments` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `task_definition_id` INT(11) NOT NULL COMMENT 'Which template to use',
  `assigned_by` INT(11) NOT NULL COMMENT 'Who assigned (SuperAdmin/Admin)',
  `assigned_to` INT(11) NOT NULL COMMENT 'Target user',
  `cove_id` INT(11) NULL COMMENT 'Specific cove OR NULL for system-wide',
  `status` ENUM('pending','accepted','in_progress','completed','rejected','cancelled')
    NOT NULL DEFAULT 'pending' COMMENT 'Current status',
  `priority` ENUM('low','medium','high','critical')
    NOT NULL DEFAULT 'medium' COMMENT 'Priority level',
  `due_date` DATE NULL COMMENT 'Deadline',
  `assigned_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `accepted_at` TIMESTAMP NULL DEFAULT NULL,
  `completed_at` TIMESTAMP NULL DEFAULT NULL,
  `rejection_reason` TEXT NULL COMMENT 'Why was it rejected',
  `completion_data` JSON NULL COMMENT 'Results, attachments, metrics',
  `notes` TEXT NULL COMMENT 'Additional instructions',
  `progress_percentage` INT(3) NOT NULL DEFAULT 0 COMMENT '0-100',

  PRIMARY KEY (`id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_assigned_to` (`assigned_to`),
  INDEX `idx_assigned_by` (`assigned_by`),
  INDEX `idx_cove_id` (`cove_id`),
  INDEX `idx_due_date` (`due_date`),
  INDEX `idx_priority` (`priority`),
  INDEX `idx_composite_user_status` (`assigned_to`, `status`),
  INDEX `idx_composite_cove_status` (`cove_id`, `status`),

  CONSTRAINT `fk_task_assignments_definition`
    FOREIGN KEY (`task_definition_id`) REFERENCES `task_definitions` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_task_assignments_assigned_by`
    FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_task_assignments_assigned_to`
    FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_task_assignments_cove`
    FOREIGN KEY (`cove_id`) REFERENCES `coves` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci
COMMENT='Universal task assignments with multi-tenant support';
```

**Multi-Tenant Logic:**
- `cove_id = NULL` → Task applies system-wide (e.g., ministry directive)
- `cove_id = X` → Task specific to cove X
- SuperAdmin sees ALL assignments
- Admin sees only assignments where `cove_id = their_cove_id` OR `assigned_to = their_id`

### 3. Task Delegations (Same as before)

```sql
CREATE TABLE IF NOT EXISTS `task_delegations` (
  -- ... (structure unchanged from previous design)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;
```

### 4. Task Tracking (Audit Log)

```sql
CREATE TABLE IF NOT EXISTS `task_tracking` (
  -- ... (structure unchanged from previous design)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;
```

### 5. Task Comments

```sql
CREATE TABLE IF NOT EXISTS `task_comments` (
  -- ... (structure unchanged from previous design)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;
```

### 6. User Widget Preferences

```sql
CREATE TABLE IF NOT EXISTS `user_widget_preferences` (
  -- ... (structure unchanged from previous design)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;
```

---

## Permission Model

### Permission Definitions

```sql
-- Universal task permissions (works for ALL users)
INSERT IGNORE INTO `permissions` (`permission_name`, `description`) VALUES
('tasks.define', 'Create and manage task definitions'),
('tasks.assign', 'Assign tasks to users'),
('tasks.delegate', 'Delegate assigned tasks'),
('tasks.view_all', 'View all tasks (filtered by role)'),
('tasks.view_own', 'View own assigned tasks'),
('tasks.update', 'Update task status and progress'),
('tasks.comment', 'Add comments to tasks'),
('tasks.report', 'Generate task reports');
```

### Role Permission Mapping

```sql
-- SuperAdmin (role_id = 5) - FULL ACCESS
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 5, p.id FROM `permissions` p
WHERE p.permission_name IN (
  'tasks.define',
  'tasks.assign',
  'tasks.delegate',
  'tasks.view_all',
  'tasks.update',
  'tasks.comment',
  'tasks.report'
);

-- Admin (role_id = 4) - ORGANIZATION MANAGEMENT
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 4, p.id FROM `permissions` p
WHERE p.permission_name IN (
  'tasks.assign',      -- Can assign to their team
  'tasks.delegate',    -- Can delegate to coordinators
  'tasks.view_all',    -- Views tasks for their cove
  'tasks.update',
  'tasks.comment',
  'tasks.report'       -- Reports for their cove
);

-- Coordinator (role_id = 3) - TASK EXECUTION
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 3, p.id FROM `permissions` p
WHERE p.permission_name IN (
  'tasks.view_own',    -- Only own tasks
  'tasks.update',
  'tasks.comment'
);

-- User (role_id = 2) - BASIC ACCESS
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 2, p.id FROM `permissions` p
WHERE p.permission_name IN (
  'tasks.view_own',
  'tasks.update',
  'tasks.comment'
);
```

---

## Multi-Tenant Isolation

### Query Filtering Strategy

#### SuperAdmin Queries (System-Wide)
```php
// SuperAdmin sees EVERYTHING
public function getAllForSuperAdmin($filters = []) {
    $sql = "
        SELECT * FROM view_admin_tasks
        WHERE 1=1
    ";

    // Optional filters
    if (!empty($filters['cove_id'])) {
        $sql .= " AND cove_id = :cove_id";
    }
    if (!empty($filters['status'])) {
        $sql .= " AND status = :status";
    }

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($filters);
    return $stmt->fetchAll();
}
```

#### Admin Queries (Cove-Specific)
```php
// Admin sees only their cove's tasks + tasks assigned TO them
public function getAllForAdmin($userId, $coveId, $filters = []) {
    $sql = "
        SELECT * FROM view_admin_tasks
        WHERE (cove_id = :cove_id OR cove_id IS NULL OR assigned_to = :user_id)
    ";

    $params = [
        'cove_id' => $coveId,
        'user_id' => $userId
    ];

    // Additional filters...

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(array_merge($params, $filters));
    return $stmt->fetchAll();
}
```

#### Coordinator/User Queries (Own Tasks Only)
```php
// Coordinator/User sees ONLY tasks assigned to them
public function getMyTasks($userId, $filters = []) {
    $sql = "
        SELECT * FROM view_admin_tasks
        WHERE assigned_to = :user_id
    ";

    $params = ['user_id' => $userId];

    // Additional filters...

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(array_merge($params, $filters));
    return $stmt->fetchAll();
}
```

### Multi-Tenant Security Checks

```php
/**
 * Verify user has permission to view this task
 */
public function canViewTask($taskId, $userId, $userRole, $userCoveId) {
    $task = $this->getTaskById($taskId);

    if (!$task) return false;

    // SuperAdmin: Can view ANY task
    if ($userRole === 'SuperAdmin') {
        return true;
    }

    // Admin: Can view if task is for their cove OR assigned to them
    if ($userRole === 'Admin') {
        return ($task['cove_id'] === $userCoveId)
            || ($task['cove_id'] === null)
            || ($task['assigned_to'] === $userId);
    }

    // Coordinator/User: Can view only if assigned to them
    return $task['assigned_to'] === $userId;
}

/**
 * Verify user can assign tasks to target user
 */
public function canAssignTo($fromUserId, $fromUserRole, $fromCoveId, $toUserId) {
    // SuperAdmin: Can assign to ANYONE
    if ($fromUserRole === 'SuperAdmin') {
        return true;
    }

    // Admin: Can assign only to users in their cove
    if ($fromUserRole === 'Admin') {
        $targetUser = $this->userModel->getUserById($toUserId);
        return $targetUser && $targetUser->getCove()->getId() === $fromCoveId;
    }

    // Others: Cannot assign
    return false;
}
```

---

## Workflows by Role

### SuperAdmin Workflow: Ministry-Wide Task Distribution

```
Scenario: Ministry requires all coves to update strategic aims by Q4

1. SuperAdmin (e.g., admin_gazi) creates task definition:
   ├── Task Name: "Q4 Strategic Aim Update"
   ├── Task Type: strategic
   ├── Category: aim_management
   ├── Template Data: {
   │     "checklist": ["Review current aims", "Update objectives", "Submit Q4 plan"],
   │     "required_fields": ["aim_title", "target_date", "responsible_person"]
   │   }
   └── Created By: admin_gazi (ID: 45)

2. SuperAdmin assigns task to ALL regional Admins:
   ├── Assign to: Admin of Ankara Cove (cove_id: 1)
   ├── Assign to: Admin of İstanbul Cove (cove_id: 2)
   ├── Assign to: Admin of İzmir Cove (cove_id: 3)
   ├── ... (all coves)
   ├── Priority: high
   ├── Due Date: 2025-12-31
   └── Notes: "Ministry directive - all coves must comply"

3. SuperAdmin monitors progress:
   ├── Dashboard shows: 45/60 coves completed (75%)
   ├── Overdue alerts: 3 coves
   ├── View detailed reports per cove
   └── Send reminders to delayed coves

4. SuperAdmin generates final report:
   └── Export completion data for ministry submission
```

### Admin Workflow: Regional Coordination

```
Scenario: Admin receives task from SuperAdmin

1. Admin (e.g., test_admin for Ankara) logs in:
   └── Notification: "1 new high-priority task from admin_gazi"

2. Admin reviews task:
   ├── Task: "Q4 Strategic Aim Update"
   ├── Due: December 31, 2025
   ├── Checklist: 3 items to complete
   └── Click "Accept Task"

3. Admin delegates to team:
   ├── Delegate to: adindar_ankara (Coordinator)
   │   └── Delegation notes: "Please review aims for your region"
   ├── Delegate to: coordinator2 (Coordinator)
   └── Delegate to: coordinator3 (Coordinator)

4. Admin tracks delegation progress:
   ├── adindar_ankara: 100% complete ✅
   ├── coordinator2: 60% in progress ⏳
   ├── coordinator3: 0% pending ❌
   └── Send reminder to coordinator3

5. Admin compiles results:
   ├── Review all delegation completions
   ├── Aggregate data into single report
   ├── Update main task progress: 80%
   └── Mark original assignment as "Completed"

6. SuperAdmin receives completion notification:
   └── Ankara cove task completed ✅
```

### Coordinator Workflow: Task Execution

```
Scenario: Coordinator executes delegated work

1. Coordinator (e.g., adindar_ankara) logs in:
   └── Dashboard shows: "1 task delegated by test_admin"

2. Coordinator reviews delegation:
   ├── Task: "Q4 Strategic Aim Update - Ankara Region"
   ├── Checklist: ["Review aims", "Update objectives", "Submit plan"]
   ├── Required fields: aim_title, target_date, responsible_person
   └── Click "Accept"

3. Coordinator starts work:
   ├── Click "Start Work" → Status: in_progress
   ├── Update progress: 25% → "Aims reviewed"
   ├── Add comment: "Current aims are aligned with ministry goals"
   └── Upload supporting document: "ankara_aims_Q4.docx"

4. Coordinator continues progress:
   ├── Update progress: 50% → "Objectives updated"
   ├── Update progress: 75% → "Q4 plan drafted"
   └── Add public comment: "Plan ready for review"

5. Coordinator completes task:
   ├── Update progress: 100%
   ├── Completion notes: "All aims updated per checklist"
   ├── Upload final document: "ankara_q4_final.pdf"
   └── Click "Mark Complete"

6. Notifications sent:
   ├── To Admin (test_admin): "adindar_ankara completed delegation"
   └── To SuperAdmin (if watching): "Ankara region completed aims"
```

---

## Implementation Guide

### Phase 1: Database Migration (Week 1, Day 1-2)

#### Step 1: Update Migration SQL

Create: `database/migrations/001_create_task_system_universal.sql`

```sql
-- Universal Task Management System
-- Works for ALL SuperAdmins (not hardcoded to specific user)

USE fg5085y3xu1ag48qw;

-- Drop existing tables if upgrading from old design
-- DROP TABLE IF EXISTS task_comments;
-- DROP TABLE IF EXISTS task_tracking;
-- DROP TABLE IF EXISTS task_delegations;
-- DROP TABLE IF EXISTS task_assignments;
-- DROP TABLE IF EXISTS task_definitions;
-- DROP TABLE IF EXISTS user_widget_preferences;

-- 1. Task Definitions
CREATE TABLE IF NOT EXISTS `task_definitions` (
  -- ... (full SQL from schema section above)
);

-- 2. Task Assignments
CREATE TABLE IF NOT EXISTS `task_assignments` (
  -- ... (full SQL from schema section above)
);

-- 3-6. Other tables...
-- ... (copy from schema section)

-- Permissions
INSERT IGNORE INTO `permissions` (`permission_name`, `description`) VALUES
('tasks.define', 'Create and manage task definitions'),
('tasks.assign', 'Assign tasks to users'),
('tasks.delegate', 'Delegate assigned tasks'),
('tasks.view_all', 'View all tasks (filtered by role)'),
('tasks.view_own', 'View own assigned tasks'),
('tasks.update', 'Update task status and progress'),
('tasks.comment', 'Add comments to tasks'),
('tasks.report', 'Generate task reports');

-- SuperAdmin permissions
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 5, p.id FROM `permissions` p
WHERE p.permission_name IN ('tasks.define', 'tasks.assign', 'tasks.delegate',
                              'tasks.view_all', 'tasks.update', 'tasks.comment', 'tasks.report');

-- Admin permissions
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 4, p.id FROM `permissions` p
WHERE p.permission_name IN ('tasks.assign', 'tasks.delegate', 'tasks.view_all',
                              'tasks.update', 'tasks.comment', 'tasks.report');

-- Coordinator permissions
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 3, p.id FROM `permissions` p
WHERE p.permission_name IN ('tasks.view_own', 'tasks.update', 'tasks.comment');

-- User permissions
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 2, p.id FROM `permissions` p
WHERE p.permission_name IN ('tasks.view_own', 'tasks.update', 'tasks.comment');

-- Sample task definitions (for ANY SuperAdmin to use as examples)
-- Note: Replace created_by with actual SuperAdmin IDs in your system
INSERT IGNORE INTO `task_definitions`
(`task_name`, `task_description`, `task_type`, `task_category`, `created_by`) VALUES
('Amaç Güncelleme Görevi', 'Stratejik amaçları gözden geçirin ve güncelleyin', 'strategic', 'aim_management', 1),
('Gösterge Veri Girişi', 'Aylık performans göstergelerini sisteme girin', 'operational', 'indicator_update', 1),
('Faaliyet Planı Raporu', 'Çeyreklik faaliyet planı raporunu hazırlayın', 'reporting', 'report_generation', 1),
('Kullanıcı Bilgi Kontrolü', 'Kullanıcı bilgilerini kontrol edin ve güncelleyin', 'operational', 'user_management', 1);

-- Triggers
DELIMITER //

CREATE TRIGGER IF NOT EXISTS `tr_task_assignments_update_timestamp`
BEFORE UPDATE ON `task_assignments`
FOR EACH ROW
BEGIN
    IF NEW.status = 'accepted' AND OLD.status != 'accepted' THEN
        SET NEW.accepted_at = NOW();
    END IF;
    IF NEW.status = 'completed' AND OLD.status != 'completed' THEN
        SET NEW.completed_at = NOW();
    END IF;
END//

CREATE TRIGGER IF NOT EXISTS `tr_task_delegations_update_timestamp`
BEFORE UPDATE ON `task_delegations`
FOR EACH ROW
BEGIN
    IF NEW.status = 'accepted' AND OLD.status != 'accepted' THEN
        SET NEW.accepted_at = NOW();
    END IF;
    IF NEW.status = 'completed' AND OLD.status != 'completed' THEN
        SET NEW.completed_at = NOW();
    END IF;
END//

DELIMITER ;

-- Views for reporting
CREATE OR REPLACE VIEW `view_superadmin_dashboard` AS
SELECT
    td.task_type,
    COUNT(DISTINCT td.id) as total_definitions,
    COUNT(DISTINCT ta.id) as total_assignments,
    SUM(CASE WHEN ta.status = 'completed' THEN 1 ELSE 0 END) as completed_assignments,
    SUM(CASE WHEN ta.status = 'in_progress' THEN 1 ELSE 0 END) as in_progress_assignments,
    SUM(CASE WHEN ta.due_date < CURDATE() AND ta.status NOT IN ('completed', 'cancelled') THEN 1 ELSE 0 END) as overdue_assignments,
    ROUND(AVG(CASE WHEN ta.status = 'completed' AND ta.completed_at IS NOT NULL
        THEN DATEDIFF(ta.completed_at, ta.assigned_at) END), 1) as avg_completion_days
FROM task_definitions td
LEFT JOIN task_assignments ta ON ta.task_definition_id = td.id
WHERE td.is_active = 1
GROUP BY td.task_type;

CREATE OR REPLACE VIEW `view_admin_tasks` AS
SELECT
    ta.*,
    td.task_name, td.task_description, td.task_type, td.task_category,
    u_assigned_by.realname as assigned_by_name,
    u_assigned_to.realname as assigned_to_name,
    c.name as cove_name,
    DATEDIFF(CURDATE(), ta.due_date) as days_overdue,
    (SELECT COUNT(*) FROM task_delegations WHERE task_assignment_id = ta.id) as delegation_count
FROM task_assignments ta
JOIN task_definitions td ON td.id = ta.task_definition_id
JOIN users u_assigned_by ON u_assigned_by.id = ta.assigned_by
JOIN users u_assigned_to ON u_assigned_to.id = ta.assigned_to
LEFT JOIN coves c ON c.id = ta.cove_id
WHERE td.is_active = 1;

-- Success message
SELECT
    'Universal Task Management System migration completed!' as message,
    (SELECT COUNT(*) FROM task_definitions) as total_definitions,
    (SELECT COUNT(*) FROM task_assignments) as total_assignments,
    (SELECT COUNT(DISTINCT u.id) FROM users u JOIN user_roles ur ON u.id = ur.userId WHERE ur.roleId = 5) as total_superadmins,
    (SELECT COUNT(*) FROM permissions WHERE permission_name LIKE 'tasks.%') as task_permissions;
```

#### Step 2: Run Migration

```bash
# Connect to MySQL
mysql -u fg508_5Y3XU1aGwa -p fg5085y3xu1ag48qw < database/migrations/001_create_task_system_universal.sql

# Or via phpMyAdmin: Import the SQL file
```

### Phase 2: Backend Implementation (Week 1, Day 3-5)

#### Files to Create:

**Entities:**
- `app/entities/TaskDefinition.php`
- `app/entities/TaskAssignment.php`
- `app/entities/TaskDelegation.php`
- `app/entities/TaskComment.php`

**Models:**
- `app/models/TaskDefinitionModel.php`
- `app/models/TaskAssignmentModel.php`
- `app/models/TaskDelegationModel.php`
- `app/models/TaskCommentModel.php`
- `app/models/TaskTrackingModel.php`

**Controllers:**
- `app/controllers/TaskController.php`

(See implementation code examples in previous design document)

### Phase 3: Frontend Implementation (Week 2)

**Views to Create:**
- `app/views/task/superadmin_dashboard.php`
- `app/views/task/admin_dashboard.php`
- `app/views/task/coordinator_dashboard.php`
- `app/views/task/create_definition.php`
- `app/views/task/assign_form.php`
- `app/views/task/my_tasks.php`
- `app/views/task/task_detail.php`
- `app/views/task/delegate_form.php`

### Phase 4: Testing (Week 3)

**Test Scenarios:**
1. SuperAdmin creates definition and assigns to multiple coves
2. Admin in Cove A cannot see tasks from Cove B
3. Coordinator can only see delegated tasks
4. Task lifecycle: pending → accepted → in_progress → completed
5. Rejection flow with reasons
6. Multi-tenant isolation verification

---

## Migration Strategy

### Upgrading from Old Design (admin_gazi-specific)

If you already ran the old migration:

```sql
-- Step 1: Backup existing data
CREATE TABLE task_definitions_backup AS SELECT * FROM task_definitions;
CREATE TABLE task_assignments_backup AS SELECT * FROM task_assignments;

-- Step 2: Update created_by if needed (if all were created by admin_gazi)
-- No changes needed - old data still works

-- Step 3: Update permissions to use new permission names
-- Already done in universal migration

-- Step 4: Test with multiple SuperAdmins
-- Each SuperAdmin can now create their own definitions
```

### Fresh Installation

Simply run:
```bash
mysql -u username -p database < 001_create_task_system_universal.sql
```

---

## Summary of Universal Design

### Key Differences from Previous Design

| Aspect | Old Design | Universal Design |
|--------|-----------|------------------|
| **Target User** | Hardcoded to admin_gazi (ID: 45) | Works for ALL 12 SuperAdmins |
| **Sample Data** | Only for admin_gazi | Generic templates for all |
| **Multi-Tenant** | Implied but not enforced | Strict cove_id filtering |
| **Scalability** | Single SuperAdmin | Unlimited SuperAdmins |
| **Permissions** | Basic | Role-based with view_own/view_all |
| **Security** | Basic checks | Full multi-tenant isolation |

### Benefits

✅ **Scalable:** Supports all 12 SuperAdmins + future additions
✅ **Secure:** Complete organizational isolation
✅ **Flexible:** Each SuperAdmin manages their own workflows
✅ **Maintainable:** No hardcoded user IDs
✅ **Professional:** Enterprise-grade multi-tenant architecture

---

**Document Version:** 2.0 (Universal)
**Last Updated:** October 3, 2025
**Supersedes:** TASK_MANAGEMENT_SYSTEM_DESIGN.md v1.0
**Status:** Ready for Implementation
**Next Step:** Run universal migration SQL
