// Particle System
class ParticleSystem {
    constructor() {
        this.canvas = document.getElementById('particleCanvas');
        this.ctx = this.canvas.getContext('2d');
        this.particles = [];
        this.mouse = { x: 0, y: 0, radius: 100 };
        
        this.init();
        this.animate();
    }
    
    init() {
        this.resize();
        window.addEventListener('resize', () => this.resize());
        this.canvas.addEventListener('mousemove', (e) => this.handleMouseMove(e));
        
        // Create particles
        for (let i = 0; i < 50; i++) {
            this.particles.push({
                x: Math.random() * this.canvas.width,
                y: Math.random() * this.canvas.height,
                size: Math.random() * 3 + 1,
                speedX: Math.random() * 1 - 0.5,
                speedY: Math.random() * 1 - 0.5,
                color: `rgba(144, 238, 144, ${Math.random() * 0.3 + 0.1})`
            });
        }
    }
    
    resize() {
        this.canvas.width = window.innerWidth;
        this.canvas.height = window.innerHeight;
    }
    
    handleMouseMove(e) {
        this.mouse.x = e.clientX;
        this.mouse.y = e.clientY;
    }
    
    animate() {
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        
        for (let particle of this.particles) {
            // Update position
            particle.x += particle.speedX;
            particle.y += particle.speedY;
            
            // Bounce off walls
            if (particle.x < 0 || particle.x > this.canvas.width) particle.speedX *= -1;
            if (particle.y < 0 || particle.y > this.canvas.height) particle.speedY *= -1;
            
            // Mouse interaction
            const dx = particle.x - this.mouse.x;
            const dy = particle.y - this.mouse.y;
            const distance = Math.sqrt(dx * dx + dy * dy);
            
            if (distance < this.mouse.radius) {
                const angle = Math.atan2(dy, dx);
                const force = (this.mouse.radius - distance) / this.mouse.radius;
                particle.x += Math.cos(angle) * force * 5;
                particle.y += Math.sin(angle) * force * 5;
            }
            
            // Draw particle
            this.ctx.beginPath();
            this.ctx.arc(particle.x, particle.y, particle.size, 0, Math.PI * 2);
            this.ctx.fillStyle = particle.color;
            this.ctx.fill();
            
            // Draw connections
            for (let otherParticle of this.particles) {
                const dx = particle.x - otherParticle.x;
                const dy = particle.y - otherParticle.y;
                const distance = Math.sqrt(dx * dx + dy * dy);
                
                if (distance < 100) {
                    this.ctx.beginPath();
                    this.ctx.strokeStyle = `rgba(144, 238, 144, ${0.1 * (1 - distance / 100)})`;
                    this.ctx.lineWidth = 0.5;
                    this.ctx.moveTo(particle.x, particle.y);
                    this.ctx.lineTo(otherParticle.x, otherParticle.y);
                    this.ctx.stroke();
                }
            }
        }
        
        requestAnimationFrame(() => this.animate());
    }
}

// Main Application
class LumersFoundation {
    constructor() {
        this.particleSystem = new ParticleSystem();
        this.scholarships = [];
        this.init();
    }
    
    init() {
        this.setupEventListeners();
        this.loadScholarships();
        this.setupAnimations();
        this.setupScrollEffects();
    }
    
