<?php
// admin/messages.php - View All Contact Messages
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../login.php");
    exit();
}

include '../config/db.php';

$admin_name = $_SESSION['admin_username'] ?? 'Admin';

// ============================================
// Handle Delete Action
// ============================================
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = mysqli_prepare($conn, "DELETE FROM contact_messages WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: messages.php?deleted=1");
    exit();
}

// ============================================
// Filter (all / unread / read)
// ============================================
$filter = $_GET['filter'] ?? 'all';
$where = "";
if ($filter === 'unread') {
    $where = "WHERE is_read = 0";
} elseif ($filter === 'read') {
    $where = "WHERE is_read = 1";
}

// ============================================
// Search
// ============================================
$search = trim($_GET['search'] ?? '');
if (!empty($search) && empty($where)) {
    $where = "WHERE name LIKE '%$search%' OR email LIKE '%$search%' OR subject LIKE '%$search%'";
} elseif (!empty($search)) {
    $where .= " AND (name LIKE '%$search%' OR email LIKE '%$search%' OR subject LIKE '%$search%')";
}

// ============================================
// Fetch Messages
// ============================================
$messages = [];
$sql = "SELECT * FROM contact_messages $where ORDER BY created_at DESC";
$res = mysqli_query($conn, $sql);
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $messages[] = $row;
    }
}

