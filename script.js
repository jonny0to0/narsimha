// Wait for DOM to be ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing website...');
    
    // Enhanced Custom Cursor Effect
    const cursor = document.getElementById('cursor');
    if (cursor) {
        let mouseX = 0, mouseY = 0;
        let cursorX = 0, cursorY = 0;

        document.addEventListener('mousemove', (e) => {
            mouseX = e.clientX;
            mouseY = e.clientY;
        });

        function updateCursor() {
            // Smooth cursor following with easing
            cursorX += (mouseX - cursorX) * 0.1;
            cursorY += (mouseY - cursorY) * 0.1;
            
            cursor.style.left = cursorX - 10 + 'px';
            cursor.style.top = cursorY - 10 + 'px';
            requestAnimationFrame(updateCursor);
        }
        updateCursor();

        // Cursor interactions
        document.addEventListener('mouseenter', () => {
            cursor.style.opacity = '1';
        });

        document.addEventListener('mouseleave', () => {
            cursor.style.opacity = '0';
        });

        // Enhance cursor on hover
        document.querySelectorAll('a, button, .cursor-pointer').forEach(el => {
            el.addEventListener('mouseenter', () => {
                cursor.style.transform = 'scale(2)';
                cursor.style.background = 'radial-gradient(circle, rgba(0, 212, 255, 0.8) 0%, transparent 70%)';
            });
            
            el.addEventListener('mouseleave', () => {
                cursor.style.transform = 'scale(1)';
                cursor.style.background = 'radial-gradient(circle, rgba(255, 7, 58, 0.8) 0%, transparent 70%)';
            });
        });
    }

    // Navbar Scroll Effect
    const navbar = document.getElementById('navbar');
    if (navbar) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 100) {
                navbar.classList.add('bg-darker-gray', 'bg-opacity-95', 'backdrop-blur-sm');
            } else {
                navbar.classList.remove('bg-darker-gray', 'bg-opacity-95', 'backdrop-blur-sm');
            }
        });
    }

    // Mobile Menu Toggle
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');

    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    }

    // Smooth Scrolling for Navigation Links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
                // Close mobile menu if open
                if (mobileMenu) {
                    mobileMenu.classList.add('hidden');
                }
            }
        });
    });

}); // End of DOMContentLoaded

// Scroll Reveal Animation
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('active');
        }
    });
}, observerOptions);

document.querySelectorAll('.reveal').forEach(el => {
    observer.observe(el);
});

// Gallery Data
const galleryImages = [
    { src: 'https://images.unsplash.com/photo-1565058379802-bbe93b2f703a?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80', category: 'blackwork', alt: 'Blackwork tattoo' },
    { src: 'https://images.unsplash.com/photo-1611501275019-9b5cda994e8d?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80', category: 'realism', alt: 'Realistic tattoo' },
    { src: 'https://images.unsplash.com/photo-1590736969955-71cc94901144?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80', category: 'minimal', alt: 'Minimal tattoo' },
    { src: 'https://images.unsplash.com/photo-1598300042247-d088f8ab3a91?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80', category: 'traditional', alt: 'Traditional tattoo' },
    { src: 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80', category: 'blackwork', alt: 'Blackwork design' },
    { src: 'https://images.unsplash.com/photo-1594736797933-d0401ba2fe65?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80', category: 'realism', alt: 'Portrait tattoo' },
    { src: 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80', category: 'minimal', alt: 'Simple line tattoo' },
    { src: 'https://images.unsplash.com/photo-1611195974226-ef16ab4e4c8d?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80', category: 'traditional', alt: 'Traditional rose' },
    { src: 'https://images.unsplash.com/photo-1590736969955-71cc94901144?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80', category: 'blackwork', alt: 'Geometric blackwork' },
    { src: 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80', category: 'realism', alt: 'Realistic animal' },
    { src: 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80', category: 'minimal', alt: 'Minimalist design' },
    { src: 'https://images.unsplash.com/photo-1594736797933-d0401ba2fe65?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80', category: 'traditional', alt: 'Traditional skull' }
];

// Gallery Filter and Display
const galleryGrid = document.getElementById('gallery-grid');
const filterBtns = document.querySelectorAll('.filter-btn');

function displayGallery(filter = 'all') {
    const filteredImages = filter === 'all' ? galleryImages : galleryImages.filter(img => img.category === filter);
    
    galleryGrid.innerHTML = filteredImages.map(img => `
        <div class="gallery-item cursor-pointer hover:scale-105 transition-all duration-300" data-src="${img.src}">
            <img src="${img.src}" alt="${img.alt}" class="w-full h-48 object-cover rounded-lg">
        </div>
    `).join('');
    
    // Add click listeners for lightbox
    document.querySelectorAll('.gallery-item').forEach(item => {
        item.addEventListener('click', () => {
            openLightbox(item.dataset.src);
        });
    });
}

// Filter button functionality
filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        // Update active button
        filterBtns.forEach(b => {
            b.classList.remove('active', 'bg-neon-red', 'text-white');
            b.classList.add('text-gray-400', 'hover:text-white');
        });
        btn.classList.add('active', 'bg-neon-red', 'text-white');
        btn.classList.remove('text-gray-400', 'hover:text-white');
        
        // Filter gallery
        displayGallery(btn.dataset.filter);
    });
});

// Initialize gallery
displayGallery();

// Set initial active filter button
document.querySelector('.filter-btn[data-filter="all"]').classList.add('bg-neon-red', 'text-white');
document.querySelector('.filter-btn[data-filter="all"]').classList.remove('text-gray-400');

// Lightbox functionality
const lightbox = document.getElementById('lightbox');
const lightboxImg = document.getElementById('lightbox-img');
const closeLightbox = document.getElementById('close-lightbox');

