<?php
class Mailer {
    private $pdo;
    private $smtpConfig;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->loadSMTPConfig();
    }
    
    /**
     * Load active SMTP configuration
     */
    private function loadSMTPConfig() {
        $stmt = $this->pdo->prepare("SELECT * FROM smtp_config WHERE is_active = 1 LIMIT 1");
        $stmt->execute();
        $this->smtpConfig = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get SMTP configuration
     */
    public function getSMTPConfig() {
        return $this->smtpConfig;
    }
    
    /**
     * Update SMTP configuration
     */
    public function updateSMTPConfig($data) {
        $stmt = $this->pdo->prepare("
            UPDATE smtp_config SET 
                name = ?, host = ?, port = ?, username = ?, 
                password = ?, encryption = ?, from_email = ?, from_name = ?
            WHERE id = ?
        ");
        
        return $stmt->execute([
            $data['name'], $data['host'], $data['port'], $data['username'],
            $data['password'], $data['encryption'], $data['from_email'], $data['from_name'],
            $data['id']
        ]);
    }
    
    /**
     * Render website as HTML email
     */
    public function renderWebsiteAsEmail($url) {
        // Get the current domain for relative URLs
        $domain = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $baseUrl = $protocol . '://' . $domain;
        
        // If URL is relative, make it absolute
        if (strpos($url, 'http') !== 0) {
            $url = $baseUrl . $url;
        }
        
        // Fetch the website content
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => [
                    'User-Agent: Mozilla/5.0 (compatible; MailerBot/1.0)',
                    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    'Accept-Language: en-US,en;q=0.5',
                    'Accept-Encoding: gzip, deflate',
                    'Connection: keep-alive',
                ]
            ]
        ]);
        
        $html = file_get_contents($url, false, $context);
        
        if ($html === false) {
            throw new Exception("Failed to fetch website content from: $url");
        }
        
        // Convert to email-friendly HTML
        $emailHtml = $this->convertToEmailHTML($html, $baseUrl);
        
        return $emailHtml;
    }
    
    /**
     * Convert website HTML to email-friendly HTML
     */
    private function convertToEmailHTML($html, $baseUrl) {
        // Remove only JavaScript (keep CSS for styling)
        $html = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $html);
        
        // Convert relative URLs to absolute
        $html = preg_replace('/src="\/([^"]*)"/', 'src="' . $baseUrl . '/$1"', $html);
        $html = preg_replace('/href="\/([^"]*)"/', 'href="' . $baseUrl . '/$1"', $html);
        
        // Convert CSS background images
        $html = preg_replace('/url\(\/([^)]*)\)/', 'url(' . $baseUrl . '/$1)', $html);
        
        // Add email-specific CSS
        $emailCSS = '
        <style>
            /* Email-specific overrides for better compatibility */
            body { margin: 0; padding: 0; font-family: Arial, sans-serif; }
            .email-container { max-width: 100%; margin: 0; padding: 0; }
            .email-content { width: 100%; max-width: 100%; }
            /* Ensure images scale properly */
            img { max-width: 100%; height: auto; }
            /* Fix button styling for email clients */
            .btn, button { display: inline-block; text-decoration: none; }
            /* Ensure proper spacing */
            * { box-sizing: border-box; }
            /* Preserve gradients and colors */
            .bg-gradient-to-r { background: linear-gradient(to right, var(--gradient-colors)) !important; }
            /* Additional email client compatibility */
            .floating-element { animation: none !important; }
            .scroll-reveal { animation: none !important; }
            .animate-float { animation: none !important; }
            .animate-pulse { animation: none !important; }
            /* Ensure proper text rendering */
            h1, h2, h3, h4, h5, h6 { margin: 0.5em 0; }
            p { margin: 0.5em 0; }
            /* Fix for Outlook */
            table { border-collapse: collapse; }
            td { vertical-align: top; }
        </style>';
        
        // Extract body content
        if (preg_match('/<body[^>]*>(.*?)<\/body>/si', $html, $matches)) {
            $bodyContent = $matches[1];
        } else {
            $bodyContent = $html;
        }
        
        // Create email template
        $emailHtml = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Sky Border Solutions</title>
            ' . $emailCSS . '
        </head>
        <body>
            <div class="email-container">
                <div class="email-content">
                    ' . $bodyContent . '
                </div>
                <div class="email-footer" style="text-align: center; padding: 20px; background: #f8f9fa; border-top: 1px solid #dee2e6; margin-top: 30px;">
                    <p style="margin: 0 0 10px 0; color: #6c757d; font-size: 14px;">This email was sent by Sky Border Solutions</p>
                    <p style="margin: 0 0 10px 0; font-size: 12px;">
                        <a href="' . $baseUrl . '/unsubscribe?email={EMAIL}&campaign={CAMPAIGN_ID}" style="color: #6c757d; text-decoration: none;">Unsubscribe</a>
                    </p>
                    <div style="width: 1px; height: 1px; opacity: 0;">
                        <img src="' . $baseUrl . '/track-email.php?type=open&email={EMAIL}&campaign={CAMPAIGN_ID}" width="1" height="1">
                    </div>
                </div>
            </div>
        </body>
        </html>';
        
        return $emailHtml;
    }
    
    /**
     * Convert Tailwind CSS classes to inline styles for email compatibility
     */
    private function convertTailwindToInlineStyles($html) {
        // Common Tailwind to inline style mappings
        $tailwindMap = [
            // Layout
            'container' => 'max-width: 1200px; margin: 0 auto; padding: 0 1rem;',
            'mx-auto' => 'margin-left: auto; margin-right: auto;',
            'px-4' => 'padding-left: 1rem; padding-right: 1rem;',
            'px-8' => 'padding-left: 2rem; padding-right: 2rem;',
            'py-8' => 'padding-top: 2rem; padding-bottom: 2rem;',
            'py-4' => 'padding-top: 1rem; padding-bottom: 1rem;',
            'mb-8' => 'margin-bottom: 2rem;',
            'mt-8' => 'margin-top: 2rem;',
            'mb-4' => 'margin-bottom: 1rem;',
            'mt-4' => 'margin-top: 1rem;',
            
            // Colors
            'bg-white' => 'background-color: #ffffff;',
            'bg-slate-900' => 'background-color: #0f172a;',
            'bg-slate-800' => 'background-color: #1e293b;',
            'bg-slate-700' => 'background-color: #334155;',
            'bg-slate-600' => 'background-color: #475569;',
            'bg-slate-500' => 'background-color: #64748b;',
            'bg-slate-400' => 'background-color: #94a3b8;',
            'bg-slate-300' => 'background-color: #cbd5e1;',
            'bg-slate-200' => 'background-color: #e2e8f0;',
            'bg-slate-100' => 'background-color: #f1f5f9;',
            'bg-slate-50' => 'background-color: #f8fafc;',
            
            'text-white' => 'color: #ffffff;',
            'text-slate-900' => 'color: #0f172a;',
            'text-slate-800' => 'color: #1e293b;',
            'text-slate-700' => 'color: #334155;',
            'text-slate-600' => 'color: #475569;',
            'text-slate-500' => 'color: #64748b;',
            'text-slate-400' => 'color: #94a3b8;',
            'text-slate-300' => 'color: #cbd5e1;',
            'text-slate-200' => 'color: #e2e8f0;',
            'text-slate-100' => 'color: #f1f5f9;',
            
            // Blue colors
            'bg-blue-800' => 'background-color: #1e40af;',
            'bg-blue-900' => 'background-color: #1e3a8a;',
            'text-blue-600' => 'color: #2563eb;',
            'text-blue-700' => 'color: #1d4ed8;',
            'text-blue-800' => 'color: #1e40af;',
            
            // Green colors
            'bg-green-700' => 'background-color: #15803d;',
            'bg-green-800' => 'background-color: #166534;',
            'text-green-600' => 'color: #16a34a;',
            'text-green-700' => 'color: #15803d;',
            
            // Gradients - convert to solid colors for email compatibility
            'bg-gradient-to-r' => 'background: linear-gradient(to right, #1e40af, #166534);',
            'from-blue-800' => 'background-color: #1e40af;',
            'to-blue-900' => 'background-color: #1e3a8a;',
            'from-green-700' => 'background-color: #15803d;',
            'to-green-800' => 'background-color: #166534;',
            'from-blue-600' => 'background-color: #2563eb;',
            'to-indigo-600' => 'background-color: #4f46e5;',
            'via-indigo-600' => 'background-color: #4f46e5;',
            'to-purple-600' => 'background-color: #9333ea;',
            
            // Typography
            'text-4xl' => 'font-size: 2.25rem; line-height: 2.5rem;',
            'text-3xl' => 'font-size: 1.875rem; line-height: 2.25rem;',
            'text-2xl' => 'font-size: 1.5rem; line-height: 2rem;',
            'text-xl' => 'font-size: 1.25rem; line-height: 1.75rem;',
            'text-lg' => 'font-size: 1.125rem; line-height: 1.75rem;',
            'text-base' => 'font-size: 1rem; line-height: 1.5rem;',
            'text-sm' => 'font-size: 0.875rem; line-height: 1.25rem;',
            'text-xs' => 'font-size: 0.75rem; line-height: 1rem;',
            'font-bold' => 'font-weight: 700;',
            'font-semibold' => 'font-weight: 600;',
            'font-medium' => 'font-weight: 500;',
            'text-center' => 'text-align: center;',
            'text-left' => 'text-align: left;',
            'text-right' => 'text-align: right;',
            
            // Spacing
            'p-6' => 'padding: 1.5rem;',
            'p-4' => 'padding: 1rem;',
            'p-2' => 'padding: 0.5rem;',
            'm-4' => 'margin: 1rem;',
            'm-2' => 'margin: 0.5rem;',
            'rounded-lg' => 'border-radius: 0.5rem;',
            'rounded-2xl' => 'border-radius: 1rem;',
            'rounded-xl' => 'border-radius: 0.75rem;',
            'rounded-md' => 'border-radius: 0.375rem;',
            'rounded' => 'border-radius: 0.25rem;',
            
            // Flexbox
            'flex' => 'display: flex;',
            'flex-col' => 'flex-direction: column;',
            'flex-row' => 'flex-direction: row;',
            'items-center' => 'align-items: center;',
            'items-start' => 'align-items: flex-start;',
            'items-end' => 'align-items: flex-end;',
            'justify-center' => 'justify-content: center;',
            'justify-between' => 'justify-content: space-between;',
            'justify-start' => 'justify-content: flex-start;',
            'justify-end' => 'justify-content: flex-end;',
            'justify-around' => 'justify-content: space-around;',
            'justify-evenly' => 'justify-content: space-evenly;',
            
            // Grid
            'grid' => 'display: grid;',
            'grid-cols-1' => 'grid-template-columns: repeat(1, minmax(0, 1fr));',
            'grid-cols-2' => 'grid-template-columns: repeat(2, minmax(0, 1fr));',
            'grid-cols-3' => 'grid-template-columns: repeat(3, minmax(0, 1fr));',
            'gap-4' => 'gap: 1rem;',
            'gap-6' => 'gap: 1.5rem;',
            'gap-8' => 'gap: 2rem;',
            
            // Shadows
            'shadow-lg' => 'box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);',
            'shadow-xl' => 'box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);',
            'shadow-md' => 'box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);',
            'shadow-sm' => 'box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);',
            
            // Responsive
            'sm:' => '',
            'md:' => '',
            'lg:' => '',
            'xl:' => '',
            '2xl:' => '',
            
            // Hover states (convert to regular styles for email)
            'hover:' => '',
            'focus:' => '',
            'active:' => '',
            'group-hover:' => '',
        ];
        
        // Apply the mappings
        foreach ($tailwindMap as $class => $style) {
            $html = str_replace('class="' . $class . '"', 'style="' . $style . '"', $html);
            $html = str_replace('class="' . $class . ' ', 'style="' . $style . ' ', $html);
            $html = str_replace(' ' . $class . '"', ' ' . $style . '"', $html);
        }
        
        // Handle complex gradient combinations
        $html = preg_replace('/class="([^"]*bg-gradient-to-r[^"]*from-blue-800[^"]*to-green-800[^"]*)"/', 'style="background: linear-gradient(to right, #1e40af, #166534);"', $html);
        $html = preg_replace('/class="([^"]*bg-gradient-to-r[^"]*from-blue-600[^"]*to-indigo-600[^"]*)"/', 'style="background: linear-gradient(to right, #2563eb, #4f46e5);"', $html);
        $html = preg_replace('/class="([^"]*bg-gradient-to-r[^"]*from-green-700[^"]*to-green-800[^"]*)"/', 'style="background: linear-gradient(to right, #15803d, #166534);"', $html);
        
        return $html;
    }
    
    /**
     * Create a new campaign
     */
    public function createCampaign($data) {
        $stmt = $this->pdo->prepare("
            INSERT INTO campaigns (name, subject, url_to_render, status, smtp_config_id, scheduled_at, rendered_html)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $data['name'],
            $data['subject'],
            $data['url_to_render'],
            $data['status'],
            $data['smtp_config_id'],
            $data['scheduled_at'],
            $data['rendered_html'] ?? null
        ]);
        
        return $this->pdo->lastInsertId();
    }
    
    /**
     * Update campaign
     */
    public function updateCampaign($id, $data) {
        $stmt = $this->pdo->prepare("
            UPDATE campaigns SET 
                name = ?, subject = ?, url_to_render = ?, status = ?, 
                smtp_config_id = ?, scheduled_at = ?, rendered_html = ?
            WHERE id = ?
        ");
        
        return $stmt->execute([
            $data['name'], $data['subject'], $data['url_to_render'], $data['status'],
            $data['smtp_config_id'], $data['scheduled_at'], $data['rendered_html'],
            $id
        ]);
    }
    
    /**
     * Get campaign by ID
     */
    public function getCampaign($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM campaigns WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get all campaigns
     */
    public function getAllCampaigns() {
        $stmt = $this->pdo->prepare("
            SELECT c.*, 
                   COUNT(cr.id) as total_recipients,
                   COUNT(CASE WHEN cr.status = 'sent' THEN 1 END) as sent_count,
                   COUNT(CASE WHEN cr.status = 'delivered' THEN 1 END) as delivered_count,
                   COUNT(CASE WHEN cr.status = 'opened' THEN 1 END) as opened_count,
                   COUNT(CASE WHEN cr.status = 'clicked' THEN 1 END) as clicked_count
            FROM campaigns c
            LEFT JOIN campaign_recipients cr ON c.id = cr.campaign_id
            GROUP BY c.id
            ORDER BY c.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Add recipients to campaign
     */
    public function addCampaignRecipients($campaignId, $contactIds, $listId = null) {
        $stmt = $this->pdo->prepare("
            INSERT INTO campaign_recipients (campaign_id, contact_id, list_id, email, tracking_id)
            SELECT ?, c.id, ?, c.email, CONCAT(?, '_', c.id, '_', UNIX_TIMESTAMP())
            FROM contacts c
            WHERE c.id IN (" . str_repeat('?,', count($contactIds) - 1) . "?)
            AND c.status = 'active'
        ");
        
        $params = array_merge([$campaignId, $listId, $campaignId], $contactIds);
        return $stmt->execute($params);
    }
    
    /**
     * Test campaign by sending to admin email
     */
    public function testCampaign($campaignId) {
        try {
            $campaign = $this->getCampaign($campaignId);
            if (!$campaign) {
                return ['success' => false, 'error' => 'Campaign not found'];
            }
            
            // Get SMTP config
            $smtpConfig = $this->getSMTPConfig();
            if (!$smtpConfig) {
                return ['success' => false, 'error' => 'No active SMTP configuration found'];
            }
            
            // Render the website as email
            $renderedHtml = $this->renderWebsiteAsEmail('/');
            
            // Send test email to admin using PHPMailer
            try {
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
                
                // Server settings
                $mail->isSMTP();
                $mail->Host = $smtpConfig['host'];
                $mail->SMTPAuth = true;
                $mail->Username = $smtpConfig['username'];
                $mail->Password = $smtpConfig['password'];
                $mail->SMTPSecure = $smtpConfig['encryption'];
                $mail->Port = $smtpConfig['port'];
                
                // Recipients
                $mail->setFrom($smtpConfig['from_email'], $smtpConfig['from_name']);
                $mail->addAddress($smtpConfig['username']); // Send to admin email
                
                // Content
                $mail->isHTML(true);
                $mail->Subject = '[TEST] ' . $campaign['subject'];
                $mail->Body = $renderedHtml;
                
                $mail->send();
                
                return ['success' => true, 'message' => 'Test campaign sent successfully to ' . $smtpConfig['username']];
                
            } catch (Exception $e) {
                return ['success' => false, 'error' => 'Email could not be sent. Mailer Error: ' . $e->getMessage()];
            }
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Send campaign emails
     */
    public function sendCampaign($campaignId, $batchSize = 10) {
        $campaign = $this->getCampaign($campaignId);
        if (!$campaign) {
            throw new Exception("Campaign not found");
        }
        
        // Get pending recipients
        $stmt = $this->pdo->prepare("
            SELECT cr.*, c.email, c.name
            FROM campaign_recipients cr
            JOIN contacts c ON cr.contact_id = c.id
            WHERE cr.campaign_id = ? AND cr.status = 'pending'
            LIMIT ?
        ");
        $stmt->execute([$campaignId, $batchSize]);
        $recipients = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $sentCount = 0;
        $errors = [];
        
        foreach ($recipients as $recipient) {
            try {
                $success = $this->sendSingleEmail($campaign, $recipient);
                if ($success) {
                    $this->updateRecipientStatus($recipient['id'], 'sent');
                    $this->logEmailEvent($campaignId, $recipient['id'], 'sent');
                    $sentCount++;
                } else {
                    $this->updateRecipientStatus($recipient['id'], 'failed', 'Failed to send email');
                    $this->logEmailEvent($campaignId, $recipient['id'], 'failed');
                    $errors[] = "Failed to send to {$recipient['email']}";
                }
            } catch (Exception $e) {
                $this->updateRecipientStatus($recipient['id'], 'failed', $e->getMessage());
                $this->logEmailEvent($campaignId, $recipient['id'], 'failed');
                $errors[] = "Error sending to {$recipient['email']}: " . $e->getMessage();
            }
        }
        
        // Update campaign status if all emails sent
        if ($sentCount > 0) {
            $this->updateCampaignStatus($campaignId, 'sent');
        }
        
        return [
            'sent' => $sentCount,
            'errors' => $errors
        ];
    }
    
    /**
     * Send single email
     */
    private function sendSingleEmail($campaign, $recipient) {
        if (!$this->smtpConfig) {
            throw new Exception("No SMTP configuration found");
        }
        
        // Prepare email content
        $emailHtml = $this->prepareEmailContent($campaign, $recipient);
        
        // Use PHPMailer or similar for SMTP sending
        // For now, we'll simulate sending
        return $this->sendViaSMTP($recipient['email'], $campaign['subject'], $emailHtml);
    }
    
    /**
     * Prepare email content with personalization
     */
    private function prepareEmailContent($campaign, $recipient) {
        $html = $campaign['rendered_html'];
        
        // Replace placeholders
        $html = str_replace('{EMAIL}', $recipient['email'], $html);
        $html = str_replace('{NAME}', $recipient['name'], $html);
        $html = str_replace('{CAMPAIGN_ID}', $campaign['id'], $html);
        
        return $html;
    }
    
    /**
     * Send email via SMTP
     */
    private function sendViaSMTP($to, $subject, $html) {
        // This is a simplified version - in production, use PHPMailer
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . $this->smtpConfig['from_name'] . ' <' . $this->smtpConfig['from_email'] . '>',
            'Reply-To: ' . $this->smtpConfig['from_email'],
            'X-Mailer: Sky Border Mailer'
        ];
        
        // For now, simulate successful sending
        // In production, implement actual SMTP sending here
        return mail($to, $subject, $html, implode("\r\n", $headers));
    }
    
    /**
     * Update recipient status
     */
    private function updateRecipientStatus($recipientId, $status, $errorMessage = null) {
        $stmt = $this->pdo->prepare("
            UPDATE campaign_recipients SET 
                status = ?, 
                " . ($status === 'sent' ? 'sent_at = NOW()' : '') . "
                " . ($status === 'delivered' ? 'delivered_at = NOW()' : '') . "
                " . ($status === 'opened' ? 'opened_at = NOW()' : '') . "
                " . ($status === 'clicked' ? 'clicked_at = NOW()' : '') . "
                " . ($status === 'bounced' ? 'bounced_at = NOW()' : '') . "
                " . ($status === 'unsubscribed' ? 'unsubscribed_at = NOW()' : '') . "
                error_message = ?
            WHERE id = ?
        ");
        
        return $stmt->execute([$status, $errorMessage, $recipientId]);
    }
    
    /**
     * Update campaign status
     */
    private function updateCampaignStatus($campaignId, $status) {
        $stmt = $this->pdo->prepare("
            UPDATE campaigns SET 
                status = ?, 
                " . ($status === 'sent' ? 'sent_at = NOW()' : '') . "
            WHERE id = ?
        ");
        
        return $stmt->execute([$status, $campaignId]);
    }
    
    /**
     * Log email event
     */
    private function logEmailEvent($campaignId, $recipientId, $eventType, $eventData = null) {
        $stmt = $this->pdo->prepare("
            INSERT INTO email_events (campaign_id, recipient_id, event_type, event_data, ip_address, user_agent)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $campaignId,
            $recipientId,
            $eventType,
            $eventData ? json_encode($eventData) : null,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    }
    
    /**
     * Track email open
     */
    public function trackEmailOpen($trackingId) {
        $stmt = $this->pdo->prepare("
            SELECT cr.*, c.id as campaign_id
            FROM campaign_recipients cr
            JOIN campaigns c ON cr.campaign_id = c.id
            WHERE cr.tracking_id = ?
        ");
        $stmt->execute([$trackingId]);
        $recipient = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($recipient) {
            $this->updateRecipientStatus($recipient['id'], 'opened');
            $this->logEmailEvent($recipient['campaign_id'], $recipient['id'], 'opened');
        }
    }
    
    /**
     * Track email click
     */
    public function trackEmailClick($trackingId) {
        $stmt = $this->pdo->prepare("
            SELECT cr.*, c.id as campaign_id
            FROM campaign_recipients cr
            JOIN campaigns c ON cr.campaign_id = c.id
            WHERE cr.tracking_id = ?
        ");
        $stmt->execute([$trackingId]);
        $recipient = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($recipient) {
            $this->updateRecipientStatus($recipient['id'], 'clicked');
            $this->logEmailEvent($recipient['campaign_id'], $recipient['id'], 'clicked');
        }
    }
    
    /**
     * Get campaign analytics
     */
    public function getCampaignAnalytics($campaignId) {
        $stmt = $this->pdo->prepare("
            SELECT 
                COUNT(*) as total_recipients,
                COUNT(CASE WHEN status = 'sent' THEN 1 END) as sent,
                COUNT(CASE WHEN status = 'delivered' THEN 1 END) as delivered,
                COUNT(CASE WHEN status = 'opened' THEN 1 END) as opened,
                COUNT(CASE WHEN status = 'clicked' THEN 1 END) as clicked,
                COUNT(CASE WHEN status = 'bounced' THEN 1 END) as bounced,
                COUNT(CASE WHEN status = 'unsubscribed' THEN 1 END) as unsubscribed,
                COUNT(CASE WHEN status = 'failed' THEN 1 END) as failed
            FROM campaign_recipients
            WHERE campaign_id = ?
        ");
        $stmt->execute([$campaignId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get overall mailer statistics
     */
    public function getMailerStats() {
        $stats = [];
        
        // Total contacts
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM contacts");
        $stats['total_contacts'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        // Active contacts
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM contacts WHERE status = 'active'");
        $stats['active_contacts'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        // Total campaigns
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM campaigns");
        $stats['total_campaigns'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        // Total emails sent
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM campaign_recipients WHERE status IN ('sent', 'delivered', 'opened', 'clicked')");
        $stats['total_emails_sent'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        // Open rate
        $stmt = $this->pdo->query("
            SELECT 
                ROUND(
                    (COUNT(CASE WHEN status = 'opened' THEN 1 END) * 100.0) / 
                    COUNT(CASE WHEN status IN ('sent', 'delivered', 'opened', 'clicked') THEN 1 END), 
                    2
                ) as open_rate
            FROM campaign_recipients 
            WHERE status IN ('sent', 'delivered', 'opened', 'clicked')
        ");
        $stats['open_rate'] = $stmt->fetch(PDO::FETCH_ASSOC)['open_rate'] ?? 0;
        
        // Click rate
        $stmt = $this->pdo->query("
            SELECT 
                ROUND(
                    (COUNT(CASE WHEN status = 'clicked' THEN 1 END) * 100.0) / 
                    COUNT(CASE WHEN status IN ('sent', 'delivered', 'opened', 'clicked') THEN 1 END), 
                    2
                ) as click_rate
            FROM campaign_recipients 
            WHERE status IN ('sent', 'delivered', 'opened', 'clicked')
        ");
        $stats['click_rate'] = $stmt->fetch(PDO::FETCH_ASSOC)['click_rate'] ?? 0;
        
        return $stats;
    }
}
?>