    setupEventListeners() {
        // Navigation
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                this.scrollToSection(link.getAttribute('href'));
            });
        });
        
        // Buttons
        document.getElementById('exploreBtn').addEventListener('click', () => {
            this.scrollToSection('#scholarships');
        });
        
        document.getElementById('startApplication').addEventListener('click', () => {
            this.showApplicationModal();
        });
        
        document.getElementById('loginBtn').addEventListener('click', () => {
            this.showLoginModal();
        });
        
        // Filter buttons
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                this.filterScholarships(btn.dataset.filter);
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
            });
        });
        
        // Form submission
        document.querySelector('.mini-application').addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleQuickApply(e.target);
        });
    }
    
    setupAnimations() {
        // Counter animation
        this.animateCounters();
        
        // Intersection Observer for scroll animations
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                }
            });
        }, { threshold: 0.1 });
        
        document.querySelectorAll('.scholarship-card, .timeline-item, .step').forEach(el => {
            observer.observe(el);
        });
    }
    
    setupScrollEffects() {
        let lastScrollY = window.scrollY;
        
        window.addEventListener('scroll', () => {
            const navbar = document.querySelector('.navbar');
            const scrollY = window.scrollY;
            
            // Navbar background
            if (scrollY > 100) {
                navbar.style.background = 'rgba(255, 255, 255, 0.98)';
                navbar.style.boxShadow = '0 5px 20px rgba(0, 0, 0, 0.1)';
            } else {
                navbar.style.background = 'rgba(255, 255, 255, 0.95)';
                navbar.style.boxShadow = 'none';
            }
            
            // Parallax effects
            document.querySelectorAll('.floating-card').forEach((card, index) => {
                const speed = 0.05 + (index * 0.02);
                card.style.transform = `translateY(${scrollY * speed}px)`;
            });
            
            lastScrollY = scrollY;
        });
    }
    
    animateCounters() {
        const counters = document.querySelectorAll('.stat-number');
        
        counters.forEach(counter => {
            const target = parseInt(counter.dataset.count);
            const duration = 2000;
            const step = target / (duration / 16);
            let current = 0;
            
            const updateCounter = () => {
                current += step;
                if (current < target) {
                    counter.textContent = Math.ceil(current);
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.textContent = target;
                }
            };
            
            const observer = new IntersectionObserver((entries) => {
                if (entries[0].isIntersecting) {
                    updateCounter();
                    observer.unobserve(counter);
                }
            });
            
            observer.observe(counter);
        });
    }
    
    async loadScholarships() {
        // Simulate API call
        this.scholarships = [
            {
                id: 1,
                title: "Undergraduate Excellence Scholarship",
                type: "undergraduate",
                amount: "$25,000",
                deadline: "March 15, 2024",
                requirements: ["3.5+ GPA", "Leadership experience", "Community service"],
                description: "Full tuition coverage for outstanding undergraduate students"
            },
            {
                id: 2,
                title: "Graduate Research Fellowship",
                type: "graduate",
                amount: "$50,000",
                deadline: "April 30, 2024",
                requirements: ["Research proposal", "3.7+ GPA", "Faculty recommendation"],
                description: "Support for graduate students pursuing research projects"
            },
            {
                id: 3,
                title: "International Student Grant",
                type: "international",
                amount: "$15,000",
                deadline: "May 1, 2024",
                requirements: ["International student", "Financial need", "Academic merit"],
                description: "Financial assistance for international students"
            },
            {
                id: 4,
                title: "STEM Innovation Award",
                type: "research",
                amount: "$35,000",
                deadline: "June 15, 2024",
                requirements: ["STEM field", "Innovation project", "3.0+ GPA"],
                description: "Support for innovative projects in STEM fields"
            }
        ];
        
        this.renderScholarships();
    }
    
    renderScholarships(filter = 'all') {
        const grid = document.getElementById('scholarshipsGrid');
        const filtered = filter === 'all' 
            ? this.scholarships 
            : this.scholarships.filter(s => s.type === filter);
        
        grid.innerHTML = filtered.map(scholarship => `
            <div class="scholarship-card" data-type="${scholarship.type}">
                <div class="scholarship-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h3 class="scholarship-title">${scholarship.title}</h3>
                <p>${scholarship.description}</p>
                <ul class="scholarship-details">
                    <li><i class="fas fa-money-bill-wave"></i> Award: ${scholarship.amount}</li>
                    <li><i class="fas fa-calendar-alt"></i> Deadline: ${scholarship.deadline}</li>
                    <li><i class="fas fa-tasks"></i> ${scholarship.requirements.length} Requirements</li>
                </ul>
                <div class="scholarship-amount">${scholarship.amount}</div>
                <button class="btn-primary" onclick="lumersFoundation.applyForScholarship(${scholarship.id})">
                    Apply Now
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        `).join('');
    }
    
    filterScholarships(type) {
        this.renderScholarships(type);
    }
    
    scrollToSection(sectionId) {
        const element = document.querySelector(sectionId);
        if (element) {
            const offsetTop = element.offsetTop - 80;
            window.scrollTo({
                top: offsetTop,
                behavior: 'smooth'
            });
        }
    }
    
    applyForScholarship(scholarshipId) {
        const scholarship = this.scholarships.find(s => s.id === scholarshipId);
        this.showApplicationModal(scholarship);
    }
    
    showApplicationModal(scholarship = null) {
        // Create modal HTML
        const modalHTML = `
            <div class="modal-overlay">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>${scholarship ? `Apply for ${scholarship.title}` : 'Start Your Application'}</h3>
                        <button class="modal-close">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form class="application-form">
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text" required>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" required>
                            </div>
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="tel" required>
                            </div>
                            <div class="form-group">
                                <label>Educational Background</label>
                                <textarea rows="3" required></textarea>
                            </div>
                            <div class="form-group">
                                <label>Why should you receive this scholarship?</label>
                                <textarea rows="4" required></textarea>
                            </div>
                            <button type="submit" class="btn-primary btn-large">
                                Submit Application
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        
        // Add event listeners
        document.querySelector('.modal-close').addEventListener('click', this.closeModal);
        document.querySelector('.modal-overlay').addEventListener('click', (e) => {
            if (e.target.classList.contains('modal-overlay')) this.closeModal();
        });
        
        document.querySelector('.application-form').addEventListener('submit', (e) => {
            e.preventDefault();
            this.submitApplication(e.target);
        });
    }
    
    showLoginModal() {
        const modalHTML = `
            <div class="modal-overlay">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Student Portal Login</h3>
                        <button class="modal-close">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form class="login-form">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" required>
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input type="password" required>
                            </div>
                            <button type="submit" class="btn-primary btn-large">
                                Sign In
                                <i class="fas fa-sign-in-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        
        document.querySelector('.modal-close').addEventListener('click', this.closeModal);
        document.querySelector('.modal-overlay').addEventListener('click', (e) => {
            if (e.target.classList.contains('modal-overlay')) this.closeModal();
        });
    }
    
    closeModal() {
        const modal = document.querySelector('.modal-overlay');
        if (modal) {
            modal.style.animation = 'fadeOut 0.3s ease forwards';
            setTimeout(() => modal.remove(), 300);
        }
    }
    
    handleQuickApply(form) {
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);
        
        // Show success message
        this.showNotification('Application started! We will contact you soon.', 'success');
        form.reset();
    }
    
    submitApplication(form) {
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);
        
        // Simulate API call
        setTimeout(() => {
            this.closeModal();
            this.showNotification('Application submitted successfully!', 'success');
        }, 2000);
    }
    
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'}"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => notification.classList.add('show'), 100);
        
        // Remove after 5 seconds
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }
}

