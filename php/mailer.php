<?php
/**
 * ICS 2102 — Web Development Semester Mini Project
 * Standalone SMTP & Email Dispatcher
 * File: php/mailer.php
 * 
 * Instructions to receive real emails on your phone/inbox:
 * 1. Open this file and configure your SMTP settings below (e.g. Gmail).
 * 2. For Gmail:
 *    - Go to Google Account > Security > 2-Step Verification > App Passwords.
 *    - Generate an App Password for "Mail".
 *    - Paste your Gmail address into SMTP_USER and the 16-character password into SMTP_PASS.
 */

// SMTP Configuration
define('SMTP_ENABLED', true);          // Set to true to use real SMTP dispatch
define('SMTP_HOST', 'smtp.gmail.com');  // e.g. smtp.gmail.com or mail.yourdomain.com
define('SMTP_PORT', 587);               // 587 (TLS) or 465 (SSL)
define('SMTP_USER', '');                // Your sending email address (e.g. yourname@gmail.com)
define('SMTP_PASS', '');                // Your email App Password (16 characters)
define('SMTP_FROM_NAME', 'Innovate Club Secretariat');
define('SMTP_FROM_EMAIL', 'noreply@innovateclub.hit.ac.zw');

/**
 * Dispatches a formatted HTML welcome email to the newly registered member.
 */
