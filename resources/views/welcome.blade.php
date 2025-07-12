<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChocolateCraft - Artisan Chocolate Makers</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Georgia', serif;
            line-height: 1.6;
            color: #2c1810;
            overflow-x: hidden;
        }

        /* Header */
        header {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(139, 69, 19, 0.95);
            backdrop-filter: blur(10px);
            z-index: 1000;
            padding: 1rem 0;
            transition: all 0.3s ease;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: #f4e4c1;
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        .nav-links a {
            color: #f4e4c1;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: #d4af37;
        }

        .menu-toggle {
            display: none;
            flex-direction: column;
            cursor: pointer;
        }

        .menu-toggle span {
            width: 25px;
            height: 3px;
            background: #f4e4c1;
            margin: 3px 0;
            transition: 0.3s;
        }

        /* Hero Section */
        .hero {
            height: 100vh;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hero-slider {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        .slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 1.5s ease-in-out;
            overflow: hidden;
        }

        .slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            /* Optimized for landscape images */
        }

        /* Ensure landscape images fill the hero section properly */
        @media (min-width: 768px) {
            .slide img {
                object-fit: cover;
                object-position: center center;
            }
        }

        /* Mobile optimization for landscape images */
        @media (max-width: 767px) {
            .slide img {
                object-fit: cover;
                object-position: center center;
            }
        }

        .slide.active {
            opacity: 1;
        }

        .slide::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(44, 24, 16, 0.6);
            z-index: 1;
        }

        /* Chocolate images for slider */
        .slide:nth-child(1) {
            background-image: url('https://images.unsplash.com/photo-1511381939415-e44015466834?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2940&q=80');
        }

        .slide:nth-child(2) {
            background-image: url('https://images.unsplash.com/photo-1549007953-2f2dc0b24019?ixlib=rb-4.0.3&ixid=M3wxMJA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2942&q=80');
        }

        .slide:nth-child(3) {
            background-image: url('https://images.unsplash.com/photo-1580915411954-282cb1b0d780?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2942&q=80');
        }

        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.3);
            z-index: 2;
        }

        .hero-content {
            position: relative;
            z-index: 3;
            color: #f4e4c1;
            max-width: 800px;
            text-align: center;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .slider-nav {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 15px;
            z-index: 4;
        }

        .slider-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(244, 228, 193, 0.5);
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid rgba(244, 228, 193, 0.8);
        }

        .slider-dot.active {
            background: #d4af37;
            border-color: #d4af37;
            transform: scale(1.2);
        }

        .slider-arrows {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 4;
        }

        .slider-arrow {
            background: rgba(244, 228, 193, 0.2);
            border: 2px solid rgba(244, 228, 193, 0.6);
            color: #f4e4c1;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1.2rem;
            backdrop-filter: blur(10px);
        }

        .slider-arrow:hover {
            background: rgba(212, 175, 55, 0.3);
            border-color: #d4af37;
            transform: scale(1.1);
        }

        .slider-arrow.prev {
            left: 30px;
        }

        .slider-arrow.next {
            right: 30px;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .hero h1 {
            font-size: 4rem;
            margin-bottom: 1rem;
            animation: fadeInUp 1s ease-out;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            animation: fadeInUp 1s ease-out 0.3s both;
        }

        .cta-button {
            display: inline-block;
            background: linear-gradient(45deg, #d4af37, #f4e4c1);
            color: #2c1810;
            padding: 1rem 2rem;
            text-decoration: none;
            border-radius: 50px;
            font-weight: bold;
            transition: all 0.3s ease;
            animation: fadeInUp 1s ease-out 0.6s both;
            box-shadow: 0 10px 30px rgba(212, 175, 55, 0.3);
        }

        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(212, 175, 55, 0.4);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* About Section */
        .about {
            padding: 5rem 2rem;
            background: #f4e4c1;
            text-align: center;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .about h2 {
            font-size: 3rem;
            margin-bottom: 2rem;
            color: #8b4513;
        }

        .about p {
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto 3rem;
            color: #5d2e0a;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .feature {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .feature:hover {
            transform: translateY(-10px);
        }

        .feature-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .feature h3 {
            color: #8b4513;
            margin-bottom: 1rem;
        }

        /* Products Section */
        .products {
            padding: 5rem 2rem;
            background: linear-gradient(135deg, #2c1810 0%, #5d2e0a 100%);
            color: #f4e4c1;
        }

        .products h2 {
            text-align: center;
            font-size: 3rem;
            margin-bottom: 3rem;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .product-card {
            background: rgba(244, 228, 193, 0.1);
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid rgba(244, 228, 193, 0.2);
        }

        .product-card:hover {
            transform: scale(1.05);
            background: rgba(244, 228, 193, 0.2);
        }

        .product-card h3 {
            color: #d4af37;
            margin-bottom: 1rem;
        }

        .price {
            font-size: 1.5rem;
            color: #d4af37;
            font-weight: bold;
            margin-top: 1rem;
        }

        /* Contact Section */
        .contact {
            padding: 5rem 2rem;
            background: #f4e4c1;
            text-align: center;
        }

        .contact h2 {
            font-size: 3rem;
            margin-bottom: 2rem;
            color: #8b4513;
        }

        .contact-form {
            max-width: 600px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #5d2e0a;
            font-weight: bold;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 1rem;
            border: 2px solid #d4af37;
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #8b4513;
        }

        .submit-btn {
            background: linear-gradient(45deg, #8b4513, #d4af37);
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 50px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(139, 69, 19, 0.3);
        }

        /* Footer */
        footer {
            background: #2c1810;
            color: #f4e4c1;
            text-align: center;
            padding: 2rem;
        }

        .social-links {
            margin-top: 1rem;
        }

        .social-links a {
            color: #d4af37;
            text-decoration: none;
            margin: 0 1rem;
            font-size: 1.2rem;
            transition: color 0.3s ease;
        }

        .social-links a:hover {
            color: #f4e4c1;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .menu-toggle {
                display: flex;
            }

            .nav-links {
                position: absolute;
                top: 100%;
                left: 0;
                width: 100%;
                background: rgba(139, 69, 19, 0.95);
                flex-direction: column;
                padding: 1rem;
                transform: translateY(-100%);
                opacity: 0;
                transition: all 0.3s ease;
            }

            .nav-links.active {
                transform: translateY(0);
                opacity: 1;
            }

            .hero h1 {
                font-size: 2.5rem;
            }

            .hero p {
                font-size: 1rem;
            }

            .about h2,
            .products h2,
            .contact h2 {
                font-size: 2rem;
            }

            .features {
                grid-template-columns: 1fr;
            }

            .product-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f4e4c1;
        }

        ::-webkit-scrollbar-thumb {
            background: #8b4513;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #5d2e0a;
        }

        .login-btn {
            background: linear-gradient(45deg, #d4af37, #8b4513);
            color: #fff;
            padding: 0.6rem 1.5rem;
            border-radius: 25px;
            font-weight: bold;
            text-decoration: none;
            margin-left: 2rem;
            transition: background 0.3s, color 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 16px rgba(212, 175, 55, 0.15);
            border: none;
            font-size: 1rem;
            display: inline-block;
        }
        .login-btn:hover {
            background: linear-gradient(45deg, #8b4513, #d4af37);
            color: #2c1810;
            box-shadow: 0 6px 24px rgba(212, 175, 55, 0.25);
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <a href="#" class="logo">
                <img src="{{ asset('assets/img/logo.png') }}" alt="ChocolateSCM Logo" style="height:55px; vertical-align:middle; margin-right:10px;">
                ChocolateSCM
            </a>
            <ul class="nav-links">
                <li><a href="#home">Home</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#products">Products</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
            <div class="menu-toggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <a href="{{ route('login') }}" class="login-btn">Login</a>
        </nav>
    </header>

    <section id="home" class="hero">
        <div class="hero-slider">
            <div class="slide active">
                <img src="{{ asset('assets/img/chocolate6.jpg') }}" alt="Artisan dark chocolate bars and cocoa beans">
            </div>
            <div class="slide">
                <img src="{{ asset('assets/img/chocolate2.jpg') }}" alt="Handcrafted chocolate truffles and bonbons">
            </div>
            <div class="slide">
                <img src="{{ asset('assets/img/chocolate5.jpg') }}" alt="Chocolate making process with cocoa powder">
            </div>
        </div>
        <div class="hero-overlay"></div>
        <div class="slider-arrows">
            <!-- <div class="slider-arrow prev">❮</div>
            <div class="slider-arrow next">❯</div> -->
        </div>
        <div class="hero-content">
            <h1>CHOCOLATE SCM</h1>
            <h3>Welcome to SCM Project.</h3>
            <p>Your Supply Management Solution starts here.</p>
            <a href="#products" class="cta-button">Discover Our Chocolates</a>
        </div>
        <div class="slider-nav">
            <div class="slider-dot active" data-slide="0"></div>
            <div class="slider-dot" data-slide="1"></div>
            <div class="slider-dot" data-slide="2"></div>
        </div>
    </section>

    <script>
        // Image Slider functionality
        let currentSlide = 0;
        const slides = document.querySelectorAll('.slide');
        const dots = document.querySelectorAll('.slider-dot');
        const totalSlides = slides.length;

        function showSlide(index) {
            // Remove active class from all slides and dots
            slides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));
            
            // Add active class to current slide and dot
            slides[index].classList.add('active');
            dots[index].classList.add('active');
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % totalSlides;
            showSlide(currentSlide);
        }

        function prevSlide() {
            currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
            showSlide(currentSlide);
        }

        // Auto-play slider
        setInterval(nextSlide, 5000);

        // Navigation arrows
        document.querySelector('.slider-arrow.next').addEventListener('click', nextSlide);
        document.querySelector('.slider-arrow.prev').addEventListener('click', prevSlide);

        // Dot navigation
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                currentSlide = index;
                showSlide(currentSlide);
            });
        });

        // Touch/swipe support for mobile
        let touchStartX = 0;
        let touchEndX = 0;

        const heroSlider = document.querySelector('.hero-slider');

        heroSlider.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        });

        heroSlider.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        });

        function handleSwipe() {
            const swipeThreshold = 50;
            const diff = touchStartX - touchEndX;
            
            if (Math.abs(diff) > swipeThreshold) {
                if (diff > 0) {
                    nextSlide(); // Swipe left
                } else {
                    prevSlide(); // Swipe right
                }
            }
        }

        // Mobile menu toggle
        const menuToggle = document.querySelector('.menu-toggle');
        const navLinks = document.querySelector('.nav-links');

        menuToggle.addEventListener('click', () => {
            navLinks.classList.toggle('active');
        });

        // Close mobile menu when clicking on a link
        document.querySelectorAll('.nav-links a').forEach(link => {
            link.addEventListener('click', () => {
                navLinks.classList.remove('active');
            });
        });

        // Header background on scroll
        window.addEventListener('scroll', () => {
            const header = document.querySelector('header');
            if (window.scrollY > 100) {
                header.style.background = 'rgba(139, 69, 19, 0.98)';
            } else {
                header.style.background = 'rgba(139, 69, 19, 0.95)';
            }
        });

        // Form submission
        document.querySelector('.contact-form').addEventListener('submit', (e) => {
            e.preventDefault();
            alert('Thank you for your message! We\'ll get back to you soon.');
            e.target.reset();
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe elements for animation
        document.querySelectorAll('.feature, .product-card').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'all 0.6s ease-out';
            observer.observe(el);
        });
    </script>
</body>
</html>