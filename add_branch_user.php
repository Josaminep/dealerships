<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Branch User</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            color: #343a40;
            margin: 0;
            padding: 0;
        }
        .content {
            margin: 0 auto;
            padding: 20px;
            max-width: 600px;
            box-sizing: border-box;
            margin-right: 300px;
        }
        .header {
            background-color: #003366;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
            margin-top: 50px;
        }
        h1 {
            font-size: 28px;
            color: white;
            margin: 0;
        }
        form {
            display: flex;
            flex-direction: column;
            background-color: #ffffff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        label {
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, select {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ced4da;
            font-size: 16px;
        }
        button {
            padding: 10px;
            background-color: #003366;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
            width: 45%;
            display: block;
            margin: 0 auto;
        }
        button:hover {
            background-color: #ebd72a;
            color: black;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .password-strength {
            height: 5px;
            width: 200px; /* Shortened width */
            background-color: #e0e0e0;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .password-strength-bar {
            height: 5px;
            border-radius: 5px;
            transition: width 0.3s;
            display: block;
            margin-left: 20px; /* Slightly shifted left */
        }
        .error {
            color: red;
            font-size: 14px;
            display: none; /* Hidden by default */
        }
    </style>
</head>

<?php include 'sidebar.php'; ?>

<body>
    <div class="content">
        <div class="header">
            <h1>Add Branch User</h1>
        </div>

        <form id="userForm" method="POST" action="">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required oninput="checkPasswordStrength()">
                <div class="password-strength">
                    <div class="password-strength-bar" id="strength-bar"></div>
                </div>
                <span class="error" id="password-error">Password is too weak!</span>
            </div>

            <div class="form-group">
                <label for="branch_id">Branch ID:</label>
                <select name="branch_id" id="branch_id" required>
                    <option value="" disabled selected>Select Branch ID</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>

            <button type="submit">Add User</button>
        </form>
    </div>

    <script>
        let passwordStrength = 0; // Global variable to track password strength

        function checkPasswordStrength() {
            const password = document.getElementById('password').value;
            const strengthBar = document.getElementById('strength-bar');
            const errorText = document.getElementById('password-error');
            passwordStrength = 0;

            // Check the strength of the password
            if (password.length >= 8) passwordStrength += 1;
            if (/[A-Z]/.test(password)) passwordStrength += 1;
            if (/[a-z]/.test(password)) passwordStrength += 1;
            if (/[0-9]/.test(password)) passwordStrength += 1;
            if (/[\W]/.test(password)) passwordStrength += 1;

            // Update the strength bar and color
            switch (passwordStrength) {
                case 1:
                    strengthBar.style.width = '20%';
                    strengthBar.style.backgroundColor = 'red';
                    break;
                case 2:
                    strengthBar.style.width = '40%';
                    strengthBar.style.backgroundColor = 'orange';
                    break;
                case 3:
                    strengthBar.style.width = '60%';
                    strengthBar.style.backgroundColor = 'yellow';
                    break;
                case 4:
                    strengthBar.style.width = '80%';
                    strengthBar.style.backgroundColor = 'lightgreen';
                    break;
                case 5:
                    strengthBar.style.width = '100%';
                    strengthBar.style.backgroundColor = 'green';
                    break;
                default:
                    strengthBar.style.width = '0%';
                    strengthBar.style.backgroundColor = 'transparent';
                    break;
            }

            // Show/hide error message
            if (passwordStrength < 3) {
                errorText.style.display = 'block';
            } else {
                errorText.style.display = 'none';
            }
        }

        // Prevent form submission if password is weak
        document.getElementById('userForm').addEventListener('submit', function(event) {
            if (passwordStrength < 3) {
                event.preventDefault();
                alert("Please choose a stronger password.");
            }
        });
    </script>
</body>
</html>
