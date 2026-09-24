<?php
/**
 * ICS 2102 — Web Development Semester Mini Project
 * Join / Contact Form Processor
 * File: php/join.php
 */

require_once __DIR__ . '/db.php';

// Set headers
$isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Invalid request method. Only POST is allowed.']);
        exit;
    } else {
        header('Location: ../join.html');
        exit;
    }
}

// 1. Sanitize and retrieve form inputs
$fullname      = isset($_POST['fullname']) ? trim(strip_tags($_POST['fullname'])) : '';
$email         = isset($_POST['email']) ? trim($_POST['email']) : '';
$phone         = isset($_POST['phone']) ? trim(strip_tags($_POST['phone'])) : '';
$course        = isset($_POST['course']) ? trim(strip_tags($_POST['course'])) : '';
$year_of_study = isset($_POST['year_of_study']) ? trim(strip_tags($_POST['year_of_study'])) : 'Year 1';
$interest      = isset($_POST['interest']) ? trim(strip_tags($_POST['interest'])) : 'General Tech';
$message       = isset($_POST['message']) ? trim(htmlspecialchars($_POST['message'], ENT_QUOTES, 'UTF-8')) : '';

$errors = [];

// 2. Server-side validation
if (empty($fullname) || strlen($fullname) < 3) {
    $errors[] = 'Full name is required and must be at least 3 characters.';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'A valid email address is required.';
}

if (empty($phone) || strlen(preg_replace('/[^0-9]/', '', $phone)) < 9) {
    $errors[] = 'A valid phone number with at least 9 digits is required.';
}

if (empty($course)) {
    $errors[] = 'Please select your department or course.';
}

if (empty($message) || strlen($message) < 10) {
    $errors[] = 'Please enter a message / reason for joining of at least 10 characters.';
}

// If validation errors exist
if (!empty($errors)) {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => implode(' ', $errors),
            'errors' => $errors
        ]);
        exit;
    } else {
        echo "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><title>Submission Error</title>";
        echo "<link rel='stylesheet' href='../css/style.css'></head><body style='padding:50px; background:#F8FAFC;'>";
        echo "<div class='container' style='max-width:600px; background:#fff; padding:40px; border-radius:16px; border:1px solid #E2E8F0; text-align:center;'>";
        echo "<h2 style='color:#EF4444;'>Submission Incomplete</h2>";
        echo "<p style='color:#64748B; margin: 16px 0;'>" . htmlspecialchars(implode(' ', $errors)) . "</p>";
        echo "<a href='../join.html' class='btn btn-primary'>&larr; Return to Form</a>";
        echo "</div></body></html>";
        exit;
    }
}

// 3. Database Insertion
$dbInserted = false;
$recordId = null;

try {
    $pdo = getDatabaseConnection();
    if ($pdo) {
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $stmt = $pdo->prepare("
            INSERT INTO club_members 
            (fullname, email, phone, course, year_of_study, interest, message, ip_address) 
            VALUES (:fullname, :email, :phone, :course, :year_of_study, :interest, :message, :ip_address)
        ");

        $stmt->execute([
            ':fullname'      => $fullname,
            ':email'         => $email,
            ':phone'         => $phone,
            ':course'        => $course,
            ':year_of_study' => $year_of_study,
            ':interest'      => $interest,
            ':message'       => $message,
            ':ip_address'    => $ip_address
        ]);

        $recordId = $pdo->lastInsertId();
        $dbInserted = true;
    }
} catch (Exception $e) {
    error_log("Database insert failure: " . $e->getMessage());
    // Continue gracefully so user still sees receipt
}

$refNumber = 'INN-' . ($recordId ? str_pad($recordId, 5, '0', STR_PAD_LEFT) : rand(10000, 99999));

// 4. Send Response
if ($isAjax) {
    header('Content-Type: application/json');
    echo json_encode([
        'status'  => 'success',
        'message' => "Welcome to Innovate Club, {$fullname}! Your membership application has been received.",
        'data'    => [
            'reference' => $refNumber,
            'fullname'  => $fullname,
            'email'     => $email,
            'course'    => $course,
            'db_saved'  => $dbInserted
        ]
    ]);
    exit;
}

// Standalone HTML Confirmation Page (if non-AJAX POST)
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Application Received — Innovate Club</title>
  <link rel="icon" type="image/svg+xml" href="../images/logo/favicon.svg">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="../css/style.css">
</head>
<body style="background-color: var(--color-bg-alt);">

  <header class="site-header">
    <div class="container header-container">
      <a href="../index.html" class="brand-link">
        <img src="../images/logo/logo.svg" alt="Innovate Club" class="brand-logo" style="height:38px;">
      </a>
      <a href="../index.html" class="btn btn-secondary btn-sm">&larr; Back to Site</a>
    </div>
  </header>

  <main class="main-content" style="padding: 60px 0;">
    <div class="container" style="max-width: 680px;">
      <div class="form-card" style="text-align: center; padding: 48px 36px;">
        <div style="width: 72px; height: 72px; border-radius: 50%; background-color: var(--color-primary-soft); color: var(--color-primary); display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 20px;">
          <i class="fa-solid fa-check"></i>
        </div>

        <h1 style="font-size: 2rem; font-weight: 800; color: var(--color-text-main); margin-bottom: 8px;">
          Application Submitted!
        </h1>
        <p style="font-size: 1.05rem; color: var(--color-text-muted); margin-bottom: 24px;">
          Thank you for applying to join <strong>Innovate Club</strong>. Your application has been logged and our executive committee will review your submission.
        </p>

        <div style="background-color: var(--color-bg-alt); border: 1px solid var(--color-border); border-radius: var(--radius-md); padding: 20px; text-align: left; margin-bottom: 28px;">
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; font-size: 0.95rem;">
            <div><strong>Application Ref:</strong><br><span style="font-family: var(--font-mono); color: var(--color-primary); font-weight: 700;"><?php echo htmlspecialchars($refNumber); ?></span></div>
            <div><strong>Applicant Name:</strong><br><?php echo htmlspecialchars($fullname); ?></div>
            <div><strong>Registered Email:</strong><br><?php echo htmlspecialchars($email); ?></div>
            <div><strong>Department/Course:</strong><br><?php echo htmlspecialchars($course); ?></div>
            <div style="grid-column: 1 / -1;"><strong>Interest Track:</strong><br><?php echo htmlspecialchars($interest); ?></div>
          </div>
        </div>

        <div style="display: flex; justify-content: center; gap: 14px; flex-wrap: wrap;">
          <a href="../index.html" class="btn btn-primary"><i class="fa-solid fa-house"></i> Home Page</a>
          <a href="../activities.html" class="btn btn-secondary"><i class="fa-solid fa-calendar-days"></i> Explore Events</a>
        </div>
      </div>
    </div>
  </main>

  <footer class="site-footer">
    <div class="container footer-bottom">
      <p>&copy; 2025 Innovate Club. All rights reserved.</p>
      <p class="footer-course-tag">ICS 2102 — Web Development Semester Mini Project</p>
    </div>
  </footer>

</body>
</html>
