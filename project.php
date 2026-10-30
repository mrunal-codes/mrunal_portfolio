<?php
// project.php - Dynamic Projects Page
include 'include/header.php';
include 'config/db.php';

// ============================================
// Fetch Projects from Database
// ============================================
$projects = [];
$res = mysqli_query($conn, "SELECT * FROM projects ORDER BY display_order ASC, id DESC");
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $projects[] = $row;
    }
}
?>

<style>
    /* ============ PROJECTS PAGE — PROFESSIONAL STYLES ============ */

    /* === Page Hero === */
    .projects-hero {
        padding: 2.5rem 0 1.5rem 0;
        text-align: center;
        animation: fadeInUp 0.7s ease;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(15px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .projects-hero .greeting {
        display: inline-block;
        font-size: 0.78rem;
        font-weight: 700;
        color: #3b82f6;
        letter-spacing: 2.5px;
        text-transform: uppercase;
        margin-bottom: 0.8rem;
        background: #eff6ff;
        padding: 0.35rem 1rem;
        border-radius: 40px;
    }

    .projects-hero h1 {
        font-size: 3rem;
        font-weight: 700;
        color: #0f172a;
        background: none;
        -webkit-text-fill-color: #0f172a;
        margin-bottom: 0.6rem;
        line-height: 1.15;
        letter-spacing: -0.02em;
    }

    .projects-hero h1 span {
        background: linear-gradient(135deg, #1e3a8a, #3b82f6, #8b5cf6);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        -webkit-text-fill-color: transparent;
    }

    .projects-hero .subtitle {
        font-size: 1.05rem;
        color: #64748b;
        max-width: 620px;
        margin: 0 auto;
        line-height: 1.7;
    }

    /* === Filter Buttons === */
    .filter-bar {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 0.6rem;
        margin: 2rem 0 2.5rem 0;
    }

    .filter-btn {
        background: #ffffff;
        border: 2px solid #dbeafe;
        color: #1e3a8a;
        padding: 0.55rem 1.3rem;
        border-radius: 40px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        font-family: inherit;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }

    .filter-btn:hover {
        transform: translateY(-3px);
        border-color: #93c5fd;
        box-shadow: 0 10px 20px -10px rgba(59, 130, 246, 0.35);
    }

    .filter-btn.active {
        background: linear-gradient(135deg, #1e3a8a, #3b82f6);
        background-size: 200% 200%;
        color: #ffffff;
        border-color: transparent;
        box-shadow: 0 12px 22px -10px #1e3a8a;
        animation: gradientShift 5s ease infinite;
    }

    @keyframes gradientShift {
        0%, 100% { background-position: 0% 50%; }
        50%      { background-position: 100% 50%; }
    }

    /* === Projects Grid === */
    .projects-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 1.8rem;
        margin-top: 0.5rem;
    }

    /* === Project Card === */
    .project-card {
        background: #ffffff;
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 25px -12px rgba(0, 0, 0, 0.1);
        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        display: flex;
        flex-direction: column;

        opacity: 0;
        transform: translateY(30px);
        animation: cardIn 0.6s cubic-bezier(0.22, 1, 0.36, 1) forwards;
    }

    @keyframes cardIn {
        to { opacity: 1; transform: translateY(0); }
    }

    .project-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 30px 55px -18px rgba(59, 130, 246, 0.4);
        border-color: #93c5fd;
    }

    .project-card.hide {
        display: none;
    }

    /* === Project Image (Top Section) === */
    .project-image {
        height: 180px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    .project-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: top;
        display: block;
    }

    .project-icon {
        font-size: 3.5rem;
        color: #ffffff;
        filter: drop-shadow(0 6px 12px rgba(0, 0, 0, 0.3));
        transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        z-index: 1;
    }

    .project-card:hover .project-icon {
        transform: scale(1.2) rotate(-6deg);
    }

    .project-image::before {
        content: "";
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.25), transparent 60%);
        opacity: 0;
        transition: opacity 0.4s ease;
    }

    .project-card:hover .project-image::before {
        opacity: 1;
    }

    /* === Project Content === */
    .project-content {
        padding: 1.4rem 1.5rem 1.5rem 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 0.8rem;
        flex: 1;
    }

    .project-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        letter-spacing: -0.01em;
    }

    .project-desc {
        font-size: 0.92rem;
        color: #475569;
        line-height: 1.65;
        margin: 0;
        flex: 1;
    }

    /* === Tech Tags === */
    .project-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem;
        margin-top: 0.2rem;
    }

    .project-tags span {
        background: #eff6ff;
        color: #1e3a8a;
        font-size: 0.72rem;
        font-weight: 700;
        padding: 0.28rem 0.75rem;
        border-radius: 30px;
        letter-spacing: 0.3px;
        border: 1px solid #dbeafe;
    }

    /* === Project Links === */
    .project-links {
        display: flex;
        gap: 0.6rem;
        margin-top: 0.6rem;
    }

    .btn-live,
    .btn-code {
        flex: 1;
        text-align: center;
        text-decoration: none;
        font-size: 0.82rem;
        font-weight: 700;
        padding: 0.65rem 0.8rem;
        border-radius: 10px;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
    }

    .btn-live {
        background: linear-gradient(135deg, #1e3a8a, #3b82f6);
        background-size: 200% 200%;
        color: #ffffff;
        box-shadow: 0 8px 18px -8px #1e3a8a;
        animation: gradientShift 5s ease infinite;
    }

    .btn-live:hover {
        transform: translateY(-3px) scale(1.03);
        box-shadow: 0 15px 25px -10px #2563eb;
    }

    .btn-code {
        background: #ffffff;
        color: #1e3a8a;
        border: 2px solid #dbeafe;
    }

    .btn-code:hover {
        background: #eff6ff;
        border-color: #93c5fd;
        transform: translateY(-3px) scale(1.03);
        box-shadow: 0 12px 22px -10px rgba(59, 130, 246, 0.35);
    }

    /* === Empty State === */
    .empty-projects {
        grid-column: 1 / -1;
        text-align: center;
        padding: 4rem 2rem;
        color: #94a3b8;
    }

    .empty-projects i {
        font-size: 4rem;
        color: #cbd5e1;
        margin-bottom: 1rem;
        display: block;
    }

    .empty-projects p {
        font-size: 1rem;
        margin: 0;
        color: #94a3b8;
    }

    /* === Responsive === */
    @media (max-width: 700px) {
        .projects-hero { padding: 1.5rem 0 1rem 0; }
        .projects-hero h1 { font-size: 2.2rem; }
        .projects-hero .subtitle { font-size: 0.95rem; }
        .projects-grid { grid-template-columns: 1fr; gap: 1.4rem; }
    }

    @media (max-width: 480px) {
        .projects-hero h1 { font-size: 1.85rem; }
        .project-image { height: 150px; }
        .project-icon { font-size: 2.8rem; }
        .project-content { padding: 1.2rem 1.2rem 1.3rem 1.2rem; }
        .project-title { font-size: 1.08rem; }
        .project-desc { font-size: 0.88rem; }
        .filter-btn { padding: 0.5rem 1rem; font-size: 0.82rem; }
        .btn-live, .btn-code { padding: 0.6rem 0.6rem; font-size: 0.78rem; }
    }
</style>

<!-- ============================================
     PROJECTS HERO
     ============================================ -->
<div class="projects-hero">

    <span class="greeting">My Work</span>

    <h1>My <span>Projects</span></h1>

    <p class="subtitle">
        A showcase of my recent work, side projects, and web development experiments.
        Each project reflects my passion for clean code and modern design.
    </p>

</div>

<hr>

<!-- ============================================
     FILTER BUTTONS
     ============================================ -->
<div class="filter-bar">
    <button class="filter-btn active" data-filter="all">🌟 All</button>
    <button class="filter-btn" data-filter="web">🌐 Web</button>
    <button class="filter-btn" data-filter="app">📱 App</button>
    <button class="filter-btn" data-filter="design">🎨 Design</button>
</div>

<!-- ============================================
     PROJECTS GRID (Dynamic)
     ============================================ -->
<div class="projects-grid">

    <?php if (empty($projects)): ?>

        <div class="empty-projects">
            <i class="fa-solid fa-folder-open"></i>
            <p>No projects added yet. Check back soon!</p>
        </div>

    <?php else: ?>

        <?php foreach ($projects as $index => $project): ?>

            <div class="project-card" data-category="<?= htmlspecialchars($project['category']) ?>" 
                 style="animation-delay: <?= 0.05 + ($index * 0.07) ?>s;">

                <!-- Project Image / Icon -->
                <div class="project-image" style="background: <?= htmlspecialchars($project['icon_color']) ?>;">

                    <?php if (!empty($project['image']) && file_exists($project['image'])): ?>
                        <img src="<?= htmlspecialchars($project['image']) ?>" 
                             alt="<?= htmlspecialchars($project['title']) ?>">
                    <?php else: ?>
                        <i class="<?= htmlspecialchars($project['icon']) ?> project-icon"></i>
                    <?php endif; ?>

                </div>

                <!-- Project Content -->
                <div class="project-content">

                    <h3 class="project-title"><?= htmlspecialchars($project['title']) ?></h3>

                    <p class="project-desc"><?= htmlspecialchars($project['description']) ?></p>

                    <!-- Tech Tags -->
                    <?php if (!empty($project['tech_stack'])): ?>
                        <div class="project-tags">
                            <?php 
                            $tags = explode(',', $project['tech_stack']);
                            foreach ($tags as $tag): 
                            ?>
                                <span><?= htmlspecialchars(trim($tag)) ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Links -->
                    <div class="project-links">
                        <?php if (!empty($project['live_link'])): ?>
                            <a href="<?= htmlspecialchars($project['live_link']) ?>" 
                               target="_blank" rel="noopener" class="btn-live">
                                <i class="fa-solid fa-link"></i> Live Demo
                            </a>
                        <?php endif; ?>

                        <?php if (!empty($project['code_link'])): ?>
                            <a href="<?= htmlspecialchars($project['code_link']) ?>" 
                               target="_blank" rel="noopener" class="btn-code">
                                <i class="fa-brands fa-github"></i> Code
                            </a>
                        <?php endif; ?>
                    </div>

                </div>

            </div>

        <?php endforeach; ?>

    <?php endif; ?>

</div>

<!-- ============================================
     JS FOR FILTER BUTTONS
     ============================================ -->
<script>
    (function () {
        const filterBtns = document.querySelectorAll('.filter-btn');
        const cards      = document.querySelectorAll('.project-card');

        filterBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                filterBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                const filter = btn.getAttribute('data-filter');

                cards.forEach(function (card) {
                    const category = card.getAttribute('data-category');

                    if (filter === 'all' || category === filter) {
                        card.classList.remove('hide');
                        card.style.animation = 'none';
                        void card.offsetWidth;
                        card.style.animation = '';
                    } else {
                        card.classList.add('hide');
                    }
                });
            });
        });
    })();
</script>

<?php include 'include/footer.php'; ?>