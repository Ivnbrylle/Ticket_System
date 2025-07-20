<?php
require_once 'config.php';
requireLogin();

$page_title = 'View Ticket';

if (!isset($_GET['id'])) {
    $_SESSION['error'] = 'Ticket ID is required.';
    redirect('tickets.php');
}

$ticket_id = sanitize($_GET['id']);

// Get ticket details
$stmt = $pdo->prepare("
    SELECT 
        t.*,
        e.name as assigned_name,
        creator.name as created_by_name
    FROM tickets t
    LEFT JOIN employees e ON t.assigned_to = e.employee_id
    LEFT JOIN employees creator ON t.created_by = creator.employee_id
    WHERE t.ticket_id = ?
");
$stmt->execute([$ticket_id]);
$ticket = $stmt->fetch();

if (!$ticket) {
    $_SESSION['error'] = 'Ticket not found.';
    redirect('tickets.php');
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_comment'])) {
        $comment = sanitize($_POST['comment']);
        
        if (!empty($comment)) {
            $stmt = $pdo->prepare("
                INSERT INTO ticket_comments (ticket_id, employee_id, comment) 
                VALUES (?, ?, ?)
            ");
            
            if ($stmt->execute([$ticket_id, $_SESSION['employee_id'], $comment])) {
                $_SESSION['success'] = 'Comment added successfully!';
                redirect("view_ticket.php?id=" . urlencode($ticket_id));
            } else {
                $_SESSION['error'] = 'Failed to add comment.';
            }
        } else {
            $_SESSION['error'] = 'Comment cannot be empty.';
        }
    } elseif (isset($_POST['update_status']) && (isAdmin() || $ticket['assigned_to'] == $_SESSION['employee_id'])) {
        $new_status = sanitize($_POST['status']);
        
        $stmt = $pdo->prepare("UPDATE tickets SET status = ?, updated_at = NOW() WHERE ticket_id = ?");
        
        if ($stmt->execute([$new_status, $ticket_id])) {
            $_SESSION['success'] = 'Ticket status updated successfully!';
            redirect("view_ticket.php?id=" . urlencode($ticket_id));
        } else {
            $_SESSION['error'] = 'Failed to update ticket status.';
        }
    } elseif (isset($_POST['assign_ticket']) && isAdmin()) {
        $assign_to = $_POST['assign_to'] ? (int)$_POST['assign_to'] : null;
        
        $stmt = $pdo->prepare("UPDATE tickets SET assigned_to = ?, updated_at = NOW() WHERE ticket_id = ?");
        
        if ($stmt->execute([$assign_to, $ticket_id])) {
            $_SESSION['success'] = 'Ticket assignment updated successfully!';
            redirect("view_ticket.php?id=" . urlencode($ticket_id));
        } else {
            $_SESSION['error'] = 'Failed to update ticket assignment.';
        }
    }
}

// Get comments
$stmt = $pdo->prepare("
    SELECT 
        c.*,
        e.name as employee_name
    FROM ticket_comments c
    JOIN employees e ON c.employee_id = e.employee_id
    WHERE c.ticket_id = ?
    ORDER BY c.created_at ASC
");
$stmt->execute([$ticket_id]);
$comments = $stmt->fetchAll();

// Get available employees for assignment (admin only)
if (isAdmin()) {
    $stmt = $pdo->query("SELECT employee_id, name FROM employees WHERE is_admin = 0 ORDER BY name ASC");
    $employees = $stmt->fetchAll();
}

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Ticket Details -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <span class="ticket-id"><?php echo htmlspecialchars($ticket['ticket_id']); ?></span>
                            - <?php echo htmlspecialchars($ticket['name']); ?>
                        </h5>
                        <div>
                            <span class="badge bg-<?php 
                                echo $ticket['status'] == 'Open' ? 'success' : 
                                    ($ticket['status'] == 'In Progress' ? 'warning' : 'secondary'); 
                            ?> me-2">
                                <?php echo htmlspecialchars($ticket['status']); ?>
                            </span>
                            <span class="badge priority-<?php echo strtolower($ticket['priority']); ?>">
                                <?php echo htmlspecialchars($ticket['priority']); ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Topic:</strong> 
                            <span class="badge bg-info"><?php echo htmlspecialchars($ticket['topic']); ?></span>
                        </div>
                        <div class="col-md-6">
                            <strong>Priority:</strong> 
                            <span class="badge priority-<?php echo strtolower($ticket['priority']); ?>">
                                <?php echo htmlspecialchars($ticket['priority']); ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Created By:</strong> 
                            <?php echo $ticket['created_by_name'] ? htmlspecialchars($ticket['created_by_name']) : 'Unknown'; ?>
                        </div>
                        <div class="col-md-6">
                            <strong>Created:</strong> 
                            <?php echo date('M j, Y H:i', strtotime($ticket['created_at'])); ?>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Assigned To:</strong> 
                            <?php echo $ticket['assigned_name'] ? htmlspecialchars($ticket['assigned_name']) : '<span class="text-muted">Unassigned</span>'; ?>
                        </div>
                        <div class="col-md-6">
                            <strong>Last Updated:</strong> 
                            <?php echo date('M j, Y H:i', strtotime($ticket['updated_at'])); ?>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <strong>Description:</strong>
                        <div class="mt-2 p-3 bg-light rounded">
                            <?php echo nl2br(htmlspecialchars($ticket['description'])); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comments Section -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Comments & Updates</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($comments)): ?>
                        <p class="text-muted">No comments yet.</p>
                    <?php else: ?>
                        <?php foreach ($comments as $comment): ?>
                            <div class="border-bottom mb-3 pb-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <strong><?php echo htmlspecialchars($comment['employee_name']); ?></strong>
                                    <small class="text-muted">
                                        <?php echo date('M j, Y H:i', strtotime($comment['created_at'])); ?>
                                    </small>
                                </div>
                                <div class="mt-2">
                                    <?php echo nl2br(htmlspecialchars($comment['comment'])); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <!-- Add Comment Form -->
                    <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] . '?id=' . urlencode($ticket['ticket_id']); ?>" class="mt-4">
                        <div class="mb-3">
                            <label for="comment" class="form-label">Add Comment</label>
                            <textarea class="form-control" id="comment" name="comment" rows="3" 
                                      placeholder="Enter your comment or update..."></textarea>
                        </div>
                        <button type="submit" name="add_comment" class="btn btn-primary">
                            <i class="fas fa-comment me-2"></i>Add Comment
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Actions Sidebar -->
        <div class="col-md-4">
            <!-- Status Update -->
            <?php if (isAdmin() || $ticket['assigned_to'] == $_SESSION['employee_id']): ?>
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0">Update Status</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] . '?id=' . urlencode($ticket['ticket_id']); ?>">
                        <div class="mb-3">
                            <select class="form-select" name="status">
                                <option value="Open" <?php echo $ticket['status'] == 'Open' ? 'selected' : ''; ?>>Open</option>
                                <option value="In Progress" <?php echo $ticket['status'] == 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                                <option value="Closed" <?php echo $ticket['status'] == 'Closed' ? 'selected' : ''; ?>>Closed</option>
                            </select>
                        </div>
                        <button type="submit" name="update_status" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-2"></i>Update Status
                        </button>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <!-- Assignment (Admin Only) -->
            <?php if (isAdmin()): ?>
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0">Assignment</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] . '?id=' . urlencode($ticket['ticket_id']); ?>">
                        <div class="mb-3">
                            <select class="form-select" name="assign_to">
                                <option value="">Unassigned</option>
                                <?php foreach ($employees as $employee): ?>
                                    <option value="<?php echo $employee['employee_id']; ?>" 
                                            <?php echo $ticket['assigned_to'] == $employee['employee_id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($employee['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" name="assign_ticket" class="btn btn-info btn-sm">
                            <i class="fas fa-user-tag me-2"></i>Update Assignment
                        </button>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <!-- Actions -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="tickets.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Tickets
                        </a>
                        <?php if ($ticket['status'] != 'Closed'): ?>
                            <a href="create_ticket.php" class="btn btn-outline-primary">
                                <i class="fas fa-plus me-2"></i>Create New Ticket
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
