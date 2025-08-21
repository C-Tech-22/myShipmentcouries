<?php
// Start session
session_start();

// Include database connection
require_once '../includes/db_connect.php';

// Check if user is logged in and is admin or staff
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'staff')) {
    header('Location: ../login.php');
    exit;
}

// Get user information
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['full_name'];
$user_role = $_SESSION['role'];

// Get dashboard statistics
try {
    // Total shipments
    $stmt = $pdo->query("SELECT COUNT(*) FROM shipments");
    $total_shipments = $stmt->fetchColumn();
    
    // Pending shipments
    $stmt = $pdo->query("SELECT COUNT(*) FROM shipments WHERE status = 'pending'");
    $pending_shipments = $stmt->fetchColumn();
    
    // In transit shipments
    $stmt = $pdo->query("SELECT COUNT(*) FROM shipments WHERE status = 'in_transit'");
    $in_transit_shipments = $stmt->fetchColumn();
    
    // Delivered shipments
    $stmt = $pdo->query("SELECT COUNT(*) FROM shipments WHERE status = 'delivered'");
    $delivered_shipments = $stmt->fetchColumn();
    
    // Recent shipments
    $stmt = $pdo->query("SELECT * FROM shipments ORDER BY created_at DESC LIMIT 10");
    $recent_shipments = $stmt->fetchAll();
    
    // Unread contact messages
    $stmt = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0");
    $unread_messages = $stmt->fetchColumn();
    
} catch(PDOException $e) {
    // Log error
    error_log("Error fetching dashboard statistics: " . $e->getMessage());
    
    // Set default values
    $total_shipments = 0;
    $pending_shipments = 0;
    $in_transit_shipments = 0;
    $delivered_shipments = 0;
    $recent_shipments = [];
    $unread_messages = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Crest Courier</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="../images/favicon.ico" type="image/x-icon">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="admin-sidebar">
            <div class="admin-sidebar-header">
                <h2>Crest Courier</h2>
                <p>Admin Panel</p>
            </div>
            
            <nav class="admin-nav">
                <div class="admin-nav-section">
                    <h3>Main</h3>
                    <ul>
                        <li><a href="index.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                        <li><a href="shipments.php"><i class="fas fa-shipping-fast"></i> Shipments</a></li>
                        <li><a href="tracking.php"><i class="fas fa-search-location"></i> Tracking Updates</a></li>
                        <li><a href="customers.php"><i class="fas fa-users"></i> Customers</a></li>
                    </ul>
                </div>
                
                <div class="admin-nav-section">
                    <h3>Management</h3>
                    <ul>
                        <li><a href="services.php"><i class="fas fa-concierge-bell"></i> Services</a></li>
                        <li><a href="messages.php"><i class="fas fa-envelope"></i> Messages <?php if ($unread_messages > 0): ?><span class="badge"><?php echo $unread_messages; ?></span><?php endif; ?></a></li>
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                            <li><a href="users.php"><i class="fas fa-user-shield"></i> Staff</a></li>
                            <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <div class="admin-nav-section">
                    <h3>Account</h3>
                    <ul>
                        <li><a href="profile.php"><i class="fas fa-user-circle"></i> My Profile</a></li>
                        <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </div>
            </nav>
        </div>
        
        <!-- Main Content -->
        <div class="admin-main">
            <!-- Top Bar -->
            <div class="admin-topbar">
                <button id="sidebar-toggle" class="sidebar-toggle">
                    <i class="fas fa-bars"></i>
                </button>
                
                <div class="admin-topbar-right">
                    <div class="admin-notification">
                        <a href="messages.php"><i class="fas fa-envelope"></i>
                        <?php if ($unread_messages > 0): ?>
                            <span class="notification-badge"><?php echo $unread_messages; ?></span>
                        <?php endif; ?>
                        </a>
                    </div>
                    
                    <div class="admin-user-dropdown">
                        <button class="dropdown-toggle">
                            <span class="admin-user-name"><?php echo htmlspecialchars($user_name); ?></span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a href="profile.php"><i class="fas fa-user-circle"></i> My Profile</a>
                            <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
                            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Content -->
            <div class="admin-content">
                <div class="admin-content-header">
                    <h1>Dashboard</h1>
                    <p>Welcome back, <?php echo htmlspecialchars($user_name); ?>!</p>
                </div>
                
                <!-- Stats Cards -->
                <div class="admin-stats">
                    <div class="admin-stat-card">
                        <div class="admin-stat-icon bg-primary">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="admin-stat-content">
                            <h3><?php echo number_format($total_shipments); ?></h3>
                            <p>Total Shipments</p>
                        </div>
                    </div>
                    
                    <div class="admin-stat-card">
                        <div class="admin-stat-icon bg-info">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="admin-stat-content">
                            <h3><?php echo number_format($pending_shipments); ?></h3>
                            <p>Pending</p>
                        </div>
                    </div>
                    
                    <div class="admin-stat-card">
                        <div class="admin-stat-icon bg-warning">
                            <i class="fas fa-shipping-fast"></i>
                        </div>
                        <div class="admin-stat-content">
                            <h3><?php echo number_format($in_transit_shipments); ?></h3>
                            <p>In Transit</p>
                        </div>
                    </div>
                    
                    <div class="admin-stat-card">
                        <div class="admin-stat-icon bg-success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="admin-stat-content">
                            <h3><?php echo number_format($delivered_shipments); ?></h3>
                            <p>Delivered</p>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Shipments -->
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h2>Recent Shipments</h2>
                        <a href="shipments.php" class="btn btn-outline btn-sm">View All</a>
                    </div>
                    <div class="admin-card-body">
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Tracking Number</th>
                                        <th>Sender</th>
                                        <th>Recipient</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($recent_shipments)): ?>
                                        <?php foreach($recent_shipments as $shipment): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($shipment['tracking_number']); ?></td>
                                                <td><?php echo htmlspecialchars($shipment['sender_name']); ?></td>
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
                                                        <a href="view-shipment.php?id=<?php echo $shipment['id']; ?>" class="btn-action btn-view" title="View"><i class="fas fa-eye"></i></a>
                                                        <a href="update-shipment.php?id=<?php echo $shipment['id']; ?>" class="btn-action btn-edit" title="Update"><i class="fas fa-edit"></i></a>
                                                        <a href="add-tracking.php?id=<?php echo $shipment['id']; ?>" class="btn-action btn-info" title="Add Tracking"><i class="fas fa-plus"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No shipments found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="admin-quick-actions">
                    <div class="admin-card-header">
                        <h2>Quick Actions</h2>
                    </div>
                    <div class="admin-quick-actions-grid">
                        <a href="add-shipment.php" class="admin-quick-action-card">
                            <div class="admin-quick-action-icon bg-primary">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div class="admin-quick-action-content">
                                <h3>Add Shipment</h3>
                                <p>Create a new shipment</p>
                            </div>
                        </a>
                        
                        <a href="add-tracking.php" class="admin-quick-action-card">
                            <div class="admin-quick-action-icon bg-info">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="admin-quick-action-content">
                                <h3>Add Tracking</h3>
                                <p>Update shipment status</p>
                            </div>
                        </a>
                        
                        <a href="messages.php" class="admin-quick-action-card">
                            <div class="admin-quick-action-icon bg-warning">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="admin-quick-action-content">
                                <h3>Messages</h3>
                                <p>View customer inquiries</p>
                            </div>
                        </a>
                        
                        <a href="reports.php" class="admin-quick-action-card">
                            <div class="admin-quick-action-icon bg-success">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <div class="admin-quick-action-content">
                                <h3>Reports</h3>
                                <p>Generate shipment reports</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="../js/main.js"></script>
    <script>
        // Sidebar toggle
        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            document.querySelector('.admin-container').classList.toggle('sidebar-collapsed');
        });
        
        // User dropdown
        document.querySelector('.dropdown-toggle').addEventListener('click', function() {
            document.querySelector('.dropdown-menu').classList.toggle('show');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.admin-user-dropdown')) {
                document.querySelector('.dropdown-menu').classList.remove('show');
            }
        });
    </script>
</body>
</html>