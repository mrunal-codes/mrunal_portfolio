<?php
// admin/about_info.php
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
// UPDATE About Info
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_about'])) {
    $full_name = trim($_POST['full_name'] ?? '');
    $role      = trim($_POST['role'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $location  = trim($_POST['location'] ?? '');
    $bio       = trim($_POST['bio'] ?? '');

    $sql = "UPDATE about_info SET full_name=?, role=?, email=?, phone=?, location=?, bio=? WHERE id=1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssssss", $full_name, $role, $email, $phone, $location, $bio);
    
    if (mysqli_stmt_execute($stmt)) {
        $success_msg = "About Info updated successfully!";
    } else {
        $error_msg = "Failed to update: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
}

// ============================================
// Fetch Current Data
// ============================================
$about = [
    'full_name' => '', 'role' => '', 'email' => '', 'phone' => '', 'location' => '', 'bio' => ''
];

$res = mysqli_query($conn, "SELECT * FROM about_info WHERE id = 1");
if ($res && mysqli_num_rows($res) > 0) {
    $about = mysqli_fetch_assoc($res);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Info · Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Roboto, sans-serif; background: #f1f5f9; color: #1e293b; line-height: 1.6; }
        .admin-main { margin-left: 260px; padding: 2rem; min-height: 100vh; }
        
        .page-header { margin-bottom: 2rem; }
        .page-header h1 { font-size: 1.5rem; color: #0f172a; }
        .page-header p { color: #64748b; font-size: 0.9rem; }

        .alert { padding: 1rem; border-radius: 10px; margin-bottom: 1.5rem; font-weight: 500; }
        .alert-success { background: #dcfce7; color: #166534; border-left: 5px solid #22c55e; }
        .alert-error { background: #fee2e2; color: #991b1b; border-left: 5px solid #ef4444; }

        .form-card { background: #fff; padding: 2rem; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); max-width: 800px; }
        
        .form-group { margin-bottom: 1.2rem; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem; color: #1e293b; }
        .form-group input, .form-group textarea { width: 100%; padding: 0.8rem; border: 1px solid #cbd5e1; border-radius: 8px; font-family: inherit; font-size: 0.95rem; }
        .form-group input:focus, .form-group textarea:focus { outline: none; border-color: #3b82f6; }
        
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        
        .btn-save { background: #1e3a8a; color: #fff; padding: 0.8rem 1.5rem; border: none; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: 0.3s; }
        .btn-save:hover { background: #3b82f6; }
        
        @media(max-width: 900px) { .admin-main { margin-left: 0; } .form-row { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <main class="admin-main">
        <div class="page-header">
            <h1>About Info</h1>
            <p>Manage your personal information here.</p>
        </div>

        <?php if (!empty($success_msg)): ?>
            <div class="alert alert-success"><i class="fa-solid fa-check"></i> <?= htmlspecialchars($success_msg) ?></div>
        <?php endif; ?>
        <?php if (!empty($error_msg)): ?>
            <div class="alert alert-error"><i class="fa-solid fa-exclamation"></i> <?= htmlspecialchars($error_msg) ?></div>
        <?php endif; ?>

        <div class="form-card">
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label>Full Name</label>
                        <!-- Safe variable use kiya hai taaki error na aaye -->
                        <input type="text" name="full_name" value="<?= htmlspecialchars($about['full_name'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Role / Designation</label>
                        <input type="text" name="role" value="<?= htmlspecialchars($about['role'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($about['email'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone" value="<?= htmlspecialchars($about['phone'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Location</label>
                    <input type="text" name="location" value="<?= htmlspecialchars($about['location'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label>Short Bio</label>
                    <textarea name="bio" rows="5"><?= htmlspecialchars($about['bio'] ?? '') ?></textarea>
                </div>

                <button type="submit" name="update_about" class="btn-save">
                    <i class="fa-solid fa-save"></i> Save Changes
                </button>
            </form>
        </div>
    </main>

</body>
</html>