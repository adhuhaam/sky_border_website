#!/bin/bash

echo "🚀 Installing PHPMailer for Sky Border Solutions"
echo "=================================================="

# Check if Composer is installed
if ! command -v composer &> /dev/null; then
    echo "❌ Composer is not installed. Installing Composer..."
    
    # Download Composer installer
    curl -sS https://getcomposer.org/installer | php
    
    # Move to global location
    if [ -w /usr/local/bin ]; then
        sudo mv composer.phar /usr/local/bin/composer
        sudo chmod +x /usr/local/bin/composer
    else
        echo "⚠️  Cannot write to /usr/local/bin. Moving to current directory..."
        mv composer.phar composer
        echo "✅ Composer installed as 'composer' in current directory"
        echo "   Run: ./composer require phpmailer/phpmailer"
        exit 1
    fi
    
    echo "✅ Composer installed successfully!"
else
    echo "✅ Composer is already installed"
fi

echo ""
echo "📦 Installing PHPMailer..."
composer require phpmailer/phpmailer

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ PHPMailer installed successfully!"
    echo ""
    echo "📁 Files installed in:"
    echo "   - vendor/phpmailer/phpmailer/src/PHPMailer.php"
    echo "   - vendor/phpmailer/phpmailer/src/SMTP.php"
    echo "   - vendor/phpmailer/phpmailer/src/Exception.php"
    echo ""
    echo "🎯 Your email system is now ready!"
    echo "   You can test SMTP and send campaigns from the admin panel."
else
    echo ""
    echo "❌ Failed to install PHPMailer"
    echo "   Please check your internet connection and try again."
    exit 1
fi
