# Ticket System CSS Architecture

This document outlines the CSS file structure for the Ticket System project.

## CSS Files Overview

### Core Stylesheets

#### 1. `css/main.css`

**Purpose**: Main application styles for all authenticated pages
**Contains**:

- CSS custom properties (variables) for consistent theming
- Global typography and font settings
- Sidebar navigation styles
- Card components (generic cards)
- Form controls and buttons
- Alert messages
- Badge and status styles
- Basic layout components

**Used by**: All pages that include `includes/header.php`

#### 2. `css/dashboard.css`

**Purpose**: Dashboard-specific components and layouts
**Contains**:

- Statistics cards with gradient backgrounds
- Action cards with hover effects
- Dashboard grid layouts
- Section headers and titles
- Responsive breakpoints for dashboard
- Compact styling for mobile devices

**Used by**: `dashboard.php` and any page with dashboard-like layouts

#### 3. `css/tables.css`

**Purpose**: Professional table styling and functionality
**Contains**:

- Table responsive containers
- Professional table header and row styling
- Status and priority badge styles
- Table filtering interface
- Compact table styling for different screen sizes
- Custom scrollbar styles
- No-results message styling

**Used by**: Pages with data tables (`tickets.php`, `my_tickets.php`, `employees.php`, etc.)

#### 4. `css/login.css`

**Purpose**: Login page styling
**Contains**:

- Full-screen gradient background
- Login card layout with split design
- Logo container styling
- Form controls specific to login
- Responsive design for mobile
- Background pattern effects

**Used by**: `login.php` only

#### 5. `css/signup.css`

**Purpose**: Signup page styling
**Contains**:

- Signup card background and layout
- Specialization grid for form fields
- Purple gradient background

**Used by**: `signup.php` only

### Utility Stylesheets

#### 6. `css/setup-database.css`

**Purpose**: Database setup page styling
**Contains**:

- Simple utility page styling
- Basic typography
- Link styling

**Used by**: `setup_database.php`

#### 7. `css/test-connection.css`

**Purpose**: Test connection page styling
**Contains**:

- Basic utility page styling
- Typography for system messages

**Used by**: `test_connection.php`

#### 8. `css/reports.css`

**Purpose**: Print media styles for reports
**Contains**:

- Print-specific styles
- Element hiding for print layout

**Used by**: `reports.php`

## JavaScript Files

#### 1. `js/table-filters.js`

**Purpose**: Interactive table filtering functionality
**Contains**:

- Auto-filter function for tables
- Event listeners for filter controls
- Search functionality
- No-results message handling
- Results count updates
- Clear filters functionality

**Used by**: All pages with filterable tables

## Color Scheme

The application uses a consistent color palette defined in CSS custom properties:

```css
:root {
  --primary-cream: #f5f0e8; /* Light background */
  --secondary-cream: #f8f5f0; /* Alternate background */
  --dark-charcoal: #2c2c2c; /* Primary dark text */
  --light-charcoal: #4a4a4a; /* Secondary text */
  --accent-gold: #d4af37; /* Accent color */
  --soft-white: #fefefe; /* Card backgrounds */
  --border-light: #e8e0d6; /* Borders */
  --shadow-subtle: rgba(44, 44, 44, 0.08); /* Light shadows */
  --shadow-medium: rgba(44, 44, 44, 0.12); /* Medium shadows */
}
```

## File Dependencies

### Header Dependencies (All authenticated pages)

```html
<link href="css/main.css" rel="stylesheet" />
<link href="css/tables.css" rel="stylesheet" />
<link href="css/dashboard.css" rel="stylesheet" />
<script src="js/table-filters.js"></script>
```

### Login Page Dependencies

```html
<link href="css/login.css" rel="stylesheet" />
```

### Signup Page Dependencies

```html
<link href="css/signup.css" rel="stylesheet" />
```

## Responsive Design

All CSS files include responsive breakpoints:

- **Mobile**: < 768px
- **Tablet**: 768px - 992px
- **Desktop**: > 992px
- **Large Desktop**: > 1200px

## Font Stack

The application uses two primary font families:

- **Inter**: Body text and UI elements
- **Playfair Display**: Headings and brand elements

## Maintenance Notes

1. **CSS Variables**: All colors and spacing should use the CSS custom properties defined in `main.css`
2. **Component Isolation**: Each CSS file should focus on specific components or pages
3. **Mobile-First**: All responsive styles follow mobile-first approach
4. **Performance**: External CSS files are preferred over inline styles for better caching
5. **Consistency**: Use the established naming conventions and class structures

## Adding New Styles

When adding new components:

1. Determine which CSS file is most appropriate
2. Use existing CSS variables for colors and spacing
3. Follow the established naming conventions
4. Add responsive breakpoints as needed
5. Test across all target devices
