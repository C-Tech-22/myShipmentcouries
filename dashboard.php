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

// Get user's shipments
try {
    $stmt = $pdo->prepare("SELECT * FROM shipments WHERE sender_email = (SELECT email FROM users WHERE id = ?) ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    $shipments = $stmt->fetchAll();
} catch(PDOException $e) {
    $shipments = [];
    // Log error
    error_log("Error fetching shipments: " . $e->getMessage());
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
                <li><a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="create-shipment.php"><i class="fas fa-box"></i> Create Shipment</a></li>
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
            <h2>Dashboard</h2>
            <a href="create-shipment.php" class="btn btn-primary"><i class="fas fa-plus"></i> New Shipment</a>
        </div>
        
        <!-- Dashboard Cards -->
        <div class="dashboard-cards">
            <div class="dashboard-card">
                <div class="dashboard-card-icon icon-primary">
                    <i class="fas fa-box"></i>
                </div>
                <div class="dashboard-card-content">
                    <h3><?php echo count($shipments); ?></h3>
                    <p>Total Shipments</p>
                </div>
            </div>
            
            <div class="dashboard-card">
                <div class="dashboard-card-icon icon-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="dashboard-card-content">
                    <h3>
                        <?php 
                            $delivered = 0;
                            foreach ($shipments as $shipment) {
                                if ($shipment['status'] === 'delivered') {
                                    $delivered++;
                                }
                            }
                            echo $delivered;
                        ?>
                    </h3>
                    <p>Delivered</p>
                </div>
            </div>
            
            <div class="dashboard-card">
                <div class="dashboard-card-icon icon-warning">
                    <i class="fas fa-shipping-fast"></i>
                </div>
                <div class="dashboard-card-content">
                    <h3>
                        <?php 
                            $in_transit = 0;
                            foreach ($shipments as $shipment) {
                                if ($shipment['status'] === 'in_transit') {
                                    $in_transit++;
                                }
                            }
                            echo $in_transit;
                        ?>
                    </h3>
                    <p>In Transit</p>
                </div>
            </div>
            
            <div class="dashboard-card">
                <div class="dashboard-card-icon icon-info">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="dashboard-card-content">
                    <h3>
                        <?php 
                            $pending = 0;
                            foreach ($shipments as $shipment) {
                                if ($shipment['status'] === 'pending') {
                                    $pending++;
                                }
                            }
                            echo $pending;
                        ?>
                    </h3>
                    <p>Pending</p>
                </div>
            </div>
        </div>
        
        <!-- Recent Shipments -->
        <div class="dashboard-table">
            <div class="dashboard-table-header">
                <h3>Recent Shipments</h3>
                <a href="my-shipments.php" class="btn btn-outline btn-sm">View All</a>
            </div>
            
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Tracking Number</th>
                            <th>Recipient</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($shipments)): ?>
                            <?php 
                            $count = 0;
                            foreach($shipments as $shipment): 
                                if ($count >= 5) break; // Show only 5 recent shipments
                                $count++;
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($shipment['tracking_number']); ?></td>
                                    <td><?php echo htmlspecialchars($shipment['recipient_name']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($shipment['created_at'])); ?></td>
                                    <td>
                                        <?php if ($shipment['status'] === 'delivered'): ?>
                                            <span class="badge bg-success">Delivered</span>
                                        <?php elseif ($shipment['status'] === 'in_transit'): ?>
                                            <span class="badge bg-warning">In Transit</span>
                                        <?php elseif ($shipment['status'] === 'pending'): ?>
                                            <span class="badge bg-info">Pending</span>
                                        <?php elseif ($shipment['status'] === 'failed'): ?>
                                            <span class="badge bg-danger">Failed</span>
                                        <?php elseif ($shipment['status'] === 'returned'): ?>
                                            <span class="badge bg-secondary">Returned</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="table-actions">
                                            <a href="track.php?tracking_number=<?php echo urlencode($shipment['tracking_number']); ?>" class="btn-action btn-view" title="Track"><i class="fas fa-search"></i></a>
                                            <a href="shipment-details.php?id=<?php echo $shipment['id']; ?>" class="btn-action btn-info" title="View Details"><i class="fas fa-eye"></i></a>
                                            <?php if ($shipment['status'] === 'pending'): ?>
                                                <a href="edit-shipment.php?id=<?php echo $shipment['id']; ?>" class="btn-action btn-edit" title="Edit"><i class="fas fa-edit"></i></a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">No shipments found. <a href="create-shipment.php">Create your first shipment</a>.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="section-title mt-5">
            <h2>Quick Actions</h2>
        </div>
        
        <div class="services-container">
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-box"></i>
                </div>
                <h3>Create Shipment</h3>
                <p>Create a new shipment and get a tracking number instantly.</p>
                <a href="create-shipment.php" class="btn btn-primary btn-sm">Create Now</a>
            </div>
            
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-search-location"></i>
                </div>
                <h3>Track Shipment</h3>
                <p>Track your shipment in real-time to know its current status.</p>
                <a href="track.php" class="btn btn-primary btn-sm">Track Now</a>
            </div>
            
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-calculator"></i>
                </div>
                <h3>Get a Quote</h3>
                <p>Calculate shipping costs for your next delivery.</p>
                <a href="quote.php" class="btn btn-primary btn-sm">Get Quote</a>
            </div>
            
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <h3>Contact Support</h3>
                <p>Need help? Our support team is ready to assist you.</p>
                <a href="support.php" class="btn btn-primary btn-sm">Contact Now</a>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer (with modified structure for dashboard)
?>
</body>
</html>