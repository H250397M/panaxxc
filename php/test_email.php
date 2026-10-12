<?php
/**
 * ICS 2102 — Web Development Semester Mini Project
 * Email Diagnostic & Test Script
 * File: php/test_email.php
 * 
 * Usage:
 * - Visit: http://localhost/panaxxc/php/test_email.php?to=your_email@gmail.com
 * - Or open in browser to inspect current SMTP settings and dispatch test email
 */

require_once __DIR__ . '/mailer.php';

$toEmail = isset($_GET['to']) ? trim($_GET['to']) : '';
$testTriggered = !empty($toEmail);
$result = null;

if ($testTriggered) {
    $result = sendClubWelcomeEmail(
        $toEmail,
        'Member Test',
        'INN-' . rand(10000, 99999),
        'BSc. Computer Science',
        'Year 2',
        'Web Development'
    );
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Email Dispatcher Diagnostic — Innovate Club</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body style="background:#F8FAFC; padding:40px 20px; font-family:sans-serif;">
  <div style="max-width:680px; margin:0 auto; background:#fff; padding:36px; border-radius:14px; border:1px solid #E2E8F0;">
    <h2 style="margin-top:0; color:#15803D;">📧 Innovate Club Email Diagnostic Tool</h2>
    <p style="color:#64748B;">Use this tool to test email dispatch directly to your smartphone / inbox.</p>

    <div style="background:#F1F5F9; padding:18px; border-radius:10px; margin:20px 0; font-size:14px; line-height:1.6;">
      <strong>Current Settings in <code>php/mailer.php</code>:</strong><br>
      • <strong>SMTP Host:</strong> <?php echo htmlspecialchars(SMTP_HOST); ?>:<?php echo htmlspecialchars(SMTP_PORT); ?><br>
      • <strong>SMTP User:</strong> <?php echo empty(SMTP_USER) ? '<span style="color:#DC2626;">(Not configured yet — using local simulation/preview mode)</span>' : htmlspecialchars(SMTP_USER); ?><br>
      • <strong>Latest Preview File:</strong> <a href="latest_email_preview.html" target="_blank" style="color:#15803D; font-weight:700;">Open Visual Email Preview &rarr;</a>
    </div>

    <form method="GET" action="test_email.php" style="margin:24px 0;">
      <label style="display:block; font-weight:700; margin-bottom:8px;">Enter your phone/inbox email to receive a test confirmation:</label>
      <div style="display:flex; gap:10px;">
        <input type="email" name="to" value="<?php echo htmlspecialchars($toEmail); ?>" placeholder="e.g. yourname@gmail.com" required style="flex:1; padding:10px 14px; border:1px solid #CBD5E1; border-radius:8px;">
        <button type="submit" class="btn btn-primary" style="padding:10px 20px;">Send Test Email</button>
      </div>
    </form>

    <?php if ($testTriggered): ?>
      <div style="padding:16px; border-radius:8px; margin:20px 0; background:<?php echo $result['success'] ? '#DCFCE7' : '#FEF3C7'; ?>; color:<?php echo $result['success'] ? '#166534' : '#92400E'; ?>;">
        <strong>Dispatch Result:</strong><br>
        <?php if ($result['success']): ?>
          ✅ Success! Email was sent via <strong><?php echo htmlspecialchars($result['method']); ?></strong> to <strong><?php echo htmlspecialchars($toEmail); ?></strong>. Check your inbox and spam folder!
        <?php else: ?>
          ⚠️ Email was logged and prepared in <strong>latest_email_preview.html</strong>.<br>
          <em>Reason:</em> Local computers running localhost/XAMPP cannot deliver emails to external phones without an SMTP relay. To deliver real emails directly to your smartphone:
          <ol style="margin:10px 0 0 20px;">
            <li>Open <code>php/mailer.php</code> in your code editor.</li>
            <li>Put your Gmail address in <code>SMTP_USER</code>.</li>
            <li>Put a 16-character Gmail App Password in <code>SMTP_PASS</code>.</li>
          </ol>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <hr style="border:none; border-top:1px solid #E2E8F0; margin:28px 0;">
    <div style="display:flex; justify-content:space-between; align-items:center;">
      <a href="../members.html" class="btn btn-secondary btn-sm">&larr; Back to Members Directory</a>
      <a href="latest_email_preview.html" target="_blank" class="btn btn-primary btn-sm">View Rendered HTML Email</a>
    </div>
  </div>
</body>
</html>
