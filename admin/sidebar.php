<?php
// admin/sidebar.php - Admin Sidebar
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);
$admin_name   = $_SESSION['admin_username'] ?? 'Admin';
?>
<aside class="admin-sidebar" id="adminSidebar">

    <!-- Sidebar Header (Logo) -->
    <div class="sidebar-header">
        <a href="dashboard.php" class="sidebar-logo">
            <div class="logo-icon">MH</div>
            <div class="logo-text">
                <span class="logo-name">Mrunal Hyalis</span>
                <span class="logo-tag">Admin Panel</span>
            </div>
        </a>
        <button class="sidebar-close-btn" id="closeSidebarBtn" aria-label="Close sidebar">✕</button>
    </div>

    <!-- Admin Info -->
    <div class="sidebar-user">
        <div class="user-avatar">
            <i class="fa-solid fa-user-shield"></i>
        </div>
        <div class="user-info">
            <span class="user-name"><?= htmlspecialchars($admin_name) ?></span>
            <span class="user-role">Administrator</span>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav">

        <span class="nav-section-label">Main</span>

        <a href="dashboard.php" class="<?= $current_page == 'dashboard.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-gauge-high"></i>
            <span>Dashboard</span>
        </a>

        <a href="messages.php" class="<?= $current_page == 'messages.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-envelope"></i>
            <span>Contact Messages</span>
        </a>

        <a href="projects.php" class="<?= $current_page == 'projects.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-briefcase"></i>
            <span>Projects</span>
        </a>

        <span class="nav-section-label">Profile</span>

        <a href="skills.php" class="<?= $current_page == 'skills.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-star"></i>
            <span>Skills</span>
        </a>

        <a href="about_info.php" class="<?= $current_page == 'about_info.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-user"></i>
            <span>About Info</span>
        </a>

        <span class="nav-section-label">Site</span>

        <a href="../index.php" target="_blank">
            <i class="fa-solid fa-globe"></i>
            <span>View Portfolio</span>
        </a>

    </nav>

    <!-- Sidebar Footer (Logout) -->
    <div class="sidebar-footer">
        <a href="../logout.php" class="logout-link">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span>Logout</span>
        </a>
    </div>

</aside>

