-- Narshimha Tattoo Studio Database Schema
-- Run this SQL to create the database structure

CREATE DATABASE IF NOT EXISTS narshimha_tattoo;
USE narshimha_tattoo;

-- Artists table
DROP TABLE IF EXISTS artists;
CREATE TABLE artists (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(20),
    specialties TEXT,
    experience_years INT DEFAULT 0,
    bio TEXT,
    image_url VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Service categories table
DROP TABLE IF EXISTS service_categories;
CREATE TABLE service_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    base_price DECIMAL(10,2) DEFAULT 0.00,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Services/Designs table
DROP TABLE IF EXISTS services;
CREATE TABLE services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    size_info VARCHAR(100),
    image_url VARCHAR(255),
    estimated_duration INT DEFAULT 60, -- minutes
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES service_categories(id) ON DELETE SET NULL
);

-- Bookings table
DROP TABLE IF EXISTS bookings;
CREATE TABLE bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    booking_reference VARCHAR(20) UNIQUE NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    preferred_artist_id INT,
    service_id INT,
    tattoo_style VARCHAR(50),
    description TEXT NOT NULL,
    preferred_date DATE,
    preferred_time TIME,
    status ENUM('pending', 'confirmed', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    total_amount DECIMAL(10,2) DEFAULT 0.00,
    deposit_amount DECIMAL(10,2) DEFAULT 0.00,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (preferred_artist_id) REFERENCES artists(id) ON DELETE SET NULL,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL
);

-- Cart sessions table (for temporary cart storage)
DROP TABLE IF EXISTS cart_sessions;
CREATE TABLE cart_sessions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    session_id VARCHAR(100) NOT NULL,
    service_id INT NOT NULL,
    quantity INT DEFAULT 1,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP DEFAULT (CURRENT_TIMESTAMP + INTERVAL 24 HOUR),
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE,
    INDEX idx_session_id (session_id),
    INDEX idx_expires_at (expires_at)
);

-- Contact messages table
DROP TABLE IF EXISTS contact_messages;
CREATE TABLE contact_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(200),
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'replied') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Booking status history
DROP TABLE IF EXISTS booking_status_history;
CREATE TABLE booking_status_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id INT NOT NULL,
    old_status VARCHAR(20),
    new_status VARCHAR(20) NOT NULL,
    notes TEXT,
    changed_by VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
);

