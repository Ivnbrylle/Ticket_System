<?php
require_once 'config.php';

$page_title = 'Login';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        $stmt = $pdo->prepare("SELECT employee_id, name, email, password, is_admin FROM employees WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['employee_id'] = $user['employee_id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['is_admin'] = $user['is_admin'];
            
            redirect('dashboard.php');
        } else {
            $error = 'Invalid email or password.';
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-cream: #F5F0E8;
            --secondary-cream: #F8F5F0;
            --dark-charcoal: #2C2C2C;
            --light-charcoal: #4A4A4A;
            --accent-gold: #D4AF37;
            --soft-white: #FEFEFE;
            --border-light: #E8E0D6;
            --shadow-subtle: rgba(44, 44, 44, 0.08);
            --shadow-medium: rgba(44, 44, 44, 0.12);
        }

        body {
            background: linear-gradient(135deg, var(--secondary-cream) 0%, var(--primary-cream) 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            color: var(--dark-charcoal);
            position: relative;
            overflow: hidden;
        }

        /* Background Pattern */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 20% 20%, rgba(212, 175, 55, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(44, 44, 44, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 40% 70%, rgba(212, 175, 55, 0.08) 0%, transparent 50%);
            z-index: 0;
        }

        .login-container {
            position: relative;
            z-index: 1;
            width: 100%;
        }

        .login-card {
            background: var(--soft-white);
            border-radius: 20px;
            box-shadow: 0 20px 60px var(--shadow-medium);
            border: 1px solid var(--border-light);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            overflow: hidden;
            display: flex;
            max-width: 900px;
            min-height: 500px;
        }

        .login-card:hover {
            box-shadow: 0 25px 80px rgba(44, 44, 44, 0.15);
            transform: translateY(-2px);
        }

        .login-header {
            background: linear-gradient(135deg, var(--dark-charcoal) 0%, var(--light-charcoal) 100%);
            color: white;
            padding: 40px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
            flex: 1;
            min-width: 350px;
        }

        .login-header::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            width: 1px;
            background: linear-gradient(180deg, transparent, var(--accent-gold), transparent);
        }

        .logo-container {
            background: var(--soft-white);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            border: 2px solid var(--accent-gold);
            transition: all 0.3s ease;
        }

        .logo-container:hover {
            transform: scale(1.05);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.2);
        }

        .logo-container img {
            width: 120px;
            height: 120px;
            border-radius: 12px;
            display: block;
            margin: 0 auto;
            object-fit: contain;
        }

        .login-header .company-name {
            font-family: 'Playfair Display', serif;
            font-weight: 600;
            margin: 0;
            font-size: 1.8rem;
            letter-spacing: 1px;
            line-height: 1.2;
        }

        .login-header p {
            margin: 0;
            opacity: 0.9;
            font-size: 16px;
            font-weight: 400;
        }

        .login-body {
            padding: 50px 40px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-width: 400px;
        }

        .form-control {
            border: 2px solid var(--border-light);
            border-radius: 10px;
            padding: 12px 16px;
            background-color: var(--soft-white);
            transition: all 0.3s ease;
            font-size: 14px;
            font-weight: 400;
        }

        .form-control:focus {
            border-color: var(--accent-gold);
            box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25);
            background-color: var(--soft-white);
        }

        .form-label {
            font-weight: 600;
            color: var(--dark-charcoal);
            margin-bottom: 8px;
            font-size: 14px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--dark-charcoal) 0%, var(--light-charcoal) 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 24px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 14px;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--light-charcoal) 0%, var(--dark-charcoal) 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px var(--shadow-medium);
        }

        .alert {
            border: none;
            border-radius: 10px;
            padding: 12px 16px;
            font-weight: 500;
            margin-bottom: 20px;
        }

        .alert-danger {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
        }

        .signup-link {
            text-align: center;
            padding: 20px 0;
            border-top: 1px solid var(--border-light);
            margin-top: 25px;
        }

        .signup-link a {
            color: var(--accent-gold);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .signup-link a:hover {
            color: var(--dark-charcoal);
            text-decoration: underline;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .login-card {
                flex-direction: column;
                max-width: 100%;
                min-height: auto;
            }
            
            .login-header {
                min-width: auto;
                padding: 30px 25px;
            }
            
            .login-header::after {
                top: auto;
                right: 0;
                bottom: 0;
                left: 0;
                width: auto;
                height: 1px;
                background: linear-gradient(90deg, transparent, var(--accent-gold), transparent);
            }
            
            .logo-container {
                padding: 15px;
                margin-bottom: 15px;
            }
            
            .logo-container img {
                width: 80px;
                height: 80px;
            }
            
            .login-header .company-name {
                font-size: 1.5rem;
            }
            
            .login-body {
                padding: 30px 25px;
                min-width: auto;
            }
        }

        @media (max-width: 992px) and (min-width: 769px) {
            .login-card {
                max-width: 750px;
            }
            
            .login-header {
                min-width: 300px;
                padding: 35px 40px;
            }
            
            .logo-container {
                padding: 18px;
            }
            
            .logo-container img {
                width: 100px;
                height: 100px;
            }
            
            .login-header .company-name {
                font-size: 1.6rem;
            }
            
            .login-body {
                min-width: 350px;
                padding: 40px 35px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="login-card mx-auto">
                        <div class="login-header">
                            <div class="logo-container">
                                <img src="images/logo.png" alt="Company Logo">
                            </div>
                        </div>
                        
                        <div class="login-body">
                            <?php if ($error): ?>
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
                                </div>
                            <?php endif; ?>
                            
                            <form method="POST">
                                <div class="mb-3">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope me-2"></i>Email Address
                                    </label>
                                    <input type="email" class="form-control" id="email" name="email" required 
                                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                                </div>
                                <div class="mb-4">
                                    <label for="password" class="form-label">
                                        <i class="fas fa-lock me-2"></i>Password
                                    </label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-sign-in-alt me-2"></i>Sign In
                                    </button>
                                </div>
                            </form>
                            
                            <div class="signup-link">
                                <p class="mb-0">Don't have an account? 
                                    <a href="signup.php">Sign up here</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
