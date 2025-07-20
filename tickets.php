<?php
require_once 'config.php';
requireLogin();

$page_title = 'All Tickets';

// Get filter parameters
$status_filter = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$topic_filter = isset($_GET['topic']) ? sanitize($_GET['topic']) : '';
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

// Build query
$where_conditions = [];
$params = [];

if (!empty($status_filter)) {
    $where_conditions[] = "t.status = ?";
    $params[] = $status_filter;
}

if (!empty($topic_filter)) {
    $where_conditions[] = "t.topic = ?";
    $params[] = $topic_filter;
}

if (!empty($search)) {
    $where_conditions[] = "(t.ticket_id LIKE ? OR t.name LIKE ? OR t.description LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

$stmt = $pdo->prepare("
    SELECT 
        t.*,
        e.name as assigned_name,
        creator.name as created_by_name
    FROM tickets t
    LEFT JOIN employees e ON t.assigned_to = e.employee_id
    LEFT JOIN employees creator ON t.created_by = creator.employee_id
    $where_clause
    ORDER BY t.created_at DESC
");

$stmt->execute($params);
$tickets = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="container-fluid px-3">
    <!-- Filters -->
    <div class="mb-3">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Filter Tickets</h6>
            </div>
            <div class="card-body py-3">
                <form method="GET" class="row g-2">
                    <div class="col-md-2">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select form-select-sm" id="status" name="status">
                            <option value="">All Statuses</option>
                            <option value="Open" <?php echo $status_filter == 'Open' ? 'selected' : ''; ?>>Open</option>
                            <option value="In Progress" <?php echo $status_filter == 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                            <option value="Closed" <?php echo $status_filter == 'Closed' ? 'selected' : ''; ?>>Closed</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="topic" class="form-label">Topic</label>
                        <select class="form-select form-select-sm" id="topic" name="topic">
                            <option value="">All Topics</option>
                            <option value="Feature Request" <?php echo $topic_filter == 'Feature Request' ? 'selected' : ''; ?>>Feature Request</option>
                            <option value="Sales" <?php echo $topic_filter == 'Sales' ? 'selected' : ''; ?>>Sales</option>
                            <option value="Usage Guide" <?php echo $topic_filter == 'Usage Guide' ? 'selected' : ''; ?>>Usage Guide</option>
                            <option value="Bugs and Technical Issues" <?php echo $topic_filter == 'Bugs and Technical Issues' ? 'selected' : ''; ?>>Bugs and Technical Issues</option>
                            <option value="General" <?php echo $topic_filter == 'General' ? 'selected' : ''; ?>>General</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control form-control-sm" id="search" name="search" 
                               value="<?php echo htmlspecialchars($search); ?>" 
                               placeholder="Ticket ID, title, or description...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <a href="create_ticket.php" class="btn btn-success btn-sm">
                                <i class="fas fa-plus"></i>
                            </a>
                        </div>
                    </div>
                </form>
                <?php if (!empty($status_filter) || !empty($topic_filter) || !empty($search)): ?>
                    <div class="mt-2">
                        <a href="tickets.php" class="btn btn-sm btn-outline-secondary">Clear Filters</a>
                        <span class="ms-2 text-muted small"><?php echo count($tickets); ?> tickets found</span>
                    </div>
                <?php else: ?>
                    <div class="mt-2">
                        <span class="text-muted small"><?php echo count($tickets); ?> total tickets</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Tickets Table -->
    <?php if (empty($tickets)): ?>
        <div class="card">
            <div class="card-body text-center py-4">
                <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                <h6 class="text-muted">No tickets found</h6>
                <p class="text-muted small">Try adjusting your filters or create a new ticket.</p>
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
                            <span class="badge bg-info" title="<?php echo htmlspecialchars($ticket['topic']); ?>">
                                <?php echo htmlspecialchars(strlen($ticket['topic']) > 12 ? substr($ticket['topic'], 0, 12) . '...' : $ticket['topic']); ?>
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
