# 🚀 Sky Border Solutions - Database Installation Guide

## 📋 **Prerequisites**
- MySQL/MariaDB database access
- Database name: `skydfcaf_sky_border`
- Admin privileges to create tables

## 🔧 **Installation Steps**

### **Step 1: Run the Database Setup Script**
1. **Access your database** (phpMyAdmin, MySQL Workbench, or command line)
2. **Select your database**: `USE skydfcaf_sky_border;`
3. **Run the setup script**: Copy and paste the contents of `setup-all-tables.sql`
4. **Verify completion**: You should see "Database setup completed successfully!"

### **Step 2: Install PHPMailer (Optional but Recommended)**
```bash
# In your project root directory
composer require phpmailer/phpmailer
```

**OR manually download:**
1. Download PHPMailer from: https://github.com/PHPMailer/PHPMailer
2. Extract to: `admin/classes/PHPMailer/`
3. Update the Mailer.php file to use the correct path

### **Step 3: Test the System**
1. **Go to Admin Panel** → SMTP Settings
2. **Add your SMTP configuration**:
   - Host: `skybordersolutions.com`
   - Port: `465`
   - Encryption: `SSL`
   - Username: `hello@skybordersolutions.com`
   - Password: `Ompl@65482*`
3. **Test the connection**
4. **Create a test campaign**

## 🗄️ **Tables Created**

| Table | Purpose |
|-------|---------|
| `campaigns` | Email campaigns |
| `smtp_config` | SMTP server configurations |
| `contacts` | Contact database |
| `contact_lists` | Contact list management |
| `contact_list_contacts` | Contact-list relationships |
| `campaign_recipients` | Campaign delivery tracking |
| `email_events` | Email engagement tracking |
| `unsubscribes` | Unsubscribe management |
| `bounces` | Bounce tracking |

## 🔍 **Troubleshooting**

### **Common Issues & Solutions**

#### **1. "Table doesn't exist" errors**
- ✅ **Solution**: Run the `setup-all-tables.sql` script
- ✅ **Verify**: Check if tables exist with `SHOW TABLES;`

#### **2. "PHPMailer class not found"**
- ✅ **Solution**: Install PHPMailer via Composer
- ✅ **Alternative**: Download manually and update paths

#### **3. "Undefined array key 'status'"**
- ✅ **Solution**: The setup script adds the `status` column to contacts table
- ✅ **Verify**: Check table structure with `DESCRIBE contacts;`

#### **4. "Column not found" errors**
- ✅ **Solution**: Run the complete setup script
- ✅ **Verify**: All columns are created with proper names

## 📧 **How the Campaign System Works**

### **1. Campaign Creation**
- Admin creates campaign with name, subject
- System automatically renders front site (`index.php`) as HTML
- Converts Tailwind CSS to email-compatible inline styles
- Stores rendered HTML in database

### **2. Email Delivery**
- System uses configured SMTP settings
- Sends beautiful HTML emails to recipients
- Tracks delivery, opens, clicks, bounces
- Manages unsubscribes automatically

### **3. Front Site as Template**
- **No more manual HTML coding!**
- Your website automatically becomes the email template
- All styling, colors, and content preserved
- Responsive design for all email clients

## 🎯 **Key Features**

- ✅ **Automatic front site rendering** as email template
- ✅ **SMTP configuration management**
- ✅ **Contact list management**
- ✅ **Campaign testing** (preview before sending)
- ✅ **Delivery tracking** and analytics
- ✅ **Bounce and unsubscribe management**
- ✅ **Professional email templates** from your website

## 🚀 **Next Steps After Installation**

1. **Configure SMTP settings** with your email server
2. **Add contacts** to your database
3. **Create contact lists** for different audiences
4. **Test campaign creation** and preview
5. **Send your first campaign!**

## 📞 **Support**

If you encounter any issues:
1. Check the error logs in your hosting panel
2. Verify database table structure
3. Test SMTP connection settings
4. Ensure PHPMailer is properly installed

---

**🎉 Congratulations!** Your email campaign system is now ready to send beautiful, professional emails using your website as the template.