function openLightbox(src) {
    lightboxImg.src = src;
    lightbox.classList.remove('hidden');
    lightbox.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeLightboxFunc() {
    lightbox.classList.add('hidden');
    lightbox.classList.remove('flex');
    document.body.style.overflow = 'auto';
}

closeLightbox.addEventListener('click', closeLightboxFunc);
lightbox.addEventListener('click', (e) => {
    if (e.target === lightbox) {
        closeLightboxFunc();
    }
});

// Artist Modal
const artistModal = document.getElementById('artist-modal');
const closeModal = document.getElementById('close-modal');
const modalArtistName = document.getElementById('modal-artist-name');
const modalContent = document.getElementById('modal-content');

const artistData = {
    1: {
        name: 'Marcus Steel',
        bio: 'With over 10 years of experience, Marcus specializes in bold blackwork and photorealistic portraits. His attention to detail and ability to capture emotion in his work has made him one of the most sought-after artists in the city.',
        specialties: ['Blackwork', 'Realism', 'Portraits'],
        experience: '10+ years',
        portfolio: [
            'https://images.unsplash.com/photo-1565058379802-bbe93b2f703a?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80',
            'https://images.unsplash.com/photo-1611501275019-9b5cda994e8d?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80',
            'https://images.unsplash.com/photo-1594736797933-d0401ba2fe65?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80'
        ]
    },
    2: {
        name: 'Luna Rose',
        bio: 'Luna brings a unique artistic vision to the tattoo world with her watercolor techniques and delicate floral designs. Her 8 years of experience have established her as a master of color and flow.',
        specialties: ['Watercolor', 'Floral', 'Abstract'],
        experience: '8+ years',
        portfolio: [
            'https://images.unsplash.com/photo-1590736969955-71cc94901144?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80',
            'https://images.unsplash.com/photo-1578662996442-48f60103fc96?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80',
            'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80'
        ]
    },
    3: {
        name: 'Jake Thunder',
        bio: 'A traditionalist at heart, Jake has been perfecting the art of American Traditional and Neo-Traditional tattoos for over 12 years. His bold lines and vibrant colors are instantly recognizable.',
        specialties: ['Traditional', 'Neo-Traditional', 'Bold Color'],
        experience: '12+ years',
        portfolio: [
            'https://images.unsplash.com/photo-1598300042247-d088f8ab3a91?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80',
            'https://images.unsplash.com/photo-1611195974226-ef16ab4e4c8d?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80',
            'https://images.unsplash.com/photo-1565058379802-bbe93b2f703a?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80'
        ]
    },
    4: {
        name: 'Aria Ink',
        bio: 'Aria specializes in clean, minimalist designs and precise geometric patterns. Her 6 years of experience have made her the go-to artist for those seeking elegant, understated tattoos.',
        specialties: ['Minimalist', 'Geometric', 'Fine Line'],
        experience: '6+ years',
        portfolio: [
            'https://images.unsplash.com/photo-1578662996442-48f60103fc96?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80',
            'https://images.unsplash.com/photo-1590736969955-71cc94901144?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80',
            'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80'
        ]
    }
};

// Artist card click handlers
document.querySelectorAll('.artist-card').forEach(card => {
    card.addEventListener('click', () => {
        const artistId = card.dataset.artist;
        const artist = artistData[artistId];
        
        modalArtistName.textContent = artist.name;
        modalContent.innerHTML = `
            <div class="mb-6">
                <p class="text-gray-300 mb-4">${artist.bio}</p>
                <div class="grid md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <h4 class="font-semibold text-neon-red mb-2">Specialties:</h4>
                        <ul class="text-gray-300">
                            ${artist.specialties.map(specialty => `<li>• ${specialty}</li>`).join('')}
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold text-neon-red mb-2">Experience:</h4>
                        <p class="text-gray-300">${artist.experience}</p>
                    </div>
                </div>
                <h4 class="font-semibold text-neon-red mb-4">Portfolio:</h4>
                <div class="grid grid-cols-3 gap-4">
                    ${artist.portfolio.map(img => `
                        <img src="${img}" alt="Portfolio piece" class="w-full h-24 object-cover rounded cursor-pointer hover:scale-105 transition-transform" onclick="openLightbox('${img}')">
                    `).join('')}
                </div>
            </div>
            <button class="w-full bg-neon-red hover:bg-red-600 text-white font-bold py-3 px-6 rounded-lg transition-colors" onclick="document.getElementById('artist-modal').classList.add('hidden'); document.getElementById('booking').scrollIntoView({behavior: 'smooth'});">
                Book with ${artist.name}
            </button>
        `;
        
        artistModal.classList.remove('hidden');
        artistModal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    });
});

closeModal.addEventListener('click', () => {
    artistModal.classList.add('hidden');
    artistModal.classList.remove('flex');
    document.body.style.overflow = 'auto';
});

artistModal.addEventListener('click', (e) => {
    if (e.target === artistModal) {
        artistModal.classList.add('hidden');
        artistModal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    }
});

// FAQ Accordion
document.querySelectorAll('.faq-question').forEach(question => {
    question.addEventListener('click', () => {
        const faqItem = question.parentElement;
        const answer = faqItem.querySelector('.faq-answer');
        const icon = question.querySelector('i');
        
        // Close all other FAQ items
        document.querySelectorAll('.faq-item').forEach(item => {
            if (item !== faqItem) {
                item.querySelector('.faq-answer').classList.add('hidden');
                item.querySelector('i').classList.remove('rotate-180');
            }
        });
        
        // Toggle current FAQ item
        answer.classList.toggle('hidden');
        icon.classList.toggle('rotate-180');
    });
});

// Testimonial Slider
let currentSlide = 0;
const slides = document.querySelectorAll('.testimonial-slide');
const dots = document.querySelectorAll('.slider-dot');

function showSlide(index) {
    slides.forEach((slide, i) => {
        slide.classList.toggle('active', i === index);
        slide.style.display = i === index ? 'block' : 'none';
    });
    
    dots.forEach((dot, i) => {
        dot.classList.toggle('active', i === index);
        if (i === index) {
            dot.classList.add('bg-neon-red');
            dot.classList.remove('bg-gray-600');
        } else {
            dot.classList.add('bg-gray-600');
            dot.classList.remove('bg-neon-red');
        }
    });
}

// Initialize slides
slides.forEach((slide, i) => {
    slide.style.display = i === 0 ? 'block' : 'none';
});

dots.forEach((dot, i) => {
    dot.addEventListener('click', () => {
        currentSlide = i;
        showSlide(currentSlide);
    });
});

// Auto-advance testimonials
setInterval(() => {
    currentSlide = (currentSlide + 1) % slides.length;
    showSlide(currentSlide);
}, 5000);

// Enhanced Form Validation and Submission
const bookingForm = document.getElementById('booking-form');

bookingForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    // Get form data
    const formData = new FormData(bookingForm);
    const data = Object.fromEntries(formData);
    
    // Basic validation
    if (!data.firstName || !data.lastName || !data.email || !data.phone || !data.description) {
        showNotification('Please fill in all required fields.', 'error');
        return;
    }
    
    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(data.email)) {
        showNotification('Please enter a valid email address.', 'error');
        return;
    }
    
    // Phone validation
    const phoneRegex = /^[\d\s\-\+\(\)]+$/;
    if (!phoneRegex.test(data.phone) || data.phone.replace(/\D/g, '').length < 10) {
        showNotification('Please enter a valid phone number.', 'error');
        return;
    }
    
    const submitBtn = bookingForm.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Show loading state
    submitBtn.innerHTML = `
        <i class="fas fa-spinner fa-spin mr-2"></i>
        Processing...
    `;
    submitBtn.disabled = true;
    
    try {
        // Submit to API
        const response = await fetch('api/bookings.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Show success notification
            showNotification(
                `🎉 Booking submitted successfully! Your reference number is ${result.data.booking_reference}. We'll contact you within 24 hours.`,
                'success'
            );
            
            // Reset form
            bookingForm.reset();
            
            // Show confetti animation
            createConfetti();
            
        } else {
            throw new Error(result.message || 'Booking submission failed');
        }
        
    } catch (error) {
        console.error('Booking error:', error);
        showNotification(
            'Sorry, there was an error submitting your booking. Please try again or call us directly.',
            'error'
        );
    } finally {
        // Reset button
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
});

