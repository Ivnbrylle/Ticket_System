<?php
require_once 'config.php';
requireLogin();

$page_title = 'Create Ticket';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $topic = sanitize($_POST['topic']);
    $description = sanitize($_POST['description']);
    $priority = sanitize($_POST['priority']);
    
    if (empty($name) || empty($topic) || empty($description)) {
        $error = 'Please fill in all required fields.';
    } else {
        // Generate unique ticket ID
        $ticket_id = generateTicketId($topic);
        
        // Check if ticket ID already exists (very unlikely but good practice)
        $stmt = $pdo->prepare("SELECT ticket_id FROM tickets WHERE ticket_id = ?");
        $stmt->execute([$ticket_id]);
        
        while ($stmt->fetch()) {
            $ticket_id = generateTicketId($topic);
            $stmt->execute([$ticket_id]);
        }
        
        // Insert ticket
        $stmt = $pdo->prepare("
            INSERT INTO tickets (ticket_id, name, topic, description, priority, created_by) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        if ($stmt->execute([$ticket_id, $name, $topic, $description, $priority, $_SESSION['employee_id']])) {
            $_SESSION['success'] = "Ticket created successfully! Ticket ID: $ticket_id";
            redirect('view_ticket.php?id=' . urlencode($ticket_id));
        } else {
            $error = 'Failed to create ticket. Please try again.';
        }
    }
}

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card shadow-lg">
                <div class="card-header text-center">
                    <h5 class="mb-0">
                        <i class="fas fa-ticket-alt me-2"></i>
                        Create New Support Ticket
                    </h5>
                    <p class="text-muted mb-0 mt-2">Submit your request and we'll get back to you promptly</p>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <?php echo $success; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-8 mb-4">
                                <label for="name" class="form-label">
                                    <i class="fas fa-heading me-1"></i>
                                    Ticket Title <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="name" name="name" required
                                       value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>"
                                       placeholder="Brief description of the issue">
                            </div>
                            
                            <div class="col-md-4 mb-4">
                                <label for="priority" class="form-label">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    Priority Level
                                </label>
                                <select class="form-select" id="priority" name="priority">
                                    <option value="Low" <?php echo (isset($_POST['priority']) && $_POST['priority'] == 'Low') ? 'selected' : ''; ?>>
                                        🟢 Low Priority
                                    </option>
                                    <option value="Medium" <?php echo (!isset($_POST['priority']) || $_POST['priority'] == 'Medium') ? 'selected' : ''; ?>>
                                        🟡 Medium Priority
                                    </option>
                                    <option value="High" <?php echo (isset($_POST['priority']) && $_POST['priority'] == 'High') ? 'selected' : ''; ?>>
                                        🟠 High Priority
                                    </option>
                                    <option value="Critical" <?php echo (isset($_POST['priority']) && $_POST['priority'] == 'Critical') ? 'selected' : ''; ?>>
                                        🔴 Critical Priority
                                    </option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="topic" class="form-label">
                                <i class="fas fa-tags me-1"></i>
                                Topic/Category <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="topic" name="topic" required>
                                <option value="">Select a topic...</option>
                                <option value="Feature Request" <?php echo (isset($_POST['topic']) && $_POST['topic'] == 'Feature Request') ? 'selected' : ''; ?>>
                                    💡 Feature Request
                                </option>
                                <option value="Sales" <?php echo (isset($_POST['topic']) && $_POST['topic'] == 'Sales') ? 'selected' : ''; ?>>
                                    💼 Sales Inquiry
                                </option>
                                <option value="Usage Guide" <?php echo (isset($_POST['topic']) && $_POST['topic'] == 'Usage Guide') ? 'selected' : ''; ?>>
                                    📚 Usage Guide & Help
                                </option>
                                <option value="Bugs and Technical Issues" <?php echo (isset($_POST['topic']) && $_POST['topic'] == 'Bugs and Technical Issues') ? 'selected' : ''; ?>>
                                    🐛 Bugs and Technical Issues
                                </option>
                                <option value="General" <?php echo (isset($_POST['topic']) && $_POST['topic'] == 'General') ? 'selected' : ''; ?>>
                                    💬 General Support
                                </option>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label for="description" class="form-label">
                                <i class="fas fa-align-left me-1"></i>
                                Detailed Description <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" id="description" name="description" rows="8" required
                                      placeholder="Please provide detailed information about your request or issue. Include steps to reproduce if applicable, expected behavior, and any error messages you've encountered..."><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                The more details you provide, the faster we can resolve your issue. Consider including screenshots, error messages, or step-by-step descriptions.
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <a href="dashboard.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane me-2"></i>Submit Ticket
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Help Section -->
            <div class="card mt-4">
                <div class="card-body text-center">
                    <h6 class="card-title">
                        <i class="fas fa-question-circle me-2"></i>
                        Need immediate assistance?
                    </h6>
                    <p class="card-text text-muted mb-3">
                        For urgent matters, you can also contact our support team directly.
                    </p>
                    <div class="row text-center">
                        <div class="col-md-4">
                            <i class="fas fa-clock text-muted"></i>
                            <small class="d-block text-muted">Response Time</small>
                            <strong>Within 24 hours</strong>
                        </div>
                        <div class="col-md-4">
                            <i class="fas fa-users text-muted"></i>
                            <small class="d-block text-muted">Expert Team</small>
                            <strong>Always Available</strong>
                        </div>
                        <div class="col-md-4">
                            <i class="fas fa-shield-alt text-muted"></i>
                            <small class="d-block text-muted">Secure & Private</small>
                            <strong>Your Data Protected</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