// Additional CSS for modals and notifications
const additionalCSS = `
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
        animation: fadeIn 0.3s ease;
    }
    
    .modal-content {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        max-width: 500px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
        animation: slideUp 0.3s ease;
    }
    
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }
    
    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: var(--text-light);
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
    }
    
    .form-group input,
    .form-group textarea,
    .form-group select {
        width: 100%;
        padding: 1rem;
        border: 2px solid var(--primary-light);
        border-radius: 10px;
        font-family: inherit;
        transition: all 0.3s ease;
    }
    
    .form-group input:focus,
    .form-group textarea:focus,
    .form-group select:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(144, 238, 144, 0.1);
    }
    
    .notification {
        position: fixed;
        top: 100px;
        right: 2rem;
        background: white;
        padding: 1rem 1.5rem;
        border-radius: 10px;
        box-shadow: var(--shadow-hover);
        border-left: 4px solid var(--primary-color);
        transform: translateX(400px);
        transition: transform 0.3s ease;
        z-index: 10001;
    }
    
    .notification.show {
        transform: translateX(0);
    }
    
    .notification-content {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .notification.success {
        border-left-color: #27ae60;
    }
    
    .notification.success i {
        color: #27ae60;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes slideUp {
        from { 
            opacity: 0;
            transform: translateY(50px);
        }
        to { 
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes fadeOut {
        from { opacity: 1; }
        to { opacity: 0; }
    }
    
    .scholarship-card,
    .timeline-item,
    .step {
        opacity: 0;
        transform: translateY(30px);
        transition: all 0.6s ease;
    }
    
    .scholarship-card.animate-in,
    .timeline-item.animate-in,
    .step.animate-in {
        opacity: 1;
        transform: translateY(0);
    }
`;

// Inject additional CSS
const style = document.createElement('style');
style.textContent = additionalCSS;
document.head.appendChild(style);

// Initialize application
const lumersFoundation = new LumersFoundation();

// Add some interactive effects
document.addEventListener('DOMContentLoaded', () => {
    // Add hover effects to cards
    document.addEventListener('mousemove', (e) => {
        const cards = document.querySelectorAll('.scholarship-card, .floating-card');
        cards.forEach(card => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            card.style.setProperty('--mouse-x', `${x}px`);
            card.style.setProperty('--mouse-y', `${y}px`);
        });
    });
    
    // Add CSS for mouse follow effect
    const mouseFollowCSS = `
        .scholarship-card,
        .floating-card {
            position: relative;
            overflow: hidden;
        }
        
        .scholarship-card::after,
        .floating-card::after {
            content: '';
            position: absolute;
            top: var(--mouse-y, 50%);
            left: var(--mouse-x, 50%);
            width: 100px;
            height: 100px;
            background: radial-gradient(circle, rgba(144, 238, 144, 0.1) 0%, transparent 70%);
            transform: translate(-50%, -50%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .scholarship-card:hover::after,
        .floating-card:hover::after {
            opacity: 1;
        }
    `;
    
    const mouseStyle = document.createElement('style');
    mouseStyle.textContent = mouseFollowCSS;
    document.head.appendChild(mouseStyle);
});