<?php
require_once 'config.php';
requireAdmin();

$page_title = 'Reports';

// Get various statistics
$stats = [];

// Total tickets by status
$stmt = $pdo->query("
    SELECT status, COUNT(*) as count 
    FROM tickets 
    GROUP BY status
");
$status_stats = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Total tickets by topic
$stmt = $pdo->query("
    SELECT topic, COUNT(*) as count 
    FROM tickets 
    GROUP BY topic
");
$topic_stats = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Employee workload report
$stmt = $pdo->query("
    SELECT 
        e.name,
        e.specialization,
        COUNT(CASE WHEN t.status = 'Open' THEN 1 END) as open_tickets,
        COUNT(CASE WHEN t.status = 'In Progress' THEN 1 END) as in_progress_tickets,
        COUNT(CASE WHEN t.status = 'Closed' THEN 1 END) as closed_tickets,
        COUNT(t.ticket_id) as total_tickets
    FROM employees e
    LEFT JOIN tickets t ON e.employee_id = t.assigned_to
    WHERE e.is_admin = 0
    GROUP BY e.employee_id, e.name, e.specialization
    ORDER BY total_tickets DESC
");
$employee_stats = $stmt->fetchAll();

// Expected output as per PDF requirements
$stmt = $pdo->query("
    SELECT 
        t.ticket_id,
        COALESCE(e.employee_id, 'Unassigned') as assigned_employee_id
    FROM tickets t
    LEFT JOIN employees e ON t.assigned_to = e.employee_id
    ORDER BY t.ticket_id
");
$expected_output = $stmt->fetchAll();

// Recent activity
$stmt = $pdo->query("
    SELECT 
        t.ticket_id,
        t.name,
        t.status,
        t.updated_at,
        e.name as assigned_name
    FROM tickets t
    LEFT JOIN employees e ON t.assigned_to = e.employee_id
    ORDER BY t.updated_at DESC
    LIMIT 10
");
$recent_activity = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="container-fluid">
    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h3><?php echo array_sum($status_stats); ?></h3>
                    <p class="mb-0">Total Tickets</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h3><?php echo isset($status_stats['Open']) ? $status_stats['Open'] : 0; ?></h3>
                    <p class="mb-0">Open Tickets</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h3><?php echo isset($status_stats['In Progress']) ? $status_stats['In Progress'] : 0; ?></h3>
                    <p class="mb-0">In Progress</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body text-center">
                    <h3><?php echo isset($status_stats['Closed']) ? $status_stats['Closed'] : 0; ?></h3>
                    <p class="mb-0">Closed Tickets</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Expected Output Report (as per PDF) -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Expected Output Report</h5>
                    <small class="text-muted">Ticket assignments as specified in requirements</small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Ticket ID</th>
                                    <th>Assigned To</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($expected_output as $output): ?>
                                <tr>
                                    <td class="ticket-id"><?php echo htmlspecialchars($output['ticket_id']); ?></td>
                                    <td>
                                        <?php if ($output['assigned_employee_id'] === 'Unassigned'): ?>
                                            <span class="text-muted">Unassigned</span>
                                        <?php else: ?>
                                            Employee ID: <?php echo htmlspecialchars($output['assigned_employee_id']); ?>
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

        <!-- Employee Workload Report -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Employee Workload Report</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Open</th>
                                    <th>In Progress</th>
                                    <th>Closed</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($employee_stats as $employee): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($employee['name']); ?></strong>
                                        <br>
                                        <small class="text-muted">
                                            <?php echo formatSpecialization($employee['specialization']); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-success"><?php echo $employee['open_tickets']; ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning"><?php echo $employee['in_progress_tickets']; ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary"><?php echo $employee['closed_tickets']; ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary"><?php echo $employee['total_tickets']; ?></span>
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

    <div class="row mt-4">
        <!-- Tickets by Topic -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Tickets by Topic</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Topic</th>
                                    <th>Count</th>
                                    <th>Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $total_tickets = array_sum($topic_stats);
                                foreach ($topic_stats as $topic => $count): 
                                    $percentage = $total_tickets > 0 ? round(($count / $total_tickets) * 100, 1) : 0;
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($topic); ?></td>
                                    <td><?php echo $count; ?></td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar" role="progressbar" 
                                                 style="width: <?php echo $percentage; ?>%">
                                                <?php echo $percentage; ?>%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Activity</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Ticket</th>
                                    <th>Status</th>
                                    <th>Assigned To</th>
                                    <th>Updated</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_activity as $activity): ?>
                                <tr>
                                    <td>
                                        <a href="view_ticket.php?id=<?php echo urlencode($activity['ticket_id']); ?>" 
                                           class="ticket-id">
                                            <?php echo htmlspecialchars($activity['ticket_id']); ?>
                                        </a>
                                        <br>
                                        <small><?php echo htmlspecialchars($activity['name']); ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo $activity['status'] == 'Open' ? 'success' : 
                                                ($activity['status'] == 'In Progress' ? 'warning' : 'secondary'); 
                                        ?>">
                                            <?php echo htmlspecialchars($activity['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo $activity['assigned_name'] ? htmlspecialchars($activity['assigned_name']) : '<span class="text-muted">Unassigned</span>'; ?>
                                    </td>
                                    <td>
                                        <small><?php echo date('M j, H:i', strtotime($activity['updated_at'])); ?></small>
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

    <!-- Export Options -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Export Options</h5>
                </div>
                <div class="card-body">
                    <p>Generate reports in different formats:</p>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                            <i class="fas fa-print me-2"></i>Print Report
                        </button>
                        <a href="assign_tickets.php" class="btn btn-outline-success">
                            <i class="fas fa-tasks me-2"></i>Auto Assign Tickets
                        </a>
                        <a href="tickets.php" class="btn btn-outline-info">
                            <i class="fas fa-list me-2"></i>View All Tickets
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .btn, .card-header, .sidebar { display: none !important; }
    .card { border: none !important; box-shadow: none !important; }
}
</style>

<?php include 'includes/footer.php'; ?>