// Count stats
$total_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM contact_messages"))['c'] ?? 0;
$unread_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM contact_messages WHERE is_read = 0"))['c'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages · Admin</title>
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

        /* Top Bar */
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

        .topbar-user span {
            font-size: 0.85rem;
            font-weight: 600;
        }

        /* Content */
        .admin-content {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Alerts */
        .alert {
            padding: 1rem 1.3rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.7rem;
            font-size: 0.92rem;
            font-weight: 500;
            animation: slideDown 0.4s ease;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-15px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border-left: 5px solid #22c55e;
        }

        /* Page Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .page-header h2 {
            font-size: 1.5rem;
            color: #0f172a;
            margin: 0 0 0.3rem 0;
            border: none;
            padding: 0;
        }

        .page-header p {
            font-size: 0.9rem;
            color: #64748b;
            margin: 0;
        }

        .stats-badges {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .stat-badge {
            background: #ffffff;
            padding: 0.55rem 1rem;
            border-radius: 30px;
            font-size: 0.82rem;
            font-weight: 600;
            color: #1e3a8a;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 10px -6px rgba(0,0,0,0.1);
        }

        .stat-badge i {
            color: #3b82f6;
        }

        .stat-badge strong {
            font-size: 1rem;
            color: #0f172a;
        }

        /* Filter Bar */
        .filter-bar {
            background: #ffffff;
            padding: 1rem 1.3rem;
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
            box-shadow: 0 4px 15px -8px rgba(0,0,0,0.08);
        }

        .filter-tabs {
            display: flex;
            gap: 0.4rem;
            flex-wrap: wrap;
        }

        .filter-tab {
            padding: 0.5rem 1rem;
            border-radius: 30px;
            font-size: 0.85rem;
            font-weight: 600;
            color: #475569;
            text-decoration: none;
            border: 1px solid #e2e8f0;
            transition: all 0.25s ease;
            background: #ffffff;
        }

        .filter-tab:hover {
            background: #eff6ff;
            border-color: #93c5fd;
            color: #1e3a8a;
        }

        .filter-tab.active {
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            color: #ffffff;
            border-color: transparent;
            box-shadow: 0 8px 18px -8px #1e3a8a;
        }

        .search-box {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .search-box input {
            padding: 0.55rem 1rem;
            border: 2px solid #cbd5e1;
            border-radius: 30px;
            font-size: 0.85rem;
            font-family: inherit;
            background: #f8fafc;
            min-width: 200px;
        }

        .search-box input:focus {
            outline: none;
            border-color: #3b82f6;
            background: #ffffff;
        }

        .search-box button {
            padding: 0.55rem 1rem;
            border: none;
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            color: #ffffff;
            border-radius: 30px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s ease;
        }

        .search-box button:hover {
            transform: scale(1.05);
        }

        /* Messages Table */
        .messages-card {
            background: #ffffff;
            border-radius: 18px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 25px -12px rgba(0,0,0,0.08);
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
            font-size: 0.72rem;
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
            vertical-align: middle;
        }

        .messages-table tbody tr {
            transition: background 0.2s ease;
        }

        .messages-table tbody tr:hover {
            background: #f8fafc;
        }

        .messages-table tbody tr:last-child td {
            border-bottom: none;
        }

        .msg-name {
            font-weight: 700;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .unread-dot {
            width: 8px;
            height: 8px;
            background: #ef4444;
            border-radius: 50%;
            display: inline-block;
            animation: pulse 2s ease infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%      { opacity: 0.6; transform: scale(1.3); }
        }

        .msg-email {
            color: #3b82f6;
            font-size: 0.85rem;
        }

        .msg-subject {
            color: #475569;
            font-weight: 500;
        }

        .msg-date {
            font-size: 0.82rem;
            color: #94a3b8;
        }

        /* Action Buttons */
        .actions {
            display: flex;
            gap: 0.4rem;
        }

        .action-btn {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            text-decoration: none;
            transition: all 0.25s ease;
        }

        .btn-view {
            background: #eff6ff;
            color: #1e3a8a;
        }

        .btn-view:hover {
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            color: #ffffff;
            transform: scale(1.1);
        }

        .btn-delete {
            background: #fee2e2;
            color: #dc2626;
        }

        .btn-delete:hover {
            background: linear-gradient(135deg, #dc2626, #ef4444);
            color: #ffffff;
            transform: scale(1.1);
        }

        /* Empty State */
        .empty-state {
            padding: 4rem 2rem;
            text-align: center;
            color: #94a3b8;
        }

        .empty-state i {
            font-size: 4rem;
            color: #cbd5e1;
            margin-bottom: 1rem;
            display: block;
        }

        .empty-state p {
            font-size: 1rem;
            margin: 0;
        }

        /* Responsive */
        @media (max-width: 900px) {
            .admin-main { margin-left: 0; }
            .menu-toggle { display: flex; align-items: center; justify-content: center; }
            .admin-content { padding: 1.5rem 1rem; }
        }

        @media (max-width: 600px) {
            .messages-card { overflow-x: auto; }
            .messages-table { min-width: 700px; }
            .topbar-user span { display: none; }
        }
    </style>
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <main class="admin-main">

        <!-- Top Bar -->
        <div class="admin-topbar">
            <div class="topbar-left">
                <button class="menu-toggle" id="menuToggle" aria-label="Open menu">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div class="topbar-title">
                    <h1>Contact Messages</h1>
                    <p>All messages from your portfolio contact form</p>
                </div>
            </div>
            <div class="topbar-user">
                <div class="mini-avatar"><i class="fa-solid fa-user"></i></div>
                <span><?= htmlspecialchars($admin_name) ?></span>
            </div>
        </div>

        <!-- Content -->
        <div class="admin-content">

            <!-- Success Alert -->
            <?php if (isset($_GET['deleted'])): ?>
                <div class="alert alert-success">
                    <i class="fa-solid fa-circle-check"></i>
                    Message deleted successfully!
                </div>
            <?php endif; ?>

            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h2>All Messages</h2>
                    <p>Manage and view all contact form submissions.</p>
                </div>

                <div class="stats-badges">
                    <span class="stat-badge">
                        <i class="fa-solid fa-inbox"></i>
                        Total: <strong><?= $total_count ?></strong>
                    </span>
                    <span class="stat-badge">
                        <i class="fa-solid fa-envelope"></i>
                        Unread: <strong><?= $unread_count ?></strong>
                    </span>
                </div>
            </div>

            <!-- Filter Bar -->
            <div class="filter-bar">
                <div class="filter-tabs">
                    <a href="?filter=all" class="filter-tab <?= $filter === 'all' ? 'active' : '' ?>">
                        <i class="fa-solid fa-list"></i> All
                    </a>
                    <a href="?filter=unread" class="filter-tab <?= $filter === 'unread' ? 'active' : '' ?>">
                        <i class="fa-solid fa-envelope"></i> Unread
                    </a>
                    <a href="?filter=read" class="filter-tab <?= $filter === 'read' ? 'active' : '' ?>">
                        <i class="fa-solid fa-envelope-open"></i> Read
                    </a>
                </div>

                <form method="GET" class="search-box">
                    <input type="text" name="search" placeholder="Search name, email, subject..." 
                           value="<?= htmlspecialchars($search) ?>">
                    <button type="submit"><i class="fa-solid fa-search"></i></button>
                </form>
            </div>

            <!-- Messages Table -->
            <div class="messages-card">

                <?php if (empty($messages)): ?>

                    <div class="empty-state">
                        <i class="fa-solid fa-inbox"></i>
                        <p>No messages found.</p>
                    </div>

                <?php else: ?>

                    <table class="messages-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Subject</th>
                                <th>Date</th>
                                <th style="text-align:center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($messages as $msg): ?>
                                <tr>
                                    <td>
                                        <div class="msg-name">
                                            <?php if (isset($msg['is_read']) && $msg['is_read'] == 0): ?>
                                                <span class="unread-dot"></span>
                                            <?php endif; ?>
                                            <?= htmlspecialchars($msg['name'] ?? 'Unknown') ?>
                                        </div>
                                    </td>
                                    <td class="msg-email"><?= htmlspecialchars($msg['email'] ?? '-') ?></td>
                                    <td class="msg-subject"><?= htmlspecialchars($msg['subject'] ?? '(No subject)') ?></td>
                                    <td class="msg-date">
                                        <?= isset($msg['created_at']) ? date('d M Y, h:i A', strtotime($msg['created_at'])) : '-' ?>
                                    </td>
                                    <td>
                                        <div class="actions" style="justify-content:center;">
                                            <a href="message_view.php?id=<?= $msg['id'] ?>" 
                                               class="action-btn btn-view" title="View">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <a href="?delete=<?= $msg['id'] ?>" 
                                               class="action-btn btn-delete" 
                                               onclick="return confirm('Delete this message?')" 
                                               title="Delete">
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
                                        </div>
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