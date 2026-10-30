<?php
// index.php - Dynamic Home Page (Professional)
include 'include/header.php';
include 'config/db.php';

// ============================================
// Fetch HERO data
// ============================================
$hero = null;
$res = mysqli_query($conn, "SELECT * FROM home_content WHERE section = 'hero' ORDER BY id DESC LIMIT 1");
if ($res && mysqli_num_rows($res) > 0) {
    $hero = mysqli_fetch_assoc($res);
}

// Parse extra_data
$hero_extra = [];
if (!empty($hero['extra_data'])) {
    foreach (explode('|', $hero['extra_data']) as $pair) {
        $parts = explode(':', $pair, 2);
        if (count($parts) === 2) {
            $hero_extra[trim($parts[0])] = trim($parts[1]);
        }
    }
}

// Split name
$name_parts = explode(' ', $hero['title'] ?? 'Mrunal Hyalis', 2);
$first_name = $name_parts[0] ?? 'Mrunal';
$last_name  = $name_parts[1] ?? '';

// ============================================
// Fetch INTRO cards
// ============================================
$intro_cards = [];
$res = mysqli_query($conn, "SELECT * FROM home_content WHERE section = 'intro' ORDER BY display_order ASC");
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $intro_cards[] = $row;
    }
}

// ============================================
// Fetch CV / Resume data
// ============================================
$cv_data = [];
$res = mysqli_query($conn, "SELECT * FROM cv ORDER BY id DESC");
if ($res && mysqli_num_rows($res) > 0) {
    while ($row = mysqli_fetch_assoc($res)) {
        $cv_data[] = $row;
    }
}
?>

