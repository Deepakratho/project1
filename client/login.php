
<style>
    * {
        box-sizing: border-box;
    }

    .login-container {
        min-height: 85vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 30px 15px;
        background: linear-gradient(135deg, #e0e7ff, #f5f3ff);
    }

    .login-card {
        width: 100%;
        max-width: 420px;
        padding: 35px;
        background: #ffffff;
        border-radius: 18px;
        box-shadow: 0 10px 35px rgba(0, 0, 0, 0.12);
    }

    .login-card h1 {
        text-align: center;
        margin-bottom: 10px;
        color: #312e81;
        font-size: 30px;
        font-weight: 700;
    }

    .login-subtitle {
        text-align: center;
        color: #6b7280;
        margin-bottom: 28px;
        font-size: 14px;
    }

    .login-field {
        margin-bottom: 20px;
    }

    .login-field label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #374151;
    }

    .login-field input {
        width: 100%;
        padding: 13px 14px;
        border: 1px solid #d1d5db;
        border-radius: 9px;
        font-size: 15px;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .login-field input:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
    }

    .login-button {
        width: 100%;
        padding: 13px;
        background: #4f46e5;
        color: white;
        border: none;
        border-radius: 9px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s, transform 0.2s;
    }

    .login-button:hover {
        background: #3730a3;
        transform: translateY(-1px);
    }

    .login-footer {
        text-align: center;
        margin-top: 22px;
        color: #6b7280;
        font-size: 14px;
    }

    .login-footer a {
        color: #4f46e5;
        font-weight: 600;
        text-decoration: none;
    }

    .login-footer a:hover {
        text-decoration: underline;
    }

    /* Login warning messages */
    .login-alert {
        padding: 12px 14px;
        margin-bottom: 20px;
        border-radius: 9px;
        font-size: 14px;
        line-height: 1.5;
    }

    .login-alert a {
        font-weight: 700;
        color: inherit;
    }

    .login-alert-warning {
        color: #854d0e;
        background: #fef3c7;
        border: 1px solid #fde68a;
    }

    .login-alert-error {
        color: #991b1b;
        background: #fee2e2;
        border: 1px solid #fecaca;
    }

    @media (max-width: 480px) {
        .login-card {
            padding: 25px 20px;
        }
    }
</style>

<div class="login-container">
    <div class="login-card">

        <h1>Welcome Back</h1>
        <p class="login-subtitle">Login to your account</p>

        <?php if (isset($_GET['error'])): ?>

            <?php if ($_GET['error'] === 'not_registered'): ?>
                <div class="login-alert login-alert-warning">
                    This account is not registered.
                    Please sign up first.
                    <a href="?signup=true">Sign Up</a>
                </div>

            <?php elseif ($_GET['error'] === 'wrong_password'): ?>
                <div class="login-alert login-alert-error">
                    Incorrect password. Please try again.
                </div>
            <?php endif; ?>

        <?php endif; ?>

        <form action="./server/request.php" method="POST">

            <div class="login-field">
                <label for="login-identifier">Email or Username</label>
                <input
                    type="text"
                    name="login-identifier"
                    id="login-identifier"
                    placeholder="Enter your email or username"
                    autocomplete="username"
                    required
                >
            </div>

            <div class="login-field">
                <label for="login-password">Password</label>
                <input
                    type="password"
                    name="password"
                    id="login-password"
                    placeholder="Enter your password"
                    autocomplete="current-password"
                    required
                >
            </div>

            <button type="submit" class="login-button">
                Login
            </button>

        </form>

        <p class="login-footer">
            Don't have an account?
            <a href="?signup=true">Sign Up</a>
        </p>

    </div>
</div>
