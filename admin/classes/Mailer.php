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
        // Remove scripts and non-email-friendly elements
        $html = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $html);
        $html = preg_replace('/<style\b[^<]*(?:(?!<\/style>)<[^<]*)*<\/style>/mi', '', $html);
        
        // Convert relative URLs to absolute
        $html = preg_replace('/src="\/([^"]*)"/', 'src="' . $baseUrl . '/$1"', $html);
        $html = preg_replace('/href="\/([^"]*)"/', 'href="' . $baseUrl . '/$1"', $html);
        
        // Convert CSS background images
        $html = preg_replace('/url\(\/([^)]*)\)/', 'url(' . $baseUrl . '/$1)', $html);
        
        // Add email-specific CSS
        $emailCSS = '
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .email-container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .email-header { text-align: center; padding: 20px 0; border-bottom: 2px solid #eee; }
            .email-content { padding: 20px 0; }
            .email-footer { text-align: center; padding: 20px 0; border-top: 2px solid #eee; font-size: 12px; color: #666; }
            .tracking-pixel { width: 1px; height: 1px; opacity: 0; }
            .unsubscribe-link { color: #666; text-decoration: none; }
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
                <div class="email-header">
                    <img src="' . $baseUrl . '/images/logo.svg" alt="Sky Border Solutions" style="max-width: 200px;">
                </div>
                <div class="email-content">
                    ' . $bodyContent . '
                </div>
                <div class="email-footer">
                    <p>This email was sent by Sky Border Solutions</p>
                    <p><a href="' . $baseUrl . '/unsubscribe?email={EMAIL}&campaign={CAMPAIGN_ID}" class="unsubscribe-link">Unsubscribe</a></p>
                    <div class="tracking-pixel">
                        <img src="' . $baseUrl . '/track-email.php?type=open&email={EMAIL}&campaign={CAMPAIGN_ID}" width="1" height="1">
                    </div>
                </div>
            </div>
        </body>
        </html>';
        
        return $emailHtml;
    }
    
    /**
     * Create a new campaign
     */
    public function createCampaign($data) {
        $stmt = $this->pdo->prepare("
            INSERT INTO campaigns (name, subject, url_to_render, status, smtp_config_id, scheduled_at)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $data['name'],
            $data['subject'],
            $data['url_to_render'],
            $data['status'],
            $data['smtp_config_id'],
            $data['scheduled_at']
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
