document.addEventListener('DOMContentLoaded', () => {
    // -------------------------------------------
    // Gestion du thème clair/sombre
    // -------------------------------------------
    const themeToggle = document.getElementById('theme-toggle');
    if (themeToggle) {
        // Vérifie le thème préféré de l'utilisateur ou utilise le thème clair par défaut
        const prefersDarkScheme = window.matchMedia("(prefers-color-scheme: dark)");
        const currentTheme = localStorage.getItem('theme');

        if (currentTheme) {
            document.body.classList.add(currentTheme);
        } else if (prefersDarkScheme.matches) {
            document.body.classList.add('dark-theme');
        }

        themeToggle.addEventListener('click', () => {
            document.body.classList.toggle('dark-theme');
            let theme = 'light-theme';
            if (document.body.classList.contains('dark-theme')) {
                theme = 'dark-theme';
            }
            localStorage.setItem('theme', theme);
        });
    }

    // -------------------------------------------
    // Gestion des carrousels
    // -------------------------------------------
    function initializeCarousel(carouselContainer) {
        const carouselTrack = carouselContainer.querySelector('.carousel-track');
        const slides = Array.from(carouselTrack.children); // Utilise .children pour obtenir les div .carousel-slide
        const prevButton = carouselContainer.querySelector('.carousel-btn.prev');
        const nextButton = carouselContainer.querySelector('.carousel-btn.next');
        const dotsContainer = carouselContainer.querySelector('.carousel-dots');

        let currentSlideIndex = 0;
        let slideWidth = slides[0].offsetWidth; // Largeur d'une slide

        // Créer les points de navigation
        slides.forEach((_, index) => {
            const dot = document.createElement('span');
            dot.classList.add('dot');
            if (index === 0) {
                dot.classList.add('active');
            }
            dot.addEventListener('click', () => {
                moveToSlide(index);
            });
            dotsContainer.appendChild(dot);
        });

        const dots = Array.from(dotsContainer.children);

        // Fonction pour déplacer le carrousel vers une slide spécifique
        function moveToSlide(targetIndex) {
            if (targetIndex < 0 || targetIndex >= slides.length) {
                return; // Empêche de sortir des limites
            }
            carouselTrack.style.transform = 'translateX(-' + (slideWidth * targetIndex) + 'px)';
            updateDots(targetIndex);
            currentSlideIndex = targetIndex;
        }

        // Met à jour l'état actif des points de navigation
        function updateDots(targetIndex) {
            dots.forEach(dot => dot.classList.remove('active'));
            if (dots[targetIndex]) {
                dots[targetIndex].classList.add('active');
            }
        }

        // Naviguer au précédent
        if (prevButton) {
            prevButton.addEventListener('click', () => {
                if (currentSlideIndex === 0) {
                    moveToSlide(slides.length - 1); // Retour à la dernière slide
                } else {
                    moveToSlide(currentSlideIndex - 1);
                }
            });
        }

        // Naviguer au suivant
        if (nextButton) {
            nextButton.addEventListener('click', () => {
                if (currentSlideIndex === slides.length - 1) {
                    moveToSlide(0); // Retour à la première slide
                } else {
                    moveToSlide(currentSlideIndex + 1);
                }
            });
        }

        // Ajuster la largeur des slides au redimensionnement de la fenêtre
        window.addEventListener('resize', () => {
            slideWidth = slides[0].offsetWidth;
            moveToSlide(currentSlideIndex); // S'assure que la slide actuelle reste visible
        });

        // Initialisation : s'assurer que la première slide est visible
        moveToSlide(0);
    }

    // Sélectionne tous les conteneurs de carrousel et les initialise
    const allCarouselContainers = document.querySelectorAll('.carousel-container');
    allCarouselContainers.forEach(carouselContainer => {
        initializeCarousel(carouselContainer);
    });
});