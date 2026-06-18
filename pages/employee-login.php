<?php
/**
 * Employee Login Page - Nijenhuis Botenverhuur
 * Allows employees to log in for manual booking creation
 */
require_once __DIR__ . '/../components/config.php';
$basePath = '..';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <?php include __DIR__ . '/../components/gtag.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Medewerker Inloggen - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="Medewerker login voor <?php echo SITE_NAME; ?>">
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
                <h1 class="admin-login-title">Medewerker inloggen</h1>
                <p class="admin-login-subtitle">Handmatige Reserveringen</p>
            </div>
            
            <div id="employeeError" class="admin-error"></div>
            <div id="employeeSuccess" class="admin-success"></div>
            
            <form id="employeeLoginForm">
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
            
            <div id="employeeLoading" class="admin-loading">
                <div class="admin-spinner"></div>
                <p>Inloggen...</p>
            </div>
            
            <a href="/" class="admin-back-link">← Terug naar website</a>
        </div>
    </div>

    <script>
        class EmployeeLogin {
            constructor() {
                this.form = document.getElementById('employeeLoginForm');
                this.usernameInput = document.getElementById('username');
                this.passwordInput = document.getElementById('password');
                this.loginBtn = document.getElementById('loginBtn');
                this.errorDiv = document.getElementById('employeeError');
                this.successDiv = document.getElementById('employeeSuccess');
                this.loadingDiv = document.getElementById('employeeLoading');
                
                this.init();
            }
            
            async init() {
                this.form.addEventListener('submit', (e) => this.handleLogin(e));
                
                const loggedIn = await this.isLoggedIn();
                if (loggedIn) {
                    this.redirectToPortal();
                }
            }
            
            detectServerEndpoint() {
                if (window.location.protocol === 'file:' || window.location.hostname === '') {
                    return 'http://localhost:8000/admin/booking-handler.py';
                }
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
                            action: 'employeeLogin',
                            username: username,
                            password: password
                        })
                    });
                    
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        const result = await response.json();
                        
                        if (result.success) {
                            this.showSuccess('Inloggen succesvol! Doorverwijzen...');
                            setTimeout(() => { this.redirectToPortal(); }, 1000);
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
            
            redirectToPortal() {
                const baseUrl = window.location.origin;
                window.location.href = `${baseUrl}/pages/employee-portal.php`;
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
                    
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        const result = await response.json();
                        return !!(result.success && result.authenticated);
                    }
                    return false;
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
            new EmployeeLogin();
        });
    </script>
    <!-- Chatbot Widget -->
</body>
</html>
