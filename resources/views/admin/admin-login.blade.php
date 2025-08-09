<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Inter', sans-serif;
            background:
                linear-gradient(135deg, rgba(25, 149, 179, 0.9) 0%, rgba(20, 122, 145, 0.9) 50%, rgba(15, 95, 112, 0.9) 100%),
                url('https://images.unsplash.com/photo-1557804506-669a67965ba0?ixlib=rb-4.0.3&auto=format&fit=crop&w=2574&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url("data:image/svg+xml,%3Csvg width='80' height='80' viewBox='0 0 80 80' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M0 0h80v80H0V0zm20 20v40h40V20H20zm20 35a15 15 0 1 1 0-30 15 15 0 0 1 0 30z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
            animation: drift 30s infinite linear;
            pointer-events: none;
        }

        body::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 30% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                        radial-gradient(circle at 80% 80%, rgba(25, 149, 179, 0.2) 0%, transparent 50%),
                        radial-gradient(circle at 40% 90%, rgba(255, 255, 255, 0.05) 0%, transparent 50%);
            pointer-events: none;
        }

        @keyframes drift {
            0% { transform: translate(0, 0); }
            25% { transform: translate(-10px, -10px); }
            50% { transform: translate(5px, -5px); }
            75% { transform: translate(-5px, 10px); }
            100% { transform: translate(0, 0); }
        }

        .login-container {
            /* Enhanced glassmorphism effect */
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(25px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow:
                0 32px 64px rgba(0, 0, 0, 0.25),
                inset 0 1px 0 rgba(255, 255, 255, 0.3);
            padding: 48px 40px;
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 1;
        }



        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            margin-bottom: 10px;
            color: #1995B3;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-wrapper {
            position: relative;
        }

        .form-input {
            width: 100%;
            padding: 16px 20px 16px 50px;
            border: 2px solid #000000;
            border-radius: 16px;
            font-size: 16px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: rgba(255, 255, 255, 0.95);
            color: #1E293B;
            backdrop-filter: blur(20px);
        }

        .form-input:focus {
            outline: none;
            border-color: #1995B3;
            background: white;
            box-shadow: 0 0 0 4px rgba(25, 149, 179, 0.1);
            transform: translateY(-1px);
        }

        .input-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748B;
            transition: color 0.3s ease;
            z-index: 2;
        }

        .form-input:focus + .input-icon {
            color: #1995B3;
        }

        .password-wrapper {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #64748B;
            cursor: pointer;
            padding: 8px;
            border-radius: 8px;
            transition: all 0.2s ease;
            z-index: 2;
        }

        .password-toggle:hover {
            color: #1995B3;
            background: rgba(25, 149, 179, 0.1);
        }

        .form-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .form-checkbox {
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 6px;
            appearance: none;
            cursor: pointer;
            position: relative;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.1);
        }

        .form-checkbox:checked {
            background: linear-gradient(135deg, #1995B3, #147A91);
            border-color: #1995B3;
        }

        .form-checkbox:checked::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 12px;
            font-weight: bold;
        }

        .checkbox-label {
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
        }

        .forgot-link {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s ease;
            position: relative;
        }

        .forgot-link:hover {
            color: white;
            text-decoration: underline;
        }

        .login-btn {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #1995B3 0%, #147A91 100%);
            color: white;
            border: none;
            border-radius: 16px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .login-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .login-btn:hover::before {
            left: 100%;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 40px rgba(25, 149, 179, 0.4);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .login-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .btn-content {
            position: relative;
            z-index: 2;
        }

        .loading-spinner {
            display: none;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }

        .spinner {
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .error-message {
            color: #FF6B6B;
            font-size: 13px;
            font-weight: 500;
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .error-message::before {
            content: '⚠';
            font-size: 14px;
        }

        .footer-text {
            text-align: center;
            margin-top: 32px;
            color: rgba(255, 255, 255, 0.7);
            font-size: 12px;
        }

        .visually-hidden {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 32px 24px;
                margin: 16px;
                border-radius: 20px;
            }

            .brand-title {
                font-size: 28px;
            }

            .form-input {
                padding: 14px 16px 14px 44px;
            }

            .login-btn {
                padding: 16px;
            }
        }
    </style>
</head>
<body>
    {{-- This is a Laravel Blade template for admin login --}}
    <div class="login-container">
        <form class="login-form" wire:submit.prevent='submit'>
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <div class="input-wrapper">
                    <input
                        type="email"
                        id="email"
                        class="form-input"
                        placeholder="Enter your email"
                        autofocus
                        wire:model='email'
                    />
                    <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                </div>
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <div class="input-wrapper">
                    <div class="password-wrapper">
                        <input
                            type="password"
                            id="password"
                            class="form-input"
                            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                            wire:model='password'
                        />
                        <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <circle cx="12" cy="16" r="1"></circle>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"></path>
                                <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"></path>
                                <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"></path>
                                <line x1="2" y1="2" x2="22" y2="22"></line>
                            </svg>
                        </button>
                    </div>
                </div>
                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-row">
                <div class="checkbox-wrapper">
                    <input
                        type="checkbox"
                        id="remember"
                        class="form-checkbox"
                        wire:model='remember'
                    />
                    <label for="remember" class="checkbox-label">Remember Me</label>
                </div>
                <a href="auth-forgot-password-basic.html" class="forgot-link">
                    Forgot Password?
                </a>
            </div>

            <button class="login-btn" type="submit" wire:loading.attr='disabled'>
                <div class="btn-content">
                    <span wire:loading.remove>Sign In</span>
                    <div class="loading-spinner" wire:loading>
                        <div class="spinner"></div>
                        <span>Authenticating...</span>
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </button>
        </form>

        <p class="footer-text">
            Protected by enterprise-grade security
        </p>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleButton = document.querySelector('.password-toggle');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleButton.innerHTML = `
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                `;
            } else {
                passwordInput.type = 'password';
                toggleButton.innerHTML = `
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"></path>
                        <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"></path>
                        <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"></path>
                        <line x1="2" y1="2" x2="22" y2="22"></line>
                    </svg>
                `;
            }
        }
    </script>
</body>
</html>
