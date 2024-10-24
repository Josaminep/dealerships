<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Registration & Login Portal</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #667eea, #764ba2);
        }

        .container {
            width: 400px; /* Reduced from 600px */
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            display: flex;
            overflow: hidden;
        }

        .form-container {
            flex: 1;
            padding: 25px; /* Reduced from 30px */
            transition: all 0.5s ease;
            display: flex; /* Set to flex */
            flex-direction: column; /* Stack elements vertically */
            justify-content: center; /* Center vertically */
            align-items: center; /* Center horizontally */
        }

        .switch-form {
            text-align: center;
            margin-top: 20px;
        }

        .switch-form p {
            color: #666;
            margin-bottom: 10px;
        }

        .switch-btn {
            color: #667eea;
            cursor: pointer;
            font-weight: 600;
            text-decoration: underline;
        }

        h2 {
            color: #333;
            margin-bottom: 25px;
            text-align: center;
            font-size: 24px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            color: #555;
            margin-bottom: 8px;
            font-size: 14px;
        }

        input {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        input:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 10px rgba(102, 126, 234, 0.1);
        }

        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .error-message {
            color: #ff4444;
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }

        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
            font-size: 14px;
        }

        .alert-error {
            background-color: #ffe6e6;
            border: 1px solid #ff9999;
            color: #cc0000;
        }

        .social-login {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }

        .social-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px; /* Reduced from 35px */
            height: 32px; /* Reduced from 35px */
            border-radius: 50%;
            margin: 0 8px; /* Reduced from 10px */
            background: #f8f9fa;
            border: 1px solid #ddd;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .social-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Login Form -->
    <div class="form-container" id="login-container">
        <h2>Welcome Back!</h2>
        
        <?php if (isset($login_error)): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($login_error); ?>
            </div>
        <?php endif; ?>

        <form id="login-form" action="signin.php" method="POST">
            <div class="form-group">
                <label for="login-email">Email Address</label>
                <input type="email" id="login-email" name="email" required>
                <div class="error-message" id="login-email-error">Please enter a valid email address</div>
            </div>

            <div class="form-group">
                <label for="login-password">Password</label>
                <input type="password" id="login-password" name="password" required>
                <div class="error-message" id="login-password-error">Please enter your password</div>
            </div>

            <button type="submit">Login</button>

            <div class="switch-form">
                <p>Don't have an account?</p>
                <span class="switch-btn" id="show-signup">Sign Up</span>
            </div>
        </form>
    </div>

 <!-- Signup Form -->
<div class="form-container" id="signup-container" style="display: none;">
    <h2>Create Account</h2>
    
    <?php if (isset($signup_error)): ?>
        <div class="alert alert-error">
            <?php echo htmlspecialchars($signup_error); ?>
        </div>
    <?php endif; ?>

    <form id="signup-form" action="signup.php" method="POST">
        <div class="form-group">
            <label for="fullname">Name</label>
            <input type="text" id="fullname" name="fullname" required>
            <div class="error-message" id="fullname-error">Please enter your name</div>
        </div>

        <div class="form-group">
            <label for="signup-email">Email Address</label>
            <input type="email" id="signup-email" name="email" required>
            <div class="error-message" id="signup-email-error">Please enter a valid email address</div>
        </div>
        
        <div class="form-group">
            <label for="signup-password">Password</label>
            <input type="password" id="signup-password" name="password" required>
            <div class="error-message" id="signup-password-error">Password must be at least 8 characters</div>
        </div>

        <div class="form-group">
            <label for="confirm-password">Confirm Password</label>
            <input type="password" id="confirm-password" name="confirm_password" required>
            <div class="error-message" id="confirm-password-error">Passwords do not match</div>
        </div>

        <button type="submit">Sign Up</button>

        <div class="switch-form">
            <p>Already have an account?</p>
            <span class="switch-btn" id="show-login">Login</span>
        </div>
    </form>
</div>


<script>
document.addEventListener('DOMContentLoaded', () => {
    const loginContainer = document.getElementById('login-container');
    const signupContainer = document.getElementById('signup-container');
    const showSignup = document.getElementById('show-signup');
    const showLogin = document.getElementById('show-login');

    showSignup.addEventListener('click', () => {
        loginContainer.style.display = 'none';
        signupContainer.style.display = 'flex';
    });

    showLogin.addEventListener('click', () => {
        signupContainer.style.display = 'none';
        loginContainer.style.display = 'flex';
    });

    // Form validation can be added here
});
</script>

</body>
</html>