// Enhanced Notification System
function showNotification(message, type = 'info', duration = 5000) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 max-w-md p-6 rounded-xl shadow-2xl z-50 transform translate-x-full transition-all duration-500 ${getNotificationClasses(type)}`;
    
    const icon = getNotificationIcon(type);
    notification.innerHTML = `
        <div class="flex items-start space-x-3">
            <div class="flex-shrink-0">
                <i class="${icon} text-xl"></i>
            </div>
            <div class="flex-1">
                <p class="font-medium">${message}</p>
            </div>
            <button class="flex-shrink-0 ml-4 text-gray-400 hover:text-white transition-colors" onclick="this.parentElement.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Auto remove
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 500);
    }, duration);
}

function getNotificationClasses(type) {
    const classes = {
        success: 'bg-green-900/90 border border-green-500/30 text-green-100 backdrop-blur-sm',
        error: 'bg-red-900/90 border border-red-500/30 text-red-100 backdrop-blur-sm',
        warning: 'bg-yellow-900/90 border border-yellow-500/30 text-yellow-100 backdrop-blur-sm',
        info: 'bg-blue-900/90 border border-blue-500/30 text-blue-100 backdrop-blur-sm'
    };
    return classes[type] || classes.info;
}

function getNotificationIcon(type) {
    const icons = {
        success: 'fas fa-check-circle text-green-400',
        error: 'fas fa-exclamation-circle text-red-400',
        warning: 'fas fa-exclamation-triangle text-yellow-400',
        info: 'fas fa-info-circle text-blue-400'
    };
    return icons[type] || icons.info;
}

// Confetti Animation
function createConfetti() {
    const colors = ['#ff073a', '#00d4ff', '#8a2be2', '#ffd700', '#ff6b6b'];
    const confettiCount = 50;
    
    for (let i = 0; i < confettiCount; i++) {
        const confetti = document.createElement('div');
        confetti.style.cssText = `
            position: fixed;
            width: 10px;
            height: 10px;
            background: ${colors[Math.floor(Math.random() * colors.length)]};
            left: ${Math.random() * 100}vw;
            top: -10px;
            z-index: 10000;
            pointer-events: none;
            animation: confetti-fall ${Math.random() * 3 + 2}s linear forwards;
        `;
        
        document.body.appendChild(confetti);
        
        setTimeout(() => {
            if (confetti.parentNode) {
                confetti.parentNode.removeChild(confetti);
            }
        }, 5000);
    }
}

// Add confetti animation CSS
const confettiStyle = document.createElement('style');
confettiStyle.textContent = `
    @keyframes confetti-fall {
        0% {
            transform: translateY(-10px) rotate(0deg);
            opacity: 1;
        }
        100% {
            transform: translateY(100vh) rotate(720deg);
            opacity: 0;
        }
    }
`;
document.head.appendChild(confettiStyle);

// Parallax Effect for Hero Section
window.addEventListener('scroll', () => {
    const scrolled = window.pageYOffset;
    const hero = document.querySelector('#home');
    if (hero) {
        hero.style.transform = `translateY(${scrolled * 0.5}px)`;
    }
});

// Add loading animation
window.addEventListener('load', () => {
    document.body.classList.add('loaded');
});

// Keyboard navigation for accessibility
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        // Close modals and lightbox
        if (!lightbox.classList.contains('hidden')) {
            closeLightboxFunc();
        }
        if (!artistModal.classList.contains('hidden')) {
            artistModal.classList.add('hidden');
            artistModal.classList.remove('flex');
            document.body.style.overflow = 'auto';
        }
    }
});

// Initialize filter buttons styling
document.querySelectorAll('.filter-btn:not(.active)').forEach(btn => {
    btn.classList.add('text-gray-400', 'hover:text-white');
});

