<?php
// admin/projects.php - Projects Manager
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
// DELETE Project
// ============================================
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // Delete image first
    $stmt = mysqli_prepare($conn, "SELECT image FROM projects WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($res)) {
        if (!empty($row['image']) && file_exists('../' . $row['image'])) {
            unlink('../' . $row['image']);
        }
    }
    mysqli_stmt_close($stmt);

    // Delete project
    $stmt = mysqli_prepare($conn, "DELETE FROM projects WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt)) {
        $success_msg = "Project deleted successfully!";
    } else {
        $error_msg = "Failed to delete project.";
    }
    mysqli_stmt_close($stmt);
}

// ============================================
// ADD / UPDATE Project
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id          = intval($_POST['id'] ?? 0);
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category    = trim($_POST['category'] ?? 'web');
    $tech_stack  = trim($_POST['tech_stack'] ?? '');
    $live_link   = trim($_POST['live_link'] ?? '');
    $code_link   = trim($_POST['code_link'] ?? '');
    $icon        = trim($_POST['icon'] ?? 'fa-solid fa-briefcase');
    $icon_color  = trim($_POST['icon_color'] ?? 'linear-gradient(135deg, #3b82f6, #8b5cf6)');
    $order       = intval($_POST['display_order'] ?? 0);

    if (empty($title) || empty($description)) {
        $error_msg = "Title and Description are required!";
    } else {
        $image_path = null;
        $upload_ok = true;

        // Image upload
        if (!empty($_FILES['image']['name'])) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed)) {
                $error_msg = "Only JPG, PNG, GIF, WEBP allowed.";
                $upload_ok = false;
            } elseif ($_FILES['image']['size'] > 5 * 1024 * 1024) {
                $error_msg = "Image size must be under 5MB.";
                $upload_ok = false;
            } else {
                $new_name = 'project_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                $upload_dir = '../images/projects/';
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

                $target = $upload_dir . $new_name;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                    $image_path = 'images/projects/' . $new_name;
                } else {
                    $error_msg = "Image upload failed.";
                    $upload_ok = false;
                }
            }
        }

        if ($upload_ok) {
            if ($id > 0) {
                // UPDATE
                if ($image_path) {
                    $stmt = mysqli_prepare($conn, "SELECT image FROM projects WHERE id = ?");
                    mysqli_stmt_bind_param($stmt, "i", $id);
                    mysqli_stmt_execute($stmt);
                    $r = mysqli_stmt_get_result($stmt);
                    if ($old = mysqli_fetch_assoc($r)) {
                        if (!empty($old['image']) && file_exists('../' . $old['image'])) {
                            unlink('../' . $old['image']);
                        }
                    }
                    mysqli_stmt_close($stmt);

                    $sql = "UPDATE projects SET title=?, description=?, category=?, tech_stack=?, live_link=?, code_link=?, icon=?, icon_color=?, display_order=?, image=? WHERE id=?";
                    $stmt = mysqli_prepare($conn, $sql);
                    mysqli_stmt_bind_param($stmt, "ssssssssisi", $title, $description, $category, $tech_stack, $live_link, $code_link, $icon, $icon_color, $order, $image_path, $id);
                } else {
                    $sql = "UPDATE projects SET title=?, description=?, category=?, tech_stack=?, live_link=?, code_link=?, icon=?, icon_color=?, display_order=? WHERE id=?";
                    $stmt = mysqli_prepare($conn, $sql);
                    mysqli_stmt_bind_param($stmt, "ssssssssii", $title, $description, $category, $tech_stack, $live_link, $code_link, $icon, $icon_color, $order, $id);
                }

                if (mysqli_stmt_execute($stmt)) {
                    $success_msg = "Project updated successfully!";
                } else {
                    $error_msg = "Update failed: " . mysqli_error($conn);
                }
                mysqli_stmt_close($stmt);
            } else {
                // INSERT
                $sql = "INSERT INTO projects (title, description, category, tech_stack, live_link, code_link, icon, icon_color, display_order, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "ssssssssis", $title, $description, $category, $tech_stack, $live_link, $code_link, $icon, $icon_color, $order, $image_path);

                if (mysqli_stmt_execute($stmt)) {
                    $success_msg = "Project added successfully!";
                } else {
                    $error_msg = "Insert failed: " . mysqli_error($conn);
                }
                mysqli_stmt_close($stmt);
            }
        }
    }
}