<style>
    /* ============================================
       HOME PAGE — PROFESSIONAL STYLES
       ============================================ */
    .home-hero {
        padding: 3rem 0 4rem 0;
        animation: fadeInUp 0.8s ease;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .home-hero .container {
        display: grid;
        grid-template-columns: 1.3fr 1fr;
        gap: 3.5rem;
        align-items: center;
    }

    /* === Left: Text === */
    .hero-text {
        display: flex;
        flex-direction: column;
    }

    .hero-greeting {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.82rem;
        font-weight: 700;
        color: #3b82f6;
        letter-spacing: 2.5px;
        text-transform: uppercase;
        background: #eff6ff;
        padding: 0.4rem 1rem;
        border-radius: 40px;
        width: fit-content;
        margin-bottom: 1.2rem;
        border: 1px solid #dbeafe;
    }

    .hero-name {
        font-size: 3.2rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
        letter-spacing: -0.03em;
        margin-bottom: 0.5rem;
    }

    .hero-name span {
        background: linear-gradient(135deg, #1e3a8a, #3b82f6, #8b5cf6);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        -webkit-text-fill-color: transparent;
    }

    .hero-role {
        font-size: 1.3rem;
        font-weight: 600;
        color: #475569;
        margin-bottom: 1.3rem;
        letter-spacing: -0.01em;
    }

    .hero-bio {
        font-size: 1.05rem;
        color: #64748b;
        line-height: 1.8;
        max-width: 540px;
        margin-bottom: 1.8rem;
    }

    /* === Buttons === */
    .hero-cta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.9rem;
        margin-bottom: 1.8rem;
    }

    .btn-primary,
    .btn-secondary {
        display: inline-flex;
        align-items: center;
        gap: 0.6rem;
        padding: 0.85rem 1.8rem;
        border-radius: 40px;
        font-size: 0.95rem;
        font-weight: 600;
        text-decoration: none;
        letter-spacing: 0.3px;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        border: none;
        cursor: pointer;
    }

    .btn-primary {
        background: linear-gradient(135deg, #1e3a8a, #3b82f6);
        background-size: 200% 200%;
        color: #ffffff;
        box-shadow: 0 10px 22px -10px #1e3a8a;
        animation: gradientShift 5s ease infinite;
    }

    .btn-primary:hover {
        transform: translateY(-4px) scale(1.03);
        box-shadow: 0 18px 30px -12px #2563eb;
    }

    .btn-secondary {
        background: #ffffff;
        color: #1e3a8a;
        border: 2px solid #dbeafe;
        box-shadow: 0 6px 15px -8px rgba(0, 0, 0, 0.08);
    }

    .btn-secondary:hover {
        background: #eff6ff;
        border-color: #93c5fd;
        transform: translateY(-4px);
        box-shadow: 0 15px 25px -12px rgba(59, 130, 246, 0.35);
    }

    @keyframes gradientShift {
        0%, 100% { background-position: 0% 50%; }
        50%      { background-position: 100% 50%; }
    }

    /* === Quick Contact === */
    .hero-contact {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px dashed #cbd5e1;
    }

    .hero-contact a {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: #475569;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .hero-contact a i {
        color: #3b82f6;
        font-size: 1rem;
    }

    .hero-contact a:hover {
        color: #1e3a8a;
        transform: translateX(4px);
    }

    /* === Right: Photo === */
    .hero-photo {
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
    }

    .photo-wrapper {
        position: relative;
        width: 260px;
        height: 260px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .photo-ring {
        position: absolute;
        inset: 0;
        border-radius: 50%;
        background: linear-gradient(135deg, #3b82f6, #8b5cf6, #ec4899, #3b82f6);
        background-size: 300% 300%;
        animation: ringRotate 8s linear infinite;
        z-index: 0;
    }

    @keyframes ringRotate {
        0%   { background-position: 0% 50%; transform: rotate(0deg); }
        100% { background-position: 300% 50%; transform: rotate(360deg); }
    }

    .photo-ring::after {
        content: '';
        position: absolute;
        inset: 4px;
        border-radius: 50%;
        background: #ffffff;
    }

    .photo-img {
        position: relative;
        z-index: 1;
        width: 240px;
        height: 240px;
        border-radius: 50%;
        overflow: hidden;
        box-shadow: 0 20px 45px -15px rgba(30, 58, 138, 0.35);
    }

    .photo-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: top;
        display: block;
        transition: transform 0.5s ease;
    }

    .photo-img:hover img {
        transform: scale(1.06);
    }

    /* ============================================
       INTRODUCTION SECTION
       ============================================ */
    .intro-section {
        padding: 3rem 0 4rem 0;
    }

    .intro-heading {
        text-align: center;
        margin-bottom: 2.8rem;
    }

    .intro-label {
        display: inline-block;
        font-size: 0.78rem;
        font-weight: 700;
        color: #3b82f6;
        letter-spacing: 2.5px;
        text-transform: uppercase;
        background: #eff6ff;
        padding: 0.35rem 1rem;
        border-radius: 40px;
        margin-bottom: 0.9rem;
        border: 1px solid #dbeafe;
    }

    .intro-heading h2 {
        font-size: 2.2rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.6rem;
        border: none;
        padding: 0;
        display: block;
        letter-spacing: -0.02em;
    }

    .intro-heading p {
        font-size: 1rem;
        color: #64748b;
        max-width: 550px;
        margin: 0 auto;
    }

    .intro-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 1.5rem;
    }

    .intro-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 1.8rem 1.5rem;
        text-align: center;
        box-shadow: 0 10px 25px -12px rgba(0, 0, 0, 0.08);
        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        position: relative;
        overflow: hidden;
    }

    .intro-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, #1e3a8a, #3b82f6, #8b5cf6);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.4s ease;
    }

    .intro-card:hover::before {
        transform: scaleX(1);
    }

    .intro-card:hover {
        transform: translateY(-8px);
        border-color: #93c5fd;
        box-shadow: 0 25px 45px -15px rgba(59, 130, 246, 0.3);
    }

    .intro-card i {
        font-size: 2.2rem;
        color: #3b82f6;
        margin-bottom: 1rem;
        display: inline-block;
        transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .intro-card:hover i {
        transform: scale(1.15) rotate(-6deg);
    }

    .intro-card h3 {
        font-size: 1.15rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.7rem;
        letter-spacing: -0.01em;
    }

    .intro-card p {
        font-size: 0.92rem;
        color: #64748b;
        line-height: 1.7;
        margin: 0;
    }

    /* ============================================
       CV / RESUME SECTION STYLES
       ============================================ */
    .cv-section {
        padding: 3rem 0 4rem 0;
        background: #f8fafc;
    }

    .cv-heading {
        text-align: center;
        margin-bottom: 2.5rem;
    }

    .cv-heading h2 {
        font-size: 2.2rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.5rem;
        letter-spacing: -0.02em;
    }

    .cv-heading p {
        font-size: 1rem;
        color: #64748b;
        max-width: 550px;
        margin: 0 auto;
    }

    .cv-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 1.5rem;
    }

    .cv-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 1.8rem;
        box-shadow: 0 10px 25px -12px rgba(0, 0, 0, 0.08);
        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .cv-card:hover {
        transform: translateY(-6px);
        border-color: #93c5fd;
        box-shadow: 0 25px 45px -15px rgba(59, 130, 246, 0.3);
    }

    .cv-card h3 {
        font-size: 1.4rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.5rem;
    }

    .cv-card .cv-meta {
        font-size: 0.9rem;
        color: #64748b;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px dashed #cbd5e1;
    }

    .cv-card .cv-meta i {
        color: #3b82f6;
        margin-right: 0.4rem;
    }

    .cv-card h4 {
        font-size: 1rem;
        font-weight: 700;
        color: #1e3a8a;
        margin: 1rem 0 0.5rem 0;
    }

    .cv-card p {
        font-size: 0.92rem;
        color: #475569;
        line-height: 1.7;
        margin: 0;
    }

    /* ============================================
       RESPONSIVE
       ============================================ */
    @media (max-width: 900px) {
        .home-hero .container {
            grid-template-columns: 1fr;
            gap: 2.5rem;
            text-align: center;
        }

        .hero-text { align-items: center; }
        .hero-name { font-size: 2.6rem; }
        .hero-role { font-size: 1.15rem; }
        .hero-bio  { text-align: center; }
        .hero-cta, .hero-contact { justify-content: center; }
        .hero-photo { order: -1; margin-bottom: 1rem; }

        .photo-wrapper { width: 220px; height: 220px; }
        .photo-img { width: 200px; height: 200px; }

        .intro-heading h2 { font-size: 1.8rem; }
        .cv-heading h2 { font-size: 1.8rem; }
    }

    @media (max-width: 480px) {
        .home-hero { padding: 2rem 0 2.5rem 0; }
        .hero-name { font-size: 2.1rem; }
        .hero-role { font-size: 1rem; }
        .hero-bio  { font-size: 0.95rem; }

        .btn-primary, .btn-secondary {
            padding: 0.8rem 1.4rem;
            font-size: 0.88rem;
        }

        .hero-contact {
            flex-direction: column;
            gap: 0.7rem;
            align-items: center;
        }

        .photo-wrapper { width: 180px; height: 180px; }
        .photo-img { width: 165px; height: 165px; }

        .intro-heading h2 { font-size: 1.5rem; }
        .intro-grid       { grid-template-columns: 1fr; }
        .cv-heading h2 { font-size: 1.5rem; }
        .cv-grid { grid-template-columns: 1fr; }
    }
