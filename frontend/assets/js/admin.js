const admin = (() => {

    function init() {
        // Cet event listener est un peu un hack. Idéalement, le routeur
        // devrait appeler une fonction spécifique lors de l'affichage d'une page.
        window.addEventListener('hashchange', () => {
            if (window.location.hash === '#admin') {
                loadUsers();
            }
        });
    }

    async function loadUsers() {
        const userListBody = document.getElementById('admin-user-list');
        if (!userListBody) return; // Ne rien faire si on n'est pas sur la page admin

        try {
            const users = await api.get('admin_get_users');
            renderUserTable(users);
        } catch (error) {
            console.error("Erreur lors du chargement des utilisateurs", error);
            userListBody.innerHTML = `<tr><td colspan="5" class="error">Erreur de chargement des données.</td></tr>`;
        }
    }

    function renderUserTable(users) {
        const userListBody = document.getElementById('admin-user-list');
        userListBody.innerHTML = ''; // Vider la table

        if (users.length === 0) {
            userListBody.innerHTML = '<tr><td colspan="5">Aucun utilisateur trouvé.</td></tr>';
            return;
        }

        users.forEach(user => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${user.id}</td>
                <td>${user.email}</td>
                <td>${user.pseudo}</td>
                <td>${user.is_admin ? 'Oui' : 'Non'}</td>
                <td class="actions">
                    ${!user.is_admin ? `<button class="promote-btn" data-user-id="${user.id}">Promouvoir</button>` : ''}
                    ${user.is_admin ? `<button class="demote-btn" data-user-id="${user.id}">Rétrograder</button>` : ''}
                </td>
            `;
            userListBody.appendChild(tr);
        });

        // Ajouter les event listeners aux nouveaux boutons
        userListBody.querySelectorAll('.promote-btn').forEach(btn => {
            btn.addEventListener('click', handleRoleChange);
        });
        userListBody.querySelectorAll('.demote-btn').forEach(btn => {
            btn.addEventListener('click', handleRoleChange);
        });
    }

    async function handleRoleChange(event) {
        const button = event.target;
        const userId = button.dataset.userId;
        const action = button.classList.contains('promote-btn') ? 'admin_promote_user' : 'admin_demote_user';

        if (!confirm(`Êtes-vous sûr de vouloir ${action === 'admin_promote_user' ? 'promouvoir' : 'rétrograder'} cet utilisateur ?`)) {
            return;
        }

        try {
            await api.post(action, { user_id: userId });
            loadUsers(); // Recharger la liste des utilisateurs pour voir le changement
        } catch (error) {
            alert(`Erreur: ${error.error || 'Une erreur est survenue.'}`);
        }
    }

    return {
        init,
        loadUsers
    };
})();
