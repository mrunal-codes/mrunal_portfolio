<?php
// contact.php - Contact Me Page (NO hamburger)
include 'config/db.php';
include 'include/header.php';
$msg_sent = false;
$error_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name']);
    $email   = trim($_POST['email']);
    $phone   = trim($_POST['phone']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    if (!empty($name) && !empty($email) && !empty($phone) && !empty($message)) {
        $sql = "INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssss", $name, $email, $phone, $subject, $message);

        if (mysqli_stmt_execute($stmt)) {
            $msg_sent = true;
        } else {
            $error_msg = "Error: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        $error_msg = "Please fill all required fields!";
    }
}


?>

    <!-- Profile Section -->
    <div class="contact-profile">
        <img src="images/Profile.jpg" alt="Mrunal Hyalis" class="contact-photo">
        <h2 class="contact-name">Mrunal Hyalis</h2>
        <p class="contact-role">Web Developer | Designer</p>
    </div>

    <!-- Contact Heading -->
    <div class="contact-heading">
        <h1>Contact Me</h1>
        <p>Let's get in touch</p>
    </div>

    <!-- Grid: Details + Form -->
    <div class="contact-grid">

        <div class="contact-details">
            <h3 class="box-title">Contact Details</h3>

            <div class="detail-item">
                <span class="detail-label">Email</span>
                <span class="detail-value">hyalismrunal306@gmail.com</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Mobile</span>
                <span class="detail-value">7038501758</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Location</span>
                <span class="detail-value">Malegaon</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Qualification</span>
                <span class="detail-value">B.Sc. Computer Science</span>
            </div>
        </div>

        <div class="contact-form-wrapper">
            <h3 class="box-title">Send Me a Message</h3>

            <?php if ($msg_sent): ?>
                <div class="alert alert-success">✅ Thank you! Your message has been received.</div>
            <?php endif; ?>

            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-error">❌ <?php echo $error_msg; ?></div>
            <?php endif; ?>

            <form method="POST" action="contact.php" class="contact-form">
                <div class="form-group">
                    <label for="name">Your Name</label>
                    <input type="text" id="name" name="name" placeholder="Enter your name" required>
                </div>
                <div class="form-group">
                    <label for="email">Your Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" placeholder="10-digit number" 
                           pattern="[0-9]{10}" maxlength="10" required>
                </div>
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" placeholder="Enter subject" required>
                </div>
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" rows="4" 
                              placeholder="Write your message here..." required></textarea>
                </div>
                <button type="submit" class="submit-btn">Send Message →</button>
            </form>
        </div>

    </div>

    <!-- Page Styles -->
    <style>
        /* Hide hamburger + drawer ONLY on this page */
       

        /* Profile */
        .contact-profile {
            text-align: center;
            margin: 1.5rem 0 2rem 0;
        }
        .contact-photo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            object-position: top;
            border: 4px solid #3b82f6;
            box-shadow: 0 8px 20px -8px rgba(59, 130, 246, 0.5);
            transition: transform 0.3s ease;
        }
        .contact-photo:hover { transform: scale(1.05); }
        .contact-name {
            margin: 0.9rem 0 0.2rem 0;
            font-size: 1.4rem;
            color: #0f172a;
            border: none;
            display: block;
            font-weight: 700;
        }
        .contact-role {
            color: #64748b;
            font-size: 0.92rem;
            margin: 0;
        }

        /* Heading */
        .contact-heading { text-align: center; margin-bottom: 2rem; }
        .contact-heading h1 { margin-bottom: 0.3rem; }
        .contact-heading p { color: #475569; font-size: 1rem; margin: 0; }

        /* Grid */
        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            gap: 2rem;
            margin-top: 1.5rem;
            align-items: start;
        }

        .box-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 1.2rem 0;
            padding-bottom: 0.6rem;
            border-bottom: 2px solid #3b82f6;
            display: inline-block;
        }

        /* Details */
        .contact-details {
            background: #ffffffd6;
            padding: 1.5rem 1.6rem;
            border-radius: 1.5rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 8px 20px -10px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .contact-details:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 30px -15px rgba(59, 130, 246, 0.2);
        }
        .detail-item {
            display: flex;
            flex-direction: column;
            padding: 0.85rem 0;
            border-bottom: 1px dashed #e2e8f0;
        }
        .detail-item:last-child { border-bottom: none; }
        .detail-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: #3b82f6;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            margin-bottom: 0.25rem;
        }
        .detail-value {
            font-size: 0.98rem;
            color: #1e293b;
            font-weight: 500;
            word-break: break-word;
        }

        /* Form */
        .contact-form-wrapper {
            background: #ffffffd6;
            padding: 1.5rem 1.6rem;
            border-radius: 1.5rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 8px 20px -10px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .contact-form-wrapper:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 30px -15px rgba(59, 130, 246, 0.2);
        }

        .form-group { margin-bottom: 1rem; }
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.4rem;
            color: #0f172a;
            font-size: 0.9rem;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.7rem 1rem;
            border: 2px solid #cbd5e1;
            border-radius: 10px;
            font-size: 0.95rem;
            font-family: inherit;
            transition: all 0.25s ease;
            background: #f8fafc;
            color: #1e293b;
        }
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #3b82f6;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
            transform: translateY(-2px);
        }
        .form-group textarea { resize: vertical; min-height: 90px; }

        .submit-btn {
            width: 100%;
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            background-size: 200% 200%;
            color: white;
            border: none;
            padding: 0.85rem 1.5rem;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 0.5rem;
            animation: gradientShift 5s ease infinite;
        }
        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 25px -10px #2563eb;
        }

        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50%      { background-position: 100% 50%; }
        }

        /* Alerts */
        .alert {
            padding: 0.85rem 1.2rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            font-size: 0.92rem;
            font-weight: 500;
        }
        .alert-success {
            background: #dcfce7;
            color: #166534;
            border-left: 5px solid #22c55e;
        }
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border-left: 5px solid #ef4444;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .contact-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            .contact-photo { width: 105px; height: 105px; }
            .contact-name { font-size: 1.25rem; }
            .contact-details,
            .contact-form-wrapper { padding: 1.3rem 1.2rem; }
        }

        @media (max-width: 480px) {
            .contact-photo { width: 95px; height: 95px; border-width: 3px; }
            .contact-name { font-size: 1.15rem; }
            .contact-role { font-size: 0.85rem; }
            .box-title { font-size: 1.05rem; }
            .detail-value { font-size: 0.92rem; }
            .form-group input,
            .form-group textarea { padding: 0.65rem 0.9rem; font-size: 0.9rem; }
            .submit-btn { padding: 0.75rem 1.2rem; font-size: 0.95rem; }
        }
    </style>

<?php include 'include/footer.php'; ?>