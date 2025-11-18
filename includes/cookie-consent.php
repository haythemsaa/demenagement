<!-- Bannière de consentement aux cookies -->
<div id="cookie-consent" class="cookie-consent" style="display: none;">
    <div class="cookie-content">
        <div class="cookie-text">
            <h4>🍪 Ce site utilise des cookies</h4>
            <p>Nous utilisons des cookies pour améliorer votre expérience et analyser notre trafic. En continuant à naviguer, vous acceptez notre utilisation des cookies.</p>
            <a href="/pages/politique-confidentialite.php#cookies" class="cookie-link">En savoir plus</a>
        </div>
        <div class="cookie-buttons">
            <button onclick="acceptCookies()" class="btn-accept">Accepter</button>
            <button onclick="refuseCookies()" class="btn-refuse">Refuser</button>
            <button onclick="showCookieSettings()" class="btn-settings">Paramétrer</button>
        </div>
    </div>
</div>

<!-- Modal de paramétrage des cookies -->
<div id="cookie-settings-modal" class="cookie-modal" style="display: none;">
    <div class="modal-overlay" onclick="closeCookieSettings()"></div>
    <div class="modal-content">
        <h3>Paramètres des cookies</h3>
        <p>Choisissez les cookies que vous souhaitez autoriser :</p>

        <div class="cookie-category">
            <div class="cookie-category-header">
                <input type="checkbox" id="cookie-essential" checked disabled>
                <label for="cookie-essential">
                    <strong>Cookies essentiels</strong>
                    <span class="required">(Requis)</span>
                </label>
            </div>
            <p class="cookie-description">Ces cookies sont nécessaires au fonctionnement du site et ne peuvent pas être désactivés.</p>
        </div>

        <div class="cookie-category">
            <div class="cookie-category-header">
                <input type="checkbox" id="cookie-analytics">
                <label for="cookie-analytics">
                    <strong>Cookies analytiques</strong>
                </label>
            </div>
            <p class="cookie-description">Ces cookies nous permettent de mesurer l'audience et d'améliorer notre site (Google Analytics).</p>
        </div>

        <div class="cookie-category">
            <div class="cookie-category-header">
                <input type="checkbox" id="cookie-marketing">
                <label for="cookie-marketing">
                    <strong>Cookies marketing</strong>
                </label>
            </div>
            <p class="cookie-description">Ces cookies permettent d'afficher des publicités personnalisées.</p>
        </div>

        <div class="modal-buttons">
            <button onclick="saveCooki ePreferences()" class="btn-save">Enregistrer mes préférences</button>
            <button onclick="closeCookieSettings()" class="btn-cancel">Annuler</button>
        </div>
    </div>
</div>

<style>
.cookie-consent {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: white;
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
    padding: 1.5rem;
    z-index: 9999;
    animation: slideUp 0.3s ease-out;
}

@keyframes slideUp {
    from {
        transform: translateY(100%);
    }
    to {
        transform: translateY(0);
    }
}

.cookie-content {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 2rem;
}

.cookie-text h4 {
    margin-bottom: 0.5rem;
    color: var(--text-dark);
}

.cookie-text p {
    margin-bottom: 0.5rem;
    color: var(--text-light);
}

.cookie-link {
    color: var(--primary-color);
    text-decoration: underline;
}

.cookie-buttons {
    display: flex;
    gap: 1rem;
    flex-shrink: 0;
}

.cookie-buttons button {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-accept {
    background: var(--primary-color);
    color: white;
}

.btn-accept:hover {
    background: var(--primary-dark);
}

.btn-refuse {
    background: var(--bg-light);
    color: var(--text-dark);
}

.btn-refuse:hover {
    background: var(--border-color);
}

.btn-settings {
    background: white;
    color: var(--text-dark);
    border: 1px solid var(--border-color);
}

.btn-settings:hover {
    background: var(--bg-light);
}

/* Modal */
.cookie-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
}

.modal-content {
    position: relative;
    background: white;
    padding: 2rem;
    border-radius: 12px;
    max-width: 600px;
    width: 90%;
    max-height: 80vh;
    overflow-y: auto;
    z-index: 1;
}

.modal-content h3 {
    margin-bottom: 1rem;
}

.cookie-category {
    margin: 1.5rem 0;
    padding: 1rem;
    background: var(--bg-light);
    border-radius: 6px;
}

