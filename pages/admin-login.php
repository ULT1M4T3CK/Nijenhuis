<?php
/**
 * Admin Login Page - Nijenhuis Botenverhuur
 */
require_once __DIR__ . '/../components/config.php';
$basePath = '..';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include __DIR__ . '/../components/gtag.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Admin Login - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="Admin login voor <?php echo SITE_NAME; ?>">
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/svg+xml" href="<?php echo assetPath('frontend/Images/logo-white.svg'); ?>">
    <link rel="stylesheet" href="<?php echo assetPath('frontend/css/styles.css'); ?>">
    <link rel="stylesheet" href="<?php echo assetPath('pages/pages-consolidated.css'); ?>">
</head>
<body>
    <?php include __DIR__ . '/../components/gtm-body.php'; ?>
    <div class="admin-login-container">
        <div class="admin-login-card">
            <div class="admin-login-logo">
                <img src="<?php echo assetPath('frontend/Images/logo-white.svg'); ?>" alt="<?php echo SITE_NAME; ?>" style="filter: brightness(0) saturate(100%) invert(27%) sepia(100%) saturate(2000%) hue-rotate(200deg) brightness(100%) contrast(100%);">
                <h1 class="admin-login-title">Admin inloggen</h1>
                <p class="admin-login-subtitle">Reservering Beheer Systeem</p>
            </div>
            
            <div id="adminError" class="admin-error"></div>
            <div id="adminSuccess" class="admin-success"></div>
            
            <form id="adminLoginForm">
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
            
            <div id="adminLoading" class="admin-loading">
                <div class="admin-spinner"></div>
                <p>Logging in...</p>
            </div>
            
            <a href="/" class="admin-back-link">← Back to Website</a>
        </div>
    </div>

    <script>
        class AdminLogin {
            constructor() {
                this.form = document.getElementById('adminLoginForm');
                this.usernameInput = document.getElementById('username');
                this.passwordInput = document.getElementById('password');
                this.loginBtn = document.getElementById('loginBtn');
                this.errorDiv = document.getElementById('adminError');
                this.successDiv = document.getElementById('adminSuccess');
                this.loadingDiv = document.getElementById('adminLoading');
                
                this.init();
            }
            
            async init() {
                this.form.addEventListener('submit', (e) => this.handleLogin(e));
                
                // Don't auto-redirect on login page - let user stay on login page even if already logged in
                // This prevents redirect loops if session check fails
                // const loggedIn = await this.isLoggedIn();
                // if (loggedIn) {
                //     this.redirectToAdmin();
                // }
            }
            
            detectServerEndpoint() {
                // If opened directly as a file (file://), use Python backend
                // This is only for local development when opening HTML files directly
                if (window.location.protocol === 'file:' || window.location.hostname === '') {
                    return 'http://localhost:8000/admin/booking-handler.py';
                }
                // When served via any web server (localhost PHP or production), use PHP handler
                const basePath = window.location.origin;
                return `${basePath}/admin/booking-handler.php`;
            }
            
            async handleLogin(e) {
                e.preventDefault();
                const username = this.usernameInput.value.trim();
                const password = this.passwordInput.value;
                
                if (!username || !password) {
                    this.showError('Voer zowel gebruikersnaam als wachtwoord in');
                    return;
                }
                
                this.showLoading(true);
                
                try {
                    const endpoint = this.detectServerEndpoint();
                    const response = await fetch(endpoint, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        credentials: 'include',
                        body: JSON.stringify({
                            action: 'login',
                            username: username,
                            password: password
                        })
                    });
                    
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        const result = await response.json();
                        
                        if (result.success) {
                            this.showSuccess('Inloggen succesvol! Doorverwijzen...');
                            setTimeout(() => { this.redirectToAdmin(); }, 1000);
                            return;
                        } else {
                            this.showError(result.message || 'Ongeldige gebruikersnaam of wachtwoord');
                        }
                    } else {
                        const text = await response.text();
                        if (text.includes('<!DOCTYPE') || text.includes('<html')) {
                            this.showError('Server error. Zorg dat de server correct draait.');
                        } else {
                            this.showError('Server gaf geen geldige response. Probeer het opnieuw.');
                        }
                    }
                } catch (error) {
                    console.error('Login error:', error);
                    this.showError('Er is een fout opgetreden bij het verbinden met de server.');
                }
                
                this.showLoading(false);
            }
            
            redirectToAdmin() {
                const baseUrl = window.location.origin;
                window.location.href = `${baseUrl}/admin/boat-management.php`;
            }
            
            async isLoggedIn() {
                try {
                    const endpoint = this.detectServerEndpoint();
                    const sessionUrl = endpoint.includes('.php') 
                        ? endpoint.replace('booking-handler.php', 'booking-handler.php?action=session')
                        : endpoint.replace('booking-handler.py', 'booking-handler.py?action=session');
                    const response = await fetch(sessionUrl, {
                        method: 'GET',
                        credentials: 'include'
                    });
                    
                    if (!response.ok) {
                        return false;
                    }
                    
                    // Read response as text first
                    const responseText = await response.text();
                    if (!responseText || responseText.trim() === '') {
                        return false;
                    }
                    
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        return false;
                    }
                    
                    let result;
                    try {
                        result = JSON.parse(responseText);
                    } catch (parseError) {
                        console.error('Session check error: Invalid JSON', responseText);
                        return false;
                    }
                    
                    return !!(result.success && result.authenticated);
                } catch (error) {
                    console.error('Session check error:', error);
                    return false;
                }
            }
            
            clearSession() {
                sessionStorage.removeItem('adminSessionToken');
                sessionStorage.removeItem('adminLoginTime');
                localStorage.removeItem('adminAuthenticated');
                localStorage.removeItem('adminUser');
            }
            
            showError(message) {
                this.errorDiv.textContent = message;
                this.errorDiv.style.display = 'block';
                this.successDiv.style.display = 'none';
            }
            
            showSuccess(message) {
                this.successDiv.textContent = message;
                this.successDiv.style.display = 'block';
                this.errorDiv.style.display = 'none';
            }
            
            showLoading(show) {
                this.loadingDiv.style.display = show ? 'block' : 'none';
                this.loginBtn.disabled = show;
            }
        }
        
        document.addEventListener('DOMContentLoaded', () => {
            new AdminLogin();
        });
    </script>
    <!-- Chatbot Widget -->
</body>
</html>