function sendClubWelcomeEmail($toEmail, $fullname, $refNumber, $course, $year_of_study, $interest) {
    $subject = "Welcome to Innovate Club! Membership Confirmation (Ref: {$refNumber})";
    
    $htmlBody = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8">
      <title>Welcome to Innovate Club</title>
      <style>
        body { margin:0; padding:0; background:#F8FAFC; font-family:-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; color:#1E293B; }
        .box { max-width:600px; margin:24px auto; background:#FFF; border-radius:14px; overflow:hidden; border:1px solid #E2E8F0; box-shadow:0 4px 12px rgba(0,0,0,0.05); }
        .hdr { background:linear-gradient(135deg, #15803D 0%, #166534 100%); padding:32px 28px; text-align:center; color:#FFF; }
        .hdr h1 { margin:0 0 6px; font-size:24px; font-weight:800; }
        .hdr p { margin:0; font-size:13px; opacity:0.9; }
        .content { padding:28px 24px; }
        .card { background:#F1F5F9; border-radius:10px; padding:18px; margin:20px 0; border-left:4px solid #15803D; }
        .row { display:flex; justify-content:space-between; padding:6px 0; font-size:14px; border-bottom:1px solid #E2E8F0; }
        .row:last-child { border-bottom:none; }
        .label { font-weight:600; color:#475569; }
        .val { font-weight:700; color:#0F172A; }
        .badge { background:#DCFCE7; color:#166534; font-size:12px; font-weight:700; padding:2px 8px; border-radius:999px; }
        .footer { background:#F8FAFC; padding:18px; text-align:center; font-size:12px; color:#94A3B8; border-top:1px solid #E2E8F0; }
      </style>
    </head>
    <body>
      <div class="box">
        <div class="hdr">
          <h1>Innovate Club</h1>
          <p>Harare Institute of Technology — Technology & Innovation Society</p>
        </div>
        <div class="content">
          <p style="font-size:17px; font-weight:700; color:#0F172A;">Dear ' . htmlspecialchars($fullname) . ',</p>
          <p style="font-size:15px; line-height:1.6; color:#334155;">
            Congratulations and welcome to <strong>Innovate Club</strong>! Your membership application has been approved and saved to our database.
          </p>

          <div class="card">
            <div class="row">
              <span class="label">Membership Reference:</span>
              <span class="val" style="color:#15803D; font-family:monospace;">' . htmlspecialchars($refNumber) . '</span>
            </div>
            <div class="row">
              <span class="label">Full Name:</span>
              <span class="val">' . htmlspecialchars($fullname) . '</span>
            </div>
            <div class="row">
              <span class="label">Registered Email:</span>
              <span class="val">' . htmlspecialchars($toEmail) . '</span>
            </div>
            <div class="row">
              <span class="label">Department / Course:</span>
              <span class="val">' . htmlspecialchars($course) . ' (' . htmlspecialchars($year_of_study) . ')</span>
            </div>
            <div class="row">
              <span class="label">Technical Track:</span>
              <span class="val">' . htmlspecialchars($interest) . '</span>
            </div>
            <div class="row">
              <span class="label">Directory Access:</span>
              <span class="val"><span class="badge">Verified Member (Read-Only)</span></span>
            </div>
          </div>

          <div style="background:#F8FAFC; border:1px dashed #CBD5E1; border-radius:10px; padding:16px; margin:20px 0;">
            <h4 style="margin:0 0 8px; color:#15803D; font-size:14px;">Next Steps for You:</h4>
            <ul style="margin:0; padding-left:18px; color:#475569; font-size:13.5px; line-height:1.6;">
              <li><strong>Weekly Sprint:</strong> Every Wednesday at 3:00 PM in Computer Lab 3.</li>
              <li><strong>Member Directory:</strong> Log into <a href="members.html" style="color:#15803D; font-weight:700;">members.html</a> using your registered email (<strong>' . htmlspecialchars($toEmail) . '</strong>) to view the member directory.</li>
            </ul>
          </div>

          <p style="font-size:14px; color:#64748B;">
            Best regards,<br>
            <strong>Innovate Club Executive Committee</strong><br>
            Harare Institute of Technology
          </p>
        </div>
        <div class="footer">
          &copy; ' . date('Y') . ' Innovate Club. All rights reserved.<br>
          Automated confirmation sent to ' . htmlspecialchars($toEmail) . '.
        </div>
      </div>
    </body>
    </html>
    ';

    // Save visual preview file for immediate viewing in browser
    $previewPath = __DIR__ . '/latest_email_preview.html';
    @file_put_contents($previewPath, $htmlBody);

    $dispatched = false;
    $methodUsed = 'log';

    // 1. Try Pure SMTP if credentials provided
    if (SMTP_ENABLED && !empty(SMTP_USER) && !empty(SMTP_PASS)) {
        $smtpResult = sendViaPureSmtp($toEmail, $subject, $htmlBody);
        if ($smtpResult === true) {
            $dispatched = true;
            $methodUsed = 'smtp';
        } else {
            error_log("SMTP Error: " . $smtpResult);
        }
    }

    // 2. If SMTP is not configured or failed, try native PHP mail()
    if (!$dispatched) {
        $fromEmail = !empty(SMTP_USER) ? SMTP_USER : SMTP_FROM_EMAIL;
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . SMTP_FROM_NAME . ' <' . $fromEmail . '>',
            'Reply-To: ' . $fromEmail,
            'X-Mailer: PHP/' . phpversion()
        ];
        $dispatched = @mail($toEmail, $subject, $htmlBody, implode("\r\n", $headers));
        if ($dispatched) {
            $methodUsed = 'native_mail';
        }
    }

    // 3. Log the dispatch attempt
    $logFile = __DIR__ . '/email_log.txt';
    $logEntry = "[" . date('Y-m-d H:i:s') . "] TO: {$toEmail} | REF: {$refNumber} | STATUS: " . ($dispatched ? "SENT via {$methodUsed}" : "QUEUED/LOCAL_PREVIEW (Configure SMTP in php/mailer.php for phone delivery)") . "\n";
    @file_put_contents($logFile, $logEntry, FILE_APPEND);

    return [
        'success'      => $dispatched,
        'method'       => $methodUsed,
        'preview_file' => 'php/latest_email_preview.html'
    ];
}

/**
 * Pure PHP SMTP socket implementation with STARTTLS and AUTH LOGIN
 */
function sendViaPureSmtp($to, $subject, $htmlBody) {
    $timeout = 15;
    $host = SMTP_HOST;
    $port = SMTP_PORT;

    $socket = @fsockopen($host, $port, $errno, $errstr, $timeout);
    if (!$socket) {
        return "Cannot connect to {$host}:{$port} - {$errstr} ({$errno})";
    }

    $response = fgets($socket, 515);

    // EHLO
    fputs($socket, "EHLO " . gethostname() . "\r\n");
    $response = '';
    while ($line = fgets($socket, 515)) {
        $response .= $line;
        if (substr($line, 3, 1) === ' ') break;
    }

    // STARTTLS
    if ($port == 587) {
        fputs($socket, "STARTTLS\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) != '220') {
            fclose($socket);
            return "STARTTLS failed: " . $response;
        }

        // Enable crypto on socket
        if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            fclose($socket);
            return "TLS handshake negotiation failed";
        }

        // Re-EHLO after TLS
        fputs($socket, "EHLO " . gethostname() . "\r\n");
        while ($line = fgets($socket, 515)) {
            if (substr($line, 3, 1) === ' ') break;
        }
    }

    // AUTH LOGIN
    fputs($socket, "AUTH LOGIN\r\n");
    $response = fgets($socket, 515);
    if (substr($response, 0, 3) != '334') {
        fclose($socket);
        return "AUTH LOGIN not accepted: " . $response;
    }

    fputs($socket, base64_encode(SMTP_USER) . "\r\n");
    $response = fgets($socket, 515);
    if (substr($response, 0, 3) != '334') {
        fclose($socket);
        return "Username rejected: " . $response;
    }

    fputs($socket, base64_encode(SMTP_PASS) . "\r\n");
    $response = fgets($socket, 515);
    if (substr($response, 0, 3) != '235') {
        fclose($socket);
        return "Password authentication failed: " . $response;
    }

    // MAIL FROM
    $from = SMTP_USER;
    fputs($socket, "MAIL FROM: <{$from}>\r\n");
    $response = fgets($socket, 515);
    if (substr($response, 0, 3) != '250') {
        fclose($socket);
        return "MAIL FROM rejected: " . $response;
    }

    // RCPT TO
    fputs($socket, "RCPT TO: <{$to}>\r\n");
    $response = fgets($socket, 515);
    if (substr($response, 0, 3) != '250') {
        fclose($socket);
        return "RCPT TO rejected: " . $response;
    }

    // DATA
    fputs($socket, "DATA\r\n");
    $response = fgets($socket, 515);
    if (substr($response, 0, 3) != '354') {
        fclose($socket);
        return "DATA rejected: " . $response;
    }

    // Construct headers and body
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . SMTP_FROM_NAME . " <{$from}>\r\n";
    $headers .= "To: <{$to}>\r\n";
    $headers .= "Subject: {$subject}\r\n";
    $headers .= "Date: " . date('r') . "\r\n";

    $message = $headers . "\r\n" . $htmlBody . "\r\n.\r\n";
    fputs($socket, $message);
    $response = fgets($socket, 515);

    fputs($socket, "QUIT\r\n");
    fclose($socket);

    return (substr($response, 0, 3) == '250') ? true : "Sending failed: " . $response;
}
