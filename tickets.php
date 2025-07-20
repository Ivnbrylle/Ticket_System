<?php
require_once 'config.php';
requireLogin();

$page_title = 'All Tickets';

// Get all tickets (no server-side filtering)
$stmt = $pdo->prepare("
    SELECT 
        t.*,
        e.name as assigned_name,
        creator.name as created_by_name
    FROM tickets t
    LEFT JOIN employees e ON t.assigned_to = e.employee_id
    LEFT JOIN employees creator ON t.created_by = creator.employee_id
    ORDER BY t.created_at DESC
");

$stmt->execute();
$tickets = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="container-fluid px-3 mb-4">
    <!-- Tickets Table with Integrated Auto-Filters -->
    <?php if (empty($tickets)): ?>
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
            <!-- Auto-Filter Controls -->
            <div class="table-filters p-3 border-bottom">
                <div class="row g-2 align-items-end">
                    <div class="col-md-2">
                        <label for="status" class="form-label small">Status</label>
                        <select class="form-select form-select-sm" id="status">
                            <option value="">All Statuses</option>
                            <option value="Open">Open</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Closed">Closed</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="topic" class="form-label small">Topic</label>
                        <select class="form-select form-select-sm" id="topic">
                            <option value="">All Topics</option>
                            <option value="Feature Request">Feature Request</option>
                            <option value="Sales">Sales</option>
                            <option value="Usage Guide">Usage Guide</option>
                            <option value="Bugs and Technical Issues">Bugs and Technical Issues</option>
                            <option value="General">General</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="search" class="form-label small">Search</label>
                        <input type="text" class="form-control form-control-sm" id="search" 
                               placeholder="Type to search tickets...">
                    </div>
                    <div class="col-md-2">
                        <a href="create_ticket.php" class="btn btn-success btn-sm">
                            <i class="fas fa-plus me-1"></i>Create Ticket
                        </a>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearFilters()">
                            Clear
                        </button>
                    </div>
                </div>
                <div class="mt-2">
                    <span class="text-muted small results-count">
                        <?php echo count($tickets); ?> total tickets
                    </span>
                </div>
            </div>
            
            <!-- Table -->
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Topic</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Assigned</th>
                        <th>Creator</th>
                        <th>Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets as $ticket): ?>
                    <tr>
                        <td>
                            <span class="ticket-id"><?php echo htmlspecialchars($ticket['ticket_id']); ?></span>
                        </td>
                        <td class="name-col">
                            <a href="view_ticket.php?id=<?php echo urlencode($ticket['ticket_id']); ?>" 
                               class="text-decoration-none fw-medium"
                               title="<?php echo htmlspecialchars($ticket['name']); ?>">
                                <?php echo htmlspecialchars(strlen($ticket['name']) > 30 ? substr($ticket['name'], 0, 30) . '...' : $ticket['name']); ?>
                            </a>
                        </td>
                        <td>
                            <span class="text-muted small" style="line-height: 1.2; display: block; max-width: 120px;">
                                <?php echo htmlspecialchars($ticket['topic']); ?>
                            </span>
                        </td>
                        <td>
                            <span class="priority-badge priority-<?php echo strtolower($ticket['priority']); ?>">
                                <?php echo htmlspecialchars($ticket['priority']); ?>
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
                        <td title="<?php echo $ticket['created_by_name'] ? htmlspecialchars($ticket['created_by_name']) : 'Unknown'; ?>">
                            <?php if ($ticket['created_by_name']): ?>
                                <?php echo htmlspecialchars(strlen($ticket['created_by_name']) > 12 ? substr($ticket['created_by_name'], 0, 12) . '...' : $ticket['created_by_name']); ?>
                            <?php else: ?>
                                <span class="text-muted">Unknown</span>
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
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
