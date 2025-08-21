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
$username = '';
$error_message = '';

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $username = sanitize_input($_POST['username']);
    $password = $_POST['password']; // Don't sanitize password
    
    // Validate inputs
    if (empty($username) || empty($password)) {
        $error_message = 'Please enter both username and password.';
    } else {
        try {
            // Check if user exists
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                // Password is correct, create session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];
                
                // Redirect based on role
                if ($user['role'] === 'admin' || $user['role'] === 'staff') {
                    header('Location: admin/index.php');
                } else {
                    header('Location: dashboard.php');
                }
                exit;
            } else {
                $error_message = 'Invalid username or password.';
            }
        } catch(PDOException $e) {
            $error_message = 'Sorry, there was an error processing your request. Please try again later.';
            // Log error
            error_log("Error during login: " . $e->getMessage());
        }
    }
}

// Include header
include 'includes/header.php';
?>

<!-- Login Page -->
<section class="section">
    <div class="container">
        <div class="auth-container">
            <div class="auth-header">
                <h2>Login to Your Account</h2>
                <p>Enter your credentials to access your account</p>
            </div>
            
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger">
                    <div class="alert-content">
                        <span><?php echo $error_message; ?></span>
                        <button type="button" class="close-btn">&times;</button>
                    </div>
                </div>
            <?php endif; ?>
            
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" class="auth-form login-form">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($username); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-input-container">
                        <input type="password" id="password" name="password" class="form-control" required>
                        <span class="password-toggle" onclick="togglePasswordVisibility('password', 'password-toggle-icon')">
                            <i id="password-toggle-icon" class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>
                
                <div class="form-group d-flex justify-content-between align-items-center">
                    <div class="form-check">
                        <input type="checkbox" id="remember" name="remember" class="form-check-input">
                        <label for="remember" class="form-check-label">Remember me</label>
                    </div>
                    <a href="forgot-password.php">Forgot Password?</a>
                </div>
                
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
            
            <div class="auth-footer">
                <p>Don't have an account? <a href="register.php">Register Now</a></p>
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
include 'includes/footer.php';
?>