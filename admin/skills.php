<?php
// admin/skills.php - Skills + CV Manager
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../login.php");
    exit();
}

include '../config/db.php';
$admin_name = $_SESSION['admin_username'] ?? 'Admin';

$success_msg = "";
$error_msg   = "";

// ============================================
// DELETE Skill
// ============================================
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = mysqli_prepare($conn, "DELETE FROM skills WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt)) $success_msg = "Skill deleted successfully!";
    else $error_msg = "Failed to delete skill.";
    mysqli_stmt_close($stmt);
}

// ============================================
// DELETE CV
// ============================================
if (isset($_GET['delete_cv']) && is_numeric($_GET['delete_cv'])) {
    $id = intval($_GET['delete_cv']);
    $stmt = mysqli_prepare($conn, "DELETE FROM cv WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt)) $success_msg = "CV deleted successfully!";
    else $error_msg = "Failed to delete CV.";
    mysqli_stmt_close($stmt);
}

// ============================================
// ADD / UPDATE Skill
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['skill_name'])) {
    $id         = intval($_POST['id'] ?? 0);
    $skill_name = trim($_POST['skill_name'] ?? '');
    $icon       = trim($_POST['icon'] ?? '💻');
    $order      = intval($_POST['display_order'] ?? 0);

    if (empty($skill_name)) {
        $error_msg = "Skill name is required!";
    } else {
        if ($id > 0) {
            $sql = "UPDATE skills SET skill_name=?, icon=?, display_order=? WHERE id=?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssii", $skill_name, $icon, $order, $id);
            if (mysqli_stmt_execute($stmt)) $success_msg = "Skill updated successfully!";
            else $error_msg = "Update failed: " . mysqli_error($conn);
            mysqli_stmt_close($stmt);
        } else {
            $sql = "INSERT INTO skills (skill_name, icon, display_order) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssi", $skill_name, $icon, $order);
            if (mysqli_stmt_execute($stmt)) $success_msg = "Skill added successfully!";
            else $error_msg = "Insert failed: " . mysqli_error($conn);
            mysqli_stmt_close($stmt);
        }
    }
}

// ============================================
// ADD / UPDATE CV
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['full_name'])) {
    $id         = intval($_POST['cv_id'] ?? 0);
    $full_name  = trim($_POST['full_name'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $phone      = trim($_POST['phone'] ?? '');
    $address    = trim($_POST['address'] ?? '');
    $education  = trim($_POST['education'] ?? '');
    $experience = trim($_POST['experience'] ?? '');
    $skills     = trim($_POST['skills'] ?? '');

    if (empty($full_name)) {
        $error_msg = "Full name is required!";
    } else {
        if ($id > 0) {
            $sql = "UPDATE cv SET full_name=?, email=?, phone=?, address=?, education=?, experience=?, skills=? WHERE id=?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sssssssi", $full_name, $email, $phone, $address, $education, $experience, $skills, $id);
            if (mysqli_stmt_execute($stmt)) $success_msg = "CV updated successfully!";
            else $error_msg = "CV update failed: " . mysqli_error($conn);
            mysqli_stmt_close($stmt);
        } else {
            $sql = "INSERT INTO cv (full_name, email, phone, address, education, experience, skills) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sssssss", $full_name, $email, $phone, $address, $education, $experience, $skills);
            if (mysqli_stmt_execute($stmt)) $success_msg = "CV added successfully!";
            else $error_msg = "CV insert failed: " . mysqli_error($conn);
            mysqli_stmt_close($stmt);
        }
    }
}

// ============================================
// Fetch All Skills
// ============================================
$skills = [];
$res = mysqli_query($conn, "SELECT * FROM skills ORDER BY display_order ASC, id ASC");
if ($res) { while ($row = mysqli_fetch_assoc($res)) $skills[] = $row; }
$total_skills = count($skills);

