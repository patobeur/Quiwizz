const auth = (() => {
    let loggedIn = false;

    function init() {
        // Listeners pour les formulaires
        document.getElementById('login-form').addEventListener('submit', handleLogin);
        document.getElementById('register-form').addEventListener('submit', handleRegister);
        document.getElementById('logout-btn').addEventListener('click', handleLogout);
        document.getElementById('request-reset-form').addEventListener('submit', handleRequestReset);
        document.getElementById('confirm-reset-form').addEventListener('submit', handleConfirmReset);


        // Vérifier si un état de connexion persiste (ex: via un appel à /me)
        checkInitialAuthStatus();
    }

    async function checkInitialAuthStatus() {
        try {
            // L'endpoint 'me' est parfait pour ça. S'il réussit, on est connecté.
            const data = await api.get('me');
            if (data && data.id) {
                loggedIn = true;
                window.app.updateAuthState(true);
                // Pré-remplir le token CSRF pour les actions futures
                api.setCsrfToken(data.csrf_token);
            }
        } catch (error) {
            // L'erreur 401 est attendue si non connecté.
            if (error.status !== 401) {
                console.error("Erreur lors de la vérification du statut d'authentification", error);
            }
            loggedIn = false;
            window.app.updateAuthState(false);
        }
    }

    async function handleLogin(e) {
        e.preventDefault();
        const email = document.getElementById('login-email').value;
        const password = document.getElementById('login-password').value;
        const errorDiv = document.getElementById('login-error');
        errorDiv.textContent = '';

        try {
            const data = await api.post('login', { email, password });
            loggedIn = true;
            window.app.updateAuthState(true);
            // Mettre à jour le token CSRF reçu après le login
            api.setCsrfToken(data.csrf_token);
            window.location.hash = 'profile';
        } catch (error) {
            errorDiv.textContent = error.error || 'Une erreur est survenue.';
        }
    }

    async function handleRegister(e) {
        e.preventDefault();
        const email = document.getElementById('register-email').value;
        const pseudo = document.getElementById('register-pseudo').value;
        const password = document.getElementById('register-password').value;
        const errorDiv = document.getElementById('register-error');
        errorDiv.textContent = '';

        try {
            await api.post('register', { email, pseudo, password });
            // Rediriger vers la page de connexion avec un message de succès
            window.location.hash = 'login';
            // On pourrait ajouter un message de succès ici
        } catch (error) {
            errorDiv.textContent = error.error || 'Une erreur est survenue.';
        }
    }

    async function handleLogout(e) {
        e.preventDefault();
        try {
            await api.post('logout', {});
            loggedIn = false;
            api.setCsrfToken(null); // Invalider le token côté client
            window.app.updateAuthState(false);
            window.location.hash = 'home';
        } catch (error) {
            console.error('Erreur lors de la déconnexion:', error);
            // Forcer la déconnexion côté client même si le serveur échoue
            loggedIn = false;
            window.app.updateAuthState(false);
            window.location.hash = 'home';
        }
    }

    async function handleRequestReset(e) {
        e.preventDefault();
        const email = document.getElementById('reset-email').value;
        const errorDiv = document.getElementById('request-reset-error');
        errorDiv.textContent = '';

        try {
            const data = await api.post('request_reset', { email });
            // Afficher un message de succès générique
            e.target.innerHTML = `<p>${data.message}</p>`;
        } catch (error) {
            // Ne pas afficher d'erreur spécifique pour des raisons de sécurité
            errorDiv.textContent = 'Une erreur est survenue. Veuillez réessayer.';
        }
    }

    async function handleConfirmReset(e) {
        e.preventDefault();
        const token = document.getElementById('reset-token-field').value;
        const password = document.getElementById('confirm-password').value;
        const errorDiv = document.getElementById('confirm-reset-error');
        errorDiv.textContent = '';

        if (!token) {
            errorDiv.textContent = 'Token de réinitialisation manquant ou invalide.';
            return;
        }

        try {
            const data = await api.post('confirm_reset', { token, password });
            // Rediriger vers la page de connexion avec un message
            window.location.hash = 'login';
            // On pourrait stocker le message dans sessionStorage pour l'afficher sur la page de login
            sessionStorage.setItem('loginMessage', data.message);
        } catch (error) {
             errorDiv.textContent = error.error || 'Une erreur est survenue.';
        }
    }

    function isLoggedIn() {
        return loggedIn;
    }

    return {
        init,
        isLoggedIn,
    };
})();
