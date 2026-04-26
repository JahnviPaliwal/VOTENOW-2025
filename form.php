<?php
// ============================================================
// form.php - User Details Form
// Collects name, department, voter ID after OTP verification
// ============================================================
session_start();
require_once 'db.php';

// Must have verified OTP
if (!isset($_SESSION['otp_verified']) || !$_SESSION['otp_verified']) {
    header("Location: login.php");
    exit();
}

// If already logged in, skip form
if (isset($_SESSION['user_id'])) {
    header("Location: vote.php");
    exit();
}

$error   = '';
$success = '';
$phone   = $_SESSION['login_phone'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $name       = trim($_POST['name']);
    $department = trim($_POST['department']);
    $voter_id   = trim($_POST['voter_id']);

    // Basic validation
    if (empty($name) || empty($department) || empty($voter_id)) {
        $error = "All fields are required.";
    } elseif (strlen($name) < 3) {
        $error = "Please enter a valid full name.";
    } elseif (strlen($voter_id) < 4) {
        $error = "Voter ID must be at least 4 characters.";
    } else {
        // Check if this phone number is already registered
        $stmt = mysqli_prepare($conn, "SELECT id, has_voted FROM users WHERE phone = ?");
        mysqli_stmt_bind_param($stmt, "s", $phone);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            // User already registered — log them in
            $row = mysqli_fetch_assoc($result);
            $_SESSION['user_id']   = $row['id'];
            $_SESSION['user_name'] = $name;
            $_SESSION['has_voted'] = $row['has_voted'];
            header("Location: vote.php");
            exit();
        } else {
            // Check if voter_id already used
            $stmt2 = mysqli_prepare($conn, "SELECT id FROM users WHERE voter_id = ?");
            mysqli_stmt_bind_param($stmt2, "s", $voter_id);
            mysqli_stmt_execute($stmt2);
            $result2 = mysqli_stmt_get_result($stmt2);

            if (mysqli_num_rows($result2) > 0) {
                $error = "This Voter ID is already registered. Please check your Voter ID.";
            } else {
                // Insert new user into database
                $stmt3 = mysqli_prepare($conn, "INSERT INTO users (name, phone, voter_id, department) VALUES (?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt3, "ssss", $name, $phone, $voter_id, $department);

                if (mysqli_stmt_execute($stmt3)) {
                    $new_id = mysqli_insert_id($conn);
                    $_SESSION['user_id']   = $new_id;
                    $_SESSION['user_name'] = $name;
                    $_SESSION['has_voted'] = 0;
                    header("Location: vote.php");
                    exit();
                } else {
                    $error = "Registration failed. Please try again.";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voter Details – VoteNow 2025</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="card">
    <!-- Title -->
    <div style="text-align:center; margin-bottom:20px;">
        <div style="font-size:42px;">📋</div>
        <div class="card-title">Voter Registration</div>
        <div class="card-subtitle">Please fill in your details to continue</div>
    </div>

    <!-- Success: OTP verified banner -->
    <div class="alert alert-success">
        ✅ OTP Verified! Phone: <strong><?php echo htmlspecialchars($phone); ?></strong>
    </div>

    <!-- Error Message -->
    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- Details Form -->
    <form method="POST" action="form.php">

        <!-- Full Name -->
        <div class="form-group">
            <label for="name">👤 Full Name</label>
            <input type="text" id="name" name="name" placeholder="Enter your full name"
                required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
        </div>

        <!-- Department -->
        <div class="form-group">
            <label for="department">🏛️ Department</label>
            <select id="department" name="department" required>
                <option value="">-- Select Department --</option>
                <option value="Computer Science" <?php echo (isset($_POST['department']) && $_POST['department'] == 'Computer Science') ? 'selected' : ''; ?>>Computer Science</option>
                <option value="Electronics Engineering" <?php echo (isset($_POST['department']) && $_POST['department'] == 'Electronics Engineering') ? 'selected' : ''; ?>>Electronics Engineering</option>
                <option value="Mechanical Engineering" <?php echo (isset($_POST['department']) && $_POST['department'] == 'Mechanical Engineering') ? 'selected' : ''; ?>>Mechanical Engineering</option>
                <option value="Civil Engineering" <?php echo (isset($_POST['department']) && $_POST['department'] == 'Civil Engineering') ? 'selected' : ''; ?>>Civil Engineering</option>
                <option value="Information Technology" <?php echo (isset($_POST['department']) && $_POST['department'] == 'Information Technology') ? 'selected' : ''; ?>>Information Technology</option>
                <option value="Electrical Engineering" <?php echo (isset($_POST['department']) && $_POST['department'] == 'Electrical Engineering') ? 'selected' : ''; ?>>Electrical Engineering</option>
                <option value="Chemical Engineering" <?php echo (isset($_POST['department']) && $_POST['department'] == 'Chemical Engineering') ? 'selected' : ''; ?>>Chemical Engineering</option>
                <option value="Other" <?php echo (isset($_POST['department']) && $_POST['department'] == 'Other') ? 'selected' : ''; ?>>Other</option>
            </select>
        </div>

        <!-- Voter ID -->
        <div class="form-group">
            <label for="voter_id">🪪 Voter ID / Enrollment No.</label>
            <input type="text" id="voter_id" name="voter_id" placeholder="e.g., CS2021001"
                required value="<?php echo isset($_POST['voter_id']) ? htmlspecialchars($_POST['voter_id']) : ''; ?>">
        </div>

        <button type="submit" class="btn btn-primary">Register & Proceed to Vote →</button>
    </form>
</div>

<script src="script.js"></script>
</body>
</html>
