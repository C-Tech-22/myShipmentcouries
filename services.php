<?php
// Include database connection
require_once 'includes/db_connect.php';

// Get services
try {
    $stmt = $pdo->query("SELECT * FROM services WHERE is_active = 1");
    $services = $stmt->fetchAll();
} catch(PDOException $e) {
    $services = [];
    // Log error
    error_log("Error fetching services: " . $e->getMessage());
}

// Include header
include 'includes/header.php';
?>

<!-- Services Page Header -->
<section class="service-page-header">
    <div class="container">
        <h1>Our Services</h1>
        <p>We offer a comprehensive range of courier and logistics services to meet all your shipping needs, from express delivery to international freight.</p>
    </div>
</section>

<!-- Services Overview -->
<section class="section">
    <div class="container">
        <div class="section-title">
            <h2>What We Offer</h2>
            <p>Explore our wide range of services designed to provide efficient and reliable shipping solutions for individuals and businesses.</p>
        </div>
        
        <div class="services-container">
            <?php if (!empty($services)): ?>
                <?php foreach($services as $service): ?>
                    <div class="service-card animate-on-scroll" id="<?php echo strtolower(str_replace(' ', '-', $service['name'])); ?>">
                        <div class="service-icon">
                            <i class="fas <?php echo htmlspecialchars($service['icon']); ?>"></i>
                        </div>
                        <h3><?php echo htmlspecialchars($service['name']); ?></h3>
                        <p><?php echo htmlspecialchars($service['description']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback services if database is not available -->
                <div class="service-card animate-on-scroll" id="express-delivery">
                    <div class="service-icon">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <h3>Express Delivery</h3>
                    <p>Same-day delivery for urgent packages within the city limits.</p>
                </div>
                
                <div class="service-card animate-on-scroll" id="standard-shipping">
                    <div class="service-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h3>Standard Shipping</h3>
                    <p>Reliable 2-3 day delivery service for regular packages.</p>
                </div>
                
                <div class="service-card animate-on-scroll" id="international-shipping">
                    <div class="service-icon">
                        <i class="fas fa-globe"></i>
                    </div>
                    <h3>International Shipping</h3>
                    <p>Worldwide shipping with customs handling and tracking.</p>
                </div>
                
                <div class="service-card animate-on-scroll" id="freight-services">
                    <div class="service-icon">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <h3>Freight Services</h3>
                    <p>Heavy cargo and bulk shipment services for businesses.</p>
                </div>
                
                <div class="service-card animate-on-scroll" id="warehousing">
                    <div class="service-icon">
                        <i class="fas fa-warehouse"></i>
                    </div>
                    <h3>Warehousing</h3>
                    <p>Secure storage solutions with inventory management.</p>
                </div>
                
                <div class="service-card animate-on-scroll" id="packaging-solutions">
                    <div class="service-icon">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <h3>Packaging Solutions</h3>
                    <p>Professional packaging services for all types of items.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Service Details -->
<section class="service-detail" id="express">
    <div class="container">
        <div class="service-detail-container">
            <div class="service-detail-image">
                <img src="images/express-delivery.jpg" alt="Express Delivery">
            </div>
            <div class="service-detail-content">
                <h2>Express Delivery</h2>
                <p>Our Express Delivery service is designed for urgent shipments that need to reach their destination as quickly as possible. We offer same-day delivery within city limits and next-day delivery for nearby cities.</p>
                <p>With our dedicated fleet of vehicles and experienced drivers, we ensure that your time-sensitive packages are delivered promptly and safely.</p>
                
                <div class="service-features">
                    <div class="service-feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Same-day delivery within city limits</span>
                    </div>
                    <div class="service-feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Next-day delivery for nearby cities</span>
                    </div>
                    <div class="service-feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Priority handling and shipping</span>
                    </div>
                    <div class="service-feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Real-time tracking and notifications</span>
                    </div>
                    <div class="service-feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Proof of delivery with signature</span>
                    </div>
                </div>
                
                <a href="contact.php" class="btn btn-primary mt-4">Get a Quote</a>
            </div>
        </div>
    </div>
</section>

<section class="service-detail bg-light" id="standard">
    <div class="container">
        <div class="service-detail-container">
            <div class="service-detail-image">
                <img src="images/standard-shipping.jpg" alt="Standard Shipping">
            </div>
            <div class="service-detail-content">
                <h2>Standard Shipping</h2>
                <p>Our Standard Shipping service provides reliable and cost-effective delivery for packages that don't require immediate delivery. With a delivery timeframe of 2-3 business days, this service is perfect for regular shipments.</p>
                <p>We handle your packages with care and ensure they reach their destination safely and on schedule.</p>
                
                <div class="service-features">
                    <div class="service-feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>2-3 business day delivery</span>
                    </div>
                    <div class="service-feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Cost-effective shipping solution</span>
                    </div>
                    <div class="service-feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Tracking and delivery updates</span>
                    </div>
                    <div class="service-feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Insurance options available</span>
                    </div>
                    <div class="service-feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Delivery confirmation</span>
                    </div>
                </div>
                
                <a href="contact.php" class="btn btn-primary mt-4">Get a Quote</a>
            </div>
        </div>
    </div>
</section>

<section class="service-detail" id="international">
    <div class="container">
        <div class="service-detail-container">
            <div class="service-detail-image">
                <img src="images/international-shipping.jpg" alt="International Shipping">
            </div>
            <div class="service-detail-content">
                <h2>International Shipping</h2>
                <p>Our International Shipping service connects you to the world. We offer reliable and efficient shipping to over 150 countries, with customs handling and documentation support to ensure smooth delivery across borders.</p>
                <p>Whether you're sending documents, packages, or larger items, our international shipping experts will help you navigate the complexities of global logistics.</p>
                
                <div class="service-features">
                    <div class="service-feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Delivery to over 150 countries</span>
                    </div>
                    <div class="service-feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Customs documentation assistance</span>
                    </div>
                    <div class="service-feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Multiple service levels available</span>
                    </div>
                    <div class="service-feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>International tracking capabilities</span>
                    </div>
                    <div class="service-feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Insurance for international shipments</span>
                    </div>
                </div>
                
                <a href="contact.php" class="btn btn-primary mt-4">Get a Quote</a>
            </div>
        </div>
    </div>
</section>

<section class="service-detail bg-light" id="freight">
    <div class="container">
        <div class="service-detail-container">
            <div class="service-detail-image">
                <img src="images/freight-services.jpg" alt="Freight Services">
            </div>
            <div class="service-detail-content">
                <h2>Freight Services</h2>
                <p>Our Freight Services are designed for businesses that need to transport large volumes of goods or heavy items. We offer road, air, and sea freight options to meet your specific requirements.</p>
                <p>With our extensive network and logistics expertise, we ensure efficient and cost-effective transportation of your cargo, regardless of size or weight.</p>
                
                <div class="service-features">
                    <div class="service-feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Road, air, and sea freight options</span>
                    </div>
                    <div class="service-feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Full container and less than container load services</span>
                    </div>
                    <div class="service-feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Oversized and heavy item transportation</span>
                    </div>
                    <div class="service-feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Cargo insurance coverage</span>
                    </div>
                    <div class="service-feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Customs clearance assistance</span>
                    </div>
                </div>
                
                <a href="contact.php" class="btn btn-primary mt-4">Get a Quote</a>
            </div>
        </div>
    </div>
</section>

<section class="service-detail" id="warehousing">
    <div class="container">
        <div class="service-detail-container">
            <div class="service-detail-image">
                <img src="images/warehousing.jpg" alt="Warehousing">
            </div>
            <div class="service-detail-content">
                <h2>Warehousing</h2>
                <p>Our Warehousing service provides secure storage solutions for your goods. With state-of-the-art facilities and inventory management systems, we ensure your products are stored safely and can be accessed or shipped quickly when needed.</p>
                <p>Whether you need short-term storage or a long-term warehousing partner, our flexible solutions can be tailored to your business requirements.</p>
                
                <div class="service-features">
                    <div class="service-feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Secure storage facilities</span>
                    </div>
                    <div class="service-feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Climate-controlled options available</span>
                    </div>
                    <div class="service-feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Inventory management system</span>
                    </div>
                    <div class="service-feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Order fulfillment services</span>
                    </div>
                    <div class="service-feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Flexible storage terms</span>
                    </div>
                </div>
                
                <a href="contact.php" class="btn btn-primary mt-4">Get a Quote</a>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section section">
    <div class="container">
        <div class="cta-content">
            <h2>Need a Custom Shipping Solution?</h2>
            <p>Contact our team to discuss your specific requirements. We can create tailored logistics solutions for your business.</p>
            <a href="contact.php" class="btn btn-primary btn-lg">Contact Us</a>
        </div>
    </div>
</section>

<?php
// Include footer
include 'includes/footer.php';
?>