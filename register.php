<?php
// Include database connection
require_once 'includes/db_connect.php';

// Start session
session_start();

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

// Initialize variables
$full_name = $email = $username = '';
$success_message = $error_message = '';

// define sanitize input
// function sanitize_input($data) {
//     $data = trim($data);            // remove extra spaces
//     $data = stripslashes($data);    // remove backslashes
//     $data = htmlspecialchars($data);// convert special chars to HTML
//     return $data;
// }
// Process registration form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $full_name = sanitize_input($_POST['full_name']);
    $email = sanitize_input($_POST['email']);
    $username = sanitize_input($_POST['username']);
    $password = $_POST['password']; // Don't sanitize password
    $confirm_password = $_POST['confirm_password']; // Don't sanitize password
    
    // Validate inputs
    if (empty($full_name) || empty($email) || empty($username) || empty($password) || empty($confirm_password)) {
        $error_message = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    } elseif (strlen($username) < 4) {
        $error_message = 'Username must be at least 4 characters long.';
    } elseif (strlen($password) < 6) {
        $error_message = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirm_password) {
        $error_message = 'Passwords do not match.';
    } else {
        try {
            // Check if username or email already exists
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            $existing_user = $stmt->fetch();
            
            if ($existing_user) {
                if ($existing_user['username'] === $username) {
                    $error_message = 'Username already exists. Please choose a different username.';
                } else {
                    $error_message = 'Email already exists. Please use a different email address.';
                }
            } else {
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert new user
                $stmt = $pdo->prepare("INSERT INTO users (full_name, email, username, password, role) VALUES (?, ?, ?, ?, 'customer')");
                $stmt->execute([$full_name, $email, $username, $hashed_password]);
                
                $success_message = 'Registration successful! You can now login with your credentials.';
                
                // Clear form fields after successful registration
                $full_name = $email = $username = '';
            }
        } catch(PDOException $e) {
            $error_message = 'Sorry, there was an error processing your registration. Please try again later.';
            // Log error
            error_log("Error during registration: " . $e->getMessage());
        }
    }
}

// Include header
include 'includes/header.php';
?>

<!-- Register Page -->
<section class="section">
    <div class="container">
        <div class="auth-container">
            <div class="auth-header">
                <h2>Create an Account</h2>
                <p>Register to track your shipments and manage your deliveries</p>
            </div>
            
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success">
                    <div class="alert-content">
                        <span><?php echo $success_message; ?></span>
                        <button type="button" class="close-btn">&times;</button>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger">
                    <div class="alert-content">
                        <span><?php echo $error_message; ?></span>
                        <button type="button" class="close-btn">&times;</button>
                    </div>
                </div>
            <?php endif; ?>
            
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" class="auth-form register-form">
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" class="form-control" value="<?php echo htmlspecialchars($full_name); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($username); ?>" required>
                    <small class="form-text text-muted">Username must be at least 4 characters long.</small>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-input-container">
                        <input type="password" id="password" name="password" class="form-control" required>
                        <span class="password-toggle" onclick="togglePasswordVisibility('password', 'password-toggle-icon')">
                            <i id="password-toggle-icon" class="fas fa-eye"></i>
                        </span>
                    </div>
                    <small class="form-text text-muted">Password must be at least 6 characters long.</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <div class="password-input-container">
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                        <span class="password-toggle" onclick="togglePasswordVisibility('confirm_password', 'confirm-password-toggle-icon')">
                            <i id="confirm-password-toggle-icon" class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" id="terms" name="terms" class="form-check-input" required>
                        <label for="terms" class="form-check-label">I agree to the <a href="terms-conditions.php">Terms & Conditions</a> and <a href="privacy-policy.php">Privacy Policy</a></label>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">Register</button>
            </form>
            
            <div class="auth-footer">
                <p>Already have an account? <a href="login.php">Login</a></p>
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
include 'includes/footer.php';
?>