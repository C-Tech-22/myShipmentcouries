<?php
// Include database connection
require_once 'includes/db_connect.php';

// Initialize variables
$tracking_number = '';
$shipment = null;
$tracking_updates = [];
$error_message = '';

// Process tracking request
if (isset($_GET['tracking_number']) && !empty($_GET['tracking_number'])) {
    $tracking_number = sanitize_input($_GET['tracking_number']);
    
    try {
        // Get shipment details
        $stmt = $pdo->prepare("SELECT * FROM shipments WHERE tracking_number = ?");
        $stmt->execute([$tracking_number]);
        $shipment = $stmt->fetch();
        
        if ($shipment) {
            // Get tracking updates
            $stmt = $pdo->prepare("SELECT * FROM tracking_updates WHERE shipment_id = ? ORDER BY timestamp DESC");
            $stmt->execute([$shipment['id']]);
            $tracking_updates = $stmt->fetchAll();
        } else {
            $error_message = 'No shipment found with the provided tracking number. Please check and try again.';
        }
    } catch(PDOException $e) {
        $error_message = 'Sorry, we encountered an error while retrieving your tracking information. Please try again later.';
        // Log error
        error_log("Error tracking shipment: " . $e->getMessage());
    }
}

// Include header
include 'includes/header.php';
?>

<!-- Track Page Header -->
<section class="track-page-header">
    <div class="container">
        <h1>Track & Trace</h1>
        <p>Track your shipment in real-time to know its current status and location.</p>
        
        <div class="tracking-form-container" style="margin-top: 30px; max-width: 600px;">
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="get" class="tracking-form">
                <input type="text" name="tracking_number" placeholder="Enter your tracking number" value="<?php echo htmlspecialchars($tracking_number); ?>" required>
                <button type="submit" class="btn btn-primary">Track Now</button>
            </form>
        </div>
    </div>
</section>

