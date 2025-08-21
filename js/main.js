/**
 * Crest Courier - Main JavaScript File
 */

document.addEventListener('DOMContentLoaded', function() {
    // Mobile Navigation Toggle
    const hamburgerMenu = document.querySelector('.hamburger-menu');
    const mainNav = document.querySelector('.main-nav');
    
    if (hamburgerMenu) {
        hamburgerMenu.addEventListener('click', function() {
            this.classList.toggle('active');
            mainNav.classList.toggle('active');
            
            // Toggle hamburger menu animation
            const bars = this.querySelectorAll('.bar');
            if (this.classList.contains('active')) {
                bars[0].style.transform = 'rotate(-45deg) translate(-5px, 6px)';
                bars[1].style.opacity = '0';
                bars[2].style.transform = 'rotate(45deg) translate(-5px, -6px)';
            } else {
                bars[0].style.transform = 'none';
                bars[1].style.opacity = '1';
                bars[2].style.transform = 'none';
            }
        });
    }
    
    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
        if (mainNav && mainNav.classList.contains('active') && !event.target.closest('.header-inner')) {
            mainNav.classList.remove('active');
            if (hamburgerMenu) {
                hamburgerMenu.classList.remove('active');
                const bars = hamburgerMenu.querySelectorAll('.bar');
                bars[0].style.transform = 'none';
                bars[1].style.opacity = '1';
                bars[2].style.transform = 'none';
            }
        }
    });
    
    // Tracking Form Validation
    const trackingForm = document.querySelector('.tracking-form');
    if (trackingForm) {
        trackingForm.addEventListener('submit', function(e) {
            const trackingInput = this.querySelector('input[name="tracking_number"]');
            if (!trackingInput.value.trim()) {
                e.preventDefault();
                showAlert('Please enter a tracking number', 'danger');
                trackingInput.focus();
            }
        });
    }
    
    // Contact Form Validation
    const contactForm = document.querySelector('.contact-form form');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            let isValid = true;
            const name = this.querySelector('input[name="name"]');
            const email = this.querySelector('input[name="email"]');
            const subject = this.querySelector('input[name="subject"]');
            const message = this.querySelector('textarea[name="message"]');
            
            // Reset previous error states
            const formControls = this.querySelectorAll('.form-control');
            formControls.forEach(control => {
                control.style.borderColor = '';
            });
            
            // Validate name
            if (!name.value.trim()) {
                name.style.borderColor = 'var(--danger-color)';
                isValid = false;
            }
            
            // Validate email
            if (!validateEmail(email.value)) {
                email.style.borderColor = 'var(--danger-color)';
                isValid = false;
            }
            
            // Validate subject
            if (!subject.value.trim()) {
                subject.style.borderColor = 'var(--danger-color)';
                isValid = false;
            }
            
            // Validate message
            if (!message.value.trim()) {
                message.style.borderColor = 'var(--danger-color)';
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
                showAlert('Please fill in all required fields correctly', 'danger');
            }
        });
    }
    
    // Login Form Validation
    const loginForm = document.querySelector('.auth-form.login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            let isValid = true;
            const username = this.querySelector('input[name="username"]');
            const password = this.querySelector('input[name="password"]');
            
            // Reset previous error states
            const formControls = this.querySelectorAll('.form-control');
            formControls.forEach(control => {
                control.style.borderColor = '';
            });
            
            // Validate username
            if (!username.value.trim()) {
                username.style.borderColor = 'var(--danger-color)';
                isValid = false;
            }
            
            // Validate password
            if (!password.value.trim()) {
                password.style.borderColor = 'var(--danger-color)';
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
                showAlert('Please enter both username and password', 'danger');
            }
        });
    }
    
    // Registration Form Validation
    const registerForm = document.querySelector('.auth-form.register-form');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            let isValid = true;
            const fullName = this.querySelector('input[name="full_name"]');
            const email = this.querySelector('input[name="email"]');
            const username = this.querySelector('input[name="username"]');
            const password = this.querySelector('input[name="password"]');
            const confirmPassword = this.querySelector('input[name="confirm_password"]');
            
            // Reset previous error states
            const formControls = this.querySelectorAll('.form-control');
            formControls.forEach(control => {
                control.style.borderColor = '';
            });
            
            // Validate full name
            if (!fullName.value.trim()) {
                fullName.style.borderColor = 'var(--danger-color)';
                isValid = false;
            }
            
            // Validate email
            if (!validateEmail(email.value)) {
                email.style.borderColor = 'var(--danger-color)';
                isValid = false;
            }
            
            // Validate username
            if (!username.value.trim() || username.value.length < 4) {
                username.style.borderColor = 'var(--danger-color)';
                isValid = false;
            }
            
            // Validate password
            if (!password.value.trim() || password.value.length < 6) {
                password.style.borderColor = 'var(--danger-color)';
                isValid = false;
            }
            
            // Validate confirm password
            if (password.value !== confirmPassword.value) {
                confirmPassword.style.borderColor = 'var(--danger-color)';
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
                showAlert('Please fill in all required fields correctly', 'danger');
            }
        });
    }
    
    // Shipment Form Validation
    const shipmentForm = document.querySelector('.shipment-form');
    if (shipmentForm) {
        shipmentForm.addEventListener('submit', function(e) {
            let isValid = true;
            const requiredFields = this.querySelectorAll('[required]');
            
            // Reset previous error states
            const formControls = this.querySelectorAll('.form-control');
            formControls.forEach(control => {
                control.style.borderColor = '';
            });
            
            // Validate all required fields
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.style.borderColor = 'var(--danger-color)';
                    isValid = false;
                }
                
                // Additional validation for email fields
                if (field.type === 'email' && field.value.trim() && !validateEmail(field.value)) {
                    field.style.borderColor = 'var(--danger-color)';
                    isValid = false;
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                showAlert('Please fill in all required fields correctly', 'danger');
            }
        });
    }
    
    // Alert Messages
    const alerts = document.querySelectorAll('.alert');
    if (alerts.length > 0) {
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.classList.add('fade-out');
                setTimeout(() => {
                    alert.remove();
                }, 500);
            }, 5000);
            
            const closeBtn = alert.querySelector('.close-btn');
            if (closeBtn) {
                closeBtn.addEventListener('click', () => {
                    alert.classList.add('fade-out');
                    setTimeout(() => {
                        alert.remove();
                    }, 500);
                });
            }
        });
    }
    
    // Animate elements on scroll
    const animateElements = document.querySelectorAll('.animate-on-scroll');
    if (animateElements.length > 0) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        
        animateElements.forEach(element => {
            observer.observe(element);
        });
    }
    
    // Initialize Google Map if it exists
    initMap();
    
    // Initialize Counters
    initCounters();
});

