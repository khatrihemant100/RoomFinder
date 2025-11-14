<?php
// Inquiry Email Sender Class
// Based on 15_mail/Contact.php - using exact same structure

// Load vendor from 15_mail directory
require_once __DIR__ . '/../15_mail/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

class InquiryMailer
{
    private $mailer;
    private $template;
    
    private string $from_address;
    private string $from_name;
    private string $host;
    private string $username;
    private string $password;
    private string $encryption;
    private int $port;

    public function __construct()
    {
        // Use same path structure as Contact.php
        $this->template = __DIR__ . "/../15_mail/templates/inquiry_mail.html";
        
        // Load .env from 15_mail directory (same as Contact.php)
        try {
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../15_mail');
            $dotenv->load();
        } catch (Exception $e) {
            // .env file not found or error loading - use defaults
            error_log("Warning: Could not load .env file: " . $e->getMessage());
        }

        // Get values from .env (same as Contact.php) with fallbacks
        $this->from_address = $_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@roomfinder.com';
        $this->from_name    = $_ENV['MAIL_FROM_NAME'] ?? 'RoomFinder';
        $this->host         = $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com';
        $this->username     = $_ENV['MAIL_USERNAME'] ?? '';
        $this->password     = $_ENV['MAIL_PASSWORD'] ?? '';
        $this->encryption   = $_ENV['MAIL_ENCRYPTION'] ?? 'tls';
        $this->port         = isset($_ENV['MAIL_PORT']) ? (int)$_ENV['MAIL_PORT'] : 587;

        $this->setupMailer();
    }

    private function setupMailer(): void
    {
        // Exact same setup as Contact.php
        $this->mailer = new PHPMailer(true);
        $this->mailer->isSMTP();
        $this->mailer->SMTPAuth   = true;
        $this->mailer->Host       = $this->host;
        $this->mailer->Username   = $this->username;
        $this->mailer->Password   = $this->password;
        $this->mailer->SMTPSecure = $this->encryption;
        $this->mailer->Port       = $this->port;
        $this->mailer->CharSet    = 'UTF-8';
        $this->mailer->Encoding   = 'base64';
    }

    /**
     * Load and replace template variables (same as Contact.php)
     */
    private function loadTemplate($values)
    {
        // Try to load template file
        if (file_exists($this->template)) {
            $template = file_get_contents($this->template);
        } else {
            // Fallback template if file doesn't exist
            $template = $this->getDefaultTemplate();
        }
        
        // Replace variables (same method as Contact.php)
        foreach ($values as $key => $value) {
            $template = str_replace("{{{$key}}}", $value, $template);
        }
        return $template;
    }

    /**
     * Default email template
     */
    private function getDefaultTemplate()
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #4A90E2; color: white; padding: 20px; text-align: center; }
                .content { background: #f9f9f9; padding: 20px; margin-top: 20px; }
                .info-box { background: white; padding: 15px; margin: 10px 0; border-left: 4px solid #4A90E2; }
                .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>New Room Inquiry - RoomFinder</h1>
                </div>
                <div class="content">
                    <p>Hello {{{owner_name}}},</p>
                    <p>You have received a new inquiry for your property listing.</p>
                    
                    <div class="info-box">
                        <h3>Property Details:</h3>
                        <p><strong>Title:</strong> {{{room_title}}}</p>
                        <p><strong>Location:</strong> {{{room_location}}}</p>
                        <p><strong>Price:</strong> ¥{{{room_price}}}</p>
                    </div>
                    
                    <div class="info-box">
                        <h3>Inquiry Details:</h3>
                        <p><strong>Name:</strong> {{{inquirer_name}}}</p>
                        <p><strong>Email:</strong> {{{inquirer_email}}}</p>
                        <p><strong>Phone:</strong> {{{inquirer_phone}}}</p>
                        <p><strong>Preferred Visit Date:</strong> {{{visit_date}}}</p>
                        <p><strong>Message:</strong><br>{{{message}}}</p>
                    </div>
                    
                    <p>Please contact the inquirer at your earliest convenience.</p>
                    <p>Best regards,<br>RoomFinder Team</p>
                </div>
                <div class="footer">
                    <p>This is an automated email from RoomFinder.</p>
                </div>
            </div>
        </body>
        </html>';
    }

    /**
     * Send inquiry email to room owner (same structure as Contact.php send method)
     */
    public function sendInquiryEmail($ownerEmail, $ownerName, $roomData, $inquiryData)
    {
        try {
            // Create HTML email (same as Contact.php)
            $html = $this->loadTemplate([
                "owner_name" => $ownerName,
                "room_title" => $roomData['title'] ?? 'N/A',
                "room_location" => $roomData['location'] ?? 'N/A',
                "room_price" => number_format($roomData['price'] ?? 0),
                "inquirer_name" => $inquiryData['name'] ?? 'N/A',
                "inquirer_email" => $inquiryData['email'] ?? 'N/A',
                "inquirer_phone" => $inquiryData['phone'] ?? 'N/A',
                "visit_date" => $inquiryData['visit_date'] ?? 'N/A',
                "message" => nl2br($inquiryData['message'] ?? 'No message provided')
            ]);
            
            // Set email properties (same as Contact.php)
            $this->mailer->setFrom($this->from_address, $this->from_name);
            $this->mailer->addAddress($ownerEmail, $ownerName);
            $this->mailer->addReplyTo($inquiryData['email'], $inquiryData['name']);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'New Room Inquiry - ' . ($roomData['title'] ?? 'Your Property');
            $this->mailer->Body = $html;
            
            // CC to from address (same as Contact.php)
            $this->mailer->addCC($this->from_address, $this->from_name);

            // Send email (same as Contact.php)
            return $this->mailer->send();
        } catch (Exception $e) {
            return $this->mailer->ErrorInfo;
        }
    }
}
?>

