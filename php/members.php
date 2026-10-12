<?php
/**
 * ICS 2102 — Web Development Semester Mini Project
 * Members Directory & Database Viewer (Protected: Registered Members Only)
 * File: php/members.php
 * 
 * Access Control:
 * - Only verified, registered club members may view the directory.
 * - Access is strictly READ-ONLY. No edits or deletions are permitted.
 */

session_start();
require_once __DIR__ . '/db.php';

$pdo = getDatabaseConnection();
$action = $_REQUEST['action'] ?? '';

// 1. Verification API Endpoint
if ($action === 'verify') {
    header('Content-Type: application/json; charset=utf-8');
    $email = isset($_REQUEST['email']) ? trim(strtolower($_REQUEST['email'])) : '';

    if (empty($email)) {
        echo json_encode(['status' => 'error', 'message' => 'Please provide an email address.']);
        exit;
    }

    if ($pdo) {
        $stmt = $pdo->prepare("SELECT id, fullname, email, course, year_of_study, interest FROM club_members WHERE LOWER(email) = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $member = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($member) {
            $_SESSION['club_member_email'] = $member['email'];
            $_SESSION['club_member_name']  = $member['fullname'];
            echo json_encode([
                'status'  => 'success',
                'message' => 'Membership verified.',
                'member'  => $member
            ]);
            exit;
        }
    }

    echo json_encode(['status' => 'error', 'message' => "Access denied: The email '{$email}' is not registered with Innovate Club."]);
    exit;
}

// 2. Logout Endpoint
if ($action === 'logout') {
    unset($_SESSION['club_member_email']);
    unset($_SESSION['club_member_name']);
    if (isset($_GET['ajax'])) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'success']);
        exit;
    }
    header('Location: members.php');
    exit;
}

// Handle Direct POST Login Form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_email'])) {
    $loginEmail = trim(strtolower($_POST['login_email']));
    if ($pdo) {
        $stmt = $pdo->prepare("SELECT id, fullname, email FROM club_members WHERE LOWER(email) = :email LIMIT 1");
        $stmt->execute([':email' => $loginEmail]);
        $member = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($member) {
            $_SESSION['club_member_email'] = $member['email'];
            $_SESSION['club_member_name']  = $member['fullname'];
            header('Location: members.php');
            exit;
        } else {
            $loginError = "Access denied: No registered member found with email '{$loginEmail}'. Please register to gain access.";
        }
    }
}

// Check Authentication Status
$isMemberLoggedIn = !empty($_SESSION['club_member_email']);

