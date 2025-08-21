<?php
// Include database connection
require_once 'includes/db_connect.php';

// Initialize variables
$name = $email = $subject = $message = '';
$success_message = $error_message = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $subject = sanitize_input($_POST['subject']);
    $message = sanitize_input($_POST['message']);
    
    // Validate inputs
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error_message = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    } else {
        // Insert into database
        try {
            $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $subject, $message]);
            
            // Clear form fields after successful submission
            $name = $email = $subject = $message = '';
            $success_message = 'Your message has been sent successfully. We will get back to you soon!';
        } catch(PDOException $e) {
            $error_message = 'Sorry, there was an error sending your message. Please try again later.';
            // Log error
            error_log("Error saving contact message: " . $e->getMessage());
        }
    }
}

// Include header
include 'includes/header.php';
?>

<!-- Contact Page Header -->
<section class="contact-page-header">
    <div class="container">
        <h1>Contact Us</h1>
        <p>Get in touch with our team for inquiries, quotes, or assistance with your shipping needs.</p>
    </div>
</section>

<!-- Contact Information -->
<section class="contact-page-content">
    <div class="container">
        <div class="contact-page-container">
            <div class="contact-card animate-on-scroll">
                <div class="contact-card-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <h3>Our Location</h3>
                <p>123 Logistics Way<br>Shipping City, SC 12345<br>United States</p>
            </div>
            
            <div class="contact-card animate-on-scroll">
                <div class="contact-card-icon">
                    <i class="fas fa-phone-alt"></i>
                </div>
                <h3>Phone Number</h3>
                <p>Main: +1 (555) 123-4567<br>Toll-Free: 1-800-COURIER<br>Fax: +1 (555) 123-4568</p>
            </div>
            
            <div class="contact-card animate-on-scroll">
                <div class="contact-card-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <h3>Email Address</h3>
                <p>General Inquiries: info@crestcourier.com<br>Customer Support: support@crestcourier.com<br>Careers: careers@crestcourier.com</p>
            </div>
        </div>
        
        <!-- Google Map -->
        <div class="map-container" id="google-map">
            <!-- Google Map will be loaded here via JavaScript -->
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3022.215573814498!2d-73.98784492404069!3d40.75850833646285!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25855c6480299%3A0x55194ec5a1ae072e!2sTimes%20Square!5m2!1s0x89c25855c6480299%3A0x55194ec5a1ae072e!2sTimes%20Square" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
        
        <!-- Contact Form -->
        <div class="section-title">
            <h2>Send Us a Message</h2>
            <p>Fill out the form below and we'll get back to you as soon as possible.</p>
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
        
        <div class="contact-container">
            <div class="contact-info">
                <h3>Contact Information</h3>
                <p>Have questions about our services? Need a quote for your shipment? Our team is here to help you with all your courier and logistics needs.</p>
                
                <div class="contact-info-item">
                    <i class="fas fa-clock"></i>
                    <div>
                        <h4>Business Hours</h4>
                        <p>Monday - Friday: 8:00 AM - 6:00 PM<br>Saturday: 9:00 AM - 2:00 PM<br>Sunday: Closed</p>
                    </div>
                </div>
                
                <div class="contact-info-item">
                    <i class="fas fa-headset"></i>
                    <div>
                        <h4>Customer Support</h4>
                        <p>Our dedicated support team is available during business hours to assist you with any questions or concerns.</p>
                    </div>
                </div>
                
                <div class="contact-info-item">
                    <i class="fas fa-truck"></i>
                    <div>
                        <h4>Shipping Inquiries</h4>
                        <p>For questions about shipping rates, delivery times, or tracking information, please include your tracking number if available.</p>
                    </div>
                </div>
            </div>
            
            <div class="contact-form">
                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                    <div class="form-group">
                        <label for="name">Full Name *</label>
                        <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Subject *</label>
                        <input type="text" id="subject" name="subject" class="form-control" value="<?php echo htmlspecialchars($subject); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message *</label>
                        <textarea id="message" name="message" class="form-control" rows="5" required><?php echo htmlspecialchars($message); ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="section bg-light">
    <div class="container">
        <div class="section-title">
            <h2>Frequently Asked Questions</h2>
            <p>Find answers to common questions about our services and shipping processes.</p>
        </div>
        
        <div class="faq-container">
            <div class="faq-item">
                <div class="faq-question">
                    <h3>How can I track my package?</h3>
                    <p>You can track your package by entering your tracking number on our Track & Trace page. You'll receive real-time updates on the status and location of your shipment.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    <h3>What are your delivery times?</h3>
                    <p>Delivery times vary depending on the service you choose. Our Express Delivery offers same-day or next-day delivery, while Standard Shipping typically takes 2-3 business days. International shipping times vary by destination.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    <h3>How do I get a shipping quote?</h3>
                    <p>You can request a shipping quote by filling out our contact form, calling our customer service, or using the quote calculator on our website. Please provide details about your shipment for an accurate quote.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    <h3>Do you offer insurance for shipments?</h3>
                    <p>Yes, we offer insurance options for all shipments. The cost varies based on the declared value of your package. We recommend insurance for valuable or fragile items.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    <h3>What items are prohibited for shipping?</h3>
                    <p>Prohibited items include hazardous materials, illegal substances, firearms, and perishable goods without proper packaging. Please contact our customer service for a complete list of restricted items.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    <h3>How do I schedule a pickup?</h3>
                    <p>You can schedule a pickup by calling our customer service, using our online booking system, or contacting your assigned account manager if you're a business client.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section section">
    <div class="container">
        <div class="cta-content">
            <h2>Ready to Ship with Crest Courier?</h2>
            <p>Experience reliable, efficient, and secure shipping services for all your courier and logistics needs.</p>
            <a href="services.php" class="btn btn-primary btn-lg">Explore Our Services</a>
        </div>
    </div>
</section>

<?php
// Include footer
include 'includes/footer.php';
?>