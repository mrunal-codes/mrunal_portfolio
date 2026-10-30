<?php
// include/header.php - Responsive header with hamburger + sidebar drawer
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mrunal Hyalis · Portfolio</title>

    <!-- ✅ FONT AWESOME CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        /* === Global Reset & Typography === */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Roboto, system-ui, -apple-system, sans-serif;
            background: linear-gradient(145deg, #f8faff 0%, #eef2f9 100%);
            color: #1e293b;
            line-height: 1.6;
            padding: 0;
            margin: 0;
            min-height: 100vh;
            display: block;
            overflow-x: hidden;
        }

        /* ============================================
           === ANIMATED BACKGROUND BLOBS ===
           ============================================ */
        .bg-blobs {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: -1;
            overflow: hidden;
            pointer-events: none;
        }

        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.35;
            animation: floatBlob 18s ease-in-out infinite;
        }

        .blob-1 { width: 400px; height: 400px; background: #60a5fa; top: -100px; left: -100px; }
        .blob-2 { width: 350px; height: 350px; background: #a78bfa; top: 40%; right: -100px; animation-delay: 3s; }
        .blob-3 { width: 300px; height: 300px; background: #34d399; bottom: -100px; left: 30%; animation-delay: 6s; }

        @keyframes floatBlob {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33%      { transform: translate(40px, -50px) scale(1.1); }
            66%      { transform: translate(-30px, 30px) scale(0.95); }
        }

        /* ============================================
           === WRAPPER ===
           ============================================ */
        .portfolio-wrapper {
            max-width: 100%;
            width: 100%;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            padding: 0 2.5rem 2rem 2.5rem;
            min-height: 100vh;
            animation: fadeInPage 0.8s ease;
        }

        @keyframes fadeInPage {
            from { opacity: 0; transform: translateY(15px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ============================================
           === STICKY HEADER BAR ===
           ============================================ */
        .header-top {
            position: sticky;
            top: 0;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1.2rem 2rem;
            padding: 1rem 0;
            margin-bottom: 2rem;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border-bottom: 1px solid rgba(203, 213, 225, 0.5);
            animation: slideDown 0.7s cubic-bezier(0.22, 1, 0.36, 1);
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-30px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .header-top::after {
            content: '';
            position: absolute;
            bottom: -1px; left: 0;
            height: 3px; width: 100%;
            background: linear-gradient(90deg, #3b82f6, #8b5cf6, #ec4899, #3b82f6);
            background-size: 300% 100%;
            animation: gradientMove 6s linear infinite;
        }

        @keyframes gradientMove {
            0%   { background-position: 0% 50%; }
            100% { background-position: 300% 50%; }
        }

        /* ============================================
           === LOGO ===
           ============================================ */
        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            animation: logoEnter 0.9s cubic-bezier(0.22, 1, 0.36, 1) 0.15s both;
        }

        @keyframes logoEnter {
            from { opacity: 0; transform: translateX(-40px) scale(0.9); }
            to   { opacity: 1; transform: translateX(0) scale(1); }
        }

        .logo:hover { transform: scale(1.05); }

        .logo-icon {
            position: relative;
            width: 52px; height: 52px;
            border-radius: 16px;
            background: linear-gradient(135deg, #1e3a8a, #3b82f6, #8b5cf6);
            background-size: 200% 200%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 1.4rem;
            font-weight: 800;
            letter-spacing: -1px;
            box-shadow: 0 8px 18px -6px rgba(30, 58, 138, 0.55),
                        0 0 0 3px rgba(255, 255, 255, 0.6);
            flex-shrink: 0;
            animation: gradientShift 5s ease infinite;
            transition: box-shadow 0.3s ease;
            overflow: hidden;
        }

        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50%      { background-position: 100% 50%; }
        }

        .logo:hover .logo-icon {
            box-shadow: 0 12px 25px -6px rgba(59, 130, 246, 0.7),
                        0 0 0 4px rgba(59, 130, 246, 0.2);
            animation: pulseGlow 1.2s ease infinite;
        }

        @keyframes pulseGlow {
            0%, 100% { transform: scale(1); }
            50%      { transform: scale(1.08); }
        }

        .logo-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: top;
            border-radius: 16px;
            display: block;
        }

        .logo-text {
            display: flex;
            flex-direction: column;
            line-height: 1.1;
        }

        .logo-text .logo-name {
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            background: linear-gradient(90deg, #0f172a, #3b82f6);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .logo-text .logo-tag {
            font-size: 0.72rem;
            font-weight: 600;
            color: #3b82f6;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        /* ============================================
           === DESKTOP NAVIGATION ===
           ============================================ */
        .navbar {
            display: flex;
            flex-wrap: wrap;
            gap: 0.6rem 0.8rem;
            justify-content: center;
            animation: navEnter 0.9s cubic-bezier(0.22, 1, 0.36, 1) 0.3s both;
        }

        @keyframes navEnter {
            from { opacity: 0; transform: translateX(40px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        .navbar a {
            position: relative;
            overflow: hidden;
            text-decoration: none;
            font-weight: 600;
            color: #1e3a8a;
            background: #e0e7ff;
            padding: 0.6rem 1.4rem;
            border-radius: 40px;
            font-size: 0.95rem;
            letter-spacing: 0.3px;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.5);
            white-space: nowrap;
            z-index: 1;
        }

        .navbar a::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.6), transparent);
            transition: left 0.6s ease;
            z-index: -1;
        }

        .navbar a:hover::before { left: 100%; }

        .navbar a:hover,
        .navbar a.active {
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            color: white;
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 12px 20px -10px #1e3a8a, 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

        .navbar a.active { animation: activePulse 2s ease-in-out infinite; }

        @keyframes activePulse {
            0%, 100% { box-shadow: 0 12px 20px -10px #1e3a8a, 0 0 0 3px rgba(59, 130, 246, 0.15); }
            50%      { box-shadow: 0 12px 20px -10px #1e3a8a, 0 0 0 8px rgba(59, 130, 246, 0.08); }
        }

        /* ============================================
           === HAMBURGER BUTTON ===
           ============================================ */
        .hamburger {
            display: none;
            flex-direction: column;
            justify-content: space-between;
            width: 44px;
            height: 44px;
            padding: 11px 10px;
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            border: none;
            border-radius: 14px;
            cursor: pointer;
            box-shadow: 0 6px 15px -6px rgba(30, 58, 138, 0.6),
                        0 0 0 3px rgba(255, 255, 255, 0.6);
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
            z-index: 120;
        }

        .hamburger:hover {
            transform: scale(1.08) rotate(3deg);
            box-shadow: 0 10px 22px -6px rgba(59, 130, 246, 0.7),
                        0 0 0 4px rgba(59, 130, 246, 0.2);
        }

        .hamburger span {
            display: block;
            height: 3px;
            width: 100%;
            background: #ffffff;
            border-radius: 3px;
            transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
            transform-origin: center;
        }

        .hamburger.open span:nth-child(1) { transform: translateY(8px) rotate(45deg); }
        .hamburger.open span:nth-child(2) { opacity: 0; transform: scaleX(0); }
        .hamburger.open span:nth-child(3) { transform: translateY(-8px) rotate(-45deg); }

        /* ============================================
           === SIDE DRAWER ===
           ============================================ */
        .side-drawer {
            position: fixed;
            top: 0;
            right: -320px;
            width: 300px;
            max-width: 82vw;
            height: 100vh;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            box-shadow: -15px 0 40px -15px rgba(15, 23, 42, 0.35);
            z-index: 200;
            padding: 1.8rem 1.6rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            transition: right 0.45s cubic-bezier(0.22, 1, 0.36, 1);
            border-left: 1px solid rgba(203, 213, 225, 0.6);
            overflow-y: auto;
        }

        .side-drawer.open { right: 0; }

        .drawer-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
            border-bottom: 2px dashed #cbd5e1;
        }

        .drawer-title {
            font-size: 1.1rem;
            font-weight: 700;
            background: linear-gradient(90deg, #1e3a8a, #3b82f6, #8b5cf6);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: 0.5px;
        }

        .close-btn {
            width: 38px;
            height: 38px;
            border: none;
            border-radius: 12px;
            background: #f1f5f9;
            color: #1e3a8a;
            font-size: 1.2rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .close-btn:hover {
            background: linear-gradient(135deg, #ef4444, #f87171);
            color: #ffffff;
            transform: rotate(90deg) scale(1.1);
            box-shadow: 0 8px 15px -6px rgba(239, 68, 68, 0.6);
        }

        .drawer-nav {
            display: flex;
            flex-direction: column;
            gap: 0.7rem;
        }

        .drawer-nav a {
            position: relative;
            display: flex;
            align-items: center;
            gap: 0.9rem;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.05rem;
            color: #1e3a8a;
            background: #f1f5f9;
            padding: 0.95rem 1.2rem;
            border-radius: 16px;
            transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
            border-left: 4px solid transparent;
            overflow: hidden;
        }

        .drawer-nav a:hover,
        .drawer-nav a.active {
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            color: #ffffff;
            transform: translateX(6px);
            border-left-color: #fbbf24;
            box-shadow: 0 12px 22px -12px #1e3a8a;
        }

        .drawer-nav a .nav-emoji {
            font-size: 1.3rem;
            display: inline-block;
            transition: transform 0.3s ease;
        }

        .drawer-nav a:hover .nav-emoji {
            transform: scale(1.2) rotate(-8deg);
        }

        .drawer-footer {
            margin-top: auto;
            padding-top: 1.5rem;
            border-top: 2px dashed #cbd5e1;
            text-align: center;
            font-size: 0.85rem;
            color: #64748b;
        }

        .drawer-footer strong {
            display: block;
            color: #1e3a8a;
            font-size: 0.95rem;
            margin-bottom: 0.2rem;
        }

        /* ============================================
           === OVERLAY ===
           ============================================ */
        .drawer-overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.5);
            backdrop-filter: blur(3px);
            -webkit-backdrop-filter: blur(3px);
            opacity: 0;
            visibility: hidden;
            z-index: 150;
            transition: opacity 0.4s ease, visibility 0.4s ease;
        }

        .drawer-overlay.open {
            opacity: 1;
            visibility: visible;
        }

        /* ============================================
           === HEADINGS & GENERAL ===
           ============================================ */
        h1 {
            font-size: 2.8rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            background: linear-gradient(135deg, #0f172a, #2563eb, #8b5cf6);
            background-size: 200% 200%;
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 0.25rem;
            text-align: center;
            line-height: 1.2;
            animation: gradientShift 6s ease infinite;
        }

        h2 {
            font-size: 1.8rem;
            font-weight: 600;
            color: #0f172a;
            margin: 1.8rem 0 1rem 0;
            padding-bottom: 0.3rem;
            border-bottom: 3px solid #3b82f6;
            display: inline-block;
            letter-spacing: -0.01em;
        }

        h3 {
            font-size: 1.4rem;
            font-weight: 600;
            color: #1e40af;
            margin: 1rem 0 0.5rem 0;
        }

        p { margin-bottom: 1rem; font-size: 1.1rem; color: #1e293b; }
        b, strong { color: #0f172a; font-weight: 600; }

        ul {
            list-style: none;
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem 1rem;
            margin: 1rem 0 1.5rem 0;
        }

        ul li {
            background: white;
            padding: 0.6rem 1.4rem;
            border-radius: 40px;
            font-weight: 500;
            color: #1e3a8a;
            box-shadow: 0 4px 10px -4px rgba(0, 0, 0, 0.1), 0 0 0 1px #dbeafe;
            transition: 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            font-size: 1rem;
        }

        ul li:hover {
            background: #dbeafe;
            transform: scale(1.05) translateY(-2px);
            box-shadow: 0 10px 15px -8px #3b82f6;
        }

        hr {
            margin: 2rem 0 1.5rem 0;
            border: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, #93c5fd, #3b82f6, #93c5fd, transparent);
            border-radius: 2px;
        }

        .contact-info p {
            background: #f1f5f9;
            padding: 0.5rem 1.2rem;
            border-radius: 16px;
            display: inline-block;
            margin-right: 1rem;
            margin-bottom: 0.75rem;
            border-left: 6px solid #2563eb;
            transition: all 0.25s ease;
        }

        .contact-info p:hover {
            transform: translateX(5px);
            background: #e0e7ff;
        }

        .project-card {
            background: #ffffffd6;
            padding: 1.5rem 1.8rem;
            border-radius: 1.8rem;
            margin: 1.2rem 0 1.8rem 0;
            border: 1px solid #e2e8f0;
            box-shadow: 0 8px 20px -10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .project-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 30px -15px rgba(59, 130, 246, 0.3);
        }

        .contact-form {
            background: #ffffffd6;
            padding: 1.8rem;
            border-radius: 1.8rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 8px 20px -10px rgba(0, 0, 0, 0.1);
            margin-top: 1rem;
        }

        .contact-form label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.4rem;
            color: #0f172a;
        }

        .contact-form input,
        .contact-form textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #cbd5e1;
            border-radius: 12px;
            font-size: 1rem;
            font-family: inherit;
            margin-bottom: 1rem;
            transition: all 0.25s ease;
            background: #f8fafc;
        }

        .contact-form input:focus,
        .contact-form textarea:focus {
            outline: none;
            border-color: #3b82f6;
            background: white;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
            transform: translateY(-2px);
        }

        .contact-form button {
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            background-size: 200% 200%;
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 40px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            animation: gradientShift 5s ease infinite;
        }

        .contact-form button:hover {
            transform: translateY(-3px) scale(1.03);
            box-shadow: 0 15px 25px -10px #2563eb;
        }

        /* ============================================
           === RESPONSIVE ===
           ============================================ */
        @media (max-width: 900px) {
            .portfolio-wrapper { padding: 0 1.5rem 1.8rem 1.5rem; }
            .navbar a { padding: 0.55rem 1.15rem; font-size: 0.9rem; }
        }

        /* IMPORTANT: Hamburger shows on screens below 768px */
        @media (max-width: 768px) {
            .portfolio-wrapper { padding: 0 1.2rem 1.5rem 1.2rem; }
            .header-top {
                padding: 0.85rem 0;
                gap: 0.6rem;
                flex-wrap: nowrap;
                justify-content: space-between;
            }
            .navbar { display: none !important; } /* Hides desktop nav */
            .hamburger { display: flex !important; } /* Shows hamburger */
            h1 { font-size: 2.1rem; }
            h2 { font-size: 1.45rem; }
            h3 { font-size: 1.15rem; }
            p  { font-size: 1rem; }
            .logo-icon { width: 46px; height: 46px; font-size: 1.2rem; border-radius: 14px; }
            .logo-text .logo-name { font-size: 1.05rem; }
            .logo-text .logo-tag  { font-size: 0.62rem; letter-spacing: 1.5px; }
        }

        @media (max-width: 480px) {
            .portfolio-wrapper { padding: 0 1rem 1.2rem 1rem; }
            h1 { font-size: 1.8rem; }
            h2 { font-size: 1.3rem; }
            .logo-icon { width: 42px; height: 42px; font-size: 1.1rem; }
            .logo-text .logo-name { font-size: 0.98rem; }
            .logo-text .logo-tag  { font-size: 0.58rem; }
            .hamburger { width: 42px; height: 42px; padding: 10px 9px; }
            ul li { padding: 0.45rem 1rem; font-size: 0.9rem; }
            .contact-form { padding: 1.2rem; }
            .project-card { padding: 1.1rem 1.3rem; }
        }

        body.no-scroll { overflow: hidden; }
    </style>
</head>
<body>

<!-- Animated background blobs -->
<div class="bg-blobs">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>
</div>

<div class="portfolio-wrapper">

    <!-- ============================================
         HEADER TOP: LOGO + NAVIGATION + HAMBURGER
         ============================================ -->
    <div class="header-top">

        <!-- Logo with Profile Photo -->
        <a href="index.php" class="logo">
            <div class="logo-icon">
                <img src="images/Profile.jpg" alt="Mrunal Hyalis">
            </div>
            <div class="logo-text">
                <span class="logo-name">Mrunal Hyalis</span>
                <span class="logo-tag">Portfolio</span>
            </div>
        </a>

        <!-- Desktop Navigation -->
        <div class="navbar">
            <?php $page = basename($_SERVER['PHP_SELF']); ?>
            <a href="index.php"   class="<?= $page == 'index.php'   ? 'active' : '' ?>"> Home</a>
            <a href="about.php"   class="<?= $page == 'about.php'   ? 'active' : '' ?>"> About</a>
            <a href="project.php" class="<?= $page == 'project.php' ? 'active' : '' ?>">Projects</a>
            <a href="contact.php" class="<?= $page == 'contact.php' ? 'active' : '' ?>"> Contact</a>
            <a href="login.php" class="<?= $page == 'login.php' ? 'active' : '' ?>">Login</a>
        </div>

        <!-- Hamburger (Mobile Only) -->
        <button class="hamburger" id="hamburgerBtn" aria-label="Open menu" aria-expanded="false">
            <span></span>
            <span></span>
            <span></span>
        </button>

    </div>

    <!-- ============================================
         SIDE DRAWER + OVERLAY
         ============================================ -->
    <div class="drawer-overlay" id="drawerOverlay"></div>

    <aside class="side-drawer" id="sideDrawer" aria-hidden="true">
        <div class="drawer-header">
            <span class="drawer-title">Menu</span>
            <button class="close-btn" id="closeDrawerBtn" aria-label="Close menu">✕</button>
        </div>

        <nav class="drawer-nav">
            <?php $page = basename($_SERVER['PHP_SELF']); ?>
            <a href="index.php"   class="<?= $page == 'index.php'   ? 'active' : '' ?>">
                <span class="nav-emoji">🏠</span> Home
            </a>
            <a href="about.php"   class="<?= $page == 'about.php'   ? 'active' : '' ?>">
                <span class="nav-emoji">👤</span> About
            </a>
            <a href="project.php" class="<?= $page == 'project.php' ? 'active' : '' ?>">
                <span class="nav-emoji">💼</span> Projects
            </a>
            <a href="contact.php" class="<?= $page == 'contact.php' ? 'active' : '' ?>">
                <span class="nav-emoji">✉️</span> Contact
            </a>
            <a href="login.php" class="<?= $page == 'login.php' ? 'active' : '' ?>">
                <span class="nav-emoji">🔐</span> Login
            </a>
        </nav>

        <div class="drawer-footer">
            <strong>Mrunal Hyalis</strong>
            © <?= date('Y') ?> · All Rights Reserved
        </div>
    </aside>

    <!-- ============================================
         JS for Hamburger + Drawer Toggle
         ============================================ -->
    <script>
        (function () {
            const hamburger = document.getElementById('hamburgerBtn');
            const drawer    = document.getElementById('sideDrawer');
            const overlay   = document.getElementById('drawerOverlay');
            const closeBtn  = document.getElementById('closeDrawerBtn');

            function openDrawer() {
                drawer.classList.add('open');
                overlay.classList.add('open');
                hamburger.classList.add('open');
                hamburger.setAttribute('aria-expanded', 'true');
                drawer.setAttribute('aria-hidden', 'false');
                document.body.classList.add('no-scroll');
            }

            function closeDrawer() {
                drawer.classList.remove('open');
                overlay.classList.remove('open');
                hamburger.classList.remove('open');
                hamburger.setAttribute('aria-expanded', 'false');
                drawer.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('no-scroll');
            }

            hamburger.addEventListener('click', function () {
                if (drawer.classList.contains('open')) {
                    closeDrawer();
                } else {
                    openDrawer();
                }
            });

            closeBtn.addEventListener('click', closeDrawer);
            overlay.addEventListener('click', closeDrawer);

            drawer.querySelectorAll('a').forEach(function (link) {
                link.addEventListener('click', closeDrawer);
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && drawer.classList.contains('open')) {
                    closeDrawer();
                }
            });
        })();
    </script>