// 3. JSON API Endpoint (?action=api)
if ($action === 'api' || (!empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false && $action !== 'verify')) {
    header('Content-Type: application/json; charset=utf-8');

    // Check if verification email passed via query/header
    $passedEmail = isset($_GET['auth_email']) ? trim(strtolower($_GET['auth_email'])) : '';
    if (!$isMemberLoggedIn && !empty($passedEmail) && $pdo) {
        $stmt = $pdo->prepare("SELECT id, fullname, email FROM club_members WHERE LOWER(email) = :email LIMIT 1");
        $stmt->execute([':email' => $passedEmail]);
        if ($stmt->fetch()) {
            $isMemberLoggedIn = true;
            $_SESSION['club_member_email'] = $passedEmail;
        }
    }

    if (!$isMemberLoggedIn) {
        http_response_code(401);
        echo json_encode([
            'status'  => 'locked',
            'message' => 'Access Restricted: You must be a registered member to view this directory.'
        ]);
        exit;
    }

    $members = [];
    if ($pdo) {
        $stmt = $pdo->query("SELECT id, fullname, email, phone, course, year_of_study, interest, created_at FROM club_members ORDER BY created_at DESC");
        $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode([
        'status'   => 'success',
        'readonly' => true,
        'count'    => count($members),
        'data'     => $members
    ]);
    exit;
}

// Fetch Members for Logged-In View
$members = [];
if ($isMemberLoggedIn && $pdo) {
    $stmt = $pdo->query("SELECT id, fullname, email, phone, course, year_of_study, interest, created_at FROM club_members ORDER BY created_at DESC");
    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Stats calculation
$totalMembers = count($members);
$recentCount = 0;
$sevenDaysAgo = strtotime('-7 days');
foreach ($members as $m) {
    if (!empty($m['created_at']) && strtotime($m['created_at']) >= $sevenDaysAgo) {
        $recentCount++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Members Directory — Innovate Club (Members Only)</title>
  <link rel="icon" type="image/svg+xml" href="../images/logo/favicon.svg">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="../css/style.css">
  <style>
    .auth-card {
      max-width: 500px;
      margin: 40px auto;
      background: #FFFFFF;
      border: 1px solid var(--color-border);
      border-radius: var(--radius-lg);
      padding: 40px 32px;
      text-align: center;
      box-shadow: var(--shadow-md);
    }
    .auth-icon {
      width: 70px;
      height: 70px;
      border-radius: 50%;
      background: var(--color-primary-soft);
      color: var(--color-primary);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2rem;
      margin: 0 auto 20px;
    }
    .member-banner {
      background: #ECFDF5;
      border: 1px solid #A7F3D0;
      border-radius: var(--radius-md);
      padding: 14px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 12px;
      margin-bottom: 28px;
    }
    .table-container {
      background: #FFFFFF;
      border: 1px solid var(--color-border);
      border-radius: var(--radius-lg);
      overflow-x: auto;
      box-shadow: var(--shadow-sm);
    }
    .members-table {
      width: 100%;
      border-collapse: collapse;
      text-align: left;
      font-size: 0.95rem;
    }
    .members-table th {
      background: #F8FAFC;
      padding: 14px 18px;
      font-weight: 700;
      color: var(--color-text-muted);
      border-bottom: 1px solid var(--color-border);
      font-size: 0.8rem;
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }
    .members-table td {
      padding: 14px 18px;
      border-bottom: 1px solid #F1F5F9;
      vertical-align: middle;
    }
    .members-table tr:hover {
      background-color: #F8FAFC;
    }
    .badge-lock {
      background: #FEF3C7;
      color: #92400E;
      font-weight: 700;
      font-size: 0.75rem;
      padding: 3px 8px;
      border-radius: 999px;
      display: inline-flex;
      align-items: center;
      gap: 4px;
    }
  </style>
</head>
<body style="background-color: var(--color-bg-alt);">

  <header class="site-header">
    <div class="container header-container">
      <a href="../index.html" class="brand-link">
        <img src="../images/logo/logo.svg" alt="Innovate Club" class="brand-logo" style="height:42px;">
      </a>
      <nav class="nav-menu">
        <ul class="nav-list">
          <li><a href="../index.html" class="nav-link">Home</a></li>
          <li><a href="../about.html" class="nav-link">About Us</a></li>
          <li><a href="../activities.html" class="nav-link">Activities</a></li>
          <li><a href="../gallery.html" class="nav-link">Gallery</a></li>
          <li><a href="../members.html" class="nav-link active">Members</a></li>
          <li><a href="../join.html" class="nav-cta-btn"><i class="fa-solid fa-user-plus"></i> Join Now</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <main class="main-content" style="padding: 40px 0 80px;">
    <div class="container">

      <?php if (!$isMemberLoggedIn): ?>
        <!-- LOCKED: Member Access Verification Form -->
        <div class="auth-card">
          <div class="auth-icon">
            <i class="fa-solid fa-lock"></i>
          </div>
          <h2 style="font-size:1.8rem; font-weight:800; color:var(--color-text-main); margin-bottom:8px;">
            Members Only Access
          </h2>
          <p style="color:var(--color-text-muted); font-size:0.95rem; margin-bottom:24px; line-height:1.5;">
            To protect privacy, the club roster is restricted to confirmed members. Enter your registered email address to verify your account and view the directory.
          </p>

          <?php if (!empty($loginError)): ?>
            <div style="background:#FEE2E2; border:1px solid #FCA5A5; color:#991B1B; padding:12px; border-radius:8px; margin-bottom:20px; font-size:0.9rem; text-align:left;">
              <i class="fa-solid fa-triangle-exclamation"></i> <?php echo htmlspecialchars($loginError); ?>
            </div>
          <?php endif; ?>

          <form method="POST" action="members.php" style="text-align:left;">
            <div class="form-group" style="margin-bottom:18px;">
              <label for="login_email" class="form-label">Registered Email Address <span class="req">*</span></label>
              <input 
                type="email" 
                id="login_email" 
                name="login_email" 
                class="form-input" 
                placeholder="student@university.edu" 
                required
              >
            </div>
            <button type="submit" class="btn btn-primary btn-lg" style="width:100%;">
              <i class="fa-solid fa-key"></i> Verify & Unlock Directory
            </button>
          </form>

          <p style="margin-top:24px; font-size:0.9rem; color:var(--color-text-muted);">
            Not registered yet? <a href="../join.html" style="color:var(--color-primary); font-weight:700;">Apply to join Innovate Club &rarr;</a>
          </p>
        </div>

      <?php else: ?>
        <!-- UNLOCKED: Read-Only Member Directory View -->

        <!-- Member Status Bar -->
        <div class="member-banner">
          <div style="display:flex; align-items:center; gap:12px;">
            <i class="fa-solid fa-circle-check" style="font-size:1.5rem; color:#059669;"></i>
            <div>
              <strong style="color:#065F46; font-size:1rem;">Verified Member: <?php echo htmlspecialchars($_SESSION['club_member_name'] ?? ''); ?></strong>
              <div style="font-size:0.85rem; color:#047857;">Signed in as <code><?php echo htmlspecialchars($_SESSION['club_member_email'] ?? ''); ?></code></div>
            </div>
          </div>
          <div style="display:flex; align-items:center; gap:12px;">
            <span class="badge-lock"><i class="fa-solid fa-eye"></i> Read-Only Mode (No Editing Permitted)</span>
            <a href="members.php?action=logout" class="btn btn-secondary btn-sm" style="padding:6px 14px; font-size:0.85rem;">
              <i class="fa-solid fa-arrow-right-from-bracket"></i> Lock Directory
            </a>
          </div>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; margin-bottom:24px;">
          <div>
            <h1 style="font-size: 2rem; font-weight:800; color:var(--color-text-main); margin-bottom:4px;">
              Club Members Directory
            </h1>
            <p style="color:var(--color-text-muted); font-size:0.95rem;">
              Official roster of registered Innovate Club students. Total confirmed members: <strong><?php echo $totalMembers; ?></strong>.
            </p>
          </div>
          <div>
            <a href="test_email.php" class="btn btn-secondary btn-sm"><i class="fa-solid fa-envelope"></i> Test Email Dispatch</a>
          </div>
        </div>

        <!-- Read-Only Members Table -->
        <div class="table-container">
          <table class="members-table">
            <thead>
              <tr>
                <th>Ref ID</th>
                <th>Member Name</th>
                <th>Registered Email</th>
                <th>Phone</th>
                <th>Department / Course</th>
                <th>Year</th>
                <th>Track</th>
                <th>Date Joined</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($members as $m): ?>
                <?php 
                  $refId = 'INN-' . str_pad($m['id'], 5, '0', STR_PAD_LEFT);
                  $joinTs = !empty($m['created_at']) ? strtotime($m['created_at']) : time();
                  $isRecent = ($joinTs >= $sevenDaysAgo);
                ?>
                <tr>
                  <td><code style="color:var(--color-primary); font-weight:700;"><?php echo htmlspecialchars($refId); ?></code></td>
                  <td>
                    <strong><?php echo htmlspecialchars($m['fullname']); ?></strong>
                    <?php if ($isRecent): ?>
                      <span style="background:#DCFCE7; color:#15803D; font-size:0.7rem; font-weight:700; padding:2px 6px; border-radius:999px; margin-left:4px;">NEW</span>
                    <?php endif; ?>
                  </td>
                  <td><a href="mailto:<?php echo htmlspecialchars($m['email']); ?>" style="color:var(--color-primary);"><?php echo htmlspecialchars($m['email']); ?></a></td>
                  <td style="color:var(--color-text-muted);"><?php echo htmlspecialchars($m['phone']); ?></td>
                  <td><?php echo htmlspecialchars($m['course']); ?></td>
                  <td><?php echo htmlspecialchars($m['year_of_study'] ?? 'Year 1'); ?></td>
                  <td><span style="background:#EEF2FF; color:#4338CA; font-size:0.75rem; font-weight:600; padding:3px 8px; border-radius:999px;"><?php echo htmlspecialchars($m['interest'] ?? 'General'); ?></span></td>
                  <td style="color:var(--color-text-muted); font-size:0.85rem;"><?php echo date('M j, Y', $joinTs); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

      <?php endif; ?>

    </div>
  </main>

  <footer class="site-footer">
    <div class="container footer-bottom">
      <p>&copy; 2025 Innovate Club. Harare Institute of Technology.</p>
      <p class="footer-course-tag">ICS 2102 — Web Development Semester Mini Project</p>
    </div>
  </footer>

</body>
</html>