<!-- Sidebar Overlay (Mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- ✅ Sidebar Styles -->
<style>
    /* ============================================
       ADMIN SIDEBAR STYLES
       ============================================ */
    .admin-sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: 260px;
        height: 100vh;
        background: linear-gradient(180deg, #1e3a8a 0%, #1e40af 100%);
        color: #ffffff;
        display: flex;
        flex-direction: column;
        padding: 1.5rem 1rem;
        z-index: 1000;
        box-shadow: 4px 0 25px -10px rgba(30, 58, 138, 0.5);
        transition: transform 0.35s cubic-bezier(0.22, 1, 0.36, 1);
        overflow-y: auto;
    }

    /* Hide scrollbar for sidebar */
    .admin-sidebar::-webkit-scrollbar {
        width: 5px;
    }
    .admin-sidebar::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 10px;
    }

    /* === Sidebar Header (Logo) === */
    .sidebar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 1.2rem;
        margin-bottom: 1rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.15);
    }

    .sidebar-logo {
        display: flex;
        align-items: center;
        gap: 0.7rem;
        text-decoration: none;
    }

    .sidebar-logo .logo-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: linear-gradient(135deg, #ffffff, #e0e7ff);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #1e3a8a;
        font-size: 1.1rem;
        font-weight: 800;
        letter-spacing: -0.5px;
        box-shadow: 0 6px 15px -6px rgba(0, 0, 0, 0.3);
        flex-shrink: 0;
    }

    .sidebar-logo .logo-text {
        display: flex;
        flex-direction: column;
        line-height: 1.15;
    }

    .sidebar-logo .logo-name {
        font-size: 0.95rem;
        font-weight: 700;
        color: #ffffff;
    }

    .sidebar-logo .logo-tag {
        font-size: 0.62rem;
        font-weight: 600;
        color: #bfdbfe;
        letter-spacing: 1.5px;
        text-transform: uppercase;
    }

    /* Close button (mobile only) */
    .sidebar-close-btn {
        display: none;
        width: 32px;
        height: 32px;
        border: none;
        background: rgba(255, 255, 255, 0.15);
        color: #ffffff;
        border-radius: 8px;
        cursor: pointer;
        font-size: 1rem;
        transition: all 0.25s ease;
    }

    .sidebar-close-btn:hover {
        background: rgba(239, 68, 68, 0.8);
        transform: rotate(90deg);
    }

    /* === Admin User Info === */
    .sidebar-user {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        padding: 0.9rem 0.8rem;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 14px;
        margin-bottom: 1.5rem;
        border: 1px solid rgba(255, 255, 255, 0.15);
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #1e3a8a;
        font-size: 1rem;
        flex-shrink: 0;
        box-shadow: 0 4px 10px -4px rgba(0, 0, 0, 0.3);
    }

    .user-info {
        display: flex;
        flex-direction: column;
        line-height: 1.25;
        overflow: hidden;
    }

    .user-name {
        font-size: 0.88rem;
        font-weight: 700;
        color: #ffffff;
        word-break: break-word;
    }

    .user-role {
        font-size: 0.72rem;
        color: #bfdbfe;
        font-weight: 500;
        letter-spacing: 0.3px;
    }

    /* === Navigation === */
    .sidebar-nav {
        display: flex;
        flex-direction: column;
        gap: 0.3rem;
        flex: 1;
    }

    .nav-section-label {
        display: block;
        font-size: 0.65rem;
        font-weight: 700;
        color: #93c5fd;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        padding: 0.9rem 0.8rem 0.4rem 0.8rem;
    }

    .sidebar-nav a {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        padding: 0.75rem 0.9rem;
        border-radius: 12px;
        text-decoration: none;
        color: #cbd5e1;
        font-size: 0.9rem;
        font-weight: 600;
        letter-spacing: 0.2px;
        transition: all 0.28s cubic-bezier(0.34, 1.56, 0.64, 1);
        position: relative;
        overflow: hidden;
    }

    .sidebar-nav a i {
        font-size: 1rem;
        width: 20px;
        text-align: center;
        transition: transform 0.3s ease;
    }

    .sidebar-nav a:hover {
        background: rgba(255, 255, 255, 0.12);
        color: #ffffff;
        transform: translateX(4px);
    }

    .sidebar-nav a:hover i {
        transform: scale(1.15);
    }

    .sidebar-nav a.active {
        background: #ffffff;
        color: #1e3a8a;
        box-shadow: 0 8px 20px -8px rgba(0, 0, 0, 0.4);
    }

    .sidebar-nav a.active i {
        color: #1e3a8a;
    }

    /* === Sidebar Footer (Logout) === */
    .sidebar-footer {
        margin-top: auto;
        padding-top: 1rem;
        border-top: 1px solid rgba(255, 255, 255, 0.15);
    }

    .logout-link {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        padding: 0.75rem 0.9rem;
        border-radius: 12px;
        text-decoration: none;
        color: #fca5a5;
        font-size: 0.9rem;
        font-weight: 600;
        transition: all 0.28s ease;
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.25);
    }

    .logout-link:hover {
        background: rgba(239, 68, 68, 0.9);
        color: #ffffff;
        transform: translateX(4px);
    }

    .logout-link i {
        font-size: 1rem;
        width: 20px;
        text-align: center;
    }

    /* === Overlay === */
    .sidebar-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.5);
        backdrop-filter: blur(3px);
        -webkit-backdrop-filter: blur(3px);
        opacity: 0;
        visibility: hidden;
        z-index: 999;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }

    .sidebar-overlay.open {
        opacity: 1;
        visibility: visible;
    }

    /* ============================================
       RESPONSIVE — Mobile
       ============================================ */
    @media (max-width: 900px) {
        .admin-sidebar {
            transform: translateX(-100%);
        }

        .admin-sidebar.open {
            transform: translateX(0);
        }

        .sidebar-close-btn {
            display: flex;
            align-items: center;
            justify-content: center;
        }
    }
</style>