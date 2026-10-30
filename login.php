<?php
// login.php - Admin Login Page (MD5 + Password Visibility)
session_start();

// Agar already logged in hai toh dashboard par bhejo
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin/dashboard.php");
    exit();
}

$error = "";

// Form submit hone par
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'config/db.php';

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        $sql = "SELECT id, username, password FROM admin WHERE username = ? LIMIT 1";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            // ✅ MD5 Password Verify
            if (md5($password) === $row['password']) {
                $_SESSION['admin_logged_in']  = true;
                $_SESSION['admin_id']         = $row['id'];
                $_SESSION['admin_username']   = $row['username'];
                header("Location: admin/dashboard.php");
                exit();
            } else {
                $error = "Invalid username or password!";
            }
        } else {
            $error = "Invalid username or password!";
        }
        mysqli_stmt_close($stmt);
    } else {
        $error = "Please fill all fields!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login · Mrunal Hyalis</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Roboto, system-ui, sans-serif;
            background: linear-gradient(135deg, #1e3a8a, #3b82f6, #8b5cf6);
            background-size: 200% 200%;
            animation: gradientShift 10s ease infinite;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50%      { background-position: 100% 50%; }
        }

        .login-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 0 30px 60px -20px rgba(0, 0, 0, 0.4);
            padding: 2.5rem 2rem;
            width: 100%;
            max-width: 420px;
            animation: fadeInUp 0.7s ease;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .login-header { text-align: center; margin-bottom: 2rem; }

        .login-icon {
            width: 70px; height: 70px;
            margin: 0 auto 1rem auto;
            border-radius: 50%;
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            display: flex; align-items: center; justify-content: center;
            color: #ffffff; font-size: 1.8rem;
            box-shadow: 0 10px 25px -8px rgba(30, 58, 138, 0.6);
        }

        .login-header h1 {
            font-size: 1.6rem; color: #0f172a;
            margin-bottom: 0.3rem;
        }

        .login-header p {
            font-size: 0.9rem; color: #64748b; margin: 0;
        }

        .form-group { margin-bottom: 1.2rem; }

        .form-group label {
            display: block; font-weight: 600;
            color: #0f172a; margin-bottom: 0.4rem;
            font-size: 0.9rem;
        }

        .input-wrap { position: relative; }

        .input-wrap > i.field-icon {
            position: absolute; left: 1rem;
            top: 50%; transform: translateY(-50%);
            color: #94a3b8; font-size: 0.95rem;
            pointer-events: none;
        }

        .form-group input {
            width: 100%;
            padding: 0.85rem 3rem 0.85rem 2.7rem;
            border: 2px solid #cbd5e1;
            border-radius: 12px;
            font-size: 0.95rem; font-family: inherit;
            background: #f8fafc; color: #1e293b;
            transition: all 0.25s ease;
        }

        .form-group input:focus {
            outline: none; border-color: #3b82f6;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
        }

        /* ✅ Password Visibility Toggle (Eye Icon) */
        .toggle-password {
            position: absolute;
            right: 0.9rem;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            color: #94a3b8;
            font-size: 1rem;
            cursor: pointer;
            padding: 0.4rem;
            border-radius: 8px;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
        }

        .toggle-password:hover {
            color: #1e3a8a;
            background: #e0e7ff;
        }

        .btn-login {
            width: 100%; padding: 0.9rem;
            border: none; border-radius: 12px;
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            background-size: 200% 200%;
            color: #ffffff; font-size: 1rem;
            font-weight: 600; cursor: pointer;
            margin-top: 0.5rem;
            transition: all 0.3s ease;
            animation: gradientShift 5s ease infinite;
            display: flex; align-items: center;
            justify-content: center; gap: 0.5rem;
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 25px -10px #2563eb;
        }

        .alert-error {
            background: #fee2e2; color: #991b1b;
            padding: 0.85rem 1rem;
            border-radius: 10px;
            border-left: 5px solid #ef4444;
            font-size: 0.88rem; font-weight: 500;
            margin-bottom: 1.2rem;
            display: flex; align-items: center; gap: 0.5rem;
            animation: shake 0.4s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25%      { transform: translateX(-6px); }
            75%      { transform: translateX(6px); }
        }

        .back-link {
            text-align: center; margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px dashed #cbd5e1;
        }

        .back-link a {
            color: #3b82f6; text-decoration: none;
            font-size: 0.88rem; font-weight: 600;
            display: inline-flex; align-items: center;
            gap: 0.4rem; transition: all 0.25s ease;
        }

        .back-link a:hover {
            color: #1e3a8a; transform: translateX(-4px);
        }

        @media (max-width: 480px) {
            .login-card { padding: 2rem 1.5rem; }
            .login-header h1 { font-size: 1.35rem; }
            .login-icon { width: 60px; height: 60px; font-size: 1.5rem; }
        }
    </style>
</head>
<body>

    <div class="login-card">

        <div class="login-header">
            <div class="login-icon">
                <i class="fa-solid fa-lock"></i>
            </div>
            <h1>Admin Login</h1>
            <p>Sign in to manage your portfolio</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">

            <!-- Username -->
            <div class="form-group">
                <label for="username">Username</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-user field-icon"></i>
                    <input type="text" id="username" name="username"
                           placeholder="Enter username" required autofocus>
                </div>
            </div>

            <!-- Password with Eye Toggle -->
            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-key field-icon"></i>
                    <input type="password" id="password" name="password"
                           placeholder="Enter password" required>
                    <button type="button" class="toggle-password" id="togglePassword" 
                            aria-label="Toggle password visibility">
                        <i class="fa-solid fa-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-login">
                <i class="fa-solid fa-right-to-bracket"></i>
                Sign In
            </button>

        </form>

        <div class="back-link">
            <a href="index.php">
                <i class="fa-solid fa-arrow-left"></i>
                Back to Portfolio
            </a>
        </div>

    </div>

    <!-- ✅ JS for Password Visibility Toggle -->
    <script>
        (function () {
            const toggleBtn = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            toggleBtn.addEventListener('click', function () {
                const isPassword = passwordInput.type === 'password';

                // Toggle type
                passwordInput.type = isPassword ? 'text' : 'password';

                // Toggle icon
                if (isPassword) {
                    eyeIcon.classList.remove('fa-eye');
                    eyeIcon.classList.add('fa-eye-slash');
                } else {
                    eyeIcon.classList.remove('fa-eye-slash');
                    eyeIcon.classList.add('fa-eye');
                }

                // Keep focus on input
                passwordInput.focus();
            });
        })();
    </script>

</body>
</html>