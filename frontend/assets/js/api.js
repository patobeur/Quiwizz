const api = (() => {
    const BASE_URL = 'api/index.php';
    let csrfToken = null;

    async function fetchCsrfToken() {
        if (csrfToken) return csrfToken;
        try {
            const response = await fetch(`${BASE_URL}?action=get_csrf_token`);
            if (!response.ok) throw new Error('Failed to fetch CSRF token');
            const data = await response.json();
            csrfToken = data.csrf_token;
            return csrfToken;
        } catch (error) {
            console.error('API Error:', error);
            return null;
        }
    }

    async function request(action, options = {}) {
        const url = `${BASE_URL}?action=${action}`;
        const headers = {
            'Accept': 'application/json',
            ...options.headers,
        };

        if (options.body && !(options.body instanceof FormData)) {
             headers['Content-Type'] = 'application/json';
             options.body = JSON.stringify(options.body);
        }

        try {
            const response = await fetch(url, { ...options, headers });
            const responseData = await response.json();

            if (!response.ok) {
                // Si la réponse contient un token CSRF (cas du login), on le met à jour
                if (responseData.csrf_token) {
                    csrfToken = responseData.csrf_token;
                }
                // Propage l'erreur pour qu'elle soit attrapée par le code appelant
                throw responseData;
            }

            // Si la réponse contient un token CSRF (cas du login), on le met à jour
            if (responseData.csrf_token) {
                csrfToken = responseData.csrf_token;
            }

            return responseData;
        } catch (error) {
            console.error(`API request failed for action: ${action}`, error);
            // Propage l'erreur pour une gestion plus fine dans l'UI
            throw error;
        }
    }

    async function post(action, data) {
        // Pour les FormData, le token doit être ajouté comme un champ.
        if (data instanceof FormData) {
            const token = await fetchCsrfToken();
            data.append('csrf_token', token);
            return request(action, {
                method: 'POST',
                body: data,
            });
        }

        // Pour les objets JSON, on peut l'ajouter directement.
        const token = await fetchCsrfToken();
        const bodyWithCsrf = { ...data, csrf_token: token };

        return request(action, {
            method: 'POST',
            body: bodyWithCsrf,
        });
    }

    function get(action) {
        return request(action, { method: 'GET' });
    }

    // Fonction pour mettre à jour le token manuellement si nécessaire
    function setCsrfToken(token) {
        csrfToken = token;
    }

    return {
        get,
        post,
        fetchCsrfToken,
        setCsrfToken,
    };
})();
