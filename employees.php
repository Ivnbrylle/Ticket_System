<?php
require_once 'config.php';
requireAdmin();

$page_title = 'Manage Employees';

$error = '';
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_employee'])) {
        $name = sanitize($_POST['name']);
        $email = sanitize($_POST['email']);
        $password = $_POST['password'];
        $specializations = isset($_POST['specialization']) ? $_POST['specialization'] : [];
        $is_admin = isset($_POST['is_admin']) ? 1 : 0;
        
        if (empty($name) || empty($email) || empty($password) || empty($specializations)) {
            $error = 'Please fill in all required fields.';
        } else {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT employee_id FROM employees WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                $error = 'Email address already exists.';
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $specialization_string = implode(',', $specializations);
                
                $stmt = $pdo->prepare("
                    INSERT INTO employees (name, email, password, specialization, is_admin) 
                    VALUES (?, ?, ?, ?, ?)
                ");
                
                if ($stmt->execute([$name, $email, $hashed_password, $specialization_string, $is_admin])) {
                    $success = 'Employee added successfully!';
                } else {
                    $error = 'Failed to add employee.';
                }
            }
        }
    } elseif (isset($_POST['delete_employee'])) {
        $employee_id = (int)$_POST['employee_id'];
        
        if ($employee_id == $_SESSION['employee_id']) {
            $error = 'You cannot delete your own account.';
        } else {
            // Set tickets assigned to this employee as unassigned
            $stmt = $pdo->prepare("UPDATE tickets SET assigned_to = NULL WHERE assigned_to = ?");
            $stmt->execute([$employee_id]);
            
            // Delete employee
            $stmt = $pdo->prepare("DELETE FROM employees WHERE employee_id = ?");
            if ($stmt->execute([$employee_id])) {
                $success = 'Employee deleted successfully!';
            } else {
                $error = 'Failed to delete employee.';
            }
        }
    }
}

// Get all employees
$stmt = $pdo->query("
    SELECT 
        e.*,
        COUNT(t.ticket_id) as active_tickets
    FROM employees e
    LEFT JOIN tickets t ON e.employee_id = t.assigned_to AND t.status != 'Closed'
    GROUP BY e.employee_id
    ORDER BY e.name ASC
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

    <div class="row">
        <!-- Add Employee Form -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Add New Employee</h5>
                </div>
                        <div class="card-body">
                            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Specializations <span class="text-danger">*</span></label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="specialization[]" value="Feature Request" id="spec1">
                                        <label class="form-check-label" for="spec1">Feature Request</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="specialization[]" value="Sales" id="spec2">
                                        <label class="form-check-label" for="spec2">Sales</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="specialization[]" value="Usage Guide" id="spec3">
                                        <label class="form-check-label" for="spec3">Usage Guide</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="specialization[]" value="Bugs and Technical Issues" id="spec4">
                                        <label class="form-check-label" for="spec4">Bugs and Technical Issues</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="specialization[]" value="General" id="spec5">
                                        <label class="form-check-label" for="spec5">General</label>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_admin" id="is_admin">
                                        <label class="form-check-label" for="is_admin">
                                            Administrator
                                        </label>
                                    </div>
                                </div>
                                
                                <button type="submit" name="add_employee" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Add Employee
                                </button>
                            </form>
                        </div>
            </div>
        </div>

        <!-- Employee List -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Current Employees</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Specializations</th>
                                    <th>Role</th>
                                    <th>Active Tickets</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($employees as $employee): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($employee['name']); ?></td>
                                    <td><?php echo htmlspecialchars($employee['email']); ?></td>
                                    <td>
                                        <?php 
                                        $specs = explode(',', $employee['specialization']);
                                        foreach ($specs as $spec) {
                                            echo '<span class="badge bg-secondary me-1">' . htmlspecialchars(trim($spec)) . '</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($employee['is_admin']): ?>
                                            <span class="badge bg-warning">Admin</span>
                                        <?php else: ?>
                                            <span class="badge bg-info">User</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $employee['active_tickets'] > 0 ? 'primary' : 'secondary'; ?>">
                                            <?php echo $employee['active_tickets']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($employee['employee_id'] != $_SESSION['employee_id']): ?>
                                            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display: inline;" 
                                                  onsubmit="return confirm('Are you sure you want to delete this employee? Their assigned tickets will become unassigned.')">
                                                <input type="hidden" name="employee_id" value="<?php echo $employee['employee_id']; ?>">
                                                <button type="submit" name="delete_employee" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-muted">(You)</span>
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