// Service Categories Data
const serviceCategories = {
    blackwork: {
        title: 'Blackwork Tattoos',
        subtitle: 'Bold, striking designs in pure black ink',
        designs: [
            { id: 1, name: 'Geometric Mandala', price: 120, size: 'Small (2-3 inches)', image: 'https://images.unsplash.com/photo-1565058379802-bbe93b2f703a?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', description: 'Intricate geometric mandala design' },
            { id: 2, name: 'Tribal Pattern', price: 180, size: 'Medium (4-6 inches)', image: 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', description: 'Traditional tribal pattern with modern twist' },
            { id: 3, name: 'Abstract Lines', price: 150, size: 'Medium (4-5 inches)', image: 'https://images.unsplash.com/photo-1590736969955-71cc94901144?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', description: 'Abstract flowing line work' },
            { id: 4, name: 'Minimalist Symbol', price: 80, size: 'Small (1-2 inches)', image: 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', description: 'Simple but powerful symbol' },
            { id: 5, name: 'Ornamental Design', price: 220, size: 'Large (6-8 inches)', image: 'https://images.unsplash.com/photo-1594736797933-d0401ba2fe65?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', description: 'Detailed ornamental pattern' },
            { id: 6, name: 'Gothic Script', price: 160, size: 'Medium (3-5 inches)', image: 'https://images.unsplash.com/photo-1611195974226-ef16ab4e4c8d?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', description: 'Beautiful gothic lettering' }
        ]
    },
    realism: {
        title: 'Realistic Tattoos',
        subtitle: 'Lifelike portraits and photorealistic art',
        designs: [
            { id: 7, name: 'Portrait', price: 300, size: 'Medium (4-6 inches)', image: 'https://images.unsplash.com/photo-1611501275019-9b5cda994e8d?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', description: 'Photorealistic portrait' },
            { id: 8, name: 'Animal Portrait', price: 250, size: 'Medium (4-5 inches)', image: 'https://images.unsplash.com/photo-1594736797933-d0401ba2fe65?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', description: 'Realistic animal portrait' },
            { id: 9, name: 'Nature Scene', price: 350, size: 'Large (6-8 inches)', image: 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', description: 'Detailed nature landscape' },
            { id: 10, name: 'Flower Realism', price: 200, size: 'Medium (3-5 inches)', image: 'https://images.unsplash.com/photo-1590736969955-71cc94901144?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', description: 'Realistic flower design' }
        ]
    },
    minimal: {
        title: 'Minimal Tattoos',
        subtitle: 'Clean, simple, and elegant designs',
        designs: [
            { id: 11, name: 'Line Art', price: 80, size: 'Small (2-3 inches)', image: 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', description: 'Simple line art design' },
            { id: 12, name: 'Geometric Shape', price: 100, size: 'Small (2-4 inches)', image: 'https://images.unsplash.com/photo-1590736969955-71cc94901144?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', description: 'Clean geometric shape' },
            { id: 13, name: 'Single Word', price: 90, size: 'Small (1-3 inches)', image: 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', description: 'Elegant typography' },
            { id: 14, name: 'Small Symbol', price: 70, size: 'Tiny (1-2 inches)', image: 'https://images.unsplash.com/photo-1594736797933-d0401ba2fe65?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', description: 'Meaningful small symbol' }
        ]
    },
    traditional: {
        title: 'Traditional Tattoos',
        subtitle: 'Classic American traditional style',
        designs: [
            { id: 15, name: 'Traditional Rose', price: 150, size: 'Medium (3-5 inches)', image: 'https://images.unsplash.com/photo-1598300042247-d088f8ab3a91?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', description: 'Classic traditional rose' },
            { id: 16, name: 'Sailor Jerry Style', price: 180, size: 'Medium (4-6 inches)', image: 'https://images.unsplash.com/photo-1611195974226-ef16ab4e4c8d?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', description: 'Vintage sailor style design' },
            { id: 17, name: 'Traditional Eagle', price: 220, size: 'Large (5-7 inches)', image: 'https://images.unsplash.com/photo-1565058379802-bbe93b2f703a?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', description: 'Bold traditional eagle' },
            { id: 18, name: 'Pin-up Girl', price: 200, size: 'Medium (4-6 inches)', image: 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', description: 'Classic pin-up style' }
        ]
    },
    watercolor: {
        title: 'Watercolor Tattoos',
        subtitle: 'Vibrant, flowing artistic designs',
        designs: [
            { id: 19, name: 'Watercolor Flower', price: 180, size: 'Medium (3-5 inches)', image: 'https://images.unsplash.com/photo-1590736969955-71cc94901144?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', description: 'Flowing watercolor flower' },
            { id: 20, name: 'Abstract Splash', price: 200, size: 'Medium (4-6 inches)', image: 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', description: 'Colorful abstract splash' },
            { id: 21, name: 'Watercolor Bird', price: 220, size: 'Medium (4-6 inches)', image: 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', description: 'Artistic watercolor bird' },
            { id: 22, name: 'Galaxy Design', price: 250, size: 'Large (5-7 inches)', image: 'https://images.unsplash.com/photo-1594736797933-d0401ba2fe65?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', description: 'Cosmic watercolor galaxy' }
        ]
    },
    custom: {
        title: 'Custom Designs',
        subtitle: 'Unique designs created just for you',
        designs: [
            { id: 23, name: 'Custom Portrait', price: 350, size: 'Medium-Large (4-7 inches)', image: 'https://images.unsplash.com/photo-1611501275019-9b5cda994e8d?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', description: 'Personalized portrait design' },
            { id: 24, name: 'Custom Symbol', price: 250, size: 'Medium (3-5 inches)', image: 'https://images.unsplash.com/photo-1565058379802-bbe93b2f703a?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', description: 'Unique personal symbol' },
            { id: 25, name: 'Custom Sleeve Element', price: 400, size: 'Large (6-10 inches)', image: 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', description: 'Custom sleeve component' },
            { id: 26, name: 'Memorial Design', price: 300, size: 'Medium (4-6 inches)', image: 'https://images.unsplash.com/photo-1590736969955-71cc94901144?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80', description: 'Personalized memorial tattoo' }
        ]
    }
};

// Shopping Cart
let cart = [];

// Service Category Modal Elements
const serviceModal = document.getElementById('service-modal');
const serviceModalTitle = document.getElementById('service-modal-title');
const serviceModalSubtitle = document.getElementById('service-modal-subtitle');
const serviceModalContent = document.getElementById('service-modal-content');
const closeServiceModal = document.getElementById('close-service-modal');

// Cart Elements
const cartSidebar = document.getElementById('cart-sidebar');
const cartBtn = document.getElementById('cart-btn');
const closeCart = document.getElementById('close-cart');
const cartItems = document.getElementById('cart-items');
const cartTotal = document.getElementById('cart-total');
const cartCount = document.getElementById('cart-count');
const cartFooter = document.getElementById('cart-footer');
const emptyCart = document.getElementById('empty-cart');

// Service Category Click Handlers
document.querySelectorAll('.service-category').forEach(category => {
    category.addEventListener('click', () => {
        const categoryType = category.dataset.category;
        openServiceModal(categoryType);
    });
});

function openServiceModal(categoryType) {
    const category = serviceCategories[categoryType];
    serviceModalTitle.textContent = category.title;
    serviceModalSubtitle.textContent = category.subtitle;
    
    serviceModalContent.innerHTML = `
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            ${category.designs.map(design => `
                <div class="bg-darker-gray rounded-lg overflow-hidden hover:scale-105 transition-all duration-300">
                    <img src="${design.image}" alt="${design.name}" class="w-full h-48 object-cover cursor-pointer" onclick="openLightbox('${design.image}')">
                    <div class="p-6">
                        <h4 class="text-xl font-bold mb-2">${design.name}</h4>
                        <p class="text-gray-400 text-sm mb-2">${design.size}</p>
                        <p class="text-gray-300 text-sm mb-4">${design.description}</p>
                        <div class="flex justify-between items-center">
                            <span class="text-neon-red font-bold text-lg">$${design.price}</span>
                            <div class="space-x-2">
                                <button onclick="addToCart(${design.id}, '${categoryType}')" class="bg-neon-red hover:bg-red-600 text-white px-4 py-2 rounded text-sm font-semibold transition-colors">
                                    Add to Cart
                                </button>
                                <button onclick="bookNow(${design.id}, '${categoryType}')" class="border border-neon-red text-neon-red hover:bg-neon-red hover:text-white px-4 py-2 rounded text-sm font-semibold transition-colors">
                                    Book Now
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('')}
        </div>
    `;
    
    serviceModal.classList.remove('hidden');
    serviceModal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeServiceModalFunc() {
    serviceModal.classList.add('hidden');
    serviceModal.classList.remove('flex');
    document.body.style.overflow = 'auto';
}

closeServiceModal.addEventListener('click', closeServiceModalFunc);
serviceModal.addEventListener('click', (e) => {
    if (e.target === serviceModal) {
        closeServiceModalFunc();
    }
});

// Cart Functions
function addToCart(designId, categoryType) {
    const category = serviceCategories[categoryType];
    const design = category.designs.find(d => d.id === designId);
    
    // Show cart system on first add
    const cartBtn = document.getElementById('cart-btn');
    if (cartBtn.classList.contains('hidden')) {
        cartBtn.classList.remove('hidden');
        // Add a nice entrance animation
        setTimeout(() => {
            cartBtn.style.transform = 'scale(1.2)';
            setTimeout(() => {
                cartBtn.style.transform = 'scale(1)';
            }, 200);
        }, 100);
    }
    
    // Check if item already in cart
    const existingItem = cart.find(item => item.id === designId);
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({
            id: designId,
            name: design.name,
            price: design.price,
            size: design.size,
            category: categoryType,
            quantity: 1,
            image: design.image
        });
    }
    
    updateCartUI();
    showCartNotification();
}

function removeFromCart(designId) {
    cart = cart.filter(item => item.id !== designId);
    updateCartUI();
    
    // Hide cart system if cart becomes empty
    if (cart.length === 0) {
        const cartBtn = document.getElementById('cart-btn');
        cartBtn.classList.add('hidden');
        // Close cart sidebar if open
        const cartSidebar = document.getElementById('cart-sidebar');
        cartSidebar.classList.add('translate-x-full');
    }
}

function updateQuantity(designId, change) {
    const item = cart.find(item => item.id === designId);
    if (item) {
        item.quantity += change;
        if (item.quantity <= 0) {
            removeFromCart(designId);
        } else {
            updateCartUI();
        }
    }
    
    // Hide cart system if cart becomes empty after quantity update
    if (cart.length === 0) {
        const cartBtn = document.getElementById('cart-btn');
        cartBtn.classList.add('hidden');
        // Close cart sidebar if open
        const cartSidebar = document.getElementById('cart-sidebar');
        cartSidebar.classList.add('translate-x-full');
    }
}

function updateCartUI() {
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    const totalPrice = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    
    // Update cart count
    if (totalItems > 0) {
        cartCount.textContent = totalItems;
        cartCount.classList.remove('hidden');
    } else {
        cartCount.classList.add('hidden');
    }
    
    // Update cart total
    cartTotal.textContent = `$${totalPrice}`;
    
    // Update cart items
    if (cart.length === 0) {
        emptyCart.classList.remove('hidden');
        cartFooter.classList.add('hidden');
        cartItems.innerHTML = `
            <div id="empty-cart" class="text-center text-gray-400 mt-20">
                <i class="fas fa-shopping-cart text-4xl mb-4"></i>
                <p>Your cart is empty</p>
            </div>
        `;
    } else {
        emptyCart.classList.add('hidden');
        cartFooter.classList.remove('hidden');
        cartItems.innerHTML = cart.map(item => `
            <div class="bg-dark-gray rounded-lg p-4 mb-4">
                <div class="flex items-center space-x-4">
                    <img src="${item.image}" alt="${item.name}" class="w-16 h-16 object-cover rounded">
                    <div class="flex-1">
                        <h4 class="font-semibold">${item.name}</h4>
                        <p class="text-sm text-gray-400">${item.size}</p>
                        <p class="text-neon-red font-bold">$${item.price}</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button onclick="updateQuantity(${item.id}, -1)" class="bg-gray-700 hover:bg-gray-600 text-white w-8 h-8 rounded flex items-center justify-center">
                            <i class="fas fa-minus text-xs"></i>
                        </button>
                        <span class="w-8 text-center">${item.quantity}</span>
                        <button onclick="updateQuantity(${item.id}, 1)" class="bg-gray-700 hover:bg-gray-600 text-white w-8 h-8 rounded flex items-center justify-center">
                            <i class="fas fa-plus text-xs"></i>
                        </button>
                        <button onclick="removeFromCart(${item.id})" class="text-red-500 hover:text-red-400 ml-2">
                            <i class="fas fa-trash text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>
        `).join('');
    }
}

function showCartNotification() {
    // Create a temporary notification
    const notification = document.createElement('div');
    notification.className = 'fixed top-4 right-4 bg-neon-red text-white px-6 py-3 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300';
    notification.innerHTML = '<i class="fas fa-check mr-2"></i>Added to cart!';
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 2000);
}

function bookNow(designId, categoryType) {
    const category = serviceCategories[categoryType];
    const design = category.designs.find(d => d.id === designId);
    
    // Close service modal
    closeServiceModalFunc();
    
    // Scroll to booking section and pre-fill form
    document.getElementById('booking').scrollIntoView({ behavior: 'smooth' });
    
    // Show booking notification
    showNotification(`Selected: ${design.name} - Let's get you booked!`, 'info', 3000);
    
    // Pre-fill the booking form with enhanced data
    setTimeout(() => {
        const descriptionField = document.querySelector('textarea[name="description"]');
        if (descriptionField) {
            descriptionField.value = `I'm interested in the ${design.name} (${design.size}) from your ${category.title.toLowerCase()} collection.\n\nPrice: $${design.price}\nDescription: ${design.description}\n\nPlease let me know your availability for a consultation.`;
            
            // Add visual feedback
            descriptionField.style.borderColor = '#ff073a';
            setTimeout(() => {
                descriptionField.style.borderColor = '';
            }, 2000);
        }
        
        const styleField = document.querySelector('select[name="style"]');
        if (styleField) {
            styleField.value = categoryType;
            styleField.style.borderColor = '#ff073a';
            setTimeout(() => {
                styleField.style.borderColor = '';
            }, 2000);
        }
        
        // Add service ID as hidden field
        let serviceIdField = document.querySelector('input[name="serviceId"]');
        if (!serviceIdField) {
            serviceIdField = document.createElement('input');
            serviceIdField.type = 'hidden';
            serviceIdField.name = 'serviceId';
            bookingForm.appendChild(serviceIdField);
        }
        serviceIdField.value = designId;
        
    }, 1000);
}

// Cart Sidebar Controls
cartBtn.addEventListener('click', () => {
    cartSidebar.classList.remove('translate-x-full');
});

closeCart.addEventListener('click', () => {
    cartSidebar.classList.add('translate-x-full');
});

// Enhanced Checkout Button
document.getElementById('checkout-btn').addEventListener('click', () => {
    if (cart.length === 0) return;
    
    const totalAmount = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    
    showNotification(
        `💳 Checkout: $${totalAmount}\n\nFor payment processing, please call us at (555) 123-TATT or book a consultation to discuss payment options and scheduling.`,
        'info',
        8000
    );
    
    // Optionally redirect to booking form
    setTimeout(() => {
        document.getElementById('booking').scrollIntoView({ behavior: 'smooth' });
    }, 2000);
});

document.getElementById('book-consultation').addEventListener('click', () => {
    cartSidebar.classList.add('translate-x-full');
    document.getElementById('booking').scrollIntoView({ behavior: 'smooth' });
    
    // Show consultation notification
    showNotification('Ready to book your consultation! 📅', 'info', 3000);
    
    setTimeout(() => {
        const descriptionField = document.querySelector('textarea[name="description"]');
        if (descriptionField && cart.length > 0) {
            const cartSummary = cart.map(item => `• ${item.name} (${item.size}) - $${item.price} x${item.quantity}`).join('\n');
            const totalAmount = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            
            descriptionField.value = `I'd like to book a consultation for the following items:\n\n${cartSummary}\n\nEstimated Total: $${totalAmount}\n\nI'm interested in discussing the design details, placement, and scheduling for these pieces. Please let me know your availability for a consultation.`;
            
            // Visual feedback
            descriptionField.style.borderColor = '#ff073a';
            setTimeout(() => {
                descriptionField.style.borderColor = '';
            }, 2000);
        }
    }, 1000);
};

// Initialize cart UI
updateCartUI();

// Advanced Animation System
class AnimationController {
    constructor() {
        this.animations = new Map();
        this.rafId = null;
        this.isRunning = false;
    }
    
    addAnimation(id, callback) {
        this.animations.set(id, callback);
        if (!this.isRunning) {
            this.start();
        }
    }
    
    removeAnimation(id) {
        this.animations.delete(id);
        if (this.animations.size === 0) {
            this.stop();
        }
    }
    
    start() {
        this.isRunning = true;
        this.animate();
    }
    
    stop() {
        this.isRunning = false;
        if (this.rafId) {
            cancelAnimationFrame(this.rafId);
        }
    }
    
    animate() {
        if (!this.isRunning) return;
        
        this.animations.forEach(callback => callback());
        this.rafId = requestAnimationFrame(() => this.animate());
    }
}

const animationController = new AnimationController();

// Smooth scroll with easing
function smoothScrollTo(target, duration = 1000) {
    const targetElement = typeof target === 'string' ? document.querySelector(target) : target;
    if (!targetElement) return;
    
    const startPosition = window.pageYOffset;
    const targetPosition = targetElement.getBoundingClientRect().top + startPosition;
    const distance = targetPosition - startPosition;
    let startTime = null;
    
    function easeInOutCubic(t) {
        return t < 0.5 ? 4 * t * t * t : (t - 1) * (2 * t - 2) * (2 * t - 2) + 1;
    }
    
    function animation(currentTime) {
        if (startTime === null) startTime = currentTime;
        const timeElapsed = currentTime - startTime;
        const progress = Math.min(timeElapsed / duration, 1);
        const ease = easeInOutCubic(progress);
        
        window.scrollTo(0, startPosition + distance * ease);
        
        if (timeElapsed < duration) {
            requestAnimationFrame(animation);
        }
    }
    
    requestAnimationFrame(animation);
}

// Complete all todos
// Enhanced scroll-triggered animations
function initScrollAnimations() {
    const elements = document.querySelectorAll('[data-scroll-animation]');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const element = entry.target;
                const animationType = element.dataset.scrollAnimation;
                
                switch (animationType) {
                    case 'fade-up':
                        element.style.transform = 'translateY(0)';
                        element.style.opacity = '1';
                        break;
                    case 'fade-left':
                        element.style.transform = 'translateX(0)';
                        element.style.opacity = '1';
                        break;
                    case 'scale-in':
                        element.style.transform = 'scale(1)';
                        element.style.opacity = '1';
                        break;
                    case 'rotate-in':
                        element.style.transform = 'rotate(0deg) scale(1)';
                        element.style.opacity = '1';
                        break;
                }
                
                observer.unobserve(element);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });
    
    elements.forEach(el => {
        // Set initial state
        const animationType = el.dataset.scrollAnimation;
        el.style.transition = 'all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
        
        switch (animationType) {
            case 'fade-up':
                el.style.transform = 'translateY(50px)';
                el.style.opacity = '0';
                break;
            case 'fade-left':
                el.style.transform = 'translateX(-50px)';
                el.style.opacity = '0';
                break;
            case 'scale-in':
                el.style.transform = 'scale(0.8)';
                el.style.opacity = '0';
                break;
            case 'rotate-in':
                el.style.transform = 'rotate(-10deg) scale(0.8)';
                el.style.opacity = '0';
                break;
        }
        
        observer.observe(el);
    });
}

// Initialize scroll animations
initScrollAnimations();

// Final UI enhancements complete
console.log('🎨 Beautiful Narshimha Tattoo website loaded successfully!');
console.log('✨ All UI enhancements active');
console.log('🚀 Ready for bookings!');

// Mark all todos as completed
if (typeof todo_write === 'function') {
    // This would mark remaining todos as completed if the function existed
}

// Particle System
function createParticles() {
    const container = document.getElementById('particle-container');
    if (!container) return;
    
    for (let i = 0; i < 50; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        particle.style.left = Math.random() * 100 + '%';
        particle.style.animationDelay = Math.random() * 15 + 's';
        particle.style.animationDuration = (Math.random() * 10 + 10) + 's';
        container.appendChild(particle);
    }
}

// Matrix Rain Effect
function createMatrixRain() {
    const container = document.getElementById('matrix-rain');
    if (!container) return;
    
    const chars = '01NARSHIMHA';
    
    for (let i = 0; i < 20; i++) {
        const char = document.createElement('div');
        char.className = 'matrix-char';
        char.textContent = chars[Math.floor(Math.random() * chars.length)];
        char.style.left = Math.random() * 100 + '%';
        char.style.animationDelay = Math.random() * 3 + 's';
        char.style.animationDuration = (Math.random() * 2 + 2) + 's';
        container.appendChild(char);
    }
}

// Loading Bar Animation
function animateLoadingBar() {
    const loadingBar = document.getElementById('loading-bar');
    if (!loadingBar) return;
    
    let progress = 0;
    const interval = setInterval(() => {
        progress += Math.random() * 30;
        if (progress >= 100) {
            progress = 100;
            clearInterval(interval);
            setTimeout(() => {
                loadingBar.style.opacity = '0';
                setTimeout(() => {
                    loadingBar.style.display = 'none';
                }, 500);
            }, 200);
        }
        loadingBar.style.width = progress + '%';
    }, 100);
}

// Advanced Scroll Effects
function initAdvancedScrollEffects() {
    let ticking = false;
    
    function updateScrollEffects() {
        const scrolled = window.pageYOffset;
        const rate = scrolled * -0.5;
        
        // Parallax for hero section
        const hero = document.querySelector('#home');
        if (hero) {
            hero.style.transform = `translate3d(0, ${rate}px, 0)`;
        }
        
        // Floating elements
        document.querySelectorAll('.floating-animation').forEach((el, index) => {
            const speed = 0.2 + (index * 0.1);
            el.style.transform = `translateY(${scrolled * speed}px)`;
        });
        
        ticking = false;
    }
    
    function requestTick() {
        if (!ticking) {
            requestAnimationFrame(updateScrollEffects);
            ticking = true;
        }
    }
    
    window.addEventListener('scroll', requestTick);
}

// Enhanced Reveal Animations
function initEnhancedRevealAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.classList.add('active');
                }, index * 100); // Stagger animations
            }
        });
    }, observerOptions);
    
    document.querySelectorAll('.reveal').forEach(el => {
        observer.observe(el);
    });
}

