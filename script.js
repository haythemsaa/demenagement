// Gestion du formulaire multi-étapes
let currentStep = 1;

function nextStep(step) {
    const currentStepElement = document.getElementById(`step${currentStep}`);
    const nextStepElement = document.getElementById(`step${step}`);

    // Validation du formulaire actuel
    const inputs = currentStepElement.querySelectorAll('input[required], select[required]');
    let isValid = true;

    inputs.forEach(input => {
        if (!input.value || (input.type === 'radio' && !document.querySelector(`input[name="${input.name}"]:checked`))) {
            isValid = false;
            input.style.borderColor = '#ef4444';
        } else {
            input.style.borderColor = '#e5e7eb';
        }
    });

    if (!isValid) {
        alert('Veuillez remplir tous les champs obligatoires');
        return;
    }

    currentStepElement.classList.remove('active');
    nextStepElement.classList.add('active');
    currentStep = step;

    // Scroll vers le formulaire
    document.getElementById('devis').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function prevStep(step) {
    const currentStepElement = document.getElementById(`step${currentStep}`);
    const prevStepElement = document.getElementById(`step${step}`);

    currentStepElement.classList.remove('active');
    prevStepElement.classList.add('active');
    currentStep = step;

    // Scroll vers le formulaire
    document.getElementById('devis').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// Soumission du formulaire de devis
document.getElementById('quoteForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    try {
        const response = await fetch('api/submit-quote.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            showMessage('success', 'Votre demande a été envoyée avec succès ! Vous allez recevoir vos devis sous 1 heure.');
            this.reset();
            currentStep = 1;
            document.querySelectorAll('.form-step').forEach(step => step.classList.remove('active'));
            document.getElementById('step1').classList.add('active');
        } else {
            showMessage('error', result.message || 'Une erreur est survenue. Veuillez réessayer.');
        }
    } catch (error) {
        showMessage('error', 'Erreur de connexion. Veuillez réessayer.');
        console.error('Erreur:', error);
    }
});

// Simulateur de volume et d'économies
function initSimulator() {
    const superficieInput = document.getElementById('sim-superficie');
    const distanceInput = document.getElementById('sim-distance');
    const formuleSelect = document.getElementById('sim-formule');
    const typeInputs = document.querySelectorAll('input[name="sim-type"]');

    function updateSimulator() {
        const superficie = parseInt(superficieInput.value);
        const distance = parseInt(distanceInput.value);
        const formule = formuleSelect.value;
        const type = document.querySelector('input[name="sim-type"]:checked')?.value || 'appartement';

        // Mise à jour des valeurs affichées
        document.getElementById('superficie-value').textContent = `${superficie} m²`;
        document.getElementById('distance-value').textContent = `${distance} km`;

        // Calcul du volume (approximatif)
        const volume = Math.round(superficie / 2);
        document.getElementById('volume-amount').textContent = `${volume} m³`;

        const camions = Math.ceil(volume / 30);
        document.getElementById('volume-amount').nextElementSibling.textContent =
            `Soit environ ${camions} camion${camions > 1 ? 's' : ''}`;

        // Calcul du prix
        let basePrice = 500;

        // Prix selon la superficie
        basePrice += superficie * 8;

        // Prix selon la distance
        basePrice += distance * 1.5;

        // Prix selon le type
        if (type === 'maison') {
            basePrice *= 1.2;
        }

        // Prix selon la formule
        let multiplier = 1;
        if (formule === 'eco') {
            multiplier = 0.6;
        } else if (formule === 'premium') {
            multiplier = 1.5;
        }

        basePrice *= multiplier;

        const minPrice = Math.round(basePrice * 0.8);
        const maxPrice = Math.round(basePrice * 1.2);

        document.getElementById('price-range').textContent = `${minPrice}€ - ${maxPrice}€`;

        // Calcul des économies (40% du prix max)
        const savings = Math.round(maxPrice * 0.4);
        document.getElementById('savings-amount').textContent = `jusqu'à ${savings}€`;
    }

    superficieInput?.addEventListener('input', updateSimulator);
    distanceInput?.addEventListener('input', updateSimulator);
    formuleSelect?.addEventListener('change', updateSimulator);
    typeInputs.forEach(input => input.addEventListener('change', updateSimulator));

    // Initialisation
    updateSimulator();
}

