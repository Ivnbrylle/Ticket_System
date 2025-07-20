<?php
require_once 'config.php';
requireLogin();

$page_title = 'My Tickets';

// Get tickets assigned to current user
$stmt = $pdo->prepare("
    SELECT 
        t.*,
        creator.name as created_by_name
    FROM tickets t
    LEFT JOIN employees creator ON t.created_by = creator.employee_id
    WHERE t.assigned_to = ?
    ORDER BY 
        CASE 
            WHEN t.status = 'Open' THEN 1
            WHEN t.status = 'In Progress' THEN 2
            WHEN t.status = 'Closed' THEN 3
        END,
        t.created_at DESC
");
$stmt->execute([$_SESSION['employee_id']]);
$tickets = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        My Assigned Tickets 
                        <span class="badge bg-secondary"><?php echo count($tickets); ?> total</span>
                    </h5>
                    <div>
                        <?php
                        $active_count = 0;
                        foreach ($tickets as $ticket) {
                            if ($ticket['status'] != 'Closed') $active_count++;
                        }
                        ?>
                        <span class="badge bg-warning"><?php echo $active_count; ?> active</span>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($tickets)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-user-tag fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No tickets assigned</h5>
                            <p class="text-muted">You don't have any tickets assigned to you yet.</p>
                            <a href="tickets.php" class="btn btn-primary">View All Tickets</a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Ticket ID</th>
                                        <th>Title</th>
                                        <th>Topic</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Created By</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tickets as $ticket): ?>
                                    <tr class="<?php echo $ticket['status'] == 'Closed' ? 'table-secondary' : ''; ?>">
                                        <td>
                                            <span class="ticket-id"><?php echo htmlspecialchars($ticket['ticket_id']); ?></span>
                                        </td>
                                        <td>
                                            <a href="view_ticket.php?id=<?php echo urlencode($ticket['ticket_id']); ?>" 
                                               class="text-decoration-none">
                                                <?php echo htmlspecialchars($ticket['name']); ?>
                                            </a>
                                        </td>
                                        <td>
                                            <span class="text-muted small" style="line-height: 1.2; display: block;">
                                                <?php echo htmlspecialchars($ticket['topic']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge priority-<?php echo strtolower($ticket['priority']); ?>">
                                                <?php echo htmlspecialchars($ticket['priority']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $ticket['status'] == 'Open' ? 'success' : 
                                                    ($ticket['status'] == 'In Progress' ? 'warning' : 'secondary'); 
                                            ?>">
                                                <?php echo htmlspecialchars($ticket['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php echo $ticket['created_by_name'] ? htmlspecialchars($ticket['created_by_name']) : '<span class="text-muted">Unknown</span>'; ?>
                                        </td>
                                        <td>
                                            <small><?php echo date('M j, Y H:i', strtotime($ticket['created_at'])); ?></small>
                                        </td>
                                        <td>
                                            <a href="view_ticket.php?id=<?php echo urlencode($ticket['ticket_id']); ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Quick Status Summary -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h6>Status Summary:</h6>
                                <?php
                                $status_counts = ['Open' => 0, 'In Progress' => 0, 'Closed' => 0];
                                foreach ($tickets as $ticket) {
                                    $status_counts[$ticket['status']]++;
                                }
                                ?>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="card bg-success text-white">
                                            <div class="card-body text-center">
                                                <h4><?php echo $status_counts['Open']; ?></h4>
                                                <small>Open</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-warning text-white">
                                            <div class="card-body text-center">
                                                <h4><?php echo $status_counts['In Progress']; ?></h4>
                                                <small>In Progress</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-secondary text-white">
                                            <div class="card-body text-center">
                                                <h4><?php echo $status_counts['Closed']; ?></h4>
                                                <small>Closed</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-info text-white">
                                            <div class="card-body text-center">
                                                <h4><?php echo count($tickets); ?></h4>
                                                <small>Total</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
