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

// Pagination settings
$results_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $results_per_page;

// Get user's email
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

// Filter settings
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$date_filter = isset($_GET['date']) ? $_GET['date'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build query based on filters
$query = "SELECT * FROM shipments WHERE sender_email = ?";
$params = [$user_email];

if (!empty($status_filter)) {
    $query .= " AND status = ?";
    $params[] = $status_filter;
}

if (!empty($date_filter)) {
    switch ($date_filter) {
        case 'today':
            $query .= " AND DATE(created_at) = CURDATE()";
            break;
        case 'week':
            $query .= " AND created_at >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)";
            break;
        case 'month':
            $query .= " AND created_at >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
            break;
        case 'year':
            $query .= " AND created_at >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
            break;
    }
}

if (!empty($search)) {
    $query .= " AND (tracking_number LIKE ? OR recipient_name LIKE ? OR recipient_address LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

// Count total results for pagination
$count_query = str_replace("SELECT *", "SELECT COUNT(*)", $query);
try {
    $stmt = $pdo->prepare($count_query);
    $stmt->execute($params);
    $total_results = $stmt->fetchColumn();
} catch(PDOException $e) {
    $total_results = 0;
    // Log error
    error_log("Error counting shipments: " . $e->getMessage());
}

$total_pages = ceil($total_results / $results_per_page);

// Add pagination to query
$query .= " ORDER BY created_at DESC LIMIT $offset, $results_per_page";

// Get shipments
try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
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
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="create-shipment.php"><i class="fas fa-box"></i> Create Shipment</a></li>
                <li><a href="my-shipments.php" class="active"><i class="fas fa-shipping-fast"></i> My Shipments</a></li>
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
            <h2>My Shipments</h2>
            <a href="create-shipment.php" class="btn btn-primary"><i class="fas fa-plus"></i> New Shipment</a>
        </div>
        
        <!-- Filter Options -->
        <div class="filter-container">
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="get" class="filter-form">
                <div class="filter-group">
                    <label for="status">Status:</label>
                    <select id="status" name="status" class="form-control">
                        <option value="">All Statuses</option>
                        <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="in_transit" <?php echo $status_filter === 'in_transit' ? 'selected' : ''; ?>>In Transit</option>
                        <option value="delivered" <?php echo $status_filter === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                        <option value="failed" <?php echo $status_filter === 'failed' ? 'selected' : ''; ?>>Failed</option>
                        <option value="returned" <?php echo $status_filter === 'returned' ? 'selected' : ''; ?>>Returned</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="date">Date Range:</label>
                    <select id="date" name="date" class="form-control">
                        <option value="">All Time</option>
                        <option value="today" <?php echo $date_filter === 'today' ? 'selected' : ''; ?>>Today</option>
                        <option value="week" <?php echo $date_filter === 'week' ? 'selected' : ''; ?>>Last Week</option>
                        <option value="month" <?php echo $date_filter === 'month' ? 'selected' : ''; ?>>Last Month</option>
                        <option value="year" <?php echo $date_filter === 'year' ? 'selected' : ''; ?>>Last Year</option>
                    </select>
                </div>
                
                <div class="filter-group search-group">
                    <input type="text" name="search" class="form-control" placeholder="Search tracking #, recipient..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Search</button>
                </div>
            </form>
        </div>
        
        <!-- Shipments Table -->
        <div class="dashboard-table">
            <div class="dashboard-table-header">
                <h3>All Shipments (<?php echo $total_results; ?>)</h3>
                <?php if (!empty($status_filter) || !empty($date_filter) || !empty($search)): ?>
                    <a href="my-shipments.php" class="btn btn-outline btn-sm">Clear Filters</a>
                <?php endif; ?>
            </div>
            
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Tracking Number</th>
                            <th>Recipient</th>
                            <th>Service Type</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($shipments)): ?>
                            <?php foreach($shipments as $shipment): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($shipment['tracking_number']); ?></td>
                                    <td><?php echo htmlspecialchars($shipment['recipient_name']); ?></td>
                                    <td><?php echo htmlspecialchars($shipment['service_type']); ?></td>
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
                                <td colspan="6" class="text-center">No shipments found. <a href="create-shipment.php">Create your first shipment</a>.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=1<?php echo !empty($status_filter) ? '&status=' . urlencode($status_filter) : ''; ?><?php echo !empty($date_filter) ? '&date=' . urlencode($date_filter) : ''; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="pagination-link">&laquo; First</a>
                        <a href="?page=<?php echo $page - 1; ?><?php echo !empty($status_filter) ? '&status=' . urlencode($status_filter) : ''; ?><?php echo !empty($date_filter) ? '&date=' . urlencode($date_filter) : ''; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="pagination-link">&lsaquo; Previous</a>
                    <?php endif; ?>
                    
                    <?php
                    $start_page = max(1, $page - 2);
                    $end_page = min($total_pages, $page + 2);
                    
                    for ($i = $start_page; $i <= $end_page; $i++):
                    ?>
                        <a href="?page=<?php echo $i; ?><?php echo !empty($status_filter) ? '&status=' . urlencode($status_filter) : ''; ?><?php echo !empty($date_filter) ? '&date=' . urlencode($date_filter) : ''; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="pagination-link <?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?><?php echo !empty($status_filter) ? '&status=' . urlencode($status_filter) : ''; ?><?php echo !empty($date_filter) ? '&date=' . urlencode($date_filter) : ''; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="pagination-link">Next &rsaquo;</a>
                        <a href="?page=<?php echo $total_pages; ?><?php echo !empty($status_filter) ? '&status=' . urlencode($status_filter) : ''; ?><?php echo !empty($date_filter) ? '&date=' . urlencode($date_filter) : ''; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="pagination-link">Last &raquo;</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Export Options -->
        <div class="export-options mt-4">
            <h3>Export Shipments</h3>
            <p>Download your shipment data in different formats.</p>
            <div class="export-buttons">
                <a href="export.php?format=csv<?php echo !empty($status_filter) ? '&status=' . urlencode($status_filter) : ''; ?><?php echo !empty($date_filter) ? '&date=' . urlencode($date_filter) : ''; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="btn btn-secondary"><i class="fas fa-file-csv"></i> Export as CSV</a>
                <a href="export.php?format=pdf<?php echo !empty($status_filter) ? '&status=' . urlencode($status_filter) : ''; ?><?php echo !empty($date_filter) ? '&date=' . urlencode($date_filter) : ''; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="btn btn-secondary"><i class="fas fa-file-pdf"></i> Export as PDF</a>
                <a href="export.php?format=excel<?php echo !empty($status_filter) ? '&status=' . urlencode($status_filter) : ''; ?><?php echo !empty($date_filter) ? '&date=' . urlencode($date_filter) : ''; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="btn btn-secondary"><i class="fas fa-file-excel"></i> Export as Excel</a>
            </div>
        </div>
    </div>
</div>

<style>
/* Additional styles for this page */
.filter-container {
    background-color: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 30px;
}

.filter-form {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    align-items: flex-end;
}

.filter-group {
    flex: 1;
    min-width: 200px;
}

.filter-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
}

