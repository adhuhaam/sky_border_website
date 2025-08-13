# 📧 PHPMailer Installation Guide for Sky Border Solutions

## 🎯 **What is PHPMailer?**

PHPMailer is a popular PHP library that makes it easy to send emails via SMTP. It's essential for your email campaign system to work properly.

## 🚀 **Installation Methods**

### **Method 1: Automatic Installation (Recommended)**

1. **Make sure you're in your project directory:**
   ```bash
   cd /Users/adhuhaam/Music/sky_web/sky_border_website
   ```

2. **Run the installation script:**
   ```bash
   ./install-phpmailer.sh
   ```

   This script will:
   - Check if Composer is installed
   - Install Composer if needed
   - Install PHPMailer automatically
   - Set up all necessary files

### **Method 2: Manual Installation with Composer**

1. **Install Composer (if not already installed):**
   ```bash
   curl -sS https://getcomposer.org/installer | php
   sudo mv composer.phar /usr/local/bin/composer
   ```

2. **Install PHPMailer:**
   ```bash
   composer require phpmailer/phpmailer
   ```

### **Method 3: Manual Download (Alternative)**

1. **Download PHPMailer:**
   - Go to: https://github.com/PHPMailer/PHPMailer
   - Click "Code" → "Download ZIP"
   - Extract the ZIP file

2. **Create directory structure:**
   ```bash
   mkdir -p admin/classes/PHPMailer/src
   mkdir -p admin/classes/PHPMailer/language
   ```

3. **Copy files:**
   ```bash
   # Copy main files
   cp PHPMailer-master/src/PHPMailer.php admin/classes/PHPMailer/src/
   cp PHPMailer-master/src/SMTP.php admin/classes/PHPMailer/src/
   cp PHPMailer-master/src/Exception.php admin/classes/PHPMailer/src/
   
   # Copy language files (optional)
   cp PHPMailer-master/language/*.php admin/classes/PHPMailer/language/
   ```

## 📁 **File Structure After Installation**

### **With Composer (Recommended):**
```
sky_border_website/
├── vendor/
│   └── phpmailer/
│       └── phpmailer/
│           ├── src/
│           │   ├── PHPMailer.php
│           │   ├── SMTP.php
│           │   └── Exception.php
│           └── language/
│               └── phpmailer.lang-en.php
├── composer.json
└── composer.lock
```

### **With Manual Download:**
```
sky_border_website/
├── admin/
│   └── classes/
│       └── PHPMailer/
│           ├── src/
│           │   ├── PHPMailer.php
│           │   ├── SMTP.php
│           │   └── Exception.php
│           └── language/
│               └── phpmailer.lang-en.php
```

## 🔧 **How Your Code Uses PHPMailer**

Your updated `Mailer.php` and `ContentManager.php` files now automatically detect PHPMailer:

```php
// Check if PHPMailer is available via Composer
if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
} 
// Check if PHPMailer is available in local classes directory
elseif (class_exists('PHPMailer')) {
    $mail = new PHPMailer(true);
} else {
    return ['success' => false, 'error' => 'PHPMailer not found. Please install it first.'];
}
```

## ✅ **Verification Steps**

### **1. Check if PHPMailer is working:**
```bash
# If using Composer
php -r "require 'vendor/autoload.php'; echo 'PHPMailer loaded successfully!';"

# If using manual installation
php -r "require 'admin/classes/PHPMailer/src/PHPMailer.php'; echo 'PHPMailer loaded successfully!';"
```

### **2. Test from Admin Panel:**
1. Go to **Admin Panel → SMTP Settings**
2. Add your SMTP configuration
3. Click **"Test Email Send"**
4. You should see: "Test email sent successfully to hello@skybordersolutions.com"

### **3. Test Campaign System:**
1. Go to **Admin Panel → Campaigns**
2. Create a test campaign
3. Click **"Test"** button
4. You should receive a test email

## 🚨 **Common Issues & Solutions**

### **Issue 1: "PHPMailer class not found"**
**Solution:**
- Make sure PHPMailer is installed
- Check file paths are correct
- Verify Composer autoloader is working

### **Issue 2: "Composer not found"**
**Solution:**
- Install Composer manually
- Or use the manual download method

### **Issue 3: "Permission denied"**
**Solution:**
```bash
chmod +x install-phpmailer.sh
```

### **Issue 4: "SMTP connection failed"**
**Solution:**
- Verify your SMTP settings
- Check firewall settings
- Ensure port 465 is open for SSL

## 🎯 **Your SMTP Configuration**

Once PHPMailer is installed, use these settings:

- **Host:** `skybordersolutions.com`
- **Port:** `465`
- **Encryption:** `SSL`
- **Username:** `hello@skybordersolutions.com`
- **Password:** `Ompl@65482*`
- **From Email:** `hello@skybordersolutions.com`
- **From Name:** `Sky Border Solutions`

## 🚀 **Next Steps After Installation**

1. **Test SMTP Connection** in Admin Panel
2. **Create Your First Campaign**
3. **Send Test Emails**
4. **Start Building Your Email Lists**

## 📞 **Need Help?**

If you encounter issues:
1. Check the error messages in your admin panel
2. Verify PHPMailer files are in the correct location
3. Test SMTP settings with a simple email client first
4. Check your hosting provider's SMTP restrictions

---

**🎉 After installing PHPMailer, your email campaign system will be fully functional!**
