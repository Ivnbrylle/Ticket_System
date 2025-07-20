<?php
require_once 'config.php';
requireAdmin();

$page_title = 'Auto Assign Tickets';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['assign_tickets'])) {
    // Get all open unassigned tickets
    $stmt = $pdo->query("
        SELECT ticket_id, topic 
        FROM tickets 
        WHERE status = 'Open' AND assigned_to IS NULL
        ORDER BY created_at ASC
    ");
    $unassigned_tickets = $stmt->fetchAll();
    
    $assigned_count = 0;
    $assignment_log = [];
    
    foreach ($unassigned_tickets as $ticket) {
        // Find employees with matching specialization
        $stmt = $pdo->prepare("
            SELECT employee_id, name, workload, specialization
            FROM employees 
            WHERE FIND_IN_SET(?, specialization) > 0 
            ORDER BY workload ASC, employee_id ASC
        ");
        $stmt->execute([$ticket['topic']]);
        $eligible_employees = $stmt->fetchAll();
        
        if (!empty($eligible_employees)) {
            // Assign to employee with least workload
            $assigned_employee = $eligible_employees[0];
            
            // Update ticket assignment
            $update_stmt = $pdo->prepare("
                UPDATE tickets 
                SET assigned_to = ?, status = 'In Progress' 
                WHERE ticket_id = ?
            ");
            
            if ($update_stmt->execute([$assigned_employee['employee_id'], $ticket['ticket_id']])) {
                // Update employee workload
                $workload_stmt = $pdo->prepare("
                    UPDATE employees 
                    SET workload = workload + 1 
                    WHERE employee_id = ?
                ");
                $workload_stmt->execute([$assigned_employee['employee_id']]);
                
                $assigned_count++;
                $assignment_log[] = [
                    'ticket_id' => $ticket['ticket_id'],
                    'employee_name' => $assigned_employee['name'],
                    'topic' => $ticket['topic']
                ];
            }
        }
    }
    
    if ($assigned_count > 0) {
        $success = "Successfully assigned $assigned_count tickets!";
        $_SESSION['assignment_log'] = $assignment_log;
    } else {
        $error = 'No tickets were assigned. Either no unassigned tickets exist or no employees have matching specializations.';
    }
}

// Get current assignment status
$stmt = $pdo->query("
    SELECT 
        COUNT(*) as total_unassigned
    FROM tickets 
    WHERE status = 'Open' AND assigned_to IS NULL
");
$unassigned_count = $stmt->fetch()['total_unassigned'];

// Get employee workloads
$stmt = $pdo->query("
    SELECT 
        e.employee_id,
        e.name,
        e.specialization,
        COUNT(t.ticket_id) as current_workload
    FROM employees e
    LEFT JOIN tickets t ON e.employee_id = t.assigned_to AND t.status != 'Closed'
    WHERE e.is_admin = 0
    GROUP BY e.employee_id, e.name, e.specialization
    ORDER BY current_workload ASC, e.name ASC
");
$employees = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="container-fluid">
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <!-- Assignment Statistics -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h3 class="mb-0"><?php echo $unassigned_count; ?></h3>
                    <p class="mb-0">Unassigned Open Tickets</p>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5>Auto Assignment Rules</h5>
                    <ul class="mb-0">
                        <li>Tickets are assigned to employees with matching specializations</li>
                        <li>Priority is given to employees with the least current workload</li>
                        <li>If multiple employees have the same workload, assignment is by employee ID</li>
                        <li>Assigned tickets automatically change status to "In Progress"</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Auto Assignment Button -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Automatic Ticket Assignment</h5>
                </div>
                <div class="card-body">
                    <?php if ($unassigned_count > 0): ?>
                        <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                            <p>There are <strong><?php echo $unassigned_count; ?></strong> unassigned open tickets ready for automatic assignment.</p>
                            <button type="submit" name="assign_tickets" class="btn btn-primary btn-lg">
                                <i class="fas fa-tasks me-2"></i>Auto Assign All Tickets
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-check-circle me-2"></i>
                            All open tickets are currently assigned. Great job!
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Assignment Results -->
    <?php if (isset($_SESSION['assignment_log']) && !empty($_SESSION['assignment_log'])): ?>
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Assignment Results</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Ticket ID</th>
                                    <th>Topic</th>
                                    <th>Assigned To</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($_SESSION['assignment_log'] as $assignment): ?>
                                <tr>
                                    <td>
                                        <a href="view_ticket.php?id=<?php echo urlencode($assignment['ticket_id']); ?>" class="text-decoration-none">
                                            <span class="ticket-id"><?php echo htmlspecialchars($assignment['ticket_id']); ?></span>
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?php echo htmlspecialchars($assignment['topic']); ?></span>
                                    </td>
                                    <td><?php echo htmlspecialchars($assignment['employee_name']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php unset($_SESSION['assignment_log']); ?>
    <?php endif; ?>

    <!-- Employee Workload Overview -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Employee Workload Overview</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Specializations</th>
                                    <th>Current Workload</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($employees as $employee): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($employee['name']); ?></td>
                                    <td>
                                        <?php 
                                        $specs = explode(',', $employee['specialization']);
                                        foreach ($specs as $spec) {
                                            echo '<span class="badge bg-secondary me-1">' . htmlspecialchars(trim($spec)) . '</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo $employee['current_workload'] == 0 ? 'success' : 
                                                ($employee['current_workload'] <= 2 ? 'warning' : 'danger'); 
                                        ?>">
                                            <?php echo $employee['current_workload']; ?> tickets
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($employee['current_workload'] == 0): ?>
                                            <span class="text-success">
                                                <i class="fas fa-check-circle me-1"></i>Available
                                            </span>
                                        <?php elseif ($employee['current_workload'] <= 2): ?>
                                            <span class="text-warning">
                                                <i class="fas fa-clock me-1"></i>Moderate Load
                                            </span>
                                        <?php else: ?>
                                            <span class="text-danger">
                                                <i class="fas fa-exclamation-triangle me-1"></i>High Load
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
