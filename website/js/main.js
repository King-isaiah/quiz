// Main JavaScript for Lumos Website
class LumosWebsite {
    constructor() {
        this.init();
    }
    
    init() {
        this.setupNavigation();
        this.setupSmoothScroll();
        this.setupVideoBackgrounds();
        this.setupScrollAnimations();
        this.setupMobileMenu();
        this.setupParallaxEffects();
    }
    
    setupNavigation() {
        const navbar = document.querySelector('.navbar');
        let lastScrollY = window.scrollY;
        
        window.addEventListener('scroll', () => {
            const scrollY = window.scrollY;
            
            // Navbar background effect
            if (scrollY > 100) {
                navbar.style.background = 'rgba(255, 255, 255, 0.98)';
                navbar.style.boxShadow = '0 5px 20px rgba(0, 0, 0, 0.1)';
            } else {
                navbar.style.background = 'rgba(255, 255, 255, 0.98)';
                navbar.style.boxShadow = 'none';
            }
            
            // Update active nav link
            this.updateActiveNavLink();
            
            lastScrollY = scrollY;
        });
        
        // Set initial active link
        this.updateActiveNavLink();
    }
    
    updateActiveNavLink() {
        const sections = document.querySelectorAll('section');
        const navLinks = document.querySelectorAll('.nav-link');
        
        let current = '';
        
        sections.forEach(section => {
            const sectionTop = section.offsetTop - 100;
            const sectionHeight = section.clientHeight;
            
            if (window.scrollY >= sectionTop && window.scrollY < sectionTop + sectionHeight) {
                current = section.getAttribute('id');
            }
        });
        
        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === `#${current}`) {
                link.classList.add('active');
            }
        });
    }
    
    setupSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });
                    
                    // Close mobile menu if open
                    document.querySelector('.nav-menu').classList.remove('active');
                }
            });
        });
    }
    
    setupVideoBackgrounds() {
        // Handle video loading and fallback
        const videos = document.querySelectorAll('.bg-video');
        
        videos.forEach(video => {
            video.addEventListener('error', () => {
                // If video fails to load, show the fallback image
                const fallback = video.querySelector('img');
                if (fallback) {
                    video.style.display = 'none';
                    fallback.style.display = 'block';
                    fallback.style.width = '100%';
                    fallback.style.height = '100%';
                    fallback.style.objectFit = 'cover';
                }
            });
            
            // Ensure video plays inline on mobile
            video.setAttribute('playsinline', '');
            video.setAttribute('muted', '');
            video.setAttribute('autoplay', '');
            video.setAttribute('loop', '');
        });
    }
    
    setupScrollAnimations() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                }
            });
        }, { threshold: 0.1 });
        
        // Observe elements for animation
        document.querySelectorAll('.program-card, .team-member, .testimonial-card, .mission-card').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'all 0.6s ease';
            
            observer.observe(el);
        });
        
        // Custom animation class
        const style = document.createElement('style');
        style.textContent = `
            .animate-in {
                opacity: 1 !important;
                transform: translateY(0) !important;
            }
        `;
        document.head.appendChild(style);
    }
    
    setupMobileMenu() {
        const menuToggle = document.querySelector('.menu-toggle');
        const navMenu = document.querySelector('.nav-menu');
        
        if (menuToggle) {
            menuToggle.addEventListener('click', () => {
                navMenu.classList.toggle('active');
                menuToggle.classList.toggle('active');
            });
            
            // Close menu when clicking outside
            document.addEventListener('click', (e) => {
                if (!navMenu.contains(e.target) && !menuToggle.contains(e.target)) {
                    navMenu.classList.remove('active');
                    menuToggle.classList.remove('active');
                }
            });
        }
    }
    
    setupParallaxEffects() {
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            
            // Parallax for mission section
            const missionSection = document.querySelector('.mission-section .section-content');
            if (missionSection) {
                const rate = scrolled * -0.3;
                missionSection.style.transform = `translateY(${rate}px)`;
            }
            
            // Parallax for team cards
            const teamCards = document.querySelectorAll('.team-member');
            teamCards.forEach((card, index) => {
                const speed = 0.05 * (index + 1);
                const yPos = -(scrolled * speed);
                card.style.transform = `translateY(${yPos}px)`;
            });
        });
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new LumosWebsite();
    
    // Add some interactive hover effects
    const cards = document.querySelectorAll('.program-card, .team-member, .testimonial-card');
    
    cards.forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            card.style.setProperty('--mouse-x', `${x}px`);
            card.style.setProperty('--mouse-y', `${y}px`);
        });
    });
    
    // Add CSS for mouse follow effect
    const mouseStyle = document.createElement('style');
    mouseStyle.textContent = `
        .program-card,
        .team-member,
        .testimonial-card {
            position: relative;
            overflow: hidden;
        }
        
        .program-card::before,
        .team-member::before,
        .testimonial-card::before {
            content: '';
            position: absolute;
            top: var(--mouse-y, 50%);
            left: var(--mouse-x, 50%);
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(144, 238, 144, 0.1) 0%, transparent 70%);
            transform: translate(-50%, -50%);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 1;
            pointer-events: none;
        }
        
        .program-card:hover::before,
        .team-member:hover::before,
        .testimonial-card:hover::before {
            opacity: 1;
        }
    `;
    document.head.appendChild(mouseStyle);
});