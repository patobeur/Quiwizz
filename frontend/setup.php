<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuiWizz - Installation</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: #f4f7f6; color: #333; margin: 0; padding: 2rem; }
        .container { max-width: 800px; margin: 2rem auto; background: #fff; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        h1, h2 { color: #2c3e50; border-bottom: 2px solid #e0e0e0; padding-bottom: 0.5rem; margin-bottom: 1.5rem; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
        .form-section { border: 1px solid #ddd; padding: 1.5rem; border-radius: 8px; }
        .form-section h2 { font-size: 1.2rem; margin-top: 0; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 0.5rem; }
        .form-group input, .form-group select { width: 100%; padding: 0.75rem; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .form-group input[type="checkbox"] { width: auto; }
        .full-width { grid-column: 1 / -1; }
        .button { background-color: #3498db; color: white; padding: 0.8rem 1.5rem; border: none; border-radius: 4px; font-size: 1rem; cursor: pointer; transition: background-color 0.3s; width: 100%; }
        .button:hover { background-color: #2980b9; }
        .message { padding: 1rem; margin-top: 1.5rem; border-radius: 4px; display: none; }
        .message.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .message.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .db-mysql-fields, .db-sqlite-fields { display: none; }
    </style>
</head>
<body>

<div class="container">
    <h1>Installation de QuiWizz</h1>
    <p>Bienvenue ! Veuillez remplir ce formulaire pour configurer et installer l'application.</p>

    <div id="message-area" class="message"></div>

    <form id="setup-form">
        <div class="form-grid">

            <div class="form-section full-width">
                <h2>Compte Administrateur</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="admin_email">Email de l'administrateur</label>
                        <input type="email" id="admin_email" name="admin_email" required>
                    </div>
                    <div class="form-group">
                        <label for="admin_pseudo">Pseudo de l'administrateur</label>
                        <input type="text" id="admin_pseudo" name="admin_pseudo" required>
                    </div>
                    <div class="form-group">
                        <label for="admin_password">Mot de passe de l'administrateur</label>
                        <input type="password" id="admin_password" name="admin_password" required>
                    </div>
                </div>
            </div>

            <div class="form-section full-width">
                <h2>Base de Données</h2>
                <div class="form-group">
                    <label for="db_driver">Driver de Base de Données</label>
                    <select id="db_driver" name="DB_DRIVER">
                        <option value="mysql" selected>MySQL</option>
                        <option value="sqlite">SQLite</option>
                    </select>
                </div>
                <div class="db-sqlite-fields">
                    <div class="form-group">
                        <label for="db_path">Chemin de la base de données SQLite</label>
                        <input type="text" id="db_path" name="DB_PATH" value="../backend/storage/app.db">
                    </div>
                </div>
                <div class="db-mysql-fields">
                     <div class="form-grid">
                        <div class="form-group">
                            <label for="db_host">Hôte (DB_HOST)</label>
                            <input type="text" id="db_host" name="DB_HOST" value="127.0.0.1">
                        </div>
                        <div class="form-group">
                            <label for="db_port">Port (DB_PORT)</label>
                            <input type="text" id="db_port" name="DB_PORT" value="3306">
                        </div>
                        <div class="form-group">
                            <label for="db_name">Nom de la base (DB_NAME)</label>
                            <input type="text" id="db_name" name="DB_NAME" value="quiwizzdb">
                        </div>
                        <div class="form-group">
                            <label for="db_user">Utilisateur (DB_USER)</label>
                            <input type="text" id="db_user" name="DB_USER" value="root">
                        </div>
                        <div class="form-group">
                            <label for="db_pass">Mot de passe (DB_PASS)</label>
                            <input type="password" id="db_pass" name="DB_PASS">
                        </div>
                        <div class="form-group">
                             <label for="db_charset">Charset (DB_CHARSET)</label>
                             <input type="text" id="db_charset" name="DB_CHARSET" value="utf8mb4">
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-section full-width">
                <h2>Configuration des E-mails (PHPMailer)</h2>
                 <div class="form-grid">
                    <div class="form-group">
                        <label for="mail_host">Hôte SMTP (MAIL_HOST)</label>
                        <input type="text" id="mail_host" name="MAIL_HOST" value="smtp.example.com">
                    </div>
                    <div class="form-group">
                        <label for="mail_port">Port SMTP (MAIL_PORT)</label>
                        <input type="number" id="mail_port" name="MAIL_PORT" value="587">
                    </div>
                    <div class="form-group">
                        <label for="mail_username">Utilisateur (MAIL_USERNAME)</label>
                        <input type="text" id="mail_username" name="MAIL_USERNAME" value="user@example.com">
                    </div>
                    <div class="form-group">
                        <label for="mail_password">Mot de passe (MAIL_PASSWORD)</label>
                        <input type="password" id="mail_password" name="MAIL_PASSWORD" value="secret">
                    </div>
                    <div class="form-group">
                        <label for="mail_from_address">Adresse d'envoi (MAIL_FROM_ADDRESS)</label>
                        <input type="email" id="mail_from_address" name="MAIL_FROM_ADDRESS" value="noreply@example.com">
                    </div>
                    <div class="form-group">
                        <label for="mail_from_name">Nom d'envoi (MAIL_FROM_NAME)</label>
                        <input type="text" id="mail_from_name" name="MAIL_FROM_NAME" value="QuiWizz">
                    </div>
                    <div class="form-group">
                        <label for="mail_encryption">Chiffrement (MAIL_ENCRYPTION)</label>
                        <select id="mail_encryption" name="MAIL_ENCRYPTION">
                            <option value="tls">TLS</option>
                            <option value="ssl">SSL</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-section full-width">
                <h2>Paramètres de l'Application</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="app_url">URL de l'application (APP_URL)</label>
                        <input type="text" id="app_url" name="APP_URL" value="http://localhost/quiwizz/frontend">
                    </div>
                    <div class="form-group">
                        <label for="debug_mode">Mode Débogage (DEBUG)</label>
                        <input type="checkbox" id="debug_mode" name="DEBUG" checked>
                    </div>
                </div>
            </div>

            <div class="full-width">
                <button type="submit" class="button" id="submit-btn">Installer QuiWizz</button>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dbDriverSelect = document.getElementById('db_driver');
        const mysqlFields = document.querySelector('.db-mysql-fields');
        const sqliteFields = document.querySelector('.db-sqlite-fields');

        function toggleDbFields() {
            if (dbDriverSelect.value === 'mysql') {
                mysqlFields.style.display = 'block';
                sqliteFields.style.display = 'none';
            } else {
                mysqlFields.style.display = 'none';
                sqliteFields.style.display = 'block';
            }
        }

        toggleDbFields();
        dbDriverSelect.addEventListener('change', toggleDbFields);

        const setupForm = document.getElementById('setup-form');
        const messageArea = document.getElementById('message-area');
        const submitBtn = document.getElementById('submit-btn');

        setupForm.addEventListener('submit', function (e) {
            e.preventDefault();
            submitBtn.disabled = true;
            submitBtn.textContent = 'Installation en cours...';
            messageArea.style.display = 'none';

            const formData = new FormData(setupForm);
            const data = {};
            formData.forEach((value, key) => {
                // Handle checkbox value for DEBUG mode
                if (key === 'DEBUG') {
                    data[key] = formData.get('DEBUG') ? true : false;
                } else {
                    data[key] = value;
                }
            });
            // Ensure DEBUG is present even if unchecked
            if (!data.hasOwnProperty('DEBUG')) {
                data['DEBUG'] = false;
            }

            fetch('../backend/index.php?action=install', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw new Error(err.error || 'Erreur réseau'); });
                }
                return response.json();
            })
            .then(result => {
                if (result.success) {
                    messageArea.className = 'message success';
                    messageArea.innerHTML = `<strong>Succès !</strong> ${result.message} Vous allez être redirigé vers la page d'accueil dans 5 secondes.`;
                    messageArea.style.display = 'block';
                    setTimeout(() => {
                        window.location.href = 'index.html';
                    }, 5000);
                } else {
                    throw new Error(result.error || 'Une erreur inconnue est survenue.');
                }
            })
            .catch(error => {
                messageArea.className = 'message error';
                messageArea.innerHTML = `<strong>Erreur :</strong> ${error.message}`;
                messageArea.style.display = 'block';
                submitBtn.disabled = false;
                submitBtn.textContent = 'Installer QuiWizz';
            });
        });
    });
</script>

</body>
</html>
