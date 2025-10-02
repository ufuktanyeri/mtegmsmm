# Task Management System - Role-Based Design Document

**Project:** MTEGM SMM Portal
**Feature:** Role-Based Task Management System
**Target User:** admin_gazi (ID: 45) - SuperAdmin
**Date:** October 3, 2025
**Status:** Design Phase

---

## Table of Contents
1. [System Overview](#system-overview)
2. [Role-Based Architecture](#role-based-architecture)
3. [Database Schema](#database-schema)
4. [User Workflows](#user-workflows)
5. [Implementation Plan](#implementation-plan)
6. [Security & Permissions](#security--permissions)
7. [UI/UX Design](#uiux-design)

---

## System Overview

### Purpose
Create a comprehensive task management system that allows **SuperAdmins** to define, assign, and track tasks across the entire MTEGM SMM network, with role-based delegation capabilities.

### Key Features
- ✅ Task definition templates (reusable task types)
- ✅ Role-based task assignment (SuperAdmin → Admin → Coordinator → User)
- ✅ Task delegation chain (Admin can delegate to Coordinators)
- ✅ Progress tracking with percentage completion
- ✅ Multi-level commenting system
- ✅ Priority levels (low, medium, high, critical)
- ✅ Due date management with overdue alerts
- ✅ Comprehensive audit trail
- ✅ Dashboard widgets for each role

### Database Tables (Already Migrated)
1. `task_definitions` - Reusable task templates
2. `task_assignments` - Direct task assignments
3. `task_delegations` - Delegation chain tracking
4. `task_tracking` - Complete audit log
5. `task_comments` - Task communication
6. `user_widget_preferences` - Dashboard customization

---

## Role-Based Architecture

### Role Hierarchy

```
┌─────────────────────────────────────────────┐
│         SuperAdmin (admin_gazi)              │
│  - Creates task definitions                  │
│  - Assigns to ANY user                       │
│  - Views ALL tasks system-wide               │
│  - Generates reports                         │
└───────────────────┬─────────────────────────┘
                    │ assigns
                    ▼
┌─────────────────────────────────────────────┐
│         Admin (test_admin - ID: 53)          │
│  - Receives tasks from SuperAdmin            │
│  - Can delegate to Coordinators              │
│  - Views tasks for their cove                │
│  - Tracks delegation progress                │
└───────────────────┬─────────────────────────┘
                    │ delegates
                    ▼
┌─────────────────────────────────────────────┐
│    Coordinator (adindar_ankara - ID: 25)     │
│  - Receives delegated tasks                  │
│  - Completes assigned work                   │
│  - Updates progress                          │
│  - Views own tasks only                      │
└───────────────────┬─────────────────────────┘
                    │ collaborates with
                    ▼
┌─────────────────────────────────────────────┐
│              User (Regular)                  │
│  - Views assigned tasks                      │
│  - Updates progress                          │
│  - Completes work                            │
│  - Limited visibility                        │
└─────────────────────────────────────────────┘
```

### Permission Matrix

| Action | SuperAdmin | Admin | Coordinator | User |
|--------|------------|-------|-------------|------|
| **Define Tasks** | ✅ | ❌ | ❌ | ❌ |
| **Assign Tasks** | ✅ | ✅ (to Coordinators) | ❌ | ❌ |
| **Delegate Tasks** | ✅ | ✅ | ❌ | ❌ |
| **View All Tasks** | ✅ | ✅ (own cove) | ❌ | ❌ |
| **Accept/Reject Tasks** | N/A | ✅ | ✅ | ✅ |
| **Update Progress** | ✅ | ✅ | ✅ | ✅ |
| **Complete Tasks** | ✅ | ✅ | ✅ | ✅ |
| **View Reports** | ✅ | ✅ (own cove) | ❌ | ❌ |
| **Comment (Internal)** | ✅ | ✅ | ❌ | ❌ |
| **Comment (Public)** | ✅ | ✅ | ✅ | ✅ |

### Permissions in Database

```sql
-- Already defined in migration:
'tasks.define'      → SuperAdmin only
'tasks.assign'      → SuperAdmin, Admin
'tasks.delegate'    → SuperAdmin, Admin
'tasks.view_all'    → SuperAdmin, Admin, Coordinator
'tasks.report'      → SuperAdmin, Admin
```

---

## Database Schema

### 1. Task Definitions (Templates)

```sql
task_definitions
├── id (PK)
├── task_name              -- "Amaç Güncelleme Görevi"
├── task_description       -- Detailed instructions
├── task_type              -- strategic|operational|reporting|urgent
├── task_category          -- aim_management|objective_setting|etc.
├── template_data (JSON)   -- Form fields, checklist items
├── is_active              -- Enable/disable template
├── created_by (FK)        -- SuperAdmin who created it
├── created_at
└── updated_at
```

**Task Types:**
- `strategic` - Long-term planning tasks
- `operational` - Day-to-day operations
- `reporting` - Data collection and reporting
- `urgent` - Critical/time-sensitive

**Task Categories:**
- `aim_management` - Strategic aims
- `objective_setting` - Goal definition
- `indicator_update` - Performance metrics
- `action_planning` - Action item creation
- `report_generation` - Report preparation
- `system_maintenance` - Technical tasks
- `user_management` - User administration
- `data_entry` - Data input tasks

### 2. Task Assignments

```sql
task_assignments
├── id (PK)
├── task_definition_id (FK) -- Which template is used
├── assigned_by (FK)         -- SuperAdmin/Admin who assigned
├── assigned_to (FK)         -- Target user
├── cove_id (FK, nullable)   -- Specific cove or NULL for all
├── status                   -- pending|accepted|in_progress|completed|rejected|cancelled
├── priority                 -- low|medium|high|critical
├── due_date                 -- Deadline
├── assigned_at
├── accepted_at (nullable)
├── completed_at (nullable)
├── rejection_reason (TEXT)
├── completion_data (JSON)   -- Results, attachments, etc.
├── notes (TEXT)
└── progress_percentage      -- 0-100
```

**Status Flow:**
```
pending → accepted → in_progress → completed
    ↓         ↓
rejected  cancelled
```

### 3. Task Delegations

```sql
task_delegations
├── id (PK)
├── task_assignment_id (FK)  -- Original assignment
├── delegated_by (FK)         -- Admin delegating
├── delegated_to (FK)         -- Coordinator receiving
├── delegation_notes (TEXT)
├── status                    -- pending|accepted|in_progress|completed|rejected
├── delegated_at
├── accepted_at (nullable)
├── completed_at (nullable)
├── progress_percentage       -- 0-100
└── completion_notes (TEXT)
```

**Delegation Chain Example:**
```
SuperAdmin (admin_gazi)
    → assigns to → Admin (test_admin)
        → delegates to → Coordinator (adindar_ankara)
```

### 4. Task Tracking (Audit Log)

```sql
task_tracking
├── id (PK)
├── task_assignment_id (FK, nullable)
├── task_delegation_id (FK, nullable)
├── user_id (FK)              -- Who performed action
├── action                    -- viewed|accepted|started|progress_update|completed|rejected|delegated|cancelled
├── action_data (JSON)        -- Additional context
├── ip_address
├── user_agent
└── tracked_at
```

**Actions Tracked:**
- `viewed` - User opened the task
- `accepted` - User accepted responsibility
- `started` - Work began
- `progress_update` - Progress % changed
- `completed` - Task finished
- `rejected` - Task declined
- `delegated` - Task passed to another user
- `cancelled` - Task aborted

### 5. Task Comments

```sql
task_comments
├── id (PK)
├── task_assignment_id (FK, nullable)
├── task_delegation_id (FK, nullable)
├── user_id (FK)
├── comment (TEXT)
├── is_internal               -- TRUE = admin-only, FALSE = visible to all
├── created_at
└── updated_at
```

### 6. User Widget Preferences

```sql
user_widget_preferences
├── id (PK)
├── user_id (FK)
├── dashboard_id              -- 'superadmin_main'|'admin_main'|'coordinator_main'
├── widget_config (JSON)      -- Dashboard layout
├── created_at
└── updated_at
```

**Widget Types:**
- `task_summary` - Overview statistics
- `assigned_tasks` - Tasks I've assigned
- `my_tasks` - Tasks assigned to me
- `team_performance` - Team metrics
- `calendar` - Due date calendar
- `performance_overview` - System-wide stats

---

## User Workflows

### Workflow 1: SuperAdmin Creates and Assigns Task

```
1. SuperAdmin (admin_gazi) logs in
   └─> Dashboard shows: task_summary, performance_overview widgets

2. Navigate to "Task Management" → "Create New Task"
   └─> Form fields:
       - Task Name: "Q1 Strategic Planning Review"
       - Description: "Review and update Q1 strategic plans"
       - Task Type: strategic
       - Category: aim_management
       - Template Data (JSON): {
           "checklist": ["Review aims", "Update objectives", "Set KPIs"],
           "required_documents": ["Q1 Plan.docx", "Budget.xlsx"]
         }

3. Click "Create Task Definition"
   └─> Saved to task_definitions table
   └─> Success message: "Task template created"

4. Navigate to "Task Management" → "Assign Tasks"
   └─> Select task definition: "Q1 Strategic Planning Review"
   └─> Assign to:
       - User: test_admin (Admin) - ID: 53
       - Cove: NULL (applies to all coves)
       - Priority: high
       - Due Date: 2025-11-02 (30 days from now)
       - Notes: "Please coordinate with all regional teams"

5. Click "Assign Task"
   └─> INSERT into task_assignments
   └─> INSERT into task_tracking (action: 'assigned')
   └─> Email notification sent to test_admin
   └─> Success message: "Task assigned to test_admin"

6. Monitor progress on dashboard
   └─> View task status changes in real-time
   └─> Check audit log in task_tracking
```

### Workflow 2: Admin Receives and Delegates Task

```
1. Admin (test_admin) logs in
   └─> Dashboard shows: assigned_tasks, team_performance widgets
   └─> Notification: "1 new task assigned to you"

2. Navigate to "My Tasks" → View task details
   └─> See task: "Q1 Strategic Planning Review"
   └─> Status: pending
   └─> Priority: high (RED badge)
   └─> Due: 2025-11-02 (30 days remaining)

3. Review task requirements
   └─> Read description
   └─> View checklist items
   └─> Download required documents

4. Decide to accept task
   └─> Click "Accept Task"
   └─> UPDATE task_assignments SET status='accepted', accepted_at=NOW()
   └─> INSERT into task_tracking (action: 'accepted')

5. Delegate to Coordinator
   └─> Click "Delegate Task"
   └─> Select user: adindar_ankara (Coordinator) - ID: 25
   └─> Select cove: Ankara SMM (cove_id: 4)
   └─> Delegation notes: "Please focus on Ankara region aims"
   └─> Click "Delegate"
   └─> INSERT into task_delegations
   └─> INSERT into task_tracking (action: 'delegated')
   └─> Email notification sent to adindar_ankara

6. Monitor delegation progress
   └─> View delegation status
   └─> Add internal comments (visible to SuperAdmin only)
   └─> Update overall task progress based on delegation completion
```

### Workflow 3: Coordinator Completes Delegated Task

```
1. Coordinator (adindar_ankara) logs in
   └─> Dashboard shows: my_tasks, calendar widgets
   └─> Notification: "1 task delegated to you"

2. View delegated task details
   └─> See: "Q1 Strategic Planning Review - Ankara Region"
   └─> Status: pending
   └─> Priority: high
   └─> Due: 2025-11-02
   └─> Delegation notes from Admin

3. Accept delegation
   └─> Click "Accept"
   └─> UPDATE task_delegations SET status='accepted', accepted_at=NOW()
   └─> INSERT into task_tracking (action: 'accepted')

4. Start working on task
   └─> Click "Start Work"
   └─> UPDATE task_delegations SET status='in_progress'
   └─> INSERT into task_tracking (action: 'started')

5. Update progress periodically
   └─> Progress: 25% → "Aims reviewed"
       └─> INSERT into task_tracking (action: 'progress_update', action_data: '{"progress": 25}')
   └─> Progress: 50% → "Objectives updated"
   └─> Progress: 75% → "KPIs set"
   └─> Add public comment: "Ankara aims updated per Q1 guidelines"

6. Complete task
   └─> Progress: 100%
   └─> Upload completion documents
   └─> Completion notes: "All Ankara aims reviewed and updated"
   └─> Click "Mark Complete"
   └─> UPDATE task_delegations SET status='completed', completed_at=NOW(), progress_percentage=100
   └─> UPDATE task_assignments SET progress_percentage = (avg of all delegations)
   └─> INSERT into task_tracking (action: 'completed')
   └─> Notification sent to Admin (test_admin)

7. Admin reviews completion
   └─> View completed delegation
   └─> If satisfied: Mark original assignment as completed
   └─> Notification sent to SuperAdmin (admin_gazi)
```

### Workflow 4: Rejection Scenario

```
1. User receives task assignment
2. Reviews requirements
3. Realizes cannot complete (lack of resources, wrong assignee, etc.)
4. Click "Reject Task"
5. Modal opens: "Reason for rejection?"
   └─> Text area: "I don't have access to the required documents"
6. Click "Confirm Rejection"
   └─> UPDATE task_assignments SET status='rejected', rejection_reason='...'
   └─> INSERT into task_tracking (action: 'rejected')
   └─> Email notification to SuperAdmin/Admin
7. SuperAdmin/Admin reviews rejection
   └─> Can reassign to different user
   └─> Can provide additional resources
   └─> Can cancel task if inappropriate
```

---

## Implementation Plan

### Phase 1: Backend Models & Controllers (Week 1)

#### 1.1 Create Entity Classes

**File:** `app/entities/TaskDefinition.php`
```php
<?php
class TaskDefinition {
    private $id;
    private $taskName;
    private $taskDescription;
    private $taskType;
    private $taskCategory;
    private $templateData; // JSON decoded array
    private $isActive;
    private $createdBy;
    private $createdAt;
    private $updatedAt;

    // Getters and setters
}
```

**File:** `app/entities/TaskAssignment.php`
```php
<?php
class TaskAssignment {
    private $id;
    private $taskDefinitionId;
    private $taskDefinition; // TaskDefinition object
    private $assignedBy;
    private $assignedByUser; // User object
    private $assignedTo;
    private $assignedToUser; // User object
    private $coveId;
    private $cove; // Cove object
    private $status;
    private $priority;
    private $dueDate;
    private $progressPercentage;
    // ... timestamps, notes, etc.
}
```

**File:** `app/entities/TaskDelegation.php`
```php
<?php
class TaskDelegation {
    private $id;
    private $taskAssignmentId;
    private $taskAssignment; // TaskAssignment object
    private $delegatedBy;
    private $delegatedByUser; // User object
    private $delegatedTo;
    private $delegatedToUser; // User object
    private $status;
    private $progressPercentage;
    // ... timestamps, notes
}
```

#### 1.2 Create Model Classes

**File:** `app/models/TaskDefinitionModel.php`
```php
<?php
require_once APP_PATH . 'models/BaseModel.php';
require_once APP_PATH . 'entities/TaskDefinition.php';

class TaskDefinitionModel extends BaseModel {

    public function create($taskName, $taskDescription, $taskType, $taskCategory, $templateData, $createdBy) {
        $stmt = $this->pdo->prepare("
            INSERT INTO task_definitions
            (task_name, task_description, task_type, task_category, template_data, created_by)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $taskName,
            $taskDescription,
            $taskType,
            $taskCategory,
            json_encode($templateData),
            $createdBy
        ]);
        return $this->pdo->lastInsertId();
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM task_definitions WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        if (!$data) return null;

        return $this->hydrateEntity($data);
    }

    public function getAllActive() {
        $stmt = $this->pdo->query("
            SELECT * FROM task_definitions
            WHERE is_active = 1
            ORDER BY created_at DESC
        ");
        return array_map([$this, 'hydrateEntity'], $stmt->fetchAll());
    }

    public function getByType($taskType) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM task_definitions
            WHERE task_type = ? AND is_active = 1
            ORDER BY task_name
        ");
        $stmt->execute([$taskType]);
        return array_map([$this, 'hydrateEntity'], $stmt->fetchAll());
    }

    private function hydrateEntity($data) {
        $task = new TaskDefinition();
        $task->setId($data['id']);
        $task->setTaskName($data['task_name']);
        $task->setTaskDescription($data['task_description']);
        $task->setTaskType($data['task_type']);
        $task->setTaskCategory($data['task_category']);
        $task->setTemplateData(json_decode($data['template_data'], true));
        $task->setIsActive($data['is_active']);
        $task->setCreatedBy($data['created_by']);
        $task->setCreatedAt($data['created_at']);
        $task->setUpdatedAt($data['updated_at']);
        return $task;
    }
}
```

**File:** `app/models/TaskAssignmentModel.php`
```php
<?php
require_once APP_PATH . 'models/BaseModel.php';
require_once APP_PATH . 'entities/TaskAssignment.php';
require_once APP_PATH . 'models/TaskDefinitionModel.php';
require_once APP_PATH . 'models/UserModel.php';
require_once APP_PATH . 'models/CoveModel.php';

class TaskAssignmentModel extends BaseModel {

    public function assign($taskDefinitionId, $assignedBy, $assignedTo, $coveId, $priority, $dueDate, $notes) {
        $stmt = $this->pdo->prepare("
            INSERT INTO task_assignments
            (task_definition_id, assigned_by, assigned_to, cove_id, priority, due_date, notes)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $taskDefinitionId, $assignedBy, $assignedTo,
            $coveId, $priority, $dueDate, $notes
        ]);

        $assignmentId = $this->pdo->lastInsertId();

        // Log tracking event
        $this->logTracking($assignmentId, null, $assignedBy, 'assigned');

        return $assignmentId;
    }

    public function updateStatus($assignmentId, $userId, $status, $additionalData = []) {
        $stmt = $this->pdo->prepare("
            UPDATE task_assignments
            SET status = ?
            WHERE id = ?
        ");
        $stmt->execute([$status, $assignmentId]);

        // Log tracking
        $this->logTracking($assignmentId, null, $userId, $status, $additionalData);
    }

    public function updateProgress($assignmentId, $userId, $progressPercentage) {
        $stmt = $this->pdo->prepare("
            UPDATE task_assignments
            SET progress_percentage = ?
            WHERE id = ?
        ");
        $stmt->execute([$progressPercentage, $assignmentId]);

        // Log tracking
        $this->logTracking($assignmentId, null, $userId, 'progress_update', [
            'progress' => $progressPercentage
        ]);
    }

    public function getByUser($userId, $status = null) {
        $sql = "
            SELECT ta.*,
                   td.task_name, td.task_description, td.task_type, td.task_category,
                   u_by.realname as assigned_by_name,
                   u_to.realname as assigned_to_name,
                   c.name as cove_name
            FROM task_assignments ta
            JOIN task_definitions td ON td.id = ta.task_definition_id
            JOIN users u_by ON u_by.id = ta.assigned_by
            JOIN users u_to ON u_to.id = ta.assigned_to
            LEFT JOIN coves c ON c.id = ta.cove_id
            WHERE ta.assigned_to = ?
        ";

        $params = [$userId];

        if ($status) {
            $sql .= " AND ta.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY ta.due_date ASC, ta.priority DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function getAllForSuperAdmin($filters = []) {
        // SuperAdmin sees ALL tasks
        $sql = "SELECT * FROM view_admin_tasks WHERE 1=1";

        // Apply filters (status, priority, date range, etc.)
        // ... filter logic

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    private function logTracking($assignmentId, $delegationId, $userId, $action, $actionData = []) {
        $stmt = $this->pdo->prepare("
            INSERT INTO task_tracking
            (task_assignment_id, task_delegation_id, user_id, action, action_data, ip_address, user_agent)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $assignmentId,
            $delegationId,
            $userId,
            $action,
            json_encode($actionData),
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    }
}
```

**File:** `app/models/TaskDelegationModel.php` (Similar structure)

#### 1.3 Create TaskController

**File:** `app/controllers/TaskController.php`
```php
<?php
require_once APP_PATH . 'controllers/BaseController.php';
require_once APP_PATH . 'models/TaskDefinitionModel.php';
require_once APP_PATH . 'models/TaskAssignmentModel.php';
require_once APP_PATH . 'models/TaskDelegationModel.php';

class TaskController extends BaseController {

    private $taskDefinitionModel;
    private $taskAssignmentModel;
    private $taskDelegationModel;

    public function __construct() {
        parent::__construct();
        $this->taskDefinitionModel = new TaskDefinitionModel();
        $this->taskAssignmentModel = new TaskAssignmentModel();
        $this->taskDelegationModel = new TaskDelegationModel();
    }

    /**
     * Dashboard - shows role-appropriate task overview
     */
    public function index() {
        $this->checkPermission('tasks', 'view_all');

        $role = $_SESSION['role_name'] ?? 'User';

        if ($role === 'SuperAdmin') {
            return $this->superAdminDashboard();
        } elseif ($role === 'Admin') {
            return $this->adminDashboard();
        } elseif ($role === 'Coordinator') {
            return $this->coordinatorDashboard();
        } else {
            return $this->userDashboard();
        }
    }

    private function superAdminDashboard() {
        // Get all tasks system-wide
        $allTasks = $this->taskAssignmentModel->getAllForSuperAdmin();

        // Get statistics
        $stats = [
            'total_definitions' => $this->taskDefinitionModel->count(),
            'total_assignments' => count($allTasks),
            'pending' => count(array_filter($allTasks, fn($t) => $t['status'] === 'pending')),
            'in_progress' => count(array_filter($allTasks, fn($t) => $t['status'] === 'in_progress')),
            'completed' => count(array_filter($allTasks, fn($t) => $t['status'] === 'completed')),
            'overdue' => count(array_filter($allTasks, fn($t) =>
                $t['due_date'] < date('Y-m-d') && $t['status'] !== 'completed'
            ))
        ];

        $this->render('task/superadmin_dashboard', [
            'stats' => $stats,
            'recentTasks' => array_slice($allTasks, 0, 10)
        ], [
            'title' => 'Görev Yönetimi - SuperAdmin',
            'breadcrumbs' => [
                ['name' => 'Ana Sayfa', 'url' => 'index.php?url=home'],
                ['name' => 'Görev Yönetimi', 'url' => null]
            ]
        ]);
    }

    /**
     * Create task definition (SuperAdmin only)
     */
    public function createDefinition() {
        $this->checkPermission('tasks', 'define');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate and create
            $taskId = $this->taskDefinitionModel->create(
                $_POST['task_name'],
                $_POST['task_description'],
                $_POST['task_type'],
                $_POST['task_category'],
                json_decode($_POST['template_data'] ?? '{}', true),
                $_SESSION['user_id']
            );

            $this->redirect('task/assignForm?definition_id=' . $taskId, 'Görev tanımı oluşturuldu');
        }

        $this->render('task/create_definition', [], [
            'title' => 'Yeni Görev Tanımı Oluştur'
        ]);
    }

    /**
     * Assign task (SuperAdmin, Admin)
     */
    public function assign() {
        $this->checkPermission('tasks', 'assign');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->taskAssignmentModel->assign(
                $_POST['task_definition_id'],
                $_SESSION['user_id'],
                $_POST['assigned_to'],
                $_POST['cove_id'] ?? null,
                $_POST['priority'],
                $_POST['due_date'],
                $_POST['notes'] ?? ''
            );

            // Send notification email
            // $this->sendTaskNotification($_POST['assigned_to']);

            $this->redirect('task/index', 'Görev atandı');
        }

        $this->render('task/assign_form', [
            'definitions' => $this->taskDefinitionModel->getAllActive()
        ], [
            'title' => 'Görev Ata'
        ]);
    }

    /**
     * View my tasks
     */
    public function myTasks() {
        $userId = $_SESSION['user_id'];
        $tasks = $this->taskAssignmentModel->getByUser($userId);

        $this->render('task/my_tasks', [
            'tasks' => $tasks
        ], [
            'title' => 'Benim Görevlerim'
        ]);
    }

    /**
     * Accept task
     */
    public function accept($assignmentId) {
        // Verify user is assigned to this task
        $this->taskAssignmentModel->updateStatus(
            $assignmentId,
            $_SESSION['user_id'],
            'accepted'
        );

        $this->redirect('task/myTasks', 'Görev kabul edildi');
    }

    /**
     * Update progress
     */
    public function updateProgress() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->taskAssignmentModel->updateProgress(
                $_POST['assignment_id'],
                $_SESSION['user_id'],
                $_POST['progress_percentage']
            );

            echo json_encode(['success' => true]);
        }
    }

    // ... more methods: delegate, reject, complete, viewTracking, etc.
}
```

### Phase 2: Views & UI (Week 2)

#### 2.1 SuperAdmin Dashboard
- Overview widgets (total tasks, completion rate, overdue count)
- Quick actions (Create Definition, Assign Task, View Reports)
- Recent activity feed
- Charts (tasks by type, tasks by status, completion trends)

#### 2.2 Admin Dashboard
- My assigned tasks list
- Delegations overview
- Team performance metrics
- Quick delegate button

#### 2.3 Task List Views
- Filterable/sortable table
- Status badges (color-coded)
- Priority indicators
- Due date warnings
- Progress bars

#### 2.4 Task Detail View
- Full task information
- Timeline/activity log
- Comment section (internal/public tabs)
- Action buttons (Accept, Reject, Start, Update Progress, Complete, Delegate)

#### 2.5 Forms
- Create Definition form (with JSON editor for template_data)
- Assign Task form (user selector, date picker, priority dropdown)
- Delegate Task form (coordinator selector, delegation notes)
- Progress Update form (slider, notes)

### Phase 3: Testing & Deployment (Week 3)

#### 3.1 Database Migration
```bash
php database/migrations/001_create_task_system_admin_gazi.sql
```

#### 3.2 Testing Scenarios
- SuperAdmin creates definition and assigns to Admin
- Admin accepts and delegates to Coordinator
- Coordinator updates progress and completes
- Admin reviews and marks assignment complete
- SuperAdmin views reports

#### 3.3 Permission Testing
- Verify each role can only access allowed actions
- Test multi-tenant isolation (cove_id filtering)

---

## Security & Permissions

### Permission Checks in Controllers

```php
// SuperAdmin-only actions
$this->checkPermission('tasks', 'define');    // createDefinition()
$this->checkPermission('tasks', 'report');    // viewReports()

// Admin/SuperAdmin actions
$this->checkPermission('tasks', 'assign');    // assign()
$this->checkPermission('tasks', 'delegate');  // delegate()

// All authenticated users
$this->checkPermission('tasks', 'view_all');  // myTasks()
```

### Multi-Tenant Filtering

```php
// SuperAdmin: See ALL tasks
if ($_SESSION['role_name'] === 'SuperAdmin') {
    $tasks = $model->getAllTasks();
}

// Admin: See only their cove's tasks
elseif ($_SESSION['role_name'] === 'Admin') {
    $coveId = $_SESSION['cove_id'];
    $tasks = $model->getTasksByCove($coveId);
}

// Coordinator/User: See only assigned to them
else {
    $userId = $_SESSION['user_id'];
    $tasks = $model->getTasksByUser($userId);
}
```

### CSRF Protection

All forms include CSRF token:
```php
<input type="hidden" name="csrf_token" value="<?= $this->getCSRFToken() ?>">
```

Validated in controller:
```php
if (!$this->validateCSRFToken($_POST['csrf_token'])) {
    $this->handleError('Invalid CSRF token', 403);
}
```

---

## UI/UX Design

### Color Coding

**Priority Levels:**
- `low` - Blue badge
- `medium` - Yellow badge
- `high` - Orange badge
- `critical` - Red badge (pulsing animation)

**Status Colors:**
- `pending` - Gray
- `accepted` - Blue
- `in_progress` - Yellow
- `completed` - Green
- `rejected` - Red
- `cancelled` - Dark Gray

**Task Types:**
- `strategic` - Purple icon (🎯)
- `operational` - Blue icon (⚙️)
- `reporting` - Green icon (📊)
- `urgent` - Red icon (⚠️)

### Responsive Layout

```
Desktop (≥1024px):
┌─────────────────────────────────────┐
│          Navbar (Top)                │
├──────────┬──────────────────────────┤
│ Sidebar  │  Main Content             │
│ (Fixed)  │  - Widgets (3 columns)    │
│          │  - Task List (Table)      │
│          │  - Charts                 │
└──────────┴──────────────────────────┘

Tablet (768-1023px):
┌─────────────────────────────────────┐
│          Navbar (Top)                │
├──────────┬──────────────────────────┤
│ Sidebar  │  Main Content             │
│(Collapsible)  - Widgets (2 columns) │
└──────────┴──────────────────────────┘

Mobile (<768px):
┌─────────────────────────────────────┐
│    Navbar (Top with hamburger)      │
├─────────────────────────────────────┤
│          Main Content                │
│          - Widgets (1 column)        │
│          - Task Cards (stacked)      │
└─────────────────────────────────────┘
```

### Notification System

**Real-time Notifications** (via polling or WebSocket):
- New task assigned
- Task accepted by assignee
- Delegation completed
- Task overdue alert
- Comment added

**Email Notifications:**
- Task assignment email (with task details, link to view)
- Delegation email
- Completion notification
- Overdue reminder (daily digest)

---

## API Endpoints (Future Phase)

For mobile app or external integrations:

```
GET    /api/v1/tasks/definitions           - List task definitions
POST   /api/v1/tasks/definitions           - Create definition
GET    /api/v1/tasks/assignments           - List assignments
POST   /api/v1/tasks/assignments           - Create assignment
PATCH  /api/v1/tasks/assignments/{id}      - Update assignment
POST   /api/v1/tasks/delegations           - Create delegation
PATCH  /api/v1/tasks/assignments/{id}/progress - Update progress
POST   /api/v1/tasks/comments              - Add comment
GET    /api/v1/tasks/tracking/{id}         - Get audit log
```

---

## Summary

This role-based task management system provides:

✅ **Hierarchical task flow:** SuperAdmin → Admin → Coordinator → User
✅ **Complete audit trail:** Every action tracked with timestamps, IP, user agent
✅ **Flexible assignment:** Tasks can target specific coves or entire network
✅ **Delegation chain:** Admins can delegate to Coordinators for distributed work
✅ **Progress tracking:** Real-time progress updates with percentage completion
✅ **Communication:** Internal (admin-only) and public comments
✅ **Dashboard widgets:** Role-specific views with customizable layouts
✅ **Security:** Permission-based access, multi-tenant isolation, CSRF protection

**Next Steps:**
1. Run database migration
2. Implement models and controllers
3. Create views and UI components
4. Test with admin_gazi, test_admin, adindar_ankara
5. Deploy to production

---

**Document Version:** 1.0
**Last Updated:** October 3, 2025
**Author:** Claude Code
**Status:** Ready for Implementation
