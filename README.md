# Support Ticket Management System

## Overview

A comprehensive PHP-based support ticket management system that allows organizations to efficiently manage customer support requests through automated assignment and tracking.

## Features

### Core Functionality

- **Employee Management**: Add, edit, delete, and search employees with specializations
- **Ticket Management**: Create, assign, track, and resolve support tickets
- **Automatic Assignment**: Intelligent ticket assignment based on employee specializations and workload
- **Multi-category Support**: Feature Request, Sales, Usage Guide, Bugs and Technical Issues, General
- **Priority System**: Low, Medium, High, Critical priority levels
- **Status Tracking**: Open, In Progress, Closed status management

### Advanced Features

- **Role-based Access Control**: Admin and regular user roles
- **Comments System**: Add updates and comments to tickets
- **Search and Filtering**: Advanced filtering by status, topic, priority
- **Dashboard Analytics**: Comprehensive reporting and statistics
- **Workload Balancing**: Automatic assignment to employees with least workload
- **Responsive Design**: Mobile-friendly Bootstrap interface

## System Requirements

### Server Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- XAMPP (for local development)

### PHP Extensions

- PDO MySQL
- Session support
- Password hashing support

## Installation

### 1. Setup Database

```sql
-- Import the database.sql file into MySQL
mysql -u root -p < database.sql
```

### 2. Configure Database Connection

Edit `config.php` and update database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ticket_system');
```

### 3. Set Permissions

Ensure web server has read/write access to the application directory.

### 4. Access Application

Navigate to `http://localhost/Ticket_System/` in your web browser.

## Default Login Credentials

### Administrator Account

- **Email**: admin@company.com
- **Password**: password
- **Role**: Admin (full access)

### Regular User Accounts

- **Email**: jordon.dale@company.com
- **Password**: password
- **Specialization**: Feature Request

- **Email**: gabriel.stanford@company.com
- **Password**: password
- **Specialization**: Sales, Usage Guide

- **Email**: riordan.tony@company.com
- **Password**: password
- **Specialization**: Sales

## System Architecture

### Database Schema

- **employees**: User accounts and specializations
- **tickets**: Support ticket information
- **ticket_comments**: Comments and updates on tickets

### File Structure

```
Ticket_System/
├── config.php              # Database and app configuration
├── index.php               # Entry point
├── login.php               # User authentication
├── logout.php              # Session termination
├── dashboard.php           # Main dashboard
├── create_ticket.php       # New ticket creation
├── view_ticket.php         # Ticket details and updates
├── tickets.php             # All tickets listing
├── my_tickets.php          # User's assigned tickets
├── employees.php           # Employee management (Admin)
├── assign_tickets.php      # Automatic assignment (Admin)
├── reports.php             # System reports (Admin)
├── database.sql            # Database schema and sample data
└── includes/
    ├── header.php          # Common header and navigation
    └── footer.php          # Common footer
```

## Usage Guide

### For Regular Users

1. **Login** with provided credentials
2. **Create Tickets** using the "Create Ticket" button
3. **View Assigned Tickets** in "My Tickets" section
4. **Update Ticket Status** when working on assigned tickets
5. **Add Comments** to provide updates on progress

### For Administrators

1. **Manage Employees** - Add/remove staff and set specializations
2. **Auto Assign Tickets** - Use intelligent assignment system
3. **View Reports** - Monitor system performance and workload
4. **Manual Assignment** - Override automatic assignments when needed
5. **System Overview** - Monitor all tickets and employee workloads

## Automatic Assignment Algorithm

The system implements an intelligent assignment algorithm:

1. **Specialization Matching**: Tickets are assigned to employees whose specializations match the ticket topic
2. **Workload Balancing**: Among qualified employees, assignment goes to those with the least current workload
3. **Fair Distribution**: If workloads are equal, assignment is made by employee ID for consistency
4. **Status Management**: Assigned tickets automatically change to "In Progress" status

## Sample Data

The system comes pre-loaded with sample tickets based on the requirements:

- HOW-UG-01234: Usage Guide request
- ASSIS-GE-11111: General assistance
- THIS-BT-42512: Technical bug report
- PRICE-SL-21222: Sales inquiry
- COLLA-FR-50123: Feature request
- And more...

## Security Features

- **Password Hashing**: Secure password storage using PHP's password_hash()
- **SQL Injection Prevention**: Prepared statements throughout
- **Session Security**: Secure session configuration
- **Access Control**: Role-based permissions
- **Input Sanitization**: All user input is sanitized

## Customization

### Adding New Ticket Categories

1. Update the ENUM values in the database schema
2. Modify the topic dropdown in `create_ticket.php`
3. Update specialization options in `employees.php`
4. Adjust the ticket ID generation logic in `config.php`

### Modifying Assignment Rules

Edit the assignment logic in `assign_tickets.php` to implement custom rules based on your organization's needs.

## Troubleshooting

### Common Issues

**Database Connection Error**

- Verify MySQL is running
- Check database credentials in `config.php`
- Ensure database exists and tables are created

**Login Issues**

- Use default credentials provided above
- Check if sessions are working properly
- Verify password hashing is functioning

**Assignment Not Working**

- Ensure employees have matching specializations
- Check that tickets are in "Open" status
- Verify employee records exist

## Support

For technical support or feature requests, please refer to the system documentation or contact your system administrator.

## License

This project is developed for educational and organizational use. Please ensure compliance with your organization's software policies.