.cookie-category-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.5rem;
}

.cookie-category-header input[type="checkbox"] {
    width: 20px;
    height: 20px;
    cursor: pointer;
}

.cookie-category-header label {
    cursor: pointer;
    flex: 1;
}

.required {
    color: var(--text-light);
    font-size: 0.875rem;
    font-weight: normal;
}

.cookie-description {
    color: var(--text-light);
    font-size: 0.875rem;
    margin-left: 2.75rem;
}

.modal-buttons {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.modal-buttons button {
    flex: 1;
    padding: 0.875rem;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-save {
    background: var(--primary-color);
    color: white;
}

.btn-save:hover {
    background: var(--primary-dark);
}

.btn-cancel {
    background: var(--bg-light);
    color: var(--text-dark);
}

.btn-cancel:hover {
    background: var(--border-color);
}

@media (max-width: 768px) {
    .cookie-content {
        flex-direction: column;
        align-items: stretch;
    }

    .cookie-buttons {
        flex-direction: column;
    }

    .modal-buttons {
        flex-direction: column;
    }
}
</style>

<script>
// Gestion du consentement aux cookies
(function() {
    // Vérifier si le consentement a déjà été donné
    function getCookieConsent() {
        const consent = localStorage.getItem('cookie_consent');
        return consent ? JSON.parse(consent) : null;
    }

    // Afficher la bannière si nécessaire
    function showCookieBanner() {
        const consent = getCookieConsent();
        if (!consent) {
            document.getElementById('cookie-consent').style.display = 'block';
        } else {
            // Charger les scripts selon les préférences
            loadScriptsBasedOnConsent(consent);
        }
    }

    // Charger les scripts selon le consentement
    function loadScriptsBasedOnConsent(consent) {
        if (consent.analytics) {
            loadGoogleAnalytics();
        }
        if (consent.marketing) {
            // Charger les scripts marketing si nécessaire
        }
    }

    // Charger Google Analytics
    function loadGoogleAnalytics() {
        if (typeof GA_TRACKING_ID !== 'undefined' && GA_TRACKING_ID) {
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', GA_TRACKING_ID);

            const script = document.createElement('script');
            script.async = true;
            script.src = 'https://www.googletagmanager.com/gtag/js?id=' + GA_TRACKING_ID;
            document.head.appendChild(script);
        }
    }

    // Accepter tous les cookies
    window.acceptCookies = function() {
        const consent = {
            essential: true,
            analytics: true,
            marketing: true,
            timestamp: new Date().toISOString()
        };
        localStorage.setItem('cookie_consent', JSON.stringify(consent));
        document.getElementById('cookie-consent').style.display = 'none';
        loadScriptsBasedOnConsent(consent);
    };

    // Refuser les cookies non essentiels
    window.refuseCookies = function() {
        const consent = {
            essential: true,
            analytics: false,
            marketing: false,
            timestamp: new Date().toISOString()
        };
        localStorage.setItem('cookie_consent', JSON.stringify(consent));
        document.getElementById('cookie-consent').style.display = 'none';
    };

    // Afficher les paramètres
    window.showCookieSettings = function() {
        document.getElementById('cookie-settings-modal').style.display = 'flex';

        // Charger les préférences actuelles
        const consent = getCookieConsent();
        if (consent) {
            document.getElementById('cookie-analytics').checked = consent.analytics;
            document.getElementById('cookie-marketing').checked = consent.marketing;
        }
    };

    // Fermer les paramètres
    window.closeCookieSettings = function() {
        document.getElementById('cookie-settings-modal').style.display = 'none';
    };

    // Enregistrer les préférences
    window.saveCookiePreferences = function() {
        const consent = {
            essential: true,
            analytics: document.getElementById('cookie-analytics').checked,
            marketing: document.getElementById('cookie-marketing').checked,
            timestamp: new Date().toISOString()
        };
        localStorage.setItem('cookie_consent', JSON.stringify(consent));
        document.getElementById('cookie-consent').style.display = 'none';
        document.getElementById('cookie-settings-modal').style.display = 'none';
        loadScriptsBasedOnConsent(consent);
    };

    // Initialiser au chargement de la page
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', showCookieBanner);
    } else {
        showCookieBanner();
    }
})();
</script>
