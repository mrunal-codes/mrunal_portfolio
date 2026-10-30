<?php
// admin/dashboard.php - Admin Dashboard
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../login.php");
    exit();
}

include '../config/db.php';

// ============================================
// Fetch Stats
// ============================================
$total_messages = 0;
$result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM contact_messages");
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $total_messages = $row['total'];
}

$total_projects = 6;
$total_skills   = 8;

$admin_name = $_SESSION['admin_username'] ?? 'Admin';

// Latest 5 messages (with phone and message)
$latest_messages = [];
$res = mysqli_query($conn, "SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5");
if ($res) {
    while ($r = mysqli_fetch_assoc($res)) {
        $latest_messages[] = $r;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard · Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Roboto, system-ui, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            min-height: 100vh;
            line-height: 1.6;
        }

        .admin-main {
            margin-left: 260px;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }

        .admin-topbar {
            background: #ffffff;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px -5px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 50;
            gap: 1rem;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .menu-toggle {
            display: none;
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-size: 1.1rem;
            transition: all 0.28s ease;
            box-shadow: 0 6px 15px -6px rgba(30, 58, 138, 0.5);
        }

        .menu-toggle:hover { transform: scale(1.06); }

        .topbar-title h1 {
            font-size: 1.35rem;
            color: #0f172a;
            font-weight: 700;
            margin: 0;
            letter-spacing: -0.01em;
        }

        .topbar-title p {
            font-size: 0.82rem;
            color: #64748b;
            margin: 0;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .topbar-user {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.4rem 0.9rem 0.4rem 0.4rem;
            background: #f8fafc;
            border-radius: 40px;
            border: 1px solid #e2e8f0;
        }

        .topbar-user .mini-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 0.85rem;
        }

        .topbar-user span {
            font-size: 0.85rem;
            font-weight: 600;
            color: #1e293b;
        }

        .admin-content {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .welcome-box {
            background: linear-gradient(135deg, #1e3a8a, #3b82f6, #8b5cf6);
            background-size: 200% 200%;
            animation: gradientShift 8s ease infinite;
            color: #ffffff;
            padding: 2rem 2.2rem;
            border-radius: 20px;
            box-shadow: 0 20px 40px -15px rgba(30, 58, 138, 0.5);
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .welcome-box::before {
            content: "";
            position: absolute;
            top: -50%;
            right: -15%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.18), transparent 70%);
            border-radius: 50%;
        }

        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50%      { background-position: 100% 50%; }
        }

        .welcome-box h2 {
            font-size: 1.7rem;
            margin-bottom: 0.4rem;
            color: #ffffff;
            position: relative;
            z-index: 1;
        }

        .welcome-box p {
            font-size: 1rem;
            color: #e0e7ff;
            margin: 0;
            position: relative;
            z-index: 1;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 1.2rem;
            margin-bottom: 2.5rem;
        }

        .stat-card {
            background: #ffffff;
            padding: 1.5rem;
            border-radius: 18px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 25px -12px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 1.1rem;
            text-decoration: none;
            color: inherit;
            transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(180deg, #1e3a8a, #3b82f6);
            transform: scaleY(0);
            transform-origin: bottom;
            transition: transform 0.35s ease;
        }

        .stat-card:hover::before { transform: scaleY(1); }

        .stat-card:hover {
            transform: translateY(-5px);
            border-color: #93c5fd;
            box-shadow: 0 22px 40px -15px rgba(59, 130, 246, 0.4);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .stat-icon.blue   { background: linear-gradient(135deg, #dbeafe, #bfdbfe); color: #1e3a8a; }
        .stat-icon.purple { background: linear-gradient(135deg, #ddd6fe, #c4b5fd); color: #7c3aed; }
        .stat-icon.yellow { background: linear-gradient(135deg, #fef3c7, #fde68a); color: #b45309; }
        .stat-icon.green  { background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #047857; }

        .stat-info h3 {
            font-size: 1.7rem;
            color: #0f172a;
            margin: 0 0 0.15rem 0;
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .stat-info p {
            font-size: 0.8rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            font-weight: 600;
            margin: 0;
        }

        .section-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.2rem;
            flex-wrap: wrap;
            gap: 0.8rem;
        }

        .section-heading h2 {
            font-size: 1.25rem;
            color: #0f172a;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .section-heading h2::before {
            content: "";
            width: 5px;
            height: 22px;
            background: linear-gradient(180deg, #1e3a8a, #3b82f6);
            border-radius: 4px;
        }

        .section-heading a {
            font-size: 0.85rem;
            color: #3b82f6;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .section-heading a:hover {
            color: #1e3a8a;
            transform: translateX(4px);
        }

        .messages-card {
            background: #ffffff;
            border-radius: 18px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 25px -12px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .messages-table {
            width: 100%;
            border-collapse: collapse;
        }

        .messages-table thead {
            background: #f8fafc;
        }

        .messages-table th {
            text-align: left;
            padding: 1rem 1.2rem;
            font-size: 0.75rem;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 2px solid #e2e8f0;
        }

        .messages-table td {
            padding: 1rem 1.2rem;
            font-size: 0.9rem;
            color: #1e293b;
            border-bottom: 1px solid #f1f5f9;
        }

        .messages-table tbody tr { transition: background 0.25s ease; }
        .messages-table tbody tr:hover { background: #f8fafc; }
        .messages-table tbody tr:last-child td { border-bottom: none; }

        .msg-name {
            font-weight: 700;
            color: #0f172a;
        }

        .msg-email {
            color: #3b82f6;
            font-size: 0.85rem;
        }

        .msg-phone {
            color: #0f172a;
            font-weight: 600;
            font-size: 0.88rem;
        }

        .msg-phone i {
            color: #22c55e;
            margin-right: 4px;
        }

        .msg-subject {
            color: #475569;
            font-weight: 500;
        }

        /* ✅ NEW: Message column */
        .msg-message {
            color: #475569;
            font-size: 0.85rem;
            max-width: 220px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .msg-date {
            font-size: 0.8rem;
            color: #94a3b8;
        }

        .empty-state {
            padding: 3rem 1.5rem;
            text-align: center;
            color: #94a3b8;
        }

        .empty-state i {
            font-size: 3rem;
            color: #cbd5e1;
            margin-bottom: 1rem;
            display: block;
        }

        .empty-state p {
            font-size: 0.95rem;
            margin: 0;
            color: #94a3b8;
        }

        @media (max-width: 900px) {
            .admin-main { margin-left: 0; }
            .menu-toggle { display: flex; align-items: center; justify-content: center; }
            .admin-content { padding: 1.5rem 1rem; }
            .welcome-box { padding: 1.5rem 1.5rem; }
            .welcome-box h2 { font-size: 1.4rem; }
            .topbar-title h1 { font-size: 1.15rem; }
            .topbar-user span { display: none; }
        }

        @media (max-width: 600px) {
            .stats-grid { grid-template-columns: 1fr; gap: 1rem; }
            .messages-card { overflow-x: auto; }
            .messages-table { min-width: 900px; }
        }
    </style>
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <main class="admin-main">

        <div class="admin-topbar">
            <div class="topbar-left">
                <button class="menu-toggle" id="menuToggle" aria-label="Open menu">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div class="topbar-title">
                    <h1>Dashboard</h1>
                    <p>Welcome back, <?= htmlspecialchars($admin_name) ?>!</p>
                </div>
            </div>
            <div class="topbar-right">
                <div class="topbar-user">
                    <div class="mini-avatar"><i class="fa-solid fa-user"></i></div>
                    <span><?= htmlspecialchars($admin_name) ?></span>
                </div>
            </div>
        </div>

        <div class="admin-content">

            <div class="welcome-box">
                <h2>👋 Hello, <?= htmlspecialchars($admin_name) ?>!</h2>
                <p>Manage your portfolio from one place — messages, projects, skills, and more.</p>
            </div>

            <div class="stats-grid">
                <a href="messages.php" class="stat-card">
                    <div class="stat-icon blue"><i class="fa-solid fa-envelope"></i></div>
                    <div class="stat-info">
                        <h3><?= $total_messages ?></h3>
                        <p>Messages</p>
                    </div>
                </a>
                <a href="projects.php" class="stat-card">
                    <div class="stat-icon purple"><i class="fa-solid fa-briefcase"></i></div>
                    <div class="stat-info">
                        <h3><?= $total_projects ?></h3>
                        <p>Projects</p>
                    </div>
                </a>
                <a href="skills.php" class="stat-card">
                    <div class="stat-icon yellow"><i class="fa-solid fa-star"></i></div>
                    <div class="stat-info">
                        <h3><?= $total_skills ?></h3>
                        <p>Skills</p>
                    </div>
                </a>
                <a href="../index.php" target="_blank" class="stat-card">
                    <div class="stat-icon green"><i class="fa-solid fa-globe"></i></div>
                    <div class="stat-info">
                        <h3>Live</h3>
                        <p>View Site</p>
                    </div>
                </a>
            </div>

            <!-- ============================================
                 RECENT MESSAGES (Name, Email, Phone, Subject, Message, Date)
                 ============================================ -->
            <div class="section-heading">
                <h2>Recent Messages</h2>
                <a href="messages.php">
                    View All <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <div class="messages-card">
                <?php if (empty($latest_messages)): ?>
                    <div class="empty-state">
                        <i class="fa-solid fa-inbox"></i>
                        <p>No messages yet. Your contact form submissions will appear here.</p>
                    </div>
                <?php else: ?>
                    <table class="messages-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Contact No.</th>
                                <th>Subject</th>
                                <th>Message</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($latest_messages as $msg): ?>
                                <tr onclick="window.location='message-view.php?id=<?= intval($msg['id']) ?>'" style="cursor:pointer;">
                                    <td class="msg-name">
                                        <?= htmlspecialchars($msg['name'] ?? 'Unknown') ?>
                                    </td>
                                    <td class="msg-email">
                                        <?= htmlspecialchars($msg['email'] ?? '-') ?>
                                    </td>
                                    <td class="msg-phone">
                                        <?php if (!empty($msg['phone'])): ?>
                                            <i class="fa-solid fa-phone"></i>
                                            <?= htmlspecialchars($msg['phone']) ?>
                                        <?php else: ?>
                                            <span style="color:#cbd5e1;">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="msg-subject">
                                        <?= htmlspecialchars($msg['subject'] ?? '(No subject)') ?>
                                    </td>
                                    <td class="msg-message" title="<?= htmlspecialchars($msg['message'] ?? '') ?>">
                                        <?php
                                            $msg_text = $msg['message'] ?? '';
                                            if (!empty($msg_text)) {
                                                echo htmlspecialchars(mb_strimwidth($msg_text, 0, 60, '...'));
                                            } else {
                                                echo '<span style="color:#cbd5e1;">—</span>';
                                            }
                                        ?>
                                    </td>
                                    <td class="msg-date">
                                        <?= isset($msg['created_at']) ? date('d M Y', strtotime($msg['created_at'])) : '-' ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

        </div>

    </main>

    <script>
        (function () {
            const sidebar      = document.getElementById('adminSidebar');
            const overlay      = document.getElementById('sidebarOverlay');
            const menuToggle   = document.getElementById('menuToggle');
            const closeSidebar = document.getElementById('closeSidebarBtn');

            function openSidebar() {
                sidebar.classList.add('open');
                overlay.classList.add('open');
                document.body.style.overflow = 'hidden';
            }
            function closeSidebarFn() {
                sidebar.classList.remove('open');
                overlay.classList.remove('open');
                document.body.style.overflow = '';
            }

            if (menuToggle)   menuToggle.addEventListener('click', openSidebar);
            if (closeSidebar) closeSidebar.addEventListener('click', closeSidebarFn);
            if (overlay)      overlay.addEventListener('click', closeSidebarFn);

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') closeSidebarFn();
            });
        })();
    </script>

</body>
</html>