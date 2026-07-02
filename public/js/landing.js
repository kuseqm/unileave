/* ============================================
   UNILEAVE LANDING PAGE - MAIN SCRIPTS
   Version: 1.0
   Date: May 4, 2026
   ============================================ */

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    
    // Initialize AOS (Animate on Scroll)
    AOS.init({
        duration: 800,
        once: true,
        offset: 100
    });
    
    // Navbar scroll effect
    const navbar = document.getElementById('navbar');
    
    if (navbar) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    }
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            
            if (targetId === '#') return;
            
            const target = document.querySelector(targetId);
            if (target) {
                const offset = 80; // Fixed header height offset
                const elementPosition = target.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - offset;
                
                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Video play button
    const videoPlayBtn = document.querySelector('.video-play-btn');
    const videoWrapper = document.querySelector('.video-wrapper');
    
    if (videoPlayBtn && videoWrapper) {
        videoPlayBtn.addEventListener('click', function() {
            // Replace thumbnail with actual video embed
            const videoHtml = '<iframe width="100%" height="400" src="https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="width:100%; height:400px;"></iframe>';
            videoWrapper.innerHTML = videoHtml;
        });
    }
    
    // Add active class to nav links on scroll
    const sections = document.querySelectorAll('section[id]');
    
    if (sections.length > 0) {
        window.addEventListener('scroll', function() {
            let current = '';
            const scrollPosition = window.scrollY + 100;
            
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                
                if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
                    current = section.getAttribute('id');
                }
            });
            
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === `#${current}`) {
                    link.classList.add('active');
                }
            });
        });
    }
    
    // Stats counter animation (optional)
    const statNumbers = document.querySelectorAll('.stat-number');
    
    if (statNumbers.length > 0) {
        const animateNumbers = () => {
            statNumbers.forEach(stat => {
                const target = parseInt(stat.getAttribute('data-target') || stat.innerText.replace(/[^0-9]/g, ''));
                if (!target) return;
                
                let current = 0;
                const increment = target / 50;
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        stat.innerText = target.toLocaleString() + (stat.innerText.includes('+') ? '+' : '');
                        clearInterval(timer);
                    } else {
                        stat.innerText = Math.floor(current).toLocaleString() + (stat.innerText.includes('+') ? '+' : '');
                    }
                }, 30);
            });
        };
        
        // Trigger when stats section is in view
        const statsSection = document.querySelector('.stats');
        if (statsSection) {
            let animated = false;
            
            window.addEventListener('scroll', () => {
                const sectionPosition = statsSection.getBoundingClientRect().top;
                const screenPosition = window.innerHeight;
                
                if (sectionPosition < screenPosition && !animated) {
                    animateNumbers();
                    animated = true;
                }
            });
        }
    }
});