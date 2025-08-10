# Sky Border Solutions Website

A comprehensive website for Sky Border Solutions, featuring client management, service categories, and a modern responsive design.

## Recent Updates

### ✅ **Services Field Added to Clients**
- **Database**: Added `services` TEXT field to clients table
- **Admin Backend**: Updated client forms to include services selection
- **Frontend**: Services now display under client names on main website

### ✅ **Service Duration Tracking Added**
- **Database**: Added duration fields to clients table:
  - `service_duration_type`: ENUM('ongoing', 'date_range')
  - `service_start_date`: DATE for start of service period
  - `service_end_date`: DATE for end of service period
- **Admin Backend**: 
  - Radio buttons for "Currently Ongoing" vs "Specific Date Range"
  - Date pickers for start and end dates (shown when date range is selected)
  - JavaScript toggle to show/hide date fields based on selection
- **Frontend**: Duration information displays on main website with visual indicators:
  - Green badge with checkmark for "Currently Ongoing"
  - Blue badge with calendar icon for date ranges

## Features

### Client Management
- **Services**: Multi-select dropdown for services provided (Recruitment, HR Consulting, Staffing, Training, Compliance, Visa Processing, Insurance Services)
- **Duration Tracking**: 
  - Currently Ongoing (default)
  - Specific Date Range with start/end dates
- **Logo Upload**: File upload system with preview
- **Categories**: Client categorization system
- **Display Order**: Custom ordering for client showcase

### Admin Panel
- **Authentication**: Secure login system
- **Content Management**: Full CRUD operations for all content types
- **File Management**: Secure file upload and deletion
- **Responsive Design**: Modern admin interface with dark mode support

### Frontend Website
- **Responsive Design**: Mobile-first approach with Tailwind CSS
- **Client Showcase**: Dynamic client display with services and duration
- **Modern UI**: Gradient text, animations, and smooth transitions
- **Dark Mode**: Automatic theme switching support

## Database Schema

### Clients Table
```sql
CREATE TABLE clients (
    id INT PRIMARY KEY AUTO_INCREMENT,
    client_name VARCHAR(255) NOT NULL,
    category_id INT,
    logo_url VARCHAR(500),
    description TEXT,
    services TEXT,
    service_duration_type ENUM('ongoing', 'date_range') DEFAULT 'ongoing',
    service_start_date DATE NULL,
    service_end_date DATE NULL,
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### Service Categories Table
```sql
CREATE TABLE service_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(255) NOT NULL,
    category_description TEXT,
    icon_class VARCHAR(100),
    color_theme VARCHAR(50),
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT 1
);
```

## Setup Instructions

### 1. Database Setup
Run the setup script to add the new fields:
```bash
# Access via browser
http://your-domain.com/admin/setup-services.php

# Or run SQL manually
mysql -u username -p database_name < admin/database/add-services-to-clients.sql
```

### 2. Admin Access
- Navigate to `/admin`
- Login with your credentials
- Go to "Clients Management"

### 3. Adding/Editing Clients
1. Click "Add Client" or edit existing client
2. Fill in client information
3. Select services from the multi-select dropdown
4. Choose duration type:
   - **Currently Ongoing**: Check the radio button (default)
   - **Specific Date Range**: Select radio button and choose start/end dates
5. Upload logo if desired
6. Save changes

## File Structure

```
sky_border_website/
├── admin/
│   ├── classes/
│   │   ├── Auth.php              # Authentication system
│   │   ├── ContentManager.php    # Database operations
│   │   └── FileUploader.php     # File handling
│   ├── views/
│   │   └── clients-content.php   # Client management interface
│   ├── clients.php               # Client backend logic
│   ├── setup-services.php        # Database setup script
│   └── database/
│       └── add-services-to-clients.sql  # Migration script
├── index.php                     # Main website frontend
└── README.md                     # This documentation
```

## Services Available

- **Recruitment**: End-to-end recruitment and talent acquisition
- **HR Consulting**: Human resources strategy and process optimization
- **Staffing**: Temporary and contract staffing solutions
- **Training**: Employee development and skills training
- **Compliance**: HR compliance and legal advisory services
- **Visa Processing**: Work permit and visa application services
- **Insurance Services**: Employee insurance and benefits management

## Duration Types

### Currently Ongoing
- Default setting for active client relationships
- Shows green badge with checkmark icon
- No date fields required

### Specific Date Range
- For completed or time-bound projects
- Requires start and end dates
- Shows blue badge with calendar icon
- Displays date range in "MMM YYYY - MMM YYYY" format

## Technical Details

- **Backend**: PHP 7.4+ with PDO database abstraction
- **Frontend**: HTML5, CSS3 (Tailwind CSS), JavaScript
- **Database**: MySQL/MariaDB with proper indexing
- **File Uploads**: Secure file handling with validation
- **Responsive**: Mobile-first design approach
- **Security**: SQL injection prevention, XSS protection

## Support

This project is proprietary software developed for Sky Border Solutions.

For technical support or questions, please contact the development team.
