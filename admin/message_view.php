<?php
// admin/message-view.php - View Single Message
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../login.php");
    exit();
}

include '../config/db.php';

$admin_name = $_SESSION['admin_username'] ?? 'Admin';

// Get message ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header("Location: messages.php");
    exit();
}

// Fetch message
$stmt = mysqli_prepare($conn, "SELECT * FROM contact_messages WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$msg = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$msg) {
    header("Location: messages.php");
    exit();
}

// Mark as read
mysqli_query($conn, "UPDATE contact_messages SET is_read = 1 WHERE id = $id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Message · Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Roboto, system-ui, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            min-height: 100vh;
        }

        .admin-main {
            margin-left: 260px;
            min-height: 100vh;
        }

        .admin-topbar {
            background: #ffffff;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px -5px rgba(0,0,0,0.1);
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
        }

        .topbar-title h1 {
            font-size: 1.35rem;
            color: #0f172a;
            margin: 0;
        }

        .topbar-title p {
            font-size: 0.82rem;
            color: #64748b;
            margin: 0;
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

        .mini-avatar {
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

        .admin-content {
            padding: 2rem;
            max-width: 900px;
            margin: 0 auto;
        }

        /* Back Link */
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #3b82f6;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            transition: all 0.25s ease;
        }

        .back-link:hover {
            color: #1e3a8a;
            transform: translateX(-4px);
        }

        /* Message Card */
        .message-card {
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 15px 30px -12px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .msg-header {
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            padding: 1.8rem 2rem;
            color: #ffffff;
        }

        .msg-header h2 {
            font-size: 1.5rem;
            margin: 0 0 0.4rem 0;
            border: none;
            padding: 0;
            color: #ffffff;
        }

        .msg-header p {
            font-size: 0.85rem;
            color: #dbeafe;
            margin: 0;
        }

        .msg-body {
            padding: 2rem;
        }

        .info-row {
            display: grid;
            grid-template-columns: 140px 1fr;
            gap: 1rem;
            padding: 0.9rem 0;
            border-bottom: 1px dashed #e2e8f0;
            align-items: center;
        }

        .info-row:last-of-type {
            border-bottom: none;
        }

        .info-label {
            font-size: 0.8rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .info-value {
            font-size: 0.95rem;
            color: #0f172a;
            font-weight: 500;
            word-break: break-word;
        }

        .info-value a {
            color: #3b82f6;
            text-decoration: none;
        }

        .info-value a:hover {
            text-decoration: underline;
        }

        /* Message Content */
        .msg-content {
            background: #f8fafc;
            padding: 1.5rem;
            border-radius: 14px;
            margin-top: 1.5rem;
            border-left: 5px solid #3b82f6;
        }

        .msg-content p {
            font-size: 1rem;
            color: #1e293b;
            line-height: 1.75;
            margin: 0;
            white-space: pre-wrap;
        }

        /* Actions */
        .msg-actions {
            padding: 1.5rem 2rem;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            display: flex;
            gap: 0.7rem;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 30px;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-reply {
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            color: #ffffff;
            box-shadow: 0 8px 18px -8px #1e3a8a;
        }

        .btn-reply:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 25px -10px #2563eb;
        }

        .btn-back {
            background: #ffffff;
            color: #1e3a8a;
            border: 2px solid #dbeafe;
        }

        .btn-back:hover {
            background: #eff6ff;
            transform: translateY(-3px);
        }

        .btn-delete {
            background: #fee2e2;
            color: #dc2626;
        }

        .btn-delete:hover {
            background: #dc2626;
            color: #ffffff;
            transform: translateY(-3px);
        }

        @media (max-width: 900px) {
            .admin-main { margin-left: 0; }
            .menu-toggle { display: flex; align-items: center; justify-content: center; }
            .admin-content { padding: 1.5rem 1rem; }
        }

        @media (max-width: 600px) {
            .info-row {
                grid-template-columns: 1fr;
                gap: 0.3rem;
            }
            .topbar-user span { display: none; }
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
                    <h1>View Message</h1>
                    <p>Full details of the contact submission</p>
                </div>
            </div>
            <div class="topbar-user">
                <div class="mini-avatar"><i class="fa-solid fa-user"></i></div>
                <span><?= htmlspecialchars($admin_name) ?></span>
            </div>
        </div>

        <div class="admin-content">

            <a href="messages.php" class="back-link">
                <i class="fa-solid fa-arrow-left"></i>
                Back to Messages
            </a>

            <div class="message-card">

                <!-- Header -->
                <div class="msg-header">
                    <h2><?= htmlspecialchars($msg['subject'] ?: '(No Subject)') ?></h2>
                    <p>
                        <i class="fa-solid fa-clock"></i>
                        <?= date('d M Y, h:i A', strtotime($msg['created_at'])) ?>
                    </p>
                </div>

                <!-- Body -->
                <div class="msg-body">

                    <div class="info-row">
                        <span class="info-label">From</span>
                        <span class="info-value"><?= htmlspecialchars($msg['name']) ?></span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Email</span>
                        <span class="info-value">
                            <a href="mailto:<?= htmlspecialchars($msg['email']) ?>">
                                <?= htmlspecialchars($msg['email']) ?>
                            </a>
                        </span>
                    </div>

                    <?php if (!empty($msg['phone'])): ?>
                        <div class="info-row">
                            <span class="info-label">Phone</span>
                            <span class="info-value">
                                <a href="tel:<?= htmlspecialchars($msg['phone']) ?>">
                                    <?= htmlspecialchars($msg['phone']) ?>
                                </a>
                            </span>
                        </div>
                    <?php endif; ?>

                    <div class="info-row">
                        <span class="info-label">Subject</span>
                        <span class="info-value"><?= htmlspecialchars($msg['subject'] ?: '(No subject)') ?></span>
                    </div>

                    <!-- Message Content -->
                    <div class="msg-content">
                        <p><?= nl2br(htmlspecialchars($msg['message'])) ?></p>
                    </div>

                </div>

                <!-- Actions -->
                <div class="msg-actions">
                    <a href="mailto:<?= htmlspecialchars($msg['email']) ?>?subject=Re: <?= rawurlencode($msg['subject']) ?>" 
                       class="btn btn-reply">
                        <i class="fa-solid fa-reply"></i> Reply via Email
                    </a>
                    <a href="messages.php" class="btn btn-back">
                        <i class="fa-solid fa-arrow-left"></i> Back
                    </a>
                    <a href="messages.php?delete=<?= $msg['id'] ?>" 
                       class="btn btn-delete"
                       onclick="return confirm('Delete this message?')">
                        <i class="fa-solid fa-trash"></i> Delete
                    </a>
                </div>

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
                if (sidebar) sidebar.classList.add('open');
                if (overlay) overlay.classList.add('open');
                document.body.style.overflow = 'hidden';
            }

            function closeSidebarFn() {
                if (sidebar) sidebar.classList.remove('open');
                if (overlay) overlay.classList.remove('open');
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