// FAQ Accordion
function initFAQ() {
    const faqQuestions = document.querySelectorAll('.faq-question');

    faqQuestions.forEach(question => {
        question.addEventListener('click', function() {
            const faqItem = this.parentElement;
            const isActive = faqItem.classList.contains('active');

            // Fermer tous les autres
            document.querySelectorAll('.faq-item').forEach(item => {
                item.classList.remove('active');
            });

            // Ouvrir celui-ci s'il n'était pas actif
            if (!isActive) {
                faqItem.classList.add('active');
            }
        });
    });
}

// Formulaire de rappel
document.getElementById('callbackForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    try {
        const response = await fetch('api/submit-callback.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            showMessage('success', 'Votre demande de rappel a été enregistrée ! Nous vous contacterons dans les meilleurs délais.');
            this.reset();
        } else {
            showMessage('error', result.message || 'Une erreur est survenue. Veuillez réessayer.');
        }
    } catch (error) {
        showMessage('error', 'Erreur de connexion. Veuillez réessayer.');
        console.error('Erreur:', error);
    }
});

// Bouton retour en haut
function initBackToTop() {
    const backToTopButton = document.getElementById('backToTop');

    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTopButton.classList.add('visible');
        } else {
            backToTopButton.classList.remove('visible');
        }
    });

    backToTopButton?.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

// Navigation fluide
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');

            if (href === '#' || href === '') return;

            e.preventDefault();

            const target = document.querySelector(href);
            if (target) {
                const headerOffset = 80;
                const elementPosition = target.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });
}

// Affichage des messages
function showMessage(type, message) {
    // Créer l'élément de message s'il n'existe pas
    let messageElement = document.querySelector('.message');

    if (!messageElement) {
        messageElement = document.createElement('div');
        messageElement.className = 'message';
        const form = document.querySelector('form');
        if (form) {
            form.parentNode.insertBefore(messageElement, form);
        }
    }

    messageElement.className = `message ${type} show`;
    messageElement.textContent = message;

    // Faire défiler vers le message
    messageElement.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

    // Masquer après 5 secondes
    setTimeout(() => {
        messageElement.classList.remove('show');
    }, 5000);
}

// Date minimale pour le formulaire (aujourd'hui)
function setMinDate() {
    const dateInput = document.getElementById('date-demenagement');
    if (dateInput) {
        const today = new Date();
        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        const formattedDate = tomorrow.toISOString().split('T')[0];
        dateInput.setAttribute('min', formattedDate);
    }
}

// Animation au scroll
function initScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-slide-up');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observer les cartes
    document.querySelectorAll('.step-card, .service-card, .testimonial-card, .factor-card').forEach(card => {
        observer.observe(card);
    });
}

// Validation en temps réel
function initRealTimeValidation() {
    // Code postal
    const postalInputs = document.querySelectorAll('input[name="depart-postal"], input[name="arrivee-postal"]');
    postalInputs.forEach(input => {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '').substring(0, 5);
        });
    });

    // Téléphone
    const phoneInputs = document.querySelectorAll('input[type="tel"]');
    phoneInputs.forEach(input => {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '').substring(0, 10);
        });
    });
}

// Gestion du menu mobile (si nécessaire)
function initMobileMenu() {
    const header = document.querySelector('.header');
    let lastScroll = 0;

    window.addEventListener('scroll', function() {
        const currentScroll = window.pageYOffset;

        if (currentScroll > lastScroll && currentScroll > 100) {
            header.style.transform = 'translateY(-100%)';
        } else {
            header.style.transform = 'translateY(0)';
        }

        lastScroll = currentScroll;
    });
}

// Tracking des événements (Google Analytics, etc.)
function trackEvent(category, action, label) {
    if (typeof gtag !== 'undefined') {
        gtag('event', action, {
            'event_category': category,
            'event_label': label
        });
    }
    console.log('Event:', category, action, label);
}

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    initSimulator();
    initFAQ();
    initBackToTop();
    initSmoothScroll();
    setMinDate();
    initScrollAnimations();
    initRealTimeValidation();
    initMobileMenu();

    // Tracking des clics sur les liens importants
    document.querySelectorAll('a[href^="tel:"]').forEach(link => {
        link.addEventListener('click', function() {
            trackEvent('Contact', 'Click', 'Phone Call');
        });
    });

    document.querySelectorAll('.btn-submit, .btn-callback').forEach(button => {
        button.addEventListener('click', function() {
            trackEvent('Form', 'Submit', this.className);
        });
    });
});

// Gestion des erreurs globales
window.addEventListener('error', function(e) {
    console.error('Erreur:', e.message);
    // Possibilité d'envoyer les erreurs à un service de logging
});

// Service Worker pour le mode hors ligne (PWA)
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        // Décommenter pour activer le service worker
        // navigator.serviceWorker.register('/sw.js');
    });
}
