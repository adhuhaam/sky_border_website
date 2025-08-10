# Sky Border Solutions Website

A professional HR consultancy and recruitment firm website with comprehensive client management capabilities.

## Features

- **Client Management**: Add, edit, and manage client portfolios with logos and service information
- **Services Tracking**: Track which services are provided to each client (Recruitment, HR Consulting, Staffing, etc.)
- **Admin Backend**: Full administrative interface for content management
- **Responsive Design**: Modern, mobile-friendly interface
- **Dynamic Content**: Database-driven content with fallback support

## Recent Updates

### Services Functionality Added
- Added `services` field to clients table
- Multiple services can be selected per client
- Services are displayed on both admin and frontend
- Available services include:
  - Recruitment
  - HR Consulting
  - Staffing
  - Training
  - Compliance
  - Visa Processing
  - Insurance Services

## Setup Instructions

### 1. Database Setup
Run the migration script to add the services field:
```bash
# Access your admin panel and navigate to:
admin/setup-services.php
```

Or run the SQL directly:
```sql
-- Add services field to clients table
ALTER TABLE clients ADD COLUMN IF NOT EXISTS services TEXT AFTER description;

-- Add index for performance
CREATE INDEX IF NOT EXISTS idx_clients_services ON clients(services(100));
```

### 2. Admin Configuration
1. Access the admin panel at `/admin/`
2. Navigate to Clients Management
3. Add or edit clients and select their services from the dropdown
4. Services will be automatically displayed on the frontend

### 3. Frontend Display
Services are automatically displayed under each client's name on the main website in the Clients section.

## File Structure

```
sky_border_website/
├── admin/                    # Admin backend
│   ├── classes/             # PHP classes
│   ├── views/               # Admin view templates
│   ├── database/            # Database scripts
│   └── setup-services.php   # Services setup script
├── images/                  # Website images
└── index.php               # Main website frontend
```

## Database Schema

### Clients Table
- `id` - Primary key
- `client_name` - Client company name
- `category_id` - Client category reference
- `logo_url` - Client logo file path
- `services` - Comma-separated list of services provided
- `display_order` - Display order for frontend
- `is_active` - Active status

## Admin Features

- **Client Management**: Full CRUD operations for clients
- **Service Selection**: Multiple service selection via dropdown
- **Logo Upload**: File upload with preview and validation
- **Category Management**: Organize clients by industry/category
- **Display Order**: Control client display order on frontend

## Frontend Features

- **Responsive Design**: Mobile-first approach
- **Client Showcase**: Display clients grouped by category
- **Service Display**: Show services under each client name
- **Dynamic Content**: Database-driven with fallback support
- **Modern UI**: Clean, professional design

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## Requirements

- PHP 7.4+
- MySQL 5.7+ or MariaDB 10.2+
- Web server (Apache/Nginx)
- File upload permissions

## Support

For technical support or questions, please contact the development team.

## License

This project is proprietary software developed for Sky Border Solutions.
