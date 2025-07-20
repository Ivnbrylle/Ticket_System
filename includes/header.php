<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?><?php echo APP_NAME; ?></title>
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
            background-color: var(--secondary-cream);
            font-family: 'Inter', sans-serif;
            color: var(--dark-charcoal);
            line-height: 1.5;
            font-size: 14px;
        }

        .sidebar {
            background: linear-gradient(180deg, var(--dark-charcoal) 0%, var(--light-charcoal) 100%);
            border-right: 1px solid var(--border-light);
            box-shadow: 2px 0 10px var(--shadow-subtle);
        }

        .sidebar .nav-link {
            color: #E5E5E5 !important;
            padding: 8px 12px;
            margin: 2px 6px;
            border-radius: 6px;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 13px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: var(--accent-gold);
            color: var(--dark-charcoal) !important;
            transform: translateX(4px);
        }

        .sidebar h5 {
            font-family: 'Playfair Display', serif;
            font-weight: 600;
            letter-spacing: 1px;
            color: var(--soft-white);
            font-size: 1rem;
        }

        .sidebar-header {
            padding: 16px 12px 12px 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 12px;
        }

        .sidebar-header small {
            color: #B8B8B8;
            font-size: 11px;
        }

        .navbar-brand {
            font-weight: 600;
            font-family: 'Playfair Display', serif;
        }

        .card {
            background-color: var(--soft-white);
            border: 1px solid var(--border-light);
            border-radius: 12px;
            box-shadow: 0 4px 20px var(--shadow-subtle);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 8px 30px var(--shadow-medium);
            transform: translateY(-2px);
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-cream) 0%, var(--secondary-cream) 100%);
            border-bottom: 1px solid var(--border-light);
            border-radius: 12px 12px 0 0 !important;
            padding: 16px 20px;
        }

        .card-header h5 {
            font-family: 'Playfair Display', serif;
            font-weight: 600;
            color: var(--dark-charcoal);
            margin: 0;
        }

        .card-body {
            padding: 20px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--dark-charcoal) 0%, var(--light-charcoal) 100%);
            border: none;
            border-radius: 6px;
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 13px;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--light-charcoal) 0%, var(--dark-charcoal) 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px var(--shadow-medium);
        }

        .btn-secondary {
            background: transparent;
            border: 2px solid var(--border-light);
            color: var(--light-charcoal);
            border-radius: 6px;
            padding: 8px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 13px;
        }

        .btn-secondary:hover {
            background-color: var(--primary-cream);
            border-color: var(--accent-gold);
            color: var(--dark-charcoal);
        }

        .form-control, .form-select {
            border: 2px solid var(--border-light);
            border-radius: 6px;
            padding: 10px 14px;
            background-color: var(--soft-white);
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--accent-gold);
            box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25);
            background-color: var(--soft-white);
        }

        .form-label {
            font-weight: 500;
            color: var(--dark-charcoal);
            margin-bottom: 6px;
            font-size: 14px;
        }

        .alert {
            border: none;
            border-radius: 8px;
            padding: 12px 16px;
            font-weight: 500;
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
        }

        .alert-danger {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
        }

        .status-open { color: #28a745; font-weight: 600; }
        .status-in-progress { color: #ffc107; font-weight: 600; }
        .status-closed { color: #6c757d; font-weight: 600; }
        .priority-low { color: #28a745; font-weight: 600; }
        .priority-medium { color: #ffc107; font-weight: 600; }
        .priority-high { color: #fd7e14; font-weight: 600; }
        .priority-critical { color: #dc3545; font-weight: 600; }

        .ticket-id {
            font-family: 'JetBrains Mono', 'Courier New', monospace;
            font-weight: 600;
            background-color: var(--primary-cream);
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 13px;
        }

        .badge {
            font-size: 12px;
            font-weight: 500;
            padding: 8px 12px;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .bg-primary {
            background: linear-gradient(135deg, var(--accent-gold) 0%, #B8941F 100%) !important;
            color: var(--dark-charcoal) !important;
        }

        .border-bottom {
            border-bottom: 2px solid var(--border-light) !important;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Playfair Display', serif;
            font-weight: 600;
            color: var(--dark-charcoal);
        }

        .h2 {
            color: var(--dark-charcoal);
            font-weight: 600;
        }

        main {
            background-color: var(--secondary-cream);
            min-height: 100vh;
        }

        .text-danger {
            color: #dc3545 !important;
        }

        .form-text {
            color: var(--light-charcoal);
            font-size: 13px;
            font-style: italic;
        }

        /* Dashboard Cards - Clean Modern Design */
        .stats-card {
            background: var(--soft-white);
            border-radius: 12px;
            padding: 18px;
            box-shadow: 0 4px 20px var(--shadow-subtle);
            border: 1px solid var(--border-light);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stats-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px var(--shadow-medium);
        }

        .stats-card.card-gold {
            background: linear-gradient(135deg, #B8941F 0%, #D4AF37 100%);
            color: white;
        }

        .stats-card.card-green {
            background: linear-gradient(135deg, #2d8659 0%, #349a6b 100%);
            color: white;
        }

        .stats-card.card-orange {
            background: linear-gradient(135deg, #e67e22 0%, #f39c12 100%);
            color: white;
        }

        .stats-card.card-red {
            background: linear-gradient(135deg, #c0392b 0%, #e74c3c 100%);
            color: white;
        }

        .stats-card.card-blue {
            background: linear-gradient(135deg, #2980b9 0%, #3498db 100%);
            color: white;
        }

        .stats-card .card-icon {
            font-size: 2rem;
            opacity: 0.9;
            position: absolute;
            top: 16px;
            right: 16px;
        }

        .stats-card .card-number {
            font-size: 2.2rem;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            margin-bottom: 6px;
            line-height: 1;
        }

        .stats-card .card-title {
            font-size: 0.9rem;
            font-weight: 500;
            opacity: 0.9;
            margin: 0;
            font-family: 'Inter', sans-serif;
        }

        /* Action Cards */
        .action-card {
            background: var(--soft-white);
            border-radius: 10px;
            padding: 16px;
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid var(--border-light);
            text-decoration: none;
            display: block;
            color: inherit;
        }

        .action-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px var(--shadow-medium);
            text-decoration: none;
            color: inherit;
        }

        .action-card.action-dark {
            background: linear-gradient(135deg, var(--dark-charcoal) 0%, var(--light-charcoal) 100%);
            color: white;
        }

        .action-card.action-green {
            background: linear-gradient(135deg, #2d8659 0%, #349a6b 100%);
            color: white;
        }

        .action-card.action-orange {
            background: linear-gradient(135deg, #e67e22 0%, #f39c12 100%);
            color: white;
        }

        .action-card.action-blue {
            background: linear-gradient(135deg, #2980b9 0%, #3498db 100%);
            color: white;
        }

        .action-card .action-icon {
            font-size: 1.3rem;
            margin-bottom: 6px;
        }

        .action-card .action-title {
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }

        /* Professional Table Styling */
        .table-responsive {
            background: var(--soft-white);
            border-radius: 12px;
            box-shadow: 0 4px 20px var(--shadow-subtle);
            border: 1px solid var(--border-light);
            overflow: hidden;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background: linear-gradient(135deg, var(--primary-cream) 0%, var(--secondary-cream) 100%);
            border: none;
            padding: 12px 16px;
            font-weight: 600;
            color: var(--dark-charcoal);
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid var(--border-light);
        }

        .table tbody td {
            padding: 12px 16px;
            border: none;
            border-bottom: 1px solid var(--border-light);
            vertical-align: middle;
            font-size: 13px;
        }

        .table tbody tr:hover {
            background-color: var(--primary-cream);
            transition: all 0.2s ease;
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Status and Priority Badges */
        .status-badge, .priority-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-badge.status-open {
            background: #d4edda;
            color: #155724;
        }

        .status-badge.status-in-progress {
            background: #fff3cd;
            color: #856404;
        }

        .status-badge.status-closed {
            background: #d1ecf1;
            color: #0c5460;
        }

        .priority-badge.priority-low {
            background: #d4edda;
            color: #155724;
        }

        .priority-badge.priority-medium {
            background: #fff3cd;
            color: #856404;
        }

        .priority-badge.priority-high {
            background: #ffeaa7;
            color: #d68910;
        }

        .priority-badge.priority-critical {
            background: #f8d7da;
            color: #721c24;
        }

        /* Section Headers */
        .section-header {
            margin-bottom: 20px;
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--dark-charcoal);
            margin-bottom: 6px;
        }

        .section-subtitle {
            color: var(--light-charcoal);
            font-size: 13px;
            margin: 0;
        }

        /* Custom scrollbar for sidebar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: var(--light-charcoal);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: var(--accent-gold);
            border-radius: 3px;
        }

        /* Responsive Grid for Cards */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
            margin-bottom: 28px;
            max-width: 1400px;
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-bottom: 28px;
            max-width: 1200px;
        }

        /* Clean spacing and layout */
        .content-section {
            margin-bottom: 32px;
            max-width: 1400px;
        }

        /* Compact layout for smaller screens */
        @media (max-width: 1200px) {
            .dashboard-grid {
                grid-template-columns: repeat(4, 1fr);
                gap: 16px;
            }
            
            .actions-grid {
                grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
                gap: 14px;
            }
        }

        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: repeat(4, 1fr);
                gap: 12px;
            }
            
            .actions-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }
        }

        @media (max-width: 480px) {
            .dashboard-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }
        }

        /* Dashboard specific optimizations */
        .dashboard-page .container-fluid {
            max-width: 1600px;
            padding-left: 12px;
            padding-right: 12px;
        }

        .dashboard-page .stats-card {
            padding: 14px;
        }

        .dashboard-page .stats-card .card-number {
            font-size: 1.9rem;
            margin-bottom: 4px;
        }

        .dashboard-page .stats-card .card-title {
            font-size: 0.8rem;
        }

        .dashboard-page .stats-card .card-icon {
            font-size: 1.7rem;
            top: 12px;
            right: 12px;
        }

        .dashboard-page .section-header {
            margin-bottom: 16px;
        }

        .dashboard-page .section-title {
            font-size: 1.2rem;
            margin-bottom: 4px;
        }

        .dashboard-page .section-subtitle {
            font-size: 12px;
        }

        .dashboard-page .content-section {
            margin-bottom: 24px;
        }

        .dashboard-page .dashboard-grid {
            gap: 14px;
            margin-bottom: 24px;
        }

        .dashboard-page .actions-grid {
            gap: 12px;
            margin-bottom: 24px;
        }

        .dashboard-page .action-card {
            padding: 12px;
        }

        .dashboard-page .action-card .action-title {
            font-size: 12px;
        }

        .dashboard-page .action-card .action-icon {
            font-size: 1.1rem;
            margin-bottom: 4px;
        }

        .dashboard-page .table thead th {
            padding: 10px 12px;
            font-size: 12px;
        }

        .dashboard-page .table tbody td {
            padding: 10px 12px;
            font-size: 12px;
        }

        .dashboard-page .btn-sm {
            padding: 4px 8px;
            font-size: 11px;
        }

        /* Enhanced table styling for single line content */
        .dashboard-page .table-responsive {
            overflow-x: auto;
            scrollbar-width: thin;
            scrollbar-color: var(--accent-gold) var(--border-light);
        }

        .dashboard-page .table-responsive::-webkit-scrollbar {
            height: 8px;
        }

        .dashboard-page .table-responsive::-webkit-scrollbar-track {
            background: var(--border-light);
            border-radius: 4px;
        }

        .dashboard-page .table-responsive::-webkit-scrollbar-thumb {
            background: var(--accent-gold);
            border-radius: 4px;
        }

        .dashboard-page .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #B8941F;
        }

        .dashboard-page .table {
            min-width: 800px;
            white-space: nowrap;
        }

        .dashboard-page .table thead th {
            padding: 8px 10px;
            font-size: 11px;
            white-space: nowrap;
            vertical-align: middle;
        }

        .dashboard-page .table tbody td {
            padding: 8px 10px;
            font-size: 11px;
            white-space: nowrap;
            vertical-align: middle;
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dashboard-page .table tbody td.name-col {
            max-width: 200px;
        }

        .dashboard-page .table tbody td.created-col {
            max-width: 90px;
            white-space: normal;
            line-height: 1.3;
            font-size: 10px;
            text-align: center;
        }

        .dashboard-page .ticket-id {
            font-size: 10px;
            padding: 2px 6px;
        }

        .dashboard-page .badge {
            font-size: 9px;
            padding: 2px 6px;
        }

        .dashboard-page .status-badge {
            font-size: 9px;
            padding: 2px 8px;
        }

        /* Apply compact table styling to all pages */
        .table-responsive {
            overflow-x: auto;
            scrollbar-width: thin;
            scrollbar-color: var(--accent-gold) var(--border-light);
        }

        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: var(--border-light);
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: var(--accent-gold);
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #B8941F;
        }

        .table-sm {
            min-width: 900px;
            white-space: nowrap;
        }

        .table-sm thead th {
            padding: 8px 10px;
            font-size: 11px;
            white-space: nowrap;
            vertical-align: middle;
        }

        .table-sm tbody td {
            padding: 8px 10px;
            font-size: 11px;
            white-space: nowrap;
            vertical-align: middle;
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .table-sm tbody td.name-col {
            max-width: 250px;
        }

        .table-sm tbody td.created-col {
            max-width: 90px;
            white-space: normal;
            line-height: 1.3;
            font-size: 10px;
            text-align: center;
        }

        .table-sm .ticket-id {
            font-size: 10px;
            padding: 2px 6px;
        }

        .table-sm .badge {
            font-size: 9px;
            padding: 2px 6px;
        }

        .table-sm .status-badge {
            font-size: 9px;
            padding: 2px 8px;
        }

        .table-sm .priority-badge {
            font-size: 9px;
            padding: 2px 8px;
        }
    </style>
</head>
<body class="<?php echo (isset($page_title) && $page_title === 'Dashboard') ? 'dashboard-page' : ''; ?>">
    <?php if (isLoggedIn()): ?>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 d-none d-md-block sidebar p-0">
                <div class="position-sticky pt-3">
                    <div class="sidebar-header text-center text-white">
                        <h5><?php echo APP_NAME; ?></h5>
                        <small>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></small>
                    </div>
                    <ul class="nav flex-column px-2">
                        <li class="nav-item">
                            <a class="nav-link text-white" href="dashboard.php">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="tickets.php">
                                <i class="fas fa-ticket-alt me-2"></i>All Tickets
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="create_ticket.php">
                                <i class="fas fa-plus me-2"></i>Create Ticket
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="my_tickets.php">
                                <i class="fas fa-user-tag me-2"></i>My Tickets
                            </a>
                        </li>
                        <?php if (isAdmin()): ?>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="employees.php">
                                <i class="fas fa-users me-2"></i>Employees
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="assign_tickets.php">
                                <i class="fas fa-tasks me-2"></i>Auto Assign
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="reports.php">
                                <i class="fas fa-chart-bar me-2"></i>Reports
                            </a>
                        </li>
                        <?php endif; ?>
                        <li class="nav-item mt-3 pt-2" style="border-top: 1px solid rgba(255, 255, 255, 0.1);">
                            <a class="nav-link text-white" href="logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><?php echo isset($page_title) ? $page_title : 'Dashboard'; ?></h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <span class="badge bg-primary">
                                <?php echo isAdmin() ? 'Admin' : 'User'; ?>
                            </span>
                        </div>
                    </div>
                </div>
    <?php endif; ?>

    <!-- Success/Error Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