-- Insert default artists (only if table is empty)
INSERT IGNORE INTO artists (name, email, specialties, experience_years, bio, image_url) VALUES
('Marcus Steel', 'marcus@narshimhatattoo.com', 'Blackwork, Realism, Portraits', 10, 'With over 10 years of experience, Marcus specializes in bold blackwork and photorealistic portraits. His attention to detail and ability to capture emotion in his work has made him one of the most sought-after artists in the city.', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'),
('Luna Rose', 'luna@narshimhatattoo.com', 'Watercolor, Floral, Abstract', 8, 'Luna brings a unique artistic vision to the tattoo world with her watercolor techniques and delicate floral designs. Her 8 years of experience have established her as a master of color and flow.', 'https://images.unsplash.com/photo-1494790108755-2616b612b786?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'),
('Jake Thunder', 'jake@narshimhatattoo.com', 'Traditional, Neo-Traditional, Bold Color', 12, 'A traditionalist at heart, Jake has been perfecting the art of American Traditional and Neo-Traditional tattoos for over 12 years. His bold lines and vibrant colors are instantly recognizable.', 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'),
('Aria Ink', 'aria@narshimhatattoo.com', 'Minimalist, Geometric, Fine Line', 6, 'Aria specializes in clean, minimalist designs and precise geometric patterns. Her 6 years of experience have made her the go-to artist for those seeking elegant, understated tattoos.', 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80');

-- Insert service categories
INSERT IGNORE INTO service_categories (name, slug, description, base_price) VALUES
('Blackwork Tattoos', 'blackwork', 'Bold, striking designs in pure black ink', 120.00),
('Realistic Tattoos', 'realism', 'Lifelike portraits and photorealistic art', 200.00),
('Minimal Tattoos', 'minimal', 'Clean, simple, and elegant designs', 80.00),
('Traditional Tattoos', 'traditional', 'Classic American traditional style', 150.00),
('Watercolor Tattoos', 'watercolor', 'Vibrant, flowing artistic designs', 180.00),
('Custom Designs', 'custom', 'Unique designs created just for you', 250.00);

-- Insert sample services/designs
INSERT IGNORE INTO services (category_id, name, description, price, size_info, image_url, estimated_duration) VALUES
-- Blackwork
(1, 'Geometric Mandala', 'Intricate geometric mandala design', 120.00, 'Small (2-3 inches)', 'https://images.unsplash.com/photo-1565058379802-bbe93b2f703a?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', 90),
(1, 'Tribal Pattern', 'Traditional tribal pattern with modern twist', 180.00, 'Medium (4-6 inches)', 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', 120),
(1, 'Abstract Lines', 'Abstract flowing line work', 150.00, 'Medium (4-5 inches)', 'https://images.unsplash.com/photo-1590736969955-71cc94901144?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', 100),
(1, 'Minimalist Symbol', 'Simple but powerful symbol', 80.00, 'Small (1-2 inches)', 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', 60),
(1, 'Ornamental Design', 'Detailed ornamental pattern', 220.00, 'Large (6-8 inches)', 'https://images.unsplash.com/photo-1594736797933-d0401ba2fe65?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', 180),
(1, 'Gothic Script', 'Beautiful gothic lettering', 160.00, 'Medium (3-5 inches)', 'https://images.unsplash.com/photo-1611195974226-ef16ab4e4c8d?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', 90),

-- Realism
(2, 'Portrait', 'Photorealistic portrait', 300.00, 'Medium (4-6 inches)', 'https://images.unsplash.com/photo-1611501275019-9b5cda994e8d?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', 240),
(2, 'Animal Portrait', 'Realistic animal portrait', 250.00, 'Medium (4-5 inches)', 'https://images.unsplash.com/photo-1594736797933-d0401ba2fe65?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', 200),
(2, 'Nature Scene', 'Detailed nature landscape', 350.00, 'Large (6-8 inches)', 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', 300),
(2, 'Flower Realism', 'Realistic flower design', 200.00, 'Medium (3-5 inches)', 'https://images.unsplash.com/photo-1590736969955-71cc94901144?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', 150),

-- Minimal
(3, 'Line Art', 'Simple line art design', 80.00, 'Small (2-3 inches)', 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', 60),
(3, 'Geometric Shape', 'Clean geometric shape', 100.00, 'Small (2-4 inches)', 'https://images.unsplash.com/photo-1590736969955-71cc94901144?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', 75),
(3, 'Single Word', 'Elegant typography', 90.00, 'Small (1-3 inches)', 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', 60),
(3, 'Small Symbol', 'Meaningful small symbol', 70.00, 'Tiny (1-2 inches)', 'https://images.unsplash.com/photo-1594736797933-d0401ba2fe65?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', 45),

-- Traditional
(4, 'Traditional Rose', 'Classic traditional rose', 150.00, 'Medium (3-5 inches)', 'https://images.unsplash.com/photo-1598300042247-d088f8ab3a91?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', 120),
(4, 'Sailor Jerry Style', 'Vintage sailor style design', 180.00, 'Medium (4-6 inches)', 'https://images.unsplash.com/photo-1611195974226-ef16ab4e4c8d?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', 150),
(4, 'Traditional Eagle', 'Bold traditional eagle', 220.00, 'Large (5-7 inches)', 'https://images.unsplash.com/photo-1565058379802-bbe93b2f703a?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', 180),
(4, 'Pin-up Girl', 'Classic pin-up style', 200.00, 'Medium (4-6 inches)', 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', 160),

-- Watercolor
(5, 'Watercolor Flower', 'Flowing watercolor flower', 180.00, 'Medium (3-5 inches)', 'https://images.unsplash.com/photo-1590736969955-71cc94901144?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', 140),
(5, 'Abstract Splash', 'Colorful abstract splash', 200.00, 'Medium (4-6 inches)', 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', 160),
(5, 'Watercolor Bird', 'Artistic watercolor bird', 220.00, 'Medium (4-6 inches)', 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', 180),
(5, 'Galaxy Design', 'Cosmic watercolor galaxy', 250.00, 'Large (5-7 inches)', 'https://images.unsplash.com/photo-1594736797933-d0401ba2fe65?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', 200),

-- Custom
(6, 'Custom Portrait', 'Personalized portrait design', 350.00, 'Medium-Large (4-7 inches)', 'https://images.unsplash.com/photo-1611501275019-9b5cda994e8d?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', 300),
(6, 'Custom Symbol', 'Unique personal symbol', 250.00, 'Medium (3-5 inches)', 'https://images.unsplash.com/photo-1565058379802-bbe93b2f703a?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', 180),
(6, 'Custom Sleeve Element', 'Custom sleeve component', 400.00, 'Large (6-10 inches)', 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', 360),
(6, 'Memorial Design', 'Personalized memorial tattoo', 300.00, 'Medium (4-6 inches)', 'https://images.unsplash.com/photo-1590736969955-71cc94901144?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', 240);

-- Create indexes for better performance
CREATE INDEX idx_bookings_email ON bookings(email);
CREATE INDEX idx_bookings_status ON bookings(status);
CREATE INDEX idx_bookings_date ON bookings(preferred_date);
CREATE INDEX idx_services_category ON services(category_id);
CREATE INDEX idx_services_active ON services(is_active);
CREATE INDEX idx_cart_session ON cart_sessions(session_id);

-- User roles and permissions
DROP TABLE IF EXISTS user_roles;
CREATE TABLE user_roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    permissions JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admin users table
DROP TABLE IF EXISTS admin_users;
CREATE TABLE admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role_id INT,
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES user_roles(id) ON DELETE SET NULL
);

-- Website content management
DROP TABLE IF EXISTS website_content;
CREATE TABLE website_content (
    id INT PRIMARY KEY AUTO_INCREMENT,
    section VARCHAR(100) NOT NULL,
    content_key VARCHAR(100) NOT NULL,
    content_value TEXT,
    content_type ENUM('text', 'html', 'json', 'image') DEFAULT 'text',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_section_key (section, content_key)
);

-- FAQs table
DROP TABLE IF EXISTS faqs;
CREATE TABLE faqs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    question TEXT NOT NULL,
    answer TEXT NOT NULL,
    category VARCHAR(100),
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Testimonials table
DROP TABLE IF EXISTS testimonials;
CREATE TABLE testimonials (
    id INT PRIMARY KEY AUTO_INCREMENT,
    client_name VARCHAR(100) NOT NULL,
    client_email VARCHAR(100),
    rating INT DEFAULT 5 CHECK (rating >= 1 AND rating <= 5),
    testimonial_text TEXT NOT NULL,
    image_url VARCHAR(255),
    is_featured BOOLEAN DEFAULT FALSE,
    is_approved BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Gallery images table
DROP TABLE IF EXISTS gallery_images;
CREATE TABLE gallery_images (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200),
    description TEXT,
    image_url VARCHAR(255) NOT NULL,
    thumbnail_url VARCHAR(255),
    category VARCHAR(100),
    tags TEXT,
    display_order INT DEFAULT 0,
    is_featured BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Media files table
DROP TABLE IF EXISTS media_files;
CREATE TABLE media_files (
    id INT PRIMARY KEY AUTO_INCREMENT,
    original_name VARCHAR(255) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INT NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    file_type ENUM('image', 'video', 'document') NOT NULL,
    alt_text VARCHAR(255),
    description TEXT,
    uploaded_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES admin_users(id) ON DELETE SET NULL
);

-- Analytics table
DROP TABLE IF EXISTS analytics;
CREATE TABLE analytics (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_type VARCHAR(100) NOT NULL,
    event_data JSON,
    user_agent TEXT,
    ip_address VARCHAR(45),
    referrer VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_event_type (event_type),
    INDEX idx_created_at (created_at)
);

-- Social media links
DROP TABLE IF EXISTS social_media;
CREATE TABLE social_media (
    id INT PRIMARY KEY AUTO_INCREMENT,
    platform VARCHAR(50) NOT NULL,
    url VARCHAR(500) NOT NULL,
    icon_class VARCHAR(100),
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default user roles
INSERT IGNORE INTO user_roles (name, description, permissions) VALUES
('super_admin', 'Full system access', '{"all": true}'),
('admin', 'Full content and booking management', '{"content": true, "bookings": true, "artists": true, "analytics": true}'),
('moderator', 'Limited content management', '{"content": true, "bookings": true, "artists": false, "analytics": false}');

-- Insert default admin user
INSERT IGNORE INTO admin_users (username, email, password_hash, role_id) VALUES
('admin', 'admin@narshimhatattoo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- Insert default website content
INSERT IGNORE INTO website_content (section, content_key, content_value, content_type) VALUES
('hero', 'title', 'Narshimha Tattoo Studio', 'text'),
('hero', 'subtitle', 'Where Art Meets Skin', 'text'),
('hero', 'description', 'Professional tattoo services with the highest standards of safety and artistry', 'text'),
('hero', 'background_image', 'img/hero-bg.png', 'image'),
('about', 'title', 'About Our Studio', 'text'),
('about', 'description', 'We are passionate artists dedicated to creating unique, meaningful tattoos that tell your story.', 'text'),
('contact', 'phone', '(555) 123-TATT', 'text'),
('contact', 'email', 'info@narshimhatattoo.com', 'text'),
('contact', 'address', '123 Ink Street, Art District, City 12345', 'text'),
('contact', 'hours', 'Mon-Sat: 10AM-8PM, Sun: 12PM-6PM', 'text');

-- Insert default FAQs
INSERT IGNORE INTO faqs (question, answer, category, display_order) VALUES
('How much does a tattoo cost?', 'Tattoo prices vary based on size, complexity, and design. We offer free consultations to provide accurate pricing.', 'Pricing', 1),
('Is it safe to get a tattoo?', 'Yes, we follow strict health and safety protocols including single-use needles, medical-grade sterilization, and proper hygiene practices.', 'Safety', 2),
('How long does the healing process take?', 'Initial healing takes 2-3 weeks, with complete healing taking 4-6 weeks. We provide detailed aftercare instructions.', 'Aftercare', 3),
('Can I bring my own design?', 'Absolutely! We welcome custom designs and can work with you to create something unique.', 'Design', 4),
('Do you offer touch-ups?', 'Yes, we offer free touch-ups within 6 months of your original appointment.', 'Service', 5);

-- Insert default testimonials
INSERT IGNORE INTO testimonials (client_name, rating, testimonial_text, is_approved, is_featured) VALUES
('Sarah Johnson', 5, 'Amazing experience! The artist was professional and the tattoo exceeded my expectations.', TRUE, TRUE),
('Mike Chen', 5, 'Clean studio, great atmosphere, and incredible artwork. Highly recommend!', TRUE, TRUE),
('Emma Davis', 5, 'The attention to detail was outstanding. I love my new tattoo!', TRUE, FALSE);

-- Insert default social media
INSERT IGNORE INTO social_media (platform, url, icon_class, display_order) VALUES
('Instagram', 'https://instagram.com/narshimhatattoo', 'fab fa-instagram', 1),
('Facebook', 'https://facebook.com/narshimhatattoo', 'fab fa-facebook', 2),
('Twitter', 'https://twitter.com/narshimhatattoo', 'fab fa-twitter', 3),
('TikTok', 'https://tiktok.com/@narshimhatattoo', 'fab fa-tiktok', 4);

-- Create additional indexes for better performance
CREATE INDEX idx_website_content_section ON website_content(section);
CREATE INDEX idx_faqs_category ON faqs(category);
CREATE INDEX idx_testimonials_approved ON testimonials(is_approved);
CREATE INDEX idx_gallery_category ON gallery_images(category);
CREATE INDEX idx_analytics_event_type ON analytics(event_type);
CREATE INDEX idx_analytics_created_at ON analytics(created_at);

-- Clean up expired cart sessions (run this periodically)
-- DELETE FROM cart_sessions WHERE expires_at < NOW();

