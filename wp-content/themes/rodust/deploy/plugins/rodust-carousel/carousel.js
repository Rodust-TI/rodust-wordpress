// Rodust Carousel Frontend JavaScript
jQuery(document).ready(function($) {
    
    /**
     * Classe do Carousel
     */
    function RodustCarousel(element) {
        this.carousel = element;
        this.slides = element.find('.carousel-slide');
        this.currentSlide = 0;
        this.totalSlides = this.slides.length;
        this.autoplay = element.data('autoplay') !== false;
        this.autoplaySpeed = element.data('autoplay-speed') || 5000;
        this.autoplayTimer = null;
        
        this.init();
    }
    
    RodustCarousel.prototype = {
        
        init: function() {
            if (this.totalSlides === 0) {
                return;
            }
            
            this.createControls();
            this.bindEvents();
            this.showSlide(0);
            
            if (this.autoplay) {
                this.startAutoplay();
            }
        },
        
        createControls: function() {
            // Setas de navegação
            if (this.carousel.data('show-arrows') !== false && this.totalSlides > 1) {
                this.carousel.append(
                    '<button class="carousel-arrows carousel-prev" aria-label="Slide anterior">❮</button>' +
                    '<button class="carousel-arrows carousel-next" aria-label="Próximo slide">❯</button>'
                );
            }
            
            // Dots de navegação
            if (this.carousel.data('show-dots') !== false && this.totalSlides > 1) {
                let dotsHtml = '<div class="carousel-dots">';
                for (let i = 0; i < this.totalSlides; i++) {
                    dotsHtml += '<button class="carousel-dot" data-slide="' + i + '" aria-label="Ir para slide ' + (i + 1) + '"></button>';
                }
                dotsHtml += '</div>';
                this.carousel.append(dotsHtml);
            }
        },
        
        bindEvents: function() {
            const self = this;
            
            // Setas
            this.carousel.on('click', '.carousel-prev', function() {
                self.prevSlide();
            });
            
            this.carousel.on('click', '.carousel-next', function() {
                self.nextSlide();
            });
            
            // Dots
            this.carousel.on('click', '.carousel-dot', function() {
                const slideIndex = parseInt($(this).data('slide'));
                self.goToSlide(slideIndex);
            });
            
            // Keyboard navigation
            $(document).on('keydown', function(e) {
                if (self.carousel.is(':hover')) {
                    if (e.which === 37) { // Left arrow
                        self.prevSlide();
                    } else if (e.which === 39) { // Right arrow
                        self.nextSlide();
                    }
                }
            });
            
            // Pause on hover
            this.carousel.on('mouseenter', function() {
                self.pauseAutoplay();
            });
            
            this.carousel.on('mouseleave', function() {
                if (self.autoplay) {
                    self.startAutoplay();
                }
            });
            
            // Touch/Swipe support para mobile
            this.addTouchSupport();
        },
        
        showSlide: function(index) {
            // Remove active de todos
            this.slides.removeClass('active');
            this.carousel.find('.carousel-dot').removeClass('active');
            
            // Adiciona active no atual
            $(this.slides[index]).addClass('active');
            this.carousel.find('.carousel-dot').eq(index).addClass('active');
            
            this.currentSlide = index;
        },
        
        nextSlide: function() {
            const nextIndex = (this.currentSlide + 1) % this.totalSlides;
            this.goToSlide(nextIndex);
        },
        
        prevSlide: function() {
            const prevIndex = (this.currentSlide - 1 + this.totalSlides) % this.totalSlides;
            this.goToSlide(prevIndex);
        },
        
        goToSlide: function(index) {
            if (index >= 0 && index < this.totalSlides && index !== this.currentSlide) {
                this.showSlide(index);
                this.resetAutoplay();
            }
        },
        
        startAutoplay: function() {
            if (this.totalSlides <= 1) return;
            
            const self = this;
            this.autoplayTimer = setInterval(function() {
                self.nextSlide();
            }, this.autoplaySpeed);
        },
        
        pauseAutoplay: function() {
            if (this.autoplayTimer) {
                clearInterval(this.autoplayTimer);
                this.autoplayTimer = null;
            }
        },
        
        resetAutoplay: function() {
            this.pauseAutoplay();
            if (this.autoplay) {
                this.startAutoplay();
            }
        },
        
        addTouchSupport: function() {
            const self = this;
            let startX = 0;
            let endX = 0;
            
            this.carousel.on('touchstart', function(e) {
                startX = e.originalEvent.touches[0].clientX;
            });
            
            this.carousel.on('touchend', function(e) {
                endX = e.originalEvent.changedTouches[0].clientX;
                self.handleSwipe();
            });
            
            this.handleSwipe = function() {
                const diffX = startX - endX;
                const threshold = 50; // Mínimo de pixels para considerar swipe
                
                if (Math.abs(diffX) > threshold) {
                    if (diffX > 0) {
                        self.nextSlide(); // Swipe left -> next
                    } else {
                        self.prevSlide(); // Swipe right -> prev
                    }
                }
            };
        }
    };

    // Inicializa todos os carousels na página
    $('.rodust-carousel').each(function() {
        new RodustCarousel($(this));
    });
});