</style>

<!-- ============================================
     HERO SECTION
     ============================================ -->
<section class="home-hero">
    <div class="container">

        <!-- LEFT: Text -->
        <div class="hero-text">

            <p class="hero-greeting">
                <i class="fa-solid fa-hand-wave"></i>
                Hello, I'm
            </p>

            <h1 class="hero-name">
                <?= htmlspecialchars($first_name) ?>
                <?php if (!empty($last_name)): ?>
                    <span><?= htmlspecialchars($last_name) ?></span>
                <?php endif; ?>
            </h1>

            <h2 class="hero-role"><?= htmlspecialchars($hero['subtitle'] ?? 'Web Developer') ?></h2>

            <p class="hero-bio">
                <?= htmlspecialchars($hero['description'] ?? '') ?>
            </p>

            <div class="hero-cta">
                <a href="about.php" class="btn-primary">
                    <i class="fa-solid fa-user"></i> About Me
                </a>
                <a href="contact.php" class="btn-secondary">
                    <i class="fa-solid fa-envelope"></i> Contact Me
                </a>
            </div>

            <div class="hero-contact">
                <?php if (!empty($hero_extra['email'])): ?>
                    <a href="mailto:<?= htmlspecialchars($hero_extra['email']) ?>">
                        <i class="fa-solid fa-envelope"></i>
                        <?= htmlspecialchars($hero_extra['email']) ?>
                    </a>
                <?php endif; ?>

                <?php if (!empty($hero_extra['phone'])): ?>
                    <a href="tel:<?= htmlspecialchars(str_replace(' ', '', $hero_extra['phone'])) ?>">
                        <i class="fa-solid fa-phone"></i>
                        <?= htmlspecialchars($hero_extra['phone']) ?>
                    </a>
                <?php endif; ?>
            </div>

        </div>

        <!-- RIGHT: Photo -->
        <div class="hero-photo">
            <div class="photo-wrapper">
                <div class="photo-ring"></div>
                <div class="photo-img">
                    <?php if (!empty($hero_extra['image']) && file_exists($hero_extra['image'])): ?>
                        <img src="<?= htmlspecialchars($hero_extra['image']) ?>" 
                             alt="<?= htmlspecialchars($hero['title'] ?? 'Profile') ?>">
                    <?php else: ?>
                        <div style="width:100%; height:100%; background:linear-gradient(135deg,#1e3a8a,#3b82f6); display:flex; align-items:center; justify-content:center; color:#fff; font-size:3rem;">
                            <i class="fa-solid fa-user"></i>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</section>


