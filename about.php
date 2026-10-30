<?php
// about.php - Dynamic CV Page (Connected to Database)
include 'include/header.php';
include 'config/db.php'; // ✅ Database connect kiya

// ✅ Database se CV data fetch karo
$cv_data = [];
$res = mysqli_query($conn, "SELECT * FROM cv ORDER BY id DESC LIMIT 1"); // Latest CV
if ($res && mysqli_num_rows($res) > 0) {
    $cv_data = mysqli_fetch_assoc($res);
}
?>

<style>
    /* ============ ABOUT PAGE — PROFESSIONAL STYLES ============ */

    .about-hero {
        padding: 2.5rem 0 1.5rem 0;
        text-align: left;
        animation: fadeInUp 0.7s ease;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(15px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .about-hero h1 {
        font-size: 3rem;
        font-weight: 700;
        color: #0f172a;
        text-align: left;
        background: none;
        -webkit-text-fill-color: #0f172a;
        margin-bottom: 0.6rem;
        line-height: 1.15;
        letter-spacing: -0.02em;
    }

    .about-hero h1 span {
        background: linear-gradient(135deg, #1e3a8a, #3b82f6, #8b5cf6);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        -webkit-text-fill-color: transparent;
    }

    .about-hero .role {
        font-size: 1.1rem;
        font-weight: 500;
        color: #475569;
        margin-bottom: 1.3rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .about-hero .role .tag {
        display: inline-block;
        background: #dbeafe;
        color: #1e3a8a;
        font-size: 0.82rem;
        font-weight: 600;
        padding: 0.28rem 0.8rem;
        border-radius: 30px;
        letter-spacing: 0.3px;
    }

    .about-hero .bio {
        font-size: 1.05rem;
        color: #334155;
        line-height: 1.8;
        max-width: 780px;
    }

    .hero-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.9rem;
        margin-top: 1.8rem;
    }

    .btn-cv, .btn-projects, .btn-contact-me {
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
        cursor: pointer;
        border: none;
        background: linear-gradient(135deg, #1e3a8a, #3b82f6);
        background-size: 200% 200%;
        color: #ffffff;
        box-shadow: 0 10px 22px -10px #1e3a8a;
        animation: gradientShift 5s ease infinite;
    }

    .btn-cv:hover, .btn-projects:hover, .btn-contact-me:hover {
        transform: translateY(-4px) scale(1.03);
        box-shadow: 0 18px 30px -12px #2563eb;
    }

    @keyframes gradientShift {
        0%, 100% { background-position: 0% 50%; }
        50%      { background-position: 100% 50%; }
    }

    .section-title {
        display: flex;
        align-items: center;
        gap: 0.7rem;
        font-size: 1.35rem;
        font-weight: 700;
        color: #0f172a;
        margin: 2.2rem 0 1.1rem 0;
        padding: 0;
        border: none;
    }

    .section-title::before {
        content: "";
        width: 5px;
        height: 22px;
        background: linear-gradient(180deg, #1e3a8a, #3b82f6);
        border-radius: 4px;
        display: inline-block;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
        margin-top: 0.5rem;
    }

    .info-card {
        background: #ffffff;
        padding: 1.1rem 1.3rem;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 6px 15px -8px rgba(0, 0, 0, 0.06);
        display: flex;
        align-items: center;
        gap: 0.9rem;
        transition: 0.28s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .info-card:hover {
        transform: translateY(-4px);
        border-color: #93c5fd;
        box-shadow: 0 15px 25px -12px rgba(59, 130, 246, 0.35);
    }

    .info-card .icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .info-card .details {
        display: flex;
        flex-direction: column;
        line-height: 1.3;
        overflow: hidden;
    }

    .info-card .label {
        font-size: 0.7rem;
        font-weight: 700;
        color: #64748b;
        letter-spacing: 1.2px;
        text-transform: uppercase;
        margin-bottom: 0.2rem;
    }

    .info-card .value {
        font-size: 0.95rem;
        font-weight: 600;
        color: #0f172a;
        word-break: break-word;
    }

    .skills-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 0.6rem;
        margin-top: 0.5rem;
    }

    .skill-pill {
        background: #ffffff;
        color: #1e3a8a;
        padding: 0.55rem 1.2rem;
        border-radius: 40px;
        font-size: 0.88rem;
        font-weight: 600;
        box-shadow: 0 4px 10px -4px rgba(0, 0, 0, 0.08), 0 0 0 1px #dbeafe;
        transition: 0.28s cubic-bezier(0.34, 1.56, 0.64, 1);
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
    }

    .skill-pill:hover {
        background: linear-gradient(135deg, #1e3a8a, #3b82f6);
        color: #ffffff;
        transform: translateY(-3px);
        box-shadow: 0 12px 20px -8px #3b82f6;
    }

    /* ============================================
       CV SECTION STYLES (DYNAMIC)
       ============================================ */
    .cv-header-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .cv-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 1.3rem;
        margin-top: 1rem;
    }

    .cv-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 1.5rem;
        box-shadow: 0 10px 25px -12px rgba(0,0,0,0.08);
        position: relative;
        transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .cv-card:hover {
        transform: translateY(-5px);
        border-color: #93c5fd;
        box-shadow: 0 20px 40px -15px rgba(59, 130, 246, 0.3);
    }

    .cv-card h3 {
        font-size: 1.25rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.7rem;
    }

    .cv-card .cv-meta {
        font-size: 0.88rem;
        color: #475569;
        line-height: 1.8;
        padding-bottom: 0.9rem;
        margin-bottom: 0.9rem;
        border-bottom: 1px dashed #cbd5e1;
    }

    .cv-card .cv-meta i {
        color: #3b82f6;
        width: 18px;
        margin-right: 0.4rem;
    }

    .cv-card h4 {
        font-size: 0.95rem;
        font-weight: 700;
        color: #1e3a8a;
        margin: 0.9rem 0 0.4rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .cv-card h4 i { color: #3b82f6; }

    .cv-card p {
        font-size: 0.9rem;
        color: #475569;
        line-height: 1.7;
        margin: 0;
    }

    .cv-pdf-btn {
        display: inline-flex; align-items: center; gap: 0.5rem;
        margin-top: 1rem; padding: 0.55rem 1.2rem;
        background: #fee2e2; color: #dc2626;
        border-radius: 40px; font-size: 0.82rem; font-weight: 600;
        text-decoration: none; transition: all 0.25s ease;
    }
    .cv-pdf-btn:hover { background: #dc2626; color: #fff; }

    @media (max-width: 700px) {
        .about-hero { padding: 1.5rem 0 1rem 0; text-align: center; }
        .about-hero h1 { font-size: 2.2rem; text-align: center; }
        .about-hero .role, .about-hero .bio { text-align: center; justify-content: center; }
        .hero-actions { justify-content: center; }
    }
    @media (max-width: 480px) {
        .about-hero h1 { font-size: 1.85rem; }
        .info-grid { grid-template-columns: 1fr; }
    }
</style>

<!-- ============================================
     ABOUT HERO
     ============================================ -->
<div class="about-hero">
    <h1><span>Mrunal Hyalis</span></h1>
    <div class="role">
        <span class="tag">Web Developer</span>
        <span class="tag">B.Sc. Computer Science</span>
    </div>
    <p class="bio">
        I am Mrunal Hyalis, a Web Developer and B.Sc. Computer Science graduate from Malegaon, Maharashtra.
        I specialize in building responsive, clean, and user-friendly websites with a focus on
        performance and modern design. Proficient in front-end technologies and passionate
        about transforming ideas into functional digital solutions, I am continuously
        enhancing my skills to deliver efficient and scalable web applications.
    </p>
    <div class="hero-actions">
        <a href="files/Mrunal_CV.pdf" target="_blank" rel="noopener noreferrer" class="btn-cv">
            <i class="fa-solid fa-file-pdf"></i> View CV
        </a>
        <a href="project.php" class="btn-projects">
            <i class="fa-solid fa-briefcase"></i> View Projects
        </a>
        <a href="contact.php" class="btn-contact-me">
            <i class="fa-solid fa-envelope"></i> Contact Me
        </a>
    </div>
</div>

<hr>

<!-- ============================================
     PERSONAL INFORMATION
     ============================================ -->
<h2 class="section-title">Personal Information</h2>
<div class="info-grid">
    <div class="info-card">
        <div class="icon">👤</div>
        <div class="details">
            <span class="label">Full Name</span>
            <span class="value">Mrunal Hyalis</span>
        </div>
    </div>
    <div class="info-card">
        <div class="icon">📧</div>
        <div class="details">
            <span class="label">Email</span>
            <span class="value">hyalismrunal306@gmail.com</span>
        </div>
    </div>
    <div class="info-card">
        <div class="icon">📱</div>
        <div class="details">
            <span class="label">Mobile</span>
            <span class="value">+91 70385 01758</span>
        </div>
    </div>
    <div class="info-card">
        <div class="icon">📍</div>
        <div class="details">
            <span class="label">Location</span>
            <span class="value">Malegaon, Maharashtra</span>
        </div>
    </div>
    <div class="info-card">
        <div class="icon">🎓</div>
        <div class="details">
            <span class="label">Qualification</span>
            <span class="value">B.Sc. Computer Science</span>
        </div>
    </div>
    <div class="info-card">
        <div class="icon">🌐</div>
        <div class="details">
            <span class="label">Languages</span>
            <span class="value">English, Hindi, Marathi</span>
        </div>
    </div>
</div>

<!-- ============================================
     SKILLS
     ============================================ -->
<h2 class="section-title">Skills &amp; Technologies</h2>
<div class="skills-grid">
    <span class="skill-pill">HTML5</span>
    <span class="skill-pill">CSS3</span>
    <span class="skill-pill">JavaScript</span>
    <span class="skill-pill">PHP</span>
    <span class="skill-pill">MySQL</span>
    <span class="skill-pill">Responsive Design</span>
    <span class="skill-pill">Bootstrap</span>
    <span class="skill-pill">Git &amp; GitHub</span>
</div>

<!-- ============================================
     ✅ DYNAMIC CV / RESUME SECTION
     ============================================ -->
<div class="cv-header-row" style="margin-top:2.2rem;">
    <h2 class="section-title" style="margin:0;">My Resume / CV</h2>
</div>

<?php if (!empty($cv_data)): ?>
    <div class="cv-grid">
        <div class="cv-card">
            <!-- Database se data fetch kiya gaya hai -->
            <h3><?= htmlspecialchars($cv_data['full_name'] ?? 'Mrunal Hyalis') ?></h3>
            
            <div class="cv-meta">
                <div><i class="fa-solid fa-envelope"></i> <?= htmlspecialchars($cv_data['email'] ?? 'N/A') ?></div>
                <div><i class="fa-solid fa-phone"></i> <?= htmlspecialchars($cv_data['phone'] ?? 'N/A') ?></div>
                <div><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($cv_data['address'] ?? 'N/A') ?></div>
            </div>

            <?php if (!empty($cv_data['education'])): ?>
                <h4><i class="fa-solid fa-graduation-cap"></i> Education</h4>
                <p><?= nl2br(htmlspecialchars($cv_data['education'])) ?></p>
            <?php endif; ?>

            <?php if (!empty($cv_data['experience'])): ?>
                <h4><i class="fa-solid fa-briefcase"></i> Experience</h4>
                <p><?= nl2br(htmlspecialchars($cv_data['experience'])) ?></p>
            <?php endif; ?>

            <?php if (!empty($cv_data['skills'])): ?>
                <h4><i class="fa-solid fa-lightbulb"></i> Skills</h4>
                <p><?= nl2br(htmlspecialchars($cv_data['skills'])) ?></p>
            <?php endif; ?>
            
            <!-- PDF Link -->
            <?php if (!empty($cv_data['cv_file']) && file_exists($cv_data['cv_file'])): ?>
                <a href="<?= htmlspecialchars($cv_data['cv_file']) ?>" target="_blank" class="cv-pdf-btn">
                    <i class="fa-solid fa-file-pdf"></i> View PDF
                </a>
            <?php else: ?>
                <a href="files/Mrunal_CV.pdf" target="_blank" class="cv-pdf-btn">
                    <i class="fa-solid fa-file-pdf"></i> View Default PDF
                </a>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <!-- Agar database me koi CV nahi hai -->
    <div class="cv-grid">
        <div class="cv-card" style="text-align: center; color: #64748b;">
            <p>Abhi tak koi CV update nahi kiya gaya hai. Admin panel se update karein.</p>
        </div>
    </div>
<?php endif; ?>

<?php include 'include/footer.php'; ?>