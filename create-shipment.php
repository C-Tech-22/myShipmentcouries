<?php
// Include database connection
require_once 'includes/db_connect.php';

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get user information
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['full_name'];
$user_role = $_SESSION['role'];

// Get user email
try {
    $stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    $user_email = $user['email'];
} catch(PDOException $e) {
    $user_email = '';
    // Log error
    error_log("Error fetching user email: " . $e->getMessage());
}

// Initialize variables
$success_message = $error_message = '';
$tracking_number = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Generate tracking number
    $tracking_number = generate_tracking_number();
    
    // Sanitize inputs
    $sender_name = sanitize_input($_POST['sender_name']);
    $sender_address = sanitize_input($_POST['sender_address']);
    $sender_phone = sanitize_input($_POST['sender_phone']);
    $sender_email = sanitize_input($_POST['sender_email']);
    $recipient_name = sanitize_input($_POST['recipient_name']);
    $recipient_address = sanitize_input($_POST['recipient_address']);
    $recipient_phone = sanitize_input($_POST['recipient_phone']);
    $recipient_email = sanitize_input($_POST['recipient_email']);
    $package_type = sanitize_input($_POST['package_type']);
    $weight = sanitize_input($_POST['weight']);
    $dimensions = sanitize_input($_POST['dimensions']);
    $service_type = sanitize_input($_POST['service_type']);
    $shipping_cost = sanitize_input($_POST['shipping_cost']);
    
    // Calculate estimated delivery date based on service type
    $today = new DateTime();
    $estimated_delivery_date = null;
    
    switch ($service_type) {
        case 'express':
            $estimated_delivery_date = $today->add(new DateInterval('P1D')); // 1 day
            break;
        case 'standard':
            $estimated_delivery_date = $today->add(new DateInterval('P3D')); // 3 days
            break;
        case 'international':
            $estimated_delivery_date = $today->add(new DateInterval('P7D')); // 7 days
            break;
        case 'freight':
            $estimated_delivery_date = $today->add(new DateInterval('P5D')); // 5 days
            break;
        default:
            $estimated_delivery_date = $today->add(new DateInterval('P3D')); // Default 3 days
    }
    
    // Format estimated delivery date
    $estimated_delivery_date = $estimated_delivery_date->format('Y-m-d');
    
    // Validate inputs
    if (empty($sender_name) || empty($sender_address) || empty($sender_phone) || 
        empty($recipient_name) || empty($recipient_address) || empty($recipient_phone) || 
        empty($package_type) || empty($weight) || empty($service_type) || empty($shipping_cost)) {
        $error_message = 'Please fill in all required fields.';
    } elseif (!empty($sender_email) && !filter_var($sender_email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid sender email address.';
    } elseif (!empty($recipient_email) && !filter_var($recipient_email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid recipient email address.';
    } elseif (!is_numeric($weight) || $weight <= 0) {
        $error_message = 'Please enter a valid weight.';
    } elseif (!is_numeric($shipping_cost) || $shipping_cost <= 0) {
        $error_message = 'Please enter a valid shipping cost.';
    } else {
        try {
            // Insert shipment into database
            $stmt = $pdo->prepare("INSERT INTO shipments (tracking_number, sender_name, sender_address, sender_phone, sender_email, 
                                recipient_name, recipient_address, recipient_phone, recipient_email, package_type, weight, 
                                dimensions, service_type, shipping_cost, status, estimated_delivery_date) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?)");
            
            $stmt->execute([
                $tracking_number, $sender_name, $sender_address, $sender_phone, $sender_email,
                $recipient_name, $recipient_address, $recipient_phone, $recipient_email, $package_type,
                $weight, $dimensions, $service_type, $shipping_cost, $estimated_delivery_date
            ]);
            
            $shipment_id = $pdo->lastInsertId();
            
            // Add initial tracking update
            $stmt = $pdo->prepare("INSERT INTO tracking_updates (shipment_id, status, location, description) 
                                VALUES (?, 'Shipment Created', ?, 'Your shipment has been registered in our system.')");
            $stmt->execute([$shipment_id, $sender_address]);
            
            $success_message = 'Shipment created successfully! Your tracking number is: ' . $tracking_number;
        } catch(PDOException $e) {
            $error_message = 'Sorry, there was an error creating your shipment. Please try again later.';
            // Log error
            error_log("Error creating shipment: " . $e->getMessage());
        }
    }
}

// Include header
include 'includes/header.php';
?>

<!-- Dashboard Content -->
<div class="dashboard-container">
    <!-- Sidebar -->
    <div class="dashboard-sidebar">
        <div class="dashboard-sidebar-header">
            <h3><i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($user_name); ?></h3>
        </div>
        
        <nav class="dashboard-nav">
            <ul>
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="create-shipment.php" class="active"><i class="fas fa-box"></i> Create Shipment</a></li>
                <li><a href="my-shipments.php"><i class="fas fa-shipping-fast"></i> My Shipments</a></li>
                <li><a href="track.php"><i class="fas fa-search-location"></i> Track Shipment</a></li>
                <li><a href="profile.php"><i class="fas fa-user"></i> My Profile</a></li>
                <li><a href="support.php"><i class="fas fa-headset"></i> Support</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </div>
    
    <!-- Main Content -->
    <div class="dashboard-main">
        <div class="dashboard-header">
            <h2>Create New Shipment</h2>
        </div>
        
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success">
                <div class="alert-content">
                    <span><?php echo $success_message; ?></span>
                    <button type="button" class="close-btn">&times;</button>
                </div>
                <div class="mt-3">
                    <a href="track.php?tracking_number=<?php echo urlencode($tracking_number); ?>" class="btn btn-primary">Track Shipment</a>
                    <a href="create-shipment.php" class="btn btn-outline">Create Another Shipment</a>
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
        
        <?php if (empty($success_message)): ?>
            <div class="contact-form shipment-form">
                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                    <div class="section-title">
                        <h3>Sender Information</h3>
                    </div>
                    
                    <div class="form-group">
                        <label for="sender_name">Full Name *</label>
                        <input type="text" id="sender_name" name="sender_name" class="form-control" value="<?php echo htmlspecialchars($user_name); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="sender_address">Address *</label>
                        <textarea id="sender_address" name="sender_address" class="form-control" rows="3" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="sender_phone">Phone Number *</label>
                        <input type="tel" id="sender_phone" name="sender_phone" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="sender_email">Email Address</label>
                        <input type="email" id="sender_email" name="sender_email" class="form-control" value="<?php echo htmlspecialchars($user_email); ?>">
                    </div>
                    
                    <div class="section-title mt-4">
                        <h3>Recipient Information</h3>
                    </div>
                    
                    <div class="form-group">
                        <label for="recipient_name">Full Name *</label>
                        <input type="text" id="recipient_name" name="recipient_name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="recipient_address">Address *</label>
                        <textarea id="recipient_address" name="recipient_address" class="form-control" rows="3" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="recipient_phone">Phone Number *</label>
                        <input type="tel" id="recipient_phone" name="recipient_phone" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="recipient_email">Email Address</label>
                        <input type="email" id="recipient_email" name="recipient_email" class="form-control">
                    </div>
                    
                    <div class="section-title mt-4">
                        <h3>Package Details</h3>
                    </div>
                    
                    <div class="form-group">
                        <label for="package_type">Package Type *</label>
                        <select id="package_type" name="package_type" class="form-control" required>
                            <option value="">Select Package Type</option>
                            <option value="document">Document</option>
                            <option value="parcel">Parcel</option>
                            <option value="large_package">Large Package</option>
                            <option value="fragile">Fragile</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="weight">Weight (kg) *</label>
                        <input type="number" id="weight" name="weight" class="form-control" step="0.01" min="0.01" required onchange="calculateShipping()">
                    </div>
                    
                    <div class="form-group">
                        <label for="dimensions">Dimensions (L x W x H cm)</label>
                        <input type="text" id="dimensions" name="dimensions" class="form-control" placeholder="e.g., 30 x 20 x 10">
                    </div>
                    
                    <div class="form-group">
                        <label for="service_type">Service Type *</label>
                        <select id="service_type" name="service_type" class="form-control" required onchange="calculateShipping()">
                            <option value="">Select Service Type</option>
                            <option value="express">Express Delivery (1-2 days)</option>
                            <option value="standard">Standard Shipping (2-3 days)</option>
                            <option value="international">International Shipping (5-7 days)</option>
                            <option value="freight">Freight Services (3-5 days)</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="shipping_cost">Shipping Cost ($) *</label>
                        <input type="number" id="shipping_cost" name="shipping_cost" class="form-control" step="0.01" min="0.01" readonly required>
                    </div>
                    
                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">Create Shipment</button>
                        <a href="dashboard.php" class="btn btn-outline">Cancel</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Calculate shipping cost based on weight and service type
function calculateShipping() {
    const weight = parseFloat(document.getElementById('weight').value) || 0;
    const serviceType = document.getElementById('service_type').value;
    const packageType = document.getElementById('package_type').value;
    
    let baseCost = 0;
    
    // Base cost by service type
    switch (serviceType) {
        case 'express':
            baseCost = 25;
            break;
        case 'standard':
            baseCost = 15;
            break;
        case 'international':
            baseCost = 50;
            break;
        case 'freight':
            baseCost = 100;
            break;
        default:
            baseCost = 15;
    }
    
    // Adjust by package type
    switch (packageType) {
        case 'document':
            baseCost *= 0.8;
            break;
        case 'parcel':
            // No adjustment for standard parcel
            break;
        case 'large_package':
            baseCost *= 1.5;
            break;
        case 'fragile':
            baseCost *= 1.3;
            break;
        default:
            // No adjustment
    }
    
    // Adjust by weight
    const costWithWeight = baseCost + (weight * 2);
    
    // Update the shipping cost field
    const shippingCostField = document.getElementById('shipping_cost');
    if (shippingCostField) {
        shippingCostField.value = costWithWeight.toFixed(2);
    }
    
    return costWithWeight;
}

// Initialize shipping cost calculation when form loads
document.addEventListener('DOMContentLoaded', function() {
    // Set initial shipping cost if weight and service type are already selected
    if (document.getElementById('weight').value && document.getElementById('service_type').value) {
        calculateShipping();
    }
    
    // Add event listeners for package type changes
    document.getElementById('package_type').addEventListener('change', calculateShipping);
});
</script>

<?php
// Include footer (with modified structure for dashboard)
?>
</body>
</html>