<!-- ============================================
     INTRODUCTION SECTION
     ============================================ -->
<section class="intro-section">
    <div class="container">

        <div class="intro-heading">
            <p class="intro-label">GET TO KNOW ME</p>
            <h2>Welcome to My Portfolio</h2>
            <p>Explore my skills, projects and professional journey.</p>
        </div>

        <div class="intro-grid">
            <?php if (empty($intro_cards)): ?>
                <p style="text-align:center; color:#94a3b8; grid-column:1/-1;">
                    No introduction cards found.
                </p>
            <?php else: ?>
                <?php foreach ($intro_cards as $card): ?>
                    <div class="intro-card">
                        <i class="<?= htmlspecialchars($card['icon'] ?? 'fa-solid fa-star') ?>"></i>
                        <h3><?= htmlspecialchars($card['title'] ?? '') ?></h3>
                        <p><?= htmlspecialchars($card['description'] ?? '') ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</section>


<!-- ============================================
     CV / RESUME SECTION
     ============================================ -->
<section class="cv-section">
    <div class="container">

        <div class="cv-heading">
            <h2>My Resume / CV</h2>
            <p>My education, experience, and skills at a glance.</p>
        </div>

        <div class="cv-grid">
            <?php if (empty($cv_data)): ?>
                <p style="text-align:center; color:#94a3b8; grid-column:1/-1;">
                    No CV data available.
                </p>
            <?php else: ?>
                <?php foreach ($cv_data as $cv): ?>
                    <div class="cv-card">
                        <h3><?= htmlspecialchars($cv['full_name']) ?></h3>
                        <div class="cv-meta">
                            <i class="fa-solid fa-envelope"></i><?= htmlspecialchars($cv['email']) ?><br>
                            <i class="fa-solid fa-phone"></i><?= htmlspecialchars($cv['phone']) ?><br>
                            <i class="fa-solid fa-location-dot"></i><?= htmlspecialchars($cv['address']) ?>
                        </div>

                        <?php if (!empty($cv['education'])): ?>
                            <h4><i class="fa-solid fa-graduation-cap"></i> Education</h4>
                            <p><?= nl2br(htmlspecialchars($cv['education'])) ?></p>
                        <?php endif; ?>

                        <?php if (!empty($cv['experience'])): ?>
                            <h4><i class="fa-solid fa-briefcase"></i> Experience</h4>
                            <p><?= nl2br(htmlspecialchars($cv['experience'])) ?></p>
                        <?php endif; ?>

                        <?php if (!empty($cv['skills'])): ?>
                            <h4><i class="fa-solid fa-lightbulb"></i> Skills</h4>
                            <p><?= nl2br(htmlspecialchars($cv['skills'])) ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</section>

<?php
include 'include/footer.php';
?>