<!-- Tracking Results -->
<section class="section">
    <div class="container">
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <div class="alert-content">
                    <span><?php echo $error_message; ?></span>
                    <button type="button" class="close-btn">&times;</button>
                </div>
            </div>
        <?php elseif ($shipment): ?>
            <div class="tracking-result">
                <h2>Shipment Details</h2>
                
                <div class="tracking-info">
                    <div class="tracking-info-item">
                        <h4>Tracking Number</h4>
                        <p><?php echo htmlspecialchars($shipment['tracking_number']); ?></p>
                    </div>
                    
                    <div class="tracking-info-item">
                        <h4>Service Type</h4>
                        <p><?php echo htmlspecialchars($shipment['service_type']); ?></p>
                    </div>
                    
                    <div class="tracking-info-item">
                        <h4>Ship Date</h4>
                        <p><?php echo date('M d, Y', strtotime($shipment['created_at'])); ?></p>
                    </div>
                    
                    <div class="tracking-info-item">
                        <h4>Estimated Delivery</h4>
                        <p><?php echo $shipment['estimated_delivery_date'] ? date('M d, Y', strtotime($shipment['estimated_delivery_date'])) : 'Pending'; ?></p>
                    </div>
                </div>
                
                <div class="tracking-status">
                    <div class="tracking-status-header">
                        <h3>
                            <?php if ($shipment['status'] == 'delivered'): ?>
                                <i class="fas fa-check-circle"></i> Delivered
                            <?php elseif ($shipment['status'] == 'in_transit'): ?>
                                <i class="fas fa-shipping-fast"></i> In Transit
                            <?php elseif ($shipment['status'] == 'pending'): ?>
                                <i class="fas fa-clock"></i> Pending
                            <?php elseif ($shipment['status'] == 'failed'): ?>
                                <i class="fas fa-exclamation-circle"></i> Delivery Failed
                            <?php elseif ($shipment['status'] == 'returned'): ?>
                                <i class="fas fa-undo"></i> Returned
                            <?php endif; ?>
                        </h3>
                        
                        <div class="status <?php echo 'status-' . $shipment['status']; ?>">
                            <?php 
                                $status_text = '';
                                switch ($shipment['status']) {
                                    case 'delivered':
                                        $status_text = 'Delivered';
                                        break;
                                    case 'in_transit':
                                        $status_text = 'In Transit';
                                        break;
                                    case 'pending':
                                        $status_text = 'Pending';
                                        break;
                                    case 'failed':
                                        $status_text = 'Delivery Failed';
                                        break;
                                    case 'returned':
                                        $status_text = 'Returned';
                                        break;
                                    default:
                                        $status_text = 'Unknown';
                                }
                                echo $status_text;
                            ?>
                        </div>
                    </div>
                </div>
                
                <div class="shipment-details">
                    <h3>Package Information</h3>
                    <div class="tracking-info">
                        <div class="tracking-info-item">
                            <h4>Weight</h4>
                            <p><?php echo htmlspecialchars($shipment['weight']); ?> kg</p>
                        </div>
                        
                        <div class="tracking-info-item">
                            <h4>Dimensions</h4>
                            <p><?php echo htmlspecialchars($shipment['dimensions'] ?: 'Not specified'); ?></p>
                        </div>
                        
                        <div class="tracking-info-item">
                            <h4>Package Type</h4>
                            <p><?php echo htmlspecialchars($shipment['package_type']); ?></p>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h3>Sender Information</h3>
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($shipment['sender_name']); ?></p>
                            <p><strong>Address:</strong> <?php echo htmlspecialchars($shipment['sender_address']); ?></p>
                            <p><strong>Phone:</strong> <?php echo htmlspecialchars($shipment['sender_phone']); ?></p>
                            <?php if (!empty($shipment['sender_email'])): ?>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($shipment['sender_email']); ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-6">
                            <h3>Recipient Information</h3>
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($shipment['recipient_name']); ?></p>
                            <p><strong>Address:</strong> <?php echo htmlspecialchars($shipment['recipient_address']); ?></p>
                            <p><strong>Phone:</strong> <?php echo htmlspecialchars($shipment['recipient_phone']); ?></p>
                            <?php if (!empty($shipment['recipient_email'])): ?>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($shipment['recipient_email']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <h3 class="mt-4">Tracking History</h3>
                <div class="timeline">
                    <?php if (!empty($tracking_updates)): ?>
                        <?php foreach($tracking_updates as $index => $update): ?>
                            <div class="timeline-item <?php echo ($index === 0) ? 'active' : ''; ?>">
                                <div class="timeline-date">
                                    <?php echo date('M d, Y - h:i A', strtotime($update['timestamp'])); ?>
                                </div>
                                <div class="timeline-content">
                                    <h4><?php echo htmlspecialchars($update['status']); ?></h4>
                                    <p><?php echo htmlspecialchars($update['location']); ?></p>
                                    <?php if (!empty($update['description'])): ?>
                                        <p><?php echo htmlspecialchars($update['description']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="timeline-item active">
                            <div class="timeline-date">
                                <?php echo date('M d, Y - h:i A', strtotime($shipment['created_at'])); ?>
                            </div>
                            <div class="timeline-content">
                                <h4>Shipment Created</h4>
                                <p>Your shipment has been registered in our system.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php elseif (!empty($tracking_number)): ?>
            <div class="alert alert-info">
                <div class="alert-content">
                    <span>Please enter a tracking number to track your shipment.</span>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Additional Information -->
        <?php if (empty($shipment) && empty($tracking_number)): ?>
            <div class="section-title">
                <h2>How to Track Your Shipment</h2>
                <p>Tracking your shipment with Crest Courier is easy and convenient.</p>
            </div>
            
            <div class="services-container">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3>Enter Tracking Number</h3>
                    <p>Enter your tracking number in the form above. You can find your tracking number in your shipping confirmation email or receipt.</p>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    <h3>View Real-Time Updates</h3>
                    <p>Get real-time updates on your shipment's status and location. Our tracking system provides detailed information about your package's journey.</p>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3>Mobile Tracking</h3>
                    <p>Track your shipment on the go using your mobile device. Our tracking page is fully responsive and works on all devices.</p>
                </div>
            </div>
            
            <div class="section-title mt-5">
                <h2>Frequently Asked Questions</h2>
            </div>
            
            <div class="faq-container">
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Where can I find my tracking number?</h3>
                        <p>Your tracking number is provided in your shipping confirmation email, receipt, or shipping label. It typically starts with "CC" followed by numbers.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>How often is tracking information updated?</h3>
                        <p>Tracking information is updated in real-time as your package moves through our network. Updates typically occur when your package arrives at or departs from a facility.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>What if my tracking information hasn't updated?</h3>
                        <p>There may be delays in tracking updates during busy periods or due to technical issues. If your tracking hasn't updated for more than 24 hours, please contact our customer service.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Can I track multiple shipments at once?</h3>
                        <p>Currently, our online tracking system allows you to track one shipment at a time. For bulk tracking, please contact our customer service or use our business portal if you're a business client.</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section section">
    <div class="container">
        <div class="cta-content">
            <h2>Need Help with Your Shipment?</h2>
            <p>Our customer service team is ready to assist you with any questions or concerns about your shipment.</p>
            <a href="contact.php" class="btn btn-primary btn-lg">Contact Us</a>
        </div>
    </div>
</section>

<?php
// Include footer
include 'includes/footer.php';
?>