// Helper Functions

// Email validation
function validateEmail(email) {
    const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}

// Show alert message
function showAlert(message, type = 'info') {
    const alertContainer = document.createElement('div');
    alertContainer.className = `alert alert-${type}`;
    alertContainer.innerHTML = `
        <div class="alert-content">
            <span>${message}</span>
            <button type="button" class="close-btn">&times;</button>
        </div>
    `;
    
    document.body.appendChild(alertContainer);
    
    setTimeout(() => {
        alertContainer.classList.add('show');
    }, 10);
    
    setTimeout(() => {
        alertContainer.classList.add('fade-out');
        setTimeout(() => {
            alertContainer.remove();
        }, 500);
    }, 5000);
    
    const closeBtn = alertContainer.querySelector('.close-btn');
    closeBtn.addEventListener('click', () => {
        alertContainer.classList.add('fade-out');
        setTimeout(() => {
            alertContainer.remove();
        }, 500);
    });
}

// Initialize Google Map
function initMap() {
    const mapContainer = document.getElementById('google-map');
    if (mapContainer && typeof google !== 'undefined') {
        const map = new google.maps.Map(mapContainer, {
            center: { lat: 40.7128, lng: -74.0060 }, // New York coordinates
            zoom: 15
        });
        
        const marker = new google.maps.Marker({
            position: { lat: 40.7128, lng: -74.0060 },
            map: map,
            title: 'Crest Courier Headquarters'
        });
    }
}

// Initialize Counters
function initCounters() {
    const counters = document.querySelectorAll('.stat-number');
    if (counters.length > 0) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const target = entry.target;
                    const countTo = parseInt(target.getAttribute('data-count'));
                    let count = 0;
                    const updateCounter = () => {
                        const increment = countTo / 100;
                        if (count < countTo) {
                            count += increment;
                            target.innerText = Math.ceil(count).toLocaleString();
                            setTimeout(updateCounter, 20);
                        } else {
                            target.innerText = countTo.toLocaleString();
                        }
                    };
                    updateCounter();
                    observer.unobserve(target);
                }
            });
        }, { threshold: 0.5 });
        
        counters.forEach(counter => {
            observer.observe(counter);
        });
    }
}

// Track shipment function
function trackShipment(trackingNumber) {
    // This would typically make an AJAX request to the server
    // For demo purposes, we'll just redirect to the tracking page
    window.location.href = `track.php?tracking_number=${encodeURIComponent(trackingNumber)}`;
}

// Calculate shipping cost (for shipment form)
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

// Toggle password visibility
function togglePasswordVisibility(inputId, iconId) {
    const passwordInput = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}