
<style>
    body {
        background: linear-gradient(135deg, #e0e7ff, #f5f3ff);
        font-family: Arial, sans-serif;
        min-height: 100vh;
    }

    .signup-container {
        max-width: 480px;
        margin: 45px auto;
        padding: 35px;
        background: #ffffff;
        border-radius: 18px;
        box-shadow: 0 10px 35px rgba(44, 62, 120, 0.15);
    }

    .signup-heading {
        text-align: center;
        color: #4338ca;
        font-size: 30px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .signup-subtitle {
        text-align: center;
        color: #777;
        font-size: 14px;
        margin-bottom: 28px;
    }

    .signup-form .form-label {
        display: block;
        color: #333;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .signup-form .form-control {
        width: 100%;
        box-sizing: border-box;
        padding: 12px 14px;
        border: 1px solid #d5d9e5;
        border-radius: 9px;
        font-size: 15px;
        background: #fafbff;
        transition: border-color 0.3s, box-shadow 0.3s;
    }

    .signup-form .form-control:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        outline: none;
        background: #fff;
    }

    .signup-field {
        margin-bottom: 20px;
    }

    .signup-button {
        width: 100%;
        padding: 13px;
        border: none;
        border-radius: 9px;
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        color: white;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .signup-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(79, 70, 229, 0.3);
    }

    .signup-footer {
        text-align: center;
        margin-top: 22px;
        font-size: 14px;
        color: #666;
    }

    .signup-footer a {
        color: #4f46e5;
        font-weight: 600;
        text-decoration: none;
    }

    .signup-footer a:hover {
        text-decoration: underline;
    }

    @media (max-width: 576px) {
        .signup-container {
            margin: 25px 15px;
            padding: 25px 20px;
        }
    }
</style>

<div class="container">
    <div class="signup-container">

        <h1 class="signup-heading">Create Account</h1>
        <p class="signup-subtitle">
            Sign up to get started with us
        </p>

        <form class="signup-form"
              method="POST"
              action="./server/request.php">

            <div class="signup-field">
                <label for="username" class="form-label">
                    Username
                </label>
                <input
                    type="text"
                    name="username"
                    class="form-control"
                    id="username"
                    placeholder="Enter your username"
                    autocomplete="username"
                    required>
            </div>

            <div class="signup-field">
                <label for="email" class="form-label">
                    Email Address
                </label>
                <input
                    type="email"
                    name="email"
                    class="form-control"
                    id="email"
                    placeholder="Enter your email"
                    autocomplete="email"
                    required>
            </div>

            <div class="signup-field">
                <label for="password" class="form-label">
                    Password
                </label>
                <input
                    type="password"
                    name="password"
                    class="form-control"
                    id="password"
                    placeholder="Create a password"
                    autocomplete="new-password"
                    minlength="8"
                    required>
            </div>

            <div class="signup-field">
                <label for="address" class="form-label">
                    Address
                </label>
                <input
                    type="text"
                    name="address"
                    class="form-control"
                    id="address"
                    placeholder="Enter your address"
                    autocomplete="street-address"
                    required>
            </div>

            <button type="submit"
                    name="signup"
                    class="signup-button">
                Create Account
            </button>

        </form>

        <div class="signup-footer">
            Already have an account?
            <a href="?login=true">Login</a>
        </div>

    </div>
</div>
