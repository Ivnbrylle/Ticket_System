<?php
require_once 'config.php';

$page_title = 'Sign Up';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $specializations = isset($_POST['specialization']) ? $_POST['specialization'] : [];
    
    // Validation
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Please fill in all required fields.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif (empty($specializations)) {
        $error = 'Please select at least one specialization.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT employee_id FROM employees WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $error = 'This email address is already registered.';
        } else {
            // Hash password and create account
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $specialization_string = implode(',', $specializations);
            
            $stmt = $pdo->prepare("
                INSERT INTO employees (name, email, password, specialization, is_admin) 
                VALUES (?, ?, ?, ?, FALSE)
            ");
            
            if ($stmt->execute([$name, $email, $hashed_password, $specialization_string])) {
                $success = 'Account created successfully! You can now log in.';
                // Auto-login the user
                $employee_id = $pdo->lastInsertId();
                $_SESSION['employee_id'] = $employee_id;
                $_SESSION['name'] = $name;
                $_SESSION['email'] = $email;
                $_SESSION['is_admin'] = 0;
                
                $_SESSION['success'] = 'Welcome to the Support Ticket System!';
                redirect('dashboard.php');
            } else {
                $error = 'Failed to create account. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title . ' - ' . APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .signup-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        .specialization-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="signup-card p-4">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-primary"><?php echo APP_NAME; ?></h2>
                        <p class="text-muted">Create your account</p>
                    </div>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required 
                                       value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>"
                                       placeholder="Enter your full name">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required 
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                                   placeholder="Enter your email address">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password" name="password" required
                                       placeholder="At least 6 characters">
                                <div class="form-text">Password must be at least 6 characters long.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required
                                       placeholder="Confirm your password">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Specializations <span class="text-danger">*</span></label>
                            <div class="form-text mb-2">Select your areas of expertise (choose at least one):</div>
                            <div class="specialization-grid">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="specialization[]" value="Feature Request" id="spec1"
                                           <?php echo (isset($_POST['specialization']) && in_array('Feature Request', $_POST['specialization'])) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="spec1">
                                        <i class="fas fa-lightbulb text-warning me-1"></i>Feature Request
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="specialization[]" value="Sales" id="spec2"
                                           <?php echo (isset($_POST['specialization']) && in_array('Sales', $_POST['specialization'])) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="spec2">
                                        <i class="fas fa-dollar-sign text-success me-1"></i>Sales
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="specialization[]" value="Usage Guide" id="spec3"
                                           <?php echo (isset($_POST['specialization']) && in_array('Usage Guide', $_POST['specialization'])) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="spec3">
                                        <i class="fas fa-book text-info me-1"></i>Usage Guide
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="specialization[]" value="Bugs and Technical Issues" id="spec4"
                                           <?php echo (isset($_POST['specialization']) && in_array('Bugs and Technical Issues', $_POST['specialization'])) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="spec4">
                                        <i class="fas fa-bug text-danger me-1"></i>Technical Issues
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="specialization[]" value="General" id="spec5"
                                           <?php echo (isset($_POST['specialization']) && in_array('General', $_POST['specialization'])) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="spec5">
                                        <i class="fas fa-question-circle text-secondary me-1"></i>General
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-user-plus me-2"></i>Create Account
                            </button>
                        </div>
                    </form>
                    
                    <div class="text-center">
                        <p class="mb-0">Already have an account? 
                            <a href="login.php" class="text-decoration-none">Sign in here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password confirmation validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (password !== confirmPassword) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });
        
        // Specialization validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const checkboxes = document.querySelectorAll('input[name="specialization[]"]:checked');
            if (checkboxes.length === 0) {
                e.preventDefault();
                alert('Please select at least one specialization.');
                return false;
            }
        });
    </script>
</body>
</html>
