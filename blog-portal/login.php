<?php
/**
 * Blog Portal Login - Separate from admin and employee
 */
require_once __DIR__ . '/portal-headers.php';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Blog Portal - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="Blog portal inloggen voor <?php echo SITE_NAME; ?>">
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/svg+xml" href="<?php echo assetPath('frontend/Images/logo-white.svg'); ?>">
    <link rel="apple-touch-icon" href="<?php echo assetPath('frontend/Images/logo-white.svg'); ?>">
    <link rel="stylesheet" href="<?php echo assetPath('frontend/css/styles.css'); ?>">
    <link rel="stylesheet" href="<?php echo assetPath('pages/pages-consolidated.css'); ?>">
</head>
<body>
    <div class="admin-login-container">
        <div class="admin-login-card">
            <div class="admin-login-logo">
                <img src="<?php echo assetPath('frontend/Images/logo-white.svg'); ?>" alt="<?php echo SITE_NAME; ?>" style="filter: brightness(0) saturate(100%) invert(27%) sepia(100%) saturate(2000%) hue-rotate(200deg) brightness(100%) contrast(100%);">
                <h1 class="admin-login-title">Blog Portal</h1>
                <p class="admin-login-subtitle">Artikelen schrijven en publiceren</p>
            </div>
            
            <div id="blogError" class="admin-error"></div>
            <div id="blogSuccess" class="admin-success"></div>
            
            <form id="blogLoginForm">
                <div class="admin-form-group">
                    <label for="username">Gebruikersnaam</label>
                    <input type="text" id="username" name="username" required autocomplete="username">
                </div>
                
                <div class="admin-form-group">
                    <label for="password">Wachtwoord</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password">
                </div>
                
                <button type="submit" class="admin-login-btn" id="loginBtn">Inloggen</button>
            </form>
            
            <div id="blogLoading" class="admin-loading">
                <div class="admin-spinner"></div>
                <p>Inloggen...</p>
            </div>
            
            <a href="/" class="admin-back-link">← Terug naar website</a>
        </div>
    </div>

    <script>
        (function() {
            const form = document.getElementById('blogLoginForm');
            const usernameInput = document.getElementById('username');
            const passwordInput = document.getElementById('password');
            const loginBtn = document.getElementById('loginBtn');
            const errorDiv = document.getElementById('blogError');
            const successDiv = document.getElementById('blogSuccess');
            const loadingDiv = document.getElementById('blogLoading');

            const apiUrl = window.location.origin + '/blog-portal/api.php';

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const username = usernameInput.value.trim();
                const password = passwordInput.value;
                if (!username || !password) {
                    errorDiv.textContent = 'Voer zowel gebruikersnaam als wachtwoord in';
                    errorDiv.style.display = 'block';
                    successDiv.style.display = 'none';
                    return;
                }
                loadingDiv.style.display = 'block';
                loginBtn.disabled = true;
                errorDiv.style.display = 'none';
                successDiv.style.display = 'none';
                try {
                    const res = await fetch(apiUrl, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        credentials: 'include',
                        body: JSON.stringify({ action: 'blogLogin', username: username, password: password })
                    });
                    const data = await res.json();
                    if (data.success) {
                        successDiv.textContent = 'Inloggen succesvol! Doorverwijzen...';
                        successDiv.style.display = 'block';
                        setTimeout(() => { window.location.href = '/blog-portal/dashboard'; }, 800);
                    } else {
                        errorDiv.textContent = data.message || 'Ongeldige gebruikersnaam of wachtwoord';
                        errorDiv.style.display = 'block';
                        loadingDiv.style.display = 'none';
                        loginBtn.disabled = false;
                    }
                } catch (err) {
                    errorDiv.textContent = 'Er is een fout opgetreden bij het verbinden.';
                    errorDiv.style.display = 'block';
                    loadingDiv.style.display = 'none';
                    loginBtn.disabled = false;
                }
            });

            const params = new URLSearchParams(window.location.search);
            if (params.get('expired') === '1') {
                errorDiv.textContent = 'Sessie verlopen. Log opnieuw in.';
                errorDiv.style.display = 'block';
            }
        })();
    </script>
</body>
</html>