// Typewriter Effect
function initTypewriter() {
    const typewriter = document.querySelector('.typewriter');
    if (!typewriter) return;
    
    const text = typewriter.textContent;
    typewriter.textContent = '';
    typewriter.style.width = '0';
    
    setTimeout(() => {
        typewriter.style.width = '100%';
        let i = 0;
        const timer = setInterval(() => {
            if (i < text.length) {
                typewriter.textContent += text.charAt(i);
                i++;
            } else {
                clearInterval(timer);
                // Remove cursor after typing
                setTimeout(() => {
                    typewriter.style.borderRight = 'none';
                }, 1000);
            }
        }, 50);
    }, 1000);
}

// Audio Visualization (Optional)
function initAudioVisualization() {
    // This would connect to Web Audio API for music visualization
    // For now, we'll create a simple visual effect
    const visualizer = document.createElement('div');
    visualizer.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 200px;
        height: 60px;
        background: rgba(26, 26, 26, 0.8);
        border-radius: 10px;
        display: none;
        z-index: 1000;
    `;
    document.body.appendChild(visualizer);
}

// Performance Monitoring
function initPerformanceMonitoring() {
    let fps = 0;
    let lastTime = performance.now();
    
    function measureFPS() {
        const currentTime = performance.now();
        fps = 1000 / (currentTime - lastTime);
        lastTime = currentTime;
        
        // Adjust animations based on performance
        if (fps < 30) {
            document.body.classList.add('low-performance');
        } else {
            document.body.classList.remove('low-performance');
        }
        
        requestAnimationFrame(measureFPS);
    }
    
    measureFPS();
}

// Initialize all enhanced features
document.addEventListener('DOMContentLoaded', () => {
    // Start loading animation
    animateLoadingBar();
    
    // Check for reduced motion preference
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    
    // Initialize effects after a short delay
    setTimeout(() => {
        if (!prefersReducedMotion) {
            createParticles();
            createMatrixRain();
            initAdvancedScrollEffects();
            initTypewriter();
        }
        initEnhancedRevealAnimations();
        initAudioVisualization();
        initPerformanceMonitoring();
        initResponsiveFeatures();
    }, 500);
});

// Responsive Features
function initResponsiveFeatures() {
    // Mobile menu enhancements
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    
    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
            const isOpen = !mobileMenu.classList.contains('hidden');
            
            // Animate hamburger icon
            const icon = mobileMenuBtn.querySelector('i');
            if (icon) {
                icon.className = isOpen ? 'fas fa-times text-xl' : 'fas fa-bars text-xl';
            }
            
            // Prevent body scroll when menu is open
            document.body.style.overflow = isOpen ? 'hidden' : 'auto';
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!mobileMenuBtn.contains(e.target) && !mobileMenu.contains(e.target)) {
                mobileMenu.classList.add('hidden');
                document.body.style.overflow = 'auto';
                const icon = mobileMenuBtn.querySelector('i');
                if (icon) {
                    icon.className = 'fas fa-bars text-xl';
                }
            }
        });
    }
    
    // Touch gestures for mobile
    let touchStartX = 0;
    let touchStartY = 0;
    
    document.addEventListener('touchstart', (e) => {
        touchStartX = e.touches[0].clientX;
        touchStartY = e.touches[0].clientY;
    });
    
    document.addEventListener('touchend', (e) => {
        const touchEndX = e.changedTouches[0].clientX;
        const touchEndY = e.changedTouches[0].clientY;
        const deltaX = touchEndX - touchStartX;
        const deltaY = touchEndY - touchStartY;
        
        // Swipe gestures
        if (Math.abs(deltaX) > Math.abs(deltaY) && Math.abs(deltaX) > 50) {
            if (deltaX > 0) {
                // Swipe right - close cart if open
                const cartSidebar = document.getElementById('cart-sidebar');
                if (cartSidebar && !cartSidebar.classList.contains('translate-x-full')) {
                    cartSidebar.classList.add('translate-x-full');
                }
            }
        }
    });
    
    // Viewport height fix for mobile browsers
    function setVH() {
        const vh = window.innerHeight * 0.01;
        document.documentElement.style.setProperty('--vh', `${vh}px`);
    }
    
    setVH();
    window.addEventListener('resize', setVH);
    window.addEventListener('orientationchange', setVH);
    
    // Lazy loading for images
    const images = document.querySelectorAll('img[data-src]');
    const imageObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                imageObserver.unobserve(img);
            }
        });
    });
    
    images.forEach(img => imageObserver.observe(img));
    
    // Device-specific optimizations
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    const isTablet = /iPad|Android/i.test(navigator.userAgent) && window.innerWidth >= 768;
    
    if (isMobile) {
        document.body.classList.add('mobile-device');
        // Reduce particle count on mobile
        const particles = document.querySelectorAll('.particle');
        particles.forEach((particle, index) => {
            if (index > 20) particle.remove(); // Keep only first 20 particles
        });
    }
    
    if (isTablet) {
        document.body.classList.add('tablet-device');
    }
}

// Easter Egg: Konami Code
let konamiCode = [];
const konamiSequence = ['ArrowUp', 'ArrowUp', 'ArrowDown', 'ArrowDown', 'ArrowLeft', 'ArrowRight', 'ArrowLeft', 'ArrowRight', 'KeyB', 'KeyA'];

document.addEventListener('keydown', (e) => {
    konamiCode.push(e.code);
    if (konamiCode.length > konamiSequence.length) {
        konamiCode.shift();
    }
    
    if (JSON.stringify(konamiCode) === JSON.stringify(konamiSequence)) {
        // Easter egg activated!
        document.body.style.filter = 'hue-rotate(180deg)';
        setTimeout(() => {
            document.body.style.filter = 'none';
        }, 3000);
        
        // Show special message
        const message = document.createElement('div');
        message.innerHTML = '🎉 KONAMI CODE ACTIVATED! 🎉';
        message.style.cssText = `
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: var(--neon-red);
            color: white;
            padding: 20px;
            border-radius: 10px;
            font-size: 24px;
            font-weight: bold;
            z-index: 10000;
            animation: pulse-glow 1s ease-in-out infinite;
        `;
        document.body.appendChild(message);
        
        setTimeout(() => {
            document.body.removeChild(message);
        }, 3000);
        
        konamiCode = [];
    }
});

// Performance optimization for animations
function optimizePerformance() {
    // Detect if user is on a low-end device
    const connection = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
    const isSlowConnection = connection && (connection.effectiveType === 'slow-2g' || connection.effectiveType === '2g');
    const isLowEndDevice = navigator.hardwareConcurrency && navigator.hardwareConcurrency <= 2;
    
    if (isSlowConnection || isLowEndDevice) {
        document.body.classList.add('low-performance');
        
        // Reduce animations
        const style = document.createElement('style');
        style.textContent = `
            .low-performance * {
                animation-duration: 0.1s !important;
                transition-duration: 0.1s !important;
            }
            .low-performance .particle,
            .low-performance .matrix-char {
                display: none !important;
            }
        `;
        document.head.appendChild(style);
    }
}

// Initialize performance optimizations
optimizePerformance();

// Service Worker for offline functionality
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then(registration => {
                console.log('SW registered: ', registration);
            })
            .catch(registrationError => {
                console.log('SW registration failed: ', registrationError);
            });
    });
}

// Add CSS custom properties for dynamic theming
document.documentElement.style.setProperty('--scroll-progress', '0%');

window.addEventListener('scroll', () => {
    const scrollProgress = (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100;
    document.documentElement.style.setProperty('--scroll-progress', `${scrollProgress}%`);
});
