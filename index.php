<?php
// Include database connection
require_once 'includes/db_connect.php';

// Get services for homepage
try {
    $stmt = $pdo->query("SELECT * FROM services WHERE is_active = 1 LIMIT 6");
    $services = $stmt->fetchAll();
} catch(PDOException $e) {
    $services = [];
    // Log error
    error_log("Error fetching services: " . $e->getMessage());
}

// Include header
include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h1>Fast & Reliable Courier Services</h1>
            <p>We deliver your packages safely and on time, anywhere in the world. Experience the best in logistics and courier services.</p>
            <div class="hero-buttons">
                <a href="services.php" class="btn btn-primary btn-lg">Our Services</a>
                <a href="contact.php" class="btn btn-outline btn-lg">Contact Us</a>
            </div>
        </div>
    </div>
</section>

<!-- Tracking Form -->
<div class="container">
    <div class="tracking-form-container">
        <h3>Track Your Shipment</h3>
        <form action="track.php" method="get" class="tracking-form">
            <input type="text" name="tracking_number" placeholder="Enter your tracking number" required>
            <button type="submit" class="btn btn-primary">Track Now</button>
        </form>
    </div>
</div>

<!-- Services Section -->
<section class="section bg-light">
    <div class="container">
        <div class="section-title">
            <h2>Our Services</h2>
            <p>We offer a wide range of courier and logistics services tailored to meet your specific needs.</p>
        </div>
        
        <div class="services-container">
            <?php if (!empty($services)): ?>
                <?php foreach($services as $service): ?>
                    <div class="service-card animate-on-scroll">
                        <div class="service-icon">
                            <i class="fas <?php echo htmlspecialchars($service['icon']); ?>"></i>
                        </div>
                        <h3><?php echo htmlspecialchars($service['name']); ?></h3>
                        <p><?php echo htmlspecialchars($service['description']); ?></p>
                        <a href="services.php#<?php echo strtolower(str_replace(' ', '-', $service['name'])); ?>" class="btn btn-outline btn-sm">Learn More</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback services if database is not available -->
                <div class="service-card animate-on-scroll">
                    <div class="service-icon">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <h3>Express Delivery</h3>
                    <p>Same-day delivery for urgent packages within the city limits.</p>
                    <a href="services.php#express-delivery" class="btn btn-outline btn-sm">Learn More</a>
                </div>
                
                <div class="service-card animate-on-scroll">
                    <div class="service-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h3>Standard Shipping</h3>
                    <p>Reliable 2-3 day delivery service for regular packages.</p>
                    <a href="services.php#standard-shipping" class="btn btn-outline btn-sm">Learn More</a>
                </div>
                
                <div class="service-card animate-on-scroll">
                    <div class="service-icon">
                        <i class="fas fa-globe"></i>
                    </div>
                    <h3>International Shipping</h3>
                    <p>Worldwide shipping with customs handling and tracking.</p>
                    <a href="services.php#international-shipping" class="btn btn-outline btn-sm">Learn More</a>
                </div>
                
                <div class="service-card animate-on-scroll">
                    <div class="service-icon">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <h3>Freight Services</h3>
                    <p>Heavy cargo and bulk shipment services for businesses.</p>
                    <a href="services.php#freight-services" class="btn btn-outline btn-sm">Learn More</a>
                </div>
                
                <div class="service-card animate-on-scroll">
                    <div class="service-icon">
                        <i class="fas fa-warehouse"></i>
                    </div>
                    <h3>Warehousing</h3>
                    <p>Secure storage solutions with inventory management.</p>
                    <a href="services.php#warehousing" class="btn btn-outline btn-sm">Learn More</a>
                </div>
                
                <div class="service-card animate-on-scroll">
                    <div class="service-icon">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <h3>Packaging Solutions</h3>
                    <p>Professional packaging services for all types of items.</p>
                    <a href="services.php#packaging-solutions" class="btn btn-outline btn-sm">Learn More</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="section">
    <div class="container">
        <div class="about-container">
            <div class="about-image">
                <img src="images/about-img.jpg" alt="About Crest Courier">
            </div>
            <div class="about-content">
                <h2>About Crest Courier</h2>
                <p>Crest Courier is a leading logistics and courier service provider with over 15 years of experience in the industry. We pride ourselves on delivering packages safely, securely, and on time.</p>
                <p>Our extensive network covers both domestic and international destinations, ensuring that your packages reach anywhere in the world efficiently.</p>
                
                <div class="about-features">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="feature-text">
                            <h4>Fast Delivery</h4>
                            <p>We ensure your packages reach their destination in the shortest time possible.</p>
                        </div>
                    </div>
                    
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="feature-text">
                            <h4>Secure Handling</h4>
                            <p>Your packages are handled with utmost care and security throughout the journey.</p>
                        </div>
                    </div>
                    
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="feature-text">
                            <h4>Real-time Tracking</h4>
                            <p>Track your shipments in real-time with our advanced tracking system.</p>
                        </div>
                    </div>
                    
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="feature-text">
                            <h4>Global Network</h4>
                            <p>Our extensive network ensures delivery to almost any location worldwide.</p>
                        </div>
                    </div>
                </div>
                
                <a href="about.php" class="btn btn-primary mt-4">Learn More About Us</a>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section section">
    <div class="container">
        <div class="stats-container">
            <div class="stat-item animate-on-scroll">
                <div class="stat-number" data-count="15000">0</div>
                <div class="stat-text">Packages Delivered Daily</div>
            </div>
            
            <div class="stat-item animate-on-scroll">
                <div class="stat-number" data-count="150">0</div>
                <div class="stat-text">Countries Covered</div>
            </div>
            
            <div class="stat-item animate-on-scroll">
                <div class="stat-number" data-count="500">0</div>
                <div class="stat-text">Delivery Vehicles</div>
            </div>
            
            <div class="stat-item animate-on-scroll">
                <div class="stat-number" data-count="98">0</div>
                <div class="stat-text">Customer Satisfaction %</div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="section bg-light">
    <div class="container">
        <div class="section-title">
            <h2>What Our Clients Say</h2>
            <p>Don't just take our word for it. Here's what our clients have to say about our services.</p>
        </div>
        
        <div class="testimonials-container">
            <div class="testimonial-item animate-on-scroll">
                <div class="testimonial-content">
                    <p>"Crest Courier has been our logistics partner for over 5 years now. Their service is consistently excellent, and they always deliver on time. I highly recommend them for all your shipping needs."</p>
                </div>
                <div class="testimonial-author">
                    <img src="images/testimonial-1.jpg" alt="John Smith">
                    <div class="author-info">
                        <h4>John Smith</h4>
                        <p>CEO, Tech Solutions Inc.</p>
                    </div>
                </div>
            </div>
            
            <div class="testimonial-item animate-on-scroll">
                <div class="testimonial-content">
                    <p>"As an e-commerce business owner, reliable shipping is crucial for my success. Crest Courier has never let me down. Their tracking system is excellent, and my customers are always happy with the delivery service."</p>
                </div>
                <div class="testimonial-author">
                    <img src="images/testimonial-2.jpg" alt="Sarah Johnson">
                    <div class="author-info">
                        <h4>Sarah Johnson</h4>
                        <p>Founder, Fashion Boutique</p>
                    </div>
                </div>
            </div>
            
            <div class="testimonial-item animate-on-scroll">
                <div class="testimonial-content">
                    <p>"I needed to ship an urgent package internationally, and Crest Courier made it happen in record time. Their customer service was exceptional, and they kept me updated throughout the entire process."</p>
                </div>
                <div class="testimonial-author">
                    <img src="images/testimonial-3.jpg" alt="Michael Brown">
                    <div class="author-info">
                        <h4>Michael Brown</h4>
                        <p>International Business Consultant</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section section">
    <div class="container">
        <div class="cta-content">
            <h2>Ready to Ship Your Package?</h2>
            <p>Experience the best in courier and logistics services. Contact us today to get started with your shipment.</p>
            <div class="hero-buttons">
                <a href="contact.php" class="btn btn-primary btn-lg">Contact Us</a>
                <a href="track.php" class="btn btn-outline btn-lg">Track Shipment</a>
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
include 'includes/footer.php';
?>