// ============================================
// Fetch All CVs
// ============================================
$cvs = [];
$res = mysqli_query($conn, "SELECT * FROM cv ORDER BY id DESC");
if ($res) { while ($row = mysqli_fetch_assoc($res)) $cvs[] = $row; }
$total_cvs = count($cvs);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skills & CV · Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Roboto, system-ui, sans-serif; background: #f1f5f9; color: #1e293b; min-height: 100vh; line-height: 1.6; }
        .admin-main { margin-left: 260px; min-height: 100vh; }
        .admin-topbar { background: #fff; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px -5px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 50; gap: 1rem; }
        .topbar-left { display: flex; align-items: center; gap: 1rem; }
        .menu-toggle { display: none; width: 42px; height: 42px; background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: #fff; border: none; border-radius: 12px; cursor: pointer; font-size: 1.1rem; }
        .topbar-title h1 { font-size: 1.35rem; color: #0f172a; font-weight: 700; margin: 0; }
        .topbar-title p  { font-size: 0.82rem; color: #64748b; margin: 0; }
        .topbar-user { display: flex; align-items: center; gap: 0.6rem; padding: 0.4rem 0.9rem 0.4rem 0.4rem; background: #f8fafc; border-radius: 40px; border: 1px solid #e2e8f0; }
        .topbar-user .mini-avatar { width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #1e3a8a, #3b82f6); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 0.85rem; }
        .topbar-user span { font-size: 0.85rem; font-weight: 600; }
        .admin-content { padding: 2rem; max-width: 1400px; margin: 0 auto; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem; }
        .page-header h2 { font-size: 1.5rem; color: #0f172a; display: flex; align-items: center; gap: 0.6rem; margin: 0; }
        .page-header h2 i { color: #3b82f6; }
        .btn-add { display: inline-flex; align-items: center; gap: 0.6rem; padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #1e3a8a, #3b82f6); background-size: 200% 200%; animation: gradientShift 5s ease infinite; color: #fff; border: none; border-radius: 40px; font-size: 0.9rem; font-weight: 600; cursor: pointer; text-decoration: none; box-shadow: 0 8px 18px -8px #1e3a8a; transition: all 0.3s ease; }
        .btn-add:hover { transform: translateY(-3px); box-shadow: 0 15px 25px -10px #2563eb; }
        @keyframes gradientShift { 0%, 100% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } }
        .alert { padding: 0.85rem 1.2rem; border-radius: 12px; margin-bottom: 1.2rem; font-size: 0.9rem; font-weight: 500; display: flex; align-items: center; gap: 0.6rem; }
        .alert-success { background: #dcfce7; color: #166534; border-left: 5px solid #22c55e; }
        .alert-error   { background: #fee2e2; color: #991b1b; border-left: 5px solid #ef4444; }

        .table-card { background: #fff; border-radius: 18px; border: 1px solid #e2e8f0; box-shadow: 0 10px 25px -12px rgba(0,0,0,0.08); overflow: hidden; margin-bottom: 2.5rem; }
        .table-responsive { overflow-x: auto; }
        .skills-table { width: 100%; border-collapse: collapse; min-width: 700px; }
        .skills-table thead { background: linear-gradient(135deg, #1e3a8a, #3b82f6); }
        .skills-table th { text-align: left; padding: 1rem 1.2rem; font-size: 0.75rem; font-weight: 700; color: #fff; text-transform: uppercase; letter-spacing: 1px; }
        .skills-table td { padding: 0.95rem 1.2rem; font-size: 0.9rem; color: #1e293b; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        .skills-table tbody tr { transition: background 0.25s ease; }
        .skills-table tbody tr:hover { background: #f8fafc; }
        .skills-table tbody tr:last-child td { border-bottom: none; }

        .skill-icon-cell { font-size: 1.6rem; text-align: center; width: 70px; }
        .skill-name-cell { font-weight: 700; color: #0f172a; }
        .skill-date-cell { font-size: 0.82rem; color: #94a3b8; white-space: nowrap; }

        .action-btns { display: flex; gap: 0.4rem; }
        .btn-icon { width: 38px; height: 38px; border-radius: 10px; border: none; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; font-size: 0.85rem; transition: all 0.25s ease; text-decoration: none; }
        .btn-edit { background: #eff6ff; color: #1e3a8a; }
        .btn-edit:hover { background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: #fff; transform: translateY(-2px); box-shadow: 0 8px 15px -6px #1e3a8a; }
        .btn-del { background: #fee2e2; color: #dc2626; }
        .btn-del:hover { background: #dc2626; color: #fff; transform: translateY(-2px); box-shadow: 0 8px 15px -6px #dc2626; }

        .empty-state { text-align: center; padding: 4rem 1.5rem; color: #94a3b8; }
        .empty-state i { font-size: 4rem; color: #cbd5e1; margin-bottom: 1rem; display: block; }
        .empty-state p { font-size: 1rem; margin: 0; color: #94a3b8; }

        .section-title { display: flex; align-items: center; gap: 0.6rem; font-size: 1.2rem; font-weight: 700; color: #0f172a; margin: 2rem 0 1rem; padding-bottom: 0.5rem; border-bottom: 2px solid #e2e8f0; }
        .section-title i { color: #3b82f6; }
        .section-title span { background: #eff6ff; color: #1e3a8a; font-size: 0.8rem; padding: 0.2rem 0.7rem; border-radius: 20px; }

        .modal-overlay { position: fixed; inset: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(4px); display: none; align-items: center; justify-content: center; z-index: 1000; padding: 1.5rem; }
        .modal-overlay.active { display: flex; }
        .modal-box { background: #fff; border-radius: 20px; width: 100%; max-width: 600px; max-height: 92vh; overflow-y: auto; box-shadow: 0 30px 60px -15px rgba(0,0,0,0.5); }
        .modal-head { padding: 1.4rem 1.8rem; background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: #fff; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 5; }
        .modal-head h3 { margin: 0; font-size: 1.15rem; }
        .modal-close { width: 36px; height: 36px; background: rgba(255,255,255,0.2); color: #fff; border: none; border-radius: 10px; cursor: pointer; font-size: 1rem; transition: all 0.25s ease; }
        .modal-close:hover { background: rgba(239,68,68,0.9); transform: rotate(90deg); }
        .modal-body { padding: 1.8rem; }
        .form-group { margin-bottom: 1.1rem; }
        .form-group label { display: block; font-size: 0.85rem; font-weight: 600; color: #0f172a; margin-bottom: 0.4rem; }
        .form-group label span { color: #ef4444; }
        .form-group input, .form-group textarea { width: 100%; padding: 0.75rem 1rem; border: 2px solid #cbd5e1; border-radius: 10px; font-size: 0.92rem; font-family: inherit; background: #f8fafc; color: #1e293b; transition: all 0.25s ease; }
        .form-group textarea { resize: vertical; min-height: 70px; }
        .form-group input:focus, .form-group textarea:focus { outline: none; border-color: #3b82f6; background: #fff; box-shadow: 0 0 0 4px rgba(59,130,246,0.15); }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .form-hint { font-size: 0.75rem; color: #94a3b8; margin-top: 0.35rem; display: block; }

        .emoji-picks { display: flex; flex-wrap: wrap; gap: 0.4rem; margin-top: 0.6rem; }
        .emoji-picks button { width: 42px; height: 42px; border: 2px solid #e2e8f0; background: #fff; border-radius: 10px; font-size: 1.3rem; cursor: pointer; transition: all 0.2s ease; }
        .emoji-picks button:hover { border-color: #3b82f6; background: #eff6ff; transform: scale(1.1); }
        .emoji-picks button.selected { border-color: #1e3a8a; background: #dbeafe; }

        .modal-foot { padding: 1.2rem 1.8rem; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 0.7rem; background: #fff; position: sticky; bottom: 0; }
        .btn-cancel { padding: 0.7rem 1.4rem; background: #f1f5f9; color: #475569; border: none; border-radius: 10px; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: all 0.25s ease; }
        .btn-cancel:hover { background: #e2e8f0; }
        .btn-save { padding: 0.7rem 1.6rem; background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: #fff; border: none; border-radius: 10px; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: all 0.25s ease; box-shadow: 0 6px 15px -8px #1e3a8a; }
        .btn-save:hover { transform: translateY(-2px); box-shadow: 0 12px 22px -10px #2563eb; }

        @media (max-width: 900px) {
            .admin-main { margin-left: 0; }
            .menu-toggle { display: flex; align-items: center; justify-content: center; }
            .admin-content { padding: 1.5rem 1rem; }
        }
        @media (max-width: 600px) {
            .topbar-user span { display: none; }
            .modal-body { padding: 1.2rem; }
            .modal-head { padding: 1rem 1.2rem; }
            .modal-foot { padding: 1rem 1.2rem; }
            .form-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <main class="admin-main">

        <div class="admin-topbar">
            <div class="topbar-left">
                <button class="menu-toggle" id="menuToggle">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div class="topbar-title">
                    <h1>Skills & CV</h1>
                    <p>Manage your portfolio skills and resume</p>
                </div>
            </div>
            <div class="topbar-user">
                <div class="mini-avatar"><i class="fa-solid fa-user"></i></div>
                <span><?= htmlspecialchars($admin_name) ?></span>
            </div>
        </div>

        <div class="admin-content">

            <?php if (!empty($success_msg)): ?>
                <div class="alert alert-success">
                    <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($success_msg) ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error_msg) ?>
                </div>
            <?php endif; ?>

            <!-- CV SECTION -->
            <div class="page-header">
                <h2><i class="fa-solid fa-file-lines"></i> My CV / Resume (<?= $total_cvs ?>)</h2>
                <button class="btn-add" onclick="openCvModal()">
                    <i class="fa-solid fa-plus"></i> Add New CV
                </button>
            </div>

            <div class="table-card">
                <?php if (empty($cvs)): ?>
                    <div class="empty-state">
                        <i class="fa-solid fa-file-circle-plus"></i>
                        <p>No CV yet. Click "Add New CV" to get started.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="skills-table">
                            <thead>
                                <tr>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Skills</th>
                                    <th style="width:130px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cvs as $cv): ?>
                                    <tr>
                                        <td class="skill-name-cell"><?= htmlspecialchars($cv['full_name']) ?></td>
                                        <td><?= htmlspecialchars($cv['email']) ?></td>
                                        <td><?= htmlspecialchars($cv['phone']) ?></td>
                                        <td><?= htmlspecialchars(mb_strimwidth($cv['skills'], 0, 40, '...')) ?></td>
                                        <td>
                                            <div class="action-btns">
                                                <a href="#" class="btn-icon btn-edit"
                                                   onclick='openEditCvModal(<?= json_encode($cv, JSON_HEX_APOS | JSON_HEX_QUOT) ?>); return false;'
                                                   title="Edit CV">
                                                    <i class="fa-solid fa-pen"></i>
                                                </a>
                                                <a href="skills.php?delete_cv=<?= $cv['id'] ?>" 
                                                   class="btn-icon btn-del"
                                                   title="Delete CV"
                                                   onclick="return confirm('Kya aap ye CV delete karna chahte hain?');">
                                                    <i class="fa-solid fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- SKILLS SECTION (Order Column Removed) -->
            <div class="section-title">
                <i class="fa-solid fa-star"></i> All Skills <span><?= $total_skills ?></span>
            </div>

            <div class="table-card">
                <?php if (empty($skills)): ?>
                    <div class="empty-state">
                        <i class="fa-solid fa-star-half-stroke"></i>
                        <p>No skills yet. Click "Add New Skill" to get started.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="skills-table">
                            <thead>
                                <tr>
                                    <th style="width:70px;">Icon</th>
                                    <th>Skill Name</th>
                                    <th>Created</th>
                                    <th style="width:130px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($skills as $s): ?>
                                    <tr>
                                        <td class="skill-icon-cell"><?= htmlspecialchars($s['icon']) ?></td>
                                        <td class="skill-name-cell"><?= htmlspecialchars($s['skill_name']) ?></td>
                                        <!-- Order Column Data Removed -->
                                        <td class="skill-date-cell"><?= isset($s['created_at']) ? date('d M Y', strtotime($s['created_at'])) : '-' ?></td>
                                        <td>
                                            <div class="action-btns">
                                                <a href="#" class="btn-icon btn-edit"
                                                   onclick='openEditModal(<?= json_encode($s, JSON_HEX_APOS | JSON_HEX_QUOT) ?>); return false;'
                                                   title="Edit Skill">
                                                    <i class="fa-solid fa-pen"></i>
                                                </a>
                                                <a href="skills.php?delete=<?= $s['id'] ?>" 
                                                   class="btn-icon btn-del"
                                                   title="Delete Skill"
                                                   onclick="return confirm('Kya aap ye skill delete karna chahte hain?');">
                                                    <i class="fa-solid fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <div style="text-align:right; margin-top:1.5rem;">
                <button class="btn-add" onclick="openModal()">
                    <i class="fa-solid fa-plus"></i> Add New Skill
                </button>
            </div>

        </div>
    </main>

    <!-- MODAL: Add / Edit Skill -->
    <div class="modal-overlay" id="skillModal">
        <div class="modal-box">
            <div class="modal-head">
                <h3 id="modalTitle">Add New Skill</h3>
                <button class="modal-close" onclick="closeModal()" type="button">✕</button>
            </div>
            <form method="POST" action="skills.php" id="skillForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="s_id" value="0">
                    <div class="form-group">
                        <label>Skill Name <span>*</span></label>
                        <input type="text" name="skill_name" id="s_name" placeholder="e.g. HTML5, React, Figma" required>
                    </div>
                    <div class="form-group">
                        <label>Icon (Emoji)</label>
                        <input type="text" name="icon" id="s_icon" value="💻" maxlength="5">
                        <span class="form-hint">Ya niche se quick pick karein 👇</span>
                        <div class="emoji-picks" id="emojiPicks">
                            <button type="button" data-emoji="💻">💻</button>
                            <button type="button" data-emoji="🎨">🎨</button>
                            <button type="button" data-emoji="⚙️">⚙️</button>
                            <button type="button" data-emoji="📱">📱</button>
                            <button type="button" data-emoji="🚀">🚀</button>
                            <button type="button" data-emoji="🔥">🔥</button>
                            <button type="button" data-emoji="⭐">⭐</button>
                            <button type="button" data-emoji="🧠">🧠</button>
                            <button type="button" data-emoji="🛠️">🛠️</button>
                            <button type="button" data-emoji="📊">📊</button>
                            <button type="button" data-emoji="🌐">🌐</button>
                            <button type="button" data-emoji="🔒">🔒</button>
                        </div>
                    </div>
                    <!-- Order Input field is still here to save hidden default 0 value -->
                    <input type="hidden" name="display_order" id="s_order" value="0">
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn-save">
                        <i class="fa-solid fa-floppy-disk"></i> Save Skill
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: Add / Edit CV -->
    <div class="modal-overlay" id="cvModal">
        <div class="modal-box">
            <div class="modal-head">
                <h3 id="cvModalTitle">Add New CV</h3>
                <button class="modal-close" onclick="closeCvModal()" type="button">✕</button>
            </div>
            <form method="POST" action="skills.php" id="cvForm">
                <div class="modal-body">
                    <input type="hidden" name="cv_id" id="cv_id" value="0">

                    <div class="form-group">
                        <label>Full Name <span>*</span></label>
                        <input type="text" name="full_name" id="cv_name" placeholder="e.g. Mrunal Hyalis" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" id="cv_email" placeholder="you@example.com">
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" name="phone" id="cv_phone" placeholder="+91 98765 43210">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Address</label>
                        <input type="text" name="address" id="cv_address" placeholder="City, State, Country">
                    </div>

                    <div class="form-group">
                        <label>Education</label>
                        <textarea name="education" id="cv_education" placeholder="B.Sc. Computer Science, XYZ University, 2020-2024"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Experience</label>
                        <textarea name="experience" id="cv_experience" placeholder="Web Developer at ABC Company, 2024-Present"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Skills</label>
                        <textarea name="skills" id="cv_skills" placeholder="HTML, CSS, JavaScript, PHP, MySQL"></textarea>
                    </div>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn-cancel" onclick="closeCvModal()">Cancel</button>
                    <button type="submit" class="btn-save">
                        <i class="fa-solid fa-floppy-disk"></i> Save CV
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // ============================
        // SKILL MODAL
        // ============================
        const modal = document.getElementById('skillModal');
        const modalTitle = document.getElementById('modalTitle');

        function openModal() {
            document.getElementById('skillForm').reset();
            document.getElementById('s_id').value = 0;
            document.getElementById('s_icon').value = '💻';
            document.getElementById('s_order').value = 0;
            modalTitle.innerText = 'Add New Skill';
            clearSelectedEmoji();
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function openEditModal(skill) {
            document.getElementById('s_id').value    = skill.id;
            document.getElementById('s_name').value  = skill.skill_name;
            document.getElementById('s_icon').value  = skill.icon;
            document.getElementById('s_order').value = skill.display_order;
            modalTitle.innerText = 'Edit Skill';
            clearSelectedEmoji();
            highlightEmoji(skill.icon);
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }

        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeModal();
        });

        // Emoji Picker
        document.querySelectorAll('#emojiPicks button').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const emoji = this.getAttribute('data-emoji');
                document.getElementById('s_icon').value = emoji;
                clearSelectedEmoji();
                this.classList.add('selected');
            });
        });
        function clearSelectedEmoji() {
            document.querySelectorAll('#emojiPicks button').forEach(function(b) {
                b.classList.remove('selected');
            });
        }
        function highlightEmoji(emoji) {
            document.querySelectorAll('#emojiPicks button').forEach(function(b) {
                if (b.getAttribute('data-emoji') === emoji) b.classList.add('selected');
            });
        }

        // ============================
        // CV MODAL
        // ============================
        const cvModal = document.getElementById('cvModal');
        const cvModalTitle = document.getElementById('cvModalTitle');

        function openCvModal() {
            document.getElementById('cvForm').reset();
            document.getElementById('cv_id').value = 0;
            cvModalTitle.innerText = 'Add New CV';
            cvModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function openEditCvModal(cv) {
            document.getElementById('cv_id').value        = cv.id;
            document.getElementById('cv_name').value      = cv.full_name;
            document.getElementById('cv_email').value     = cv.email;
            document.getElementById('cv_phone').value     = cv.phone;
            document.getElementById('cv_address').value   = cv.address;
            document.getElementById('cv_education').value = cv.education;
            document.getElementById('cv_experience').value= cv.experience;
            document.getElementById('cv_skills').value    = cv.skills;
            cvModalTitle.innerText = 'Edit CV';
            cvModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeCvModal() {
            cvModal.classList.remove('active');
            document.body.style.overflow = '';
        }

        cvModal.addEventListener('click', function(e) {
            if (e.target === cvModal) closeCvModal();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
                closeCvModal();
            }
        });
    </script>

</body>
</html>