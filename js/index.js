 // Swiper Carousel Initialization
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Swiper
        const swiper = new Swiper('.hero-swiper', {
            // Auto play settings
            autoplay: {
                delay: 2000, // 2 seconds
                disableOnInteraction: false,
            },

            // Loop continuously
            loop: true,

            // Effect
            effect: 'fade',
            fadeEffect: {
                crossFade: true
            },

            // Speed
            speed: 1000,

            // Navigation arrows
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },

            // Pagination
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },

            // Responsive breakpoints
            breakpoints: {
                // when window width is >= 320px
                320: {
                    slidesPerView: 1,
                    spaceBetween: 0
                },
                // when window width is >= 768px
                768: {
                    slidesPerView: 1,
                    spaceBetween: 0
                },
                // when window width is >= 1024px
                1024: {
                    slidesPerView: 1,
                    spaceBetween: 0
                }
            }
        });

        // Program Filter Functionality
        const filterButtons = document.querySelectorAll('.program-filter-btn');
        const programCards = document.querySelectorAll('.program-card');

        filterButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Update active button
                filterButtons.forEach(btn => {
                    btn.classList.remove('active', 'bg-accent', 'text-white');
                    btn.classList.add('bg-gray-200', 'text-gray-700');
                });
                button.classList.add('active', 'bg-accent', 'text-white');
                button.classList.remove('bg-gray-200', 'text-gray-700');

                // Filter programs
                const filter = button.getAttribute('data-filter');

                programCards.forEach(card => {
                    if (filter === 'all' || card.getAttribute('data-category') === filter) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });

        // Pause autoplay on hover (optional)
        const heroSwiper = document.querySelector('.hero-swiper');
        if (heroSwiper) {
            heroSwiper.addEventListener('mouseenter', () => {
                swiper.autoplay.stop();
            });

            heroSwiper.addEventListener('mouseleave', () => {
                swiper.autoplay.start();
            });
        }
    });

    // FAQ Toggle Functionality
    document.addEventListener('DOMContentLoaded', function() {
        const faqItems = document.querySelectorAll('.faq-item');

        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question');
            const answer = item.querySelector('.faq-answer');

            question.addEventListener('click', () => {
                // Close all other items
                faqItems.forEach(otherItem => {
                    if (otherItem !== item && otherItem.classList.contains('active')) {
                        otherItem.classList.remove('active');
                    }
                });

                // Toggle current item
                item.classList.toggle('active');
            });
        });

        // Open first FAQ by default
        if (faqItems.length > 0) {
            faqItems[0].classList.add('active');
        }
    });