.search-group {
    display: flex;
    gap: 10px;
    flex: 2;
}

.search-group .form-control {
    flex: 1;
}

.badge {
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
    display: inline-block;
}

.bg-success {
    background-color: rgba(40, 167, 69, 0.1);
    color: #28a745;
}

.bg-warning {
    background-color: rgba(255, 193, 7, 0.1);
    color: #ffc107;
}

.bg-info {
    background-color: rgba(23, 162, 184, 0.1);
    color: #17a2b8;
}

.bg-danger {
    background-color: rgba(220, 53, 69, 0.1);
    color: #dc3545;
}

.bg-secondary {
    background-color: rgba(108, 117, 125, 0.1);
    color: #6c757d;
}

.pagination {
    display: flex;
    justify-content: center;
    margin-top: 30px;
    gap: 5px;
}

.pagination-link {
    display: inline-block;
    padding: 8px 12px;
    border-radius: 5px;
    background-color: #f8f9fa;
    color: var(--dark-color);
    transition: var(--transition);
}

.pagination-link:hover {
    background-color: var(--secondary-color);
    color: white;
}

.pagination-link.active {
    background-color: var(--primary-color);
    color: white;
}

.export-options {
    background-color: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
}

.export-buttons {
    display: flex;
    gap: 15px;
    margin-top: 15px;
    flex-wrap: wrap;
}

@media (max-width: 768px) {
    .filter-form {
        flex-direction: column;
    }
    
    .filter-group {
        width: 100%;
    }
    
    .search-group {
        width: 100%;
    }
    
    .export-buttons {
        flex-direction: column;
    }
    
    .export-buttons .btn {
        width: 100%;
    }
}
</style>

<?php
// Include footer (with modified structure for dashboard)
?>
</body>
</html>