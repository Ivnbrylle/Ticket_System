<?php
require_once 'config.php';
requireLogin();

$page_title = 'Dashboard';

// Get statistics
$stats = [];

// Total tickets
$stmt = $pdo->query("SELECT COUNT(*) as total FROM tickets");
$stats['total_tickets'] = $stmt->fetch()['total'];

// Open tickets
$stmt = $pdo->query("SELECT COUNT(*) as total FROM tickets WHERE status = 'Open'");
$stats['open_tickets'] = $stmt->fetch()['total'];

// My assigned tickets
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM tickets WHERE assigned_to = ? AND status != 'Closed'");
$stmt->execute([$_SESSION['employee_id']]);
$stats['my_tickets'] = $stmt->fetch()['total'];

// Unassigned tickets
$stmt = $pdo->query("SELECT COUNT(*) as total FROM tickets WHERE assigned_to IS NULL AND status = 'Open'");
$stats['unassigned_tickets'] = $stmt->fetch()['total'];

// Recent tickets
$stmt = $pdo->prepare("
    SELECT t.*, e.name as assigned_name 
    FROM tickets t 
    LEFT JOIN employees e ON t.assigned_to = e.employee_id 
    ORDER BY t.created_at DESC 
    LIMIT 10
");
$stmt->execute();
$recent_tickets = $stmt->fetchAll();

// My workload
$stmt = $pdo->prepare("
    SELECT COUNT(*) as workload 
    FROM tickets 
    WHERE assigned_to = ? AND status != 'Closed'
");
$stmt->execute([$_SESSION['employee_id']]);
$my_workload = $stmt->fetch()['workload'];

// Update workload in database
$stmt = $pdo->prepare("UPDATE employees SET workload = ? WHERE employee_id = ?");
$stmt->execute([$my_workload, $_SESSION['employee_id']]);

include 'includes/header.php';
?>

<div class="container-fluid px-3">
    <!-- Statistics Cards -->
    <div class="dashboard-grid">
        <div class="stats-card card-gold">
            <div class="card-icon"><i class="fas fa-ticket-alt"></i></div>
            <div class="card-number"><?php echo $stats['total_tickets']; ?></div>
            <div class="card-title">Total Tickets</div>
        </div>
        <div class="stats-card card-green">
            <div class="card-icon"><i class="fas fa-folder-open"></i></div>
            <div class="card-number"><?php echo $stats['open_tickets']; ?></div>
            <div class="card-title">Open Tickets</div>
        </div>
        <div class="stats-card card-orange">
            <div class="card-icon"><i class="fas fa-user-tag"></i></div>
            <div class="card-number"><?php echo $stats['my_tickets']; ?></div>
            <div class="card-title">My Active Tickets</div>
        </div>
        <div class="stats-card card-red">
            <div class="card-icon"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="card-number"><?php echo $stats['unassigned_tickets']; ?></div>
            <div class="card-title">Unassigned</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="content-section">
        <div class="section-header">
            <h3 class="section-title">Quick Actions</h3>
            <p class="section-subtitle">Frequently used actions to manage tickets efficiently</p>
        </div>
        <div class="actions-grid">
            <a href="create_ticket.php" class="action-card action-dark">
                <div class="action-icon"><i class="fas fa-plus"></i></div>
                <div class="action-title">Create New Ticket</div>
            </a>
            <a href="tickets.php?status=Open" class="action-card action-green">
                <div class="action-icon"><i class="fas fa-eye"></i></div>
                <div class="action-title">View Open Tickets</div>
            </a>
            <a href="my_tickets.php" class="action-card action-orange">
                <div class="action-icon"><i class="fas fa-user-tag"></i></div>
                <div class="action-title">My Assigned Tickets</div>
            </a>
            <?php if (isAdmin()): ?>
            <a href="assign_tickets.php" class="action-card action-blue">
                <div class="action-icon"><i class="fas fa-tasks"></i></div>
                <div class="action-title">Auto Assign Tickets</div>
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Tickets -->
    <div class="content-section">
        <div class="section-header">
            <h3 class="section-title">Recent Tickets</h3>
            <p class="section-subtitle">Latest tickets in the system requiring attention</p>
        </div>
        
        <?php if (empty($recent_tickets)): ?>
            <div class="card">
                <div class="card-body text-center py-4">
                    <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                    <h6 class="text-muted">No tickets found</h6>
                    <p class="text-muted small">Create your first ticket to get started.</p>
                    <a href="create_ticket.php" class="btn btn-primary mt-2">
                        <i class="fas fa-plus me-1"></i>Create New Ticket
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Topic</th>
                            <th>Status</th>
                            <th>Assigned</th>
                            <th>Created</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_tickets as $ticket): ?>
                        <tr>
                            <td>
                                <span class="ticket-id"><?php echo htmlspecialchars($ticket['ticket_id']); ?></span>
                            </td>
                            <td class="name-col">
                                <a href="view_ticket.php?id=<?php echo urlencode($ticket['ticket_id']); ?>" 
                                   class="text-decoration-none fw-medium" 
                                   title="<?php echo htmlspecialchars($ticket['name']); ?>">
                                    <?php echo htmlspecialchars(strlen($ticket['name']) > 25 ? substr($ticket['name'], 0, 25) . '...' : $ticket['name']); ?>
                                </a>
                            </td>
                            <td>
                                <span class="badge bg-info" title="<?php echo htmlspecialchars($ticket['topic']); ?>">
                                    <?php echo htmlspecialchars(strlen($ticket['topic']) > 10 ? substr($ticket['topic'], 0, 10) . '...' : $ticket['topic']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $ticket['status'])); ?>">
                                    <?php echo htmlspecialchars($ticket['status']); ?>
                                </span>
                            </td>
                            <td title="<?php echo $ticket['assigned_name'] ? htmlspecialchars($ticket['assigned_name']) : 'Unassigned'; ?>">
                                <?php if ($ticket['assigned_name']): ?>
                                    <?php echo htmlspecialchars(strlen($ticket['assigned_name']) > 12 ? substr($ticket['assigned_name'], 0, 12) . '...' : $ticket['assigned_name']); ?>
                                <?php else: ?>
                                    <span class="text-muted">Unassigned</span>
                                <?php endif; ?>
                            </td>
                            <td class="created-col">
                                <small class="text-muted">
                                    <?php echo date('M j, Y', strtotime($ticket['created_at'])); ?><br>
                                    <?php echo date('H:i', strtotime($ticket['created_at'])); ?>
                                </small>
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
            <div class="text-center mt-3">
                <a href="tickets.php" class="btn btn-outline-primary">
                    <i class="fas fa-list me-1"></i>View All Tickets
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