// Fetch all projects
$projects = [];
$res = mysqli_query($conn, "SELECT * FROM projects ORDER BY display_order ASC, id DESC");
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $projects[] = $row;
    }
}
$total_projects = count($projects);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects · Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Roboto, system-ui, sans-serif;
            background: #f1f5f9; color: #1e293b;
            min-height: 100vh; line-height: 1.6;
        }
        .admin-main { margin-left: 260px; min-height: 100vh; }
        .admin-topbar {
            background: #fff; padding: 1rem 2rem;
            display: flex; justify-content: space-between;
            align-items: center; box-shadow: 0 2px 10px -5px rgba(0,0,0,0.1);
            position: sticky; top: 0; z-index: 50; gap: 1rem;
        }
        .topbar-left { display: flex; align-items: center; gap: 1rem; }
        .menu-toggle {
            display: none; width: 42px; height: 42px;
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            color: #fff; border: none; border-radius: 12px;
            cursor: pointer; font-size: 1.1rem;
        }
        .topbar-title h1 { font-size: 1.35rem; color: #0f172a; font-weight: 700; margin: 0; }
        .topbar-title p  { font-size: 0.82rem; color: #64748b; margin: 0; }
        .topbar-user {
            display: flex; align-items: center; gap: 0.6rem;
            padding: 0.4rem 0.9rem 0.4rem 0.4rem;
            background: #f8fafc; border-radius: 40px; border: 1px solid #e2e8f0;
        }
        .topbar-user .mini-avatar {
            width: 32px; height: 32px; border-radius: 50%;
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 0.85rem;
        }
        .topbar-user span { font-size: 0.85rem; font-weight: 600; }
        .admin-content { padding: 2rem; max-width: 1400px; margin: 0 auto; }
        .page-header {
            display: flex; justify-content: space-between;
            align-items: center; margin-bottom: 1.5rem;
            flex-wrap: wrap; gap: 1rem;
        }
        .page-header h2 {
            font-size: 1.5rem; color: #0f172a;
            display: flex; align-items: center; gap: 0.6rem; margin: 0;
        }
        .page-header h2 i { color: #3b82f6; }
        .btn-add {
            display: inline-flex; align-items: center; gap: 0.6rem;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            background-size: 200% 200%;
            animation: gradientShift 5s ease infinite;
            color: #fff; border: none; border-radius: 40px;
            font-size: 0.9rem; font-weight: 600;
            cursor: pointer; text-decoration: none;
            box-shadow: 0 8px 18px -8px #1e3a8a;
            transition: all 0.3s ease;
        }
        .btn-add:hover { transform: translateY(-3px); box-shadow: 0 15px 25px -10px #2563eb; }
        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50%      { background-position: 100% 50%; }
        }
        .alert {
            padding: 0.85rem 1.2rem; border-radius: 12px;
            margin-bottom: 1.2rem; font-size: 0.9rem;
            font-weight: 500; display: flex;
            align-items: center; gap: 0.6rem;
        }
        .alert-success { background: #dcfce7; color: #166534; border-left: 5px solid #22c55e; }
        .alert-error   { background: #fee2e2; color: #991b1b; border-left: 5px solid #ef4444; }
        .projects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        .project-card {
            background: #fff; border-radius: 18px;
            overflow: hidden; border: 1px solid #e2e8f0;
            box-shadow: 0 10px 25px -12px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            display: flex; flex-direction: column;
        }
        .project-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px -15px rgba(59,130,246,0.3);
            border-color: #93c5fd;
        }
        .project-image {
            height: 150px;
            display: flex; align-items: center; justify-content: center;
            position: relative; overflow: hidden;
        }
        .project-image img { width: 100%; height: 100%; object-fit: cover; object-position: top; }
        .project-image i {
            font-size: 3rem; color: #fff;
            filter: drop-shadow(0 6px 12px rgba(0,0,0,0.3));
        }
        .project-body {
            padding: 1.2rem 1.3rem;
            display: flex; flex-direction: column; gap: 0.7rem; flex: 1;
        }
        .project-title { font-size: 1.1rem; font-weight: 700; color: #0f172a; margin: 0; }
        .project-desc {
            font-size: 0.88rem; color: #64748b;
            line-height: 1.6; margin: 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .project-cat {
            display: inline-block;
            background: #eff6ff; color: #1e3a8a;
            font-size: 0.7rem; font-weight: 700;
            padding: 0.25rem 0.7rem; border-radius: 30px;
            text-transform: uppercase; letter-spacing: 0.5px;
            width: fit-content; border: 1px solid #dbeafe;
        }
        .project-actions {
            display: flex; gap: 0.5rem;
            margin-top: auto; padding-top: 0.5rem;
            border-top: 1px solid #f1f5f9;
        }
        .btn-icon {
            flex: 1; height: 38px;
            border-radius: 10px; border: none;
            cursor: pointer; display: inline-flex;
            align-items: center; justify-content: center;
            gap: 0.4rem; font-size: 0.8rem;
            font-weight: 600; transition: all 0.25s ease;
            text-decoration: none;
        }
        .btn-edit { background: #eff6ff; color: #1e3a8a; }
        .btn-edit:hover { background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: #fff; }
        .btn-del { background: #fee2e2; color: #dc2626; }
        .btn-del:hover { background: #dc2626; color: #fff; }
        .empty-state {
            grid-column: 1/-1; text-align: center;
            padding: 4rem 1.5rem; background: #fff;
            border-radius: 18px; border: 1px solid #e2e8f0;
        }
        .empty-state i {
            font-size: 4rem; color: #cbd5e1;
            margin-bottom: 1rem; display: block;
        }
        .empty-state p { color: #94a3b8; margin: 0 0 1.5rem 0; }
        .modal-overlay {
            position: fixed; inset: 0;
            background: rgba(15,23,42,0.6);
            backdrop-filter: blur(4px);
            display: none; align-items: center;
            justify-content: center; z-index: 1000;
            padding: 1.5rem;
        }
        .modal-overlay.active { display: flex; }
        .modal-box {
            background: #fff; border-radius: 20px;
            width: 100%; max-width: 650px;
            max-height: 92vh; overflow-y: auto;
            box-shadow: 0 30px 60px -15px rgba(0,0,0,0.5);
        }
        .modal-head {
            padding: 1.4rem 1.8rem;
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            color: #fff; display: flex;
            justify-content: space-between; align-items: center;
            position: sticky; top: 0; z-index: 2;
        }
        .modal-head h3 { margin: 0; font-size: 1.2rem; }
        .modal-close {
            width: 36px; height: 36px;
            background: rgba(255,255,255,0.2);
            color: #fff; border: none;
            border-radius: 10px; cursor: pointer;
            font-size: 1rem; transition: all 0.25s ease;
        }
        .modal-close:hover { background: rgba(239,68,68,0.9); transform: rotate(90deg); }
        .modal-body { padding: 1.8rem; }
        .form-row {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 1rem; margin-bottom: 1rem;
        }
        .form-row.single { grid-template-columns: 1fr; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label {
            font-size: 0.85rem; font-weight: 600;
            color: #0f172a; margin-bottom: 0.4rem;
        }
        .form-group label span { color: #ef4444; }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%; padding: 0.7rem 1rem;
            border: 2px solid #cbd5e1; border-radius: 10px;
            font-size: 0.92rem; font-family: inherit;
            background: #f8fafc; color: #1e293b;
            transition: all 0.25s ease;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none; border-color: #3b82f6;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(59,130,246,0.15);
        }
        .form-group textarea { resize: vertical; min-height: 90px; }
        .form-hint { font-size: 0.75rem; color: #94a3b8; margin-top: 0.3rem; }
        .img-preview {
            width: 100%; height: 140px;
            border: 2px dashed #cbd5e1;
            border-radius: 10px; display: flex;
            align-items: center; justify-content: center;
            background: #f8fafc; overflow: hidden;
            margin-top: 0.5rem;
        }
        .img-preview img { width: 100%; height: 100%; object-fit: cover; }
        .img-preview i { font-size: 2rem; color: #cbd5e1; }
        .modal-foot {
            padding: 1.2rem 1.8rem;
            border-top: 1px solid #e2e8f0;
            display: flex; justify-content: flex-end;
            gap: 0.7rem; position: sticky;
            bottom: 0; background: #fff;
        }
        .btn-cancel {
            padding: 0.7rem 1.4rem;
            background: #f1f5f9; color: #475569;
            border: none; border-radius: 10px;
            font-size: 0.9rem; font-weight: 600;
            cursor: pointer; transition: all 0.25s ease;
        }
        .btn-cancel:hover { background: #e2e8f0; }
        .btn-save {
            padding: 0.7rem 1.6rem;
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            color: #fff; border: none;
            border-radius: 10px; font-size: 0.9rem;
            font-weight: 600; cursor: pointer;
            transition: all 0.25s ease;
            box-shadow: 0 6px 15px -8px #1e3a8a;
        }
        .btn-save:hover { transform: translateY(-2px); box-shadow: 0 12px 22px -10px #2563eb; }
        @media (max-width: 900px) {
            .admin-main { margin-left: 0; }
            .menu-toggle { display: flex; align-items: center; justify-content: center; }
            .admin-content { padding: 1.5rem 1rem; }
        }
        @media (max-width: 600px) {
            .topbar-user span { display: none; }
            .projects-grid { grid-template-columns: 1fr; }
            .form-row { grid-template-columns: 1fr; }
            .modal-body { padding: 1.2rem; }
            .modal-head { padding: 1rem 1.2rem; }
            .modal-foot { padding: 1rem 1.2rem; }
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
                    <h1>Projects</h1>
                    <p>Manage all your portfolio projects</p>
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
                    <i class="fa-solid fa-circle-check"></i>
                    <?= htmlspecialchars($success_msg) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <?= htmlspecialchars($error_msg) ?>
                </div>
            <?php endif; ?>

            <div class="page-header">
                <h2>
                    <i class="fa-solid fa-briefcase"></i>
                    All Projects (<?= $total_projects ?>)
                </h2>
                <button class="btn-add" onclick="openModal()">
                    <i class="fa-solid fa-plus"></i>
                    Add New Project
                </button>
            </div>

            <div class="projects-grid">

                <?php if (empty($projects)): ?>
                    <div class="empty-state">
                        <i class="fa-solid fa-folder-open"></i>
                        <p>No projects yet. Click "Add New Project" to get started.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($projects as $p): ?>
                        <div class="project-card">
                            <div class="project-image" style="background: <?= htmlspecialchars($p['icon_color']) ?>;">
                                <?php if (!empty($p['image']) && file_exists('../' . $p['image'])): ?>
                                    <img src="../<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['title']) ?>">
                                <?php else: ?>
                                    <i class="<?= htmlspecialchars($p['icon']) ?>"></i>
                                <?php endif; ?>
                            </div>
                            <div class="project-body">
                                <span class="project-cat"><?= htmlspecialchars($p['category']) ?></span>
                                <h3 class="project-title"><?= htmlspecialchars($p['title']) ?></h3>
                                <p class="project-desc"><?= htmlspecialchars($p['description']) ?></p>
                                <div class="project-actions">
                                    <a href="#" class="btn-icon btn-edit"
                                       onclick='openEditModal(<?= json_encode($p, JSON_HEX_APOS | JSON_HEX_QUOT) ?>); return false;'>
                                        <i class="fa-solid fa-pen"></i> Edit
                                    </a>
                                    <a href="projects.php?delete=<?= $p['id'] ?>" 
                                       class="btn-icon btn-del"
                                       onclick="return confirm('Kya aap ye project delete karna chahte hain?');">
                                        <i class="fa-solid fa-trash"></i> Delete
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

            </div>
        </div>

    </main>

    <!-- Modal -->
    <div class="modal-overlay" id="projectModal">
        <div class="modal-box">

            <div class="modal-head">
                <h3 id="modalTitle">Add New Project</h3>
                <button class="modal-close" onclick="closeModal()" type="button">✕</button>
            </div>

            <form method="POST" action="projects.php" enctype="multipart/form-data" id="projectForm">

                <div class="modal-body">

                    <input type="hidden" name="id" id="p_id" value="0">

                    <div class="form-row single">
                        <div class="form-group">
                            <label>Project Title <span>*</span></label>
                            <input type="text" name="title" id="p_title" placeholder="e.g. Find Your Residence" required>
                        </div>
                    </div>

                    <div class="form-row single">
                        <div class="form-group">
                            <label>Description <span>*</span></label>
                            <textarea name="description" id="p_description" placeholder="Short description..." required></textarea>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Category</label>
                            <select name="category" id="p_category">
                                <option value="web">🌐 Web</option>
                                <option value="app">📱 App</option>
                                <option value="design">🎨 Design</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Display Order</label>
                            <input type="number" name="display_order" id="p_order" value="0" min="0">
                            <span class="form-hint">Chota number = upar</span>
                        </div>
                    </div>

                    <div class="form-row single">
                        <div class="form-group">
                            <label>Tech Stack</label>
                            <input type="text" name="tech_stack" id="p_tech" placeholder="HTML,PHP,MySQL">
                            <span class="form-hint">Comma se separate karein</span>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Live Demo Link</label>
                            <input type="url" name="live_link" id="p_live" placeholder="https://example.com">
                        </div>
                        <div class="form-group">
                            <label>Code Link</label>
                            <input type="url" name="code_link" id="p_code" placeholder="https://github.com/...">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Font Awesome Icon</label>
                            <input type="text" name="icon" id="p_icon" value="fa-solid fa-briefcase">
                            <span class="form-hint">Ex: <code>fa-solid fa-house</code></span>
                        </div>
                        <div class="form-group">
                            <label>Icon Background</label>
                            <select name="icon_color" id="p_color">
                                <option value="linear-gradient(135deg, #3b82f6, #8b5cf6)">🔵 Blue → Purple</option>
                                <option value="linear-gradient(135deg, #10b981, #059669)">🟢 Green</option>
                                <option value="linear-gradient(135deg, #f59e0b, #ef4444)">🟠 Orange → Red</option>
                                <option value="linear-gradient(135deg, #ec4899, #8b5cf6)">🩷 Pink → Purple</option>
                                <option value="linear-gradient(135deg, #6366f1, #3b82f6)">🟦 Indigo</option>
                                <option value="linear-gradient(135deg, #14b8a6, #06b6d4)">🩵 Teal</option>
                                <option value="linear-gradient(135deg, #1e3a8a, #3b82f6)">🔷 Navy</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row single">
                        <div class="form-group">
                            <label>Project Image (Optional)</label>
                            <input type="file" name="image" id="p_image" accept="image/*" onchange="previewImg(event)">
                            <div class="img-preview" id="imgPreview">
                                <i class="fa-solid fa-image"></i>
                            </div>
                            <span class="form-hint">Max 5MB. Image nahi doge toh icon dikhega.</span>
                        </div>
                    </div>

                </div>

                <div class="modal-foot">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn-save">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Save Project
                    </button>
                </div>

            </form>

        </div>
    </div>

    <script>
        const modal = document.getElementById('projectModal');
        const modalTitle = document.getElementById('modalTitle');

        function openModal() {
            document.getElementById('projectForm').reset();
            document.getElementById('p_id').value = 0;
            document.getElementById('p_icon').value = 'fa-solid fa-briefcase';
            document.getElementById('p_color').value = 'linear-gradient(135deg, #3b82f6, #8b5cf6)';
            document.getElementById('p_order').value = 0;
            modalTitle.innerText = 'Add New Project';
            resetPreview();
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function openEditModal(project) {
            document.getElementById('p_id').value           = project.id;
            document.getElementById('p_title').value        = project.title;
            document.getElementById('p_description').value  = project.description;
            document.getElementById('p_category').value     = project.category;
            document.getElementById('p_order').value        = project.display_order;
            document.getElementById('p_tech').value         = project.tech_stack;
            document.getElementById('p_live').value         = project.live_link || '';
            document.getElementById('p_code').value         = project.code_link || '';
            document.getElementById('p_icon').value         = project.icon;
            document.getElementById('p_color').value        = project.icon_color;

            modalTitle.innerText = 'Edit Project';

            resetPreview();
            if (project.image) {
                showPreview('../' + project.image);
            }

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

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeModal();
        });

        function previewImg(event) {
            const file = event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(e) { showPreview(e.target.result); };
            reader.readAsDataURL(file);
        }

        function showPreview(src) {
            document.getElementById('imgPreview').innerHTML = '<img src="' + src + '" alt="preview">';
        }

        function resetPreview() {
            document.getElementById('imgPreview').innerHTML = '<i class="fa-solid fa-image"></i>';
        }

        // Mobile sidebar
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