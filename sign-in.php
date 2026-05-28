<?php
session_start();

$error_message = '';

// Handle credentials submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        try {
            // Establish database connection
            $pdo = new PDO("mysql:host=localhost;dbname=loginsystem;charset=utf8", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Fetch record matching user input email
            $stmt = $pdo->prepare("SELECT idUsers, uidUsers, emailUsers, pwdUsers, roleUsers FROM users WHERE emailUsers = :email LIMIT 1");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify plaintext credentials against database record
            if ($user && $password === $user['pwdUsers']) {
                
                // Prevent session fixation exploits
                session_regenerate_id(true);

                // Map user variables to global session state
                $_SESSION['user_id']   = $user['idUsers'];   
                $_SESSION['user_name'] = $user['uidUsers'];  
                $_SESSION['user_role'] = $user['roleUsers'];  
                $_SESSION['logged_in'] = true;

                // Route successfully authorized account to student space
                header("Location: student.php"); 
                exit;
            } else {
                $error_message = "Invalid email or password.";
            }
        } catch (PDOException $e) {
            $error_message = "Database Error: " . $e->getMessage();
        }
    } else {
        $error_message = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="index.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <style>
        html, body {
            overflow: hidden; 
            height: auto;
        }
        #details-section { display: none; }
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 8px;
        }
        .error-message-alert {
            background-color: #FFEBEE;
            color: #D32F2F;
            border: 1px solid #FFCDD2;
            padding: 12px;
            border-radius: 4px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            margin-bottom: 15px;
            text-align: center;
        }
        .signin-container-overlay {
            position: relative !important; 
            top: 0 !important;
            margin: -80px auto 50px auto !important;
            transform: none !important;
            left: 0 !important;
        }
    </style>
</head>
<body>
    <header class="navbar">
        <div class="nav-brand">
            <div class="home-icon" onclick="window.location.href='index.php'" style="cursor: pointer;">
                <img src="images/home.png" class="home" alt="Home">
            </div>
            <h1 class="navbar-title">PUP-CEA Room Reservation System</h1>
        </div>
        <button class="signin" onclick="window.location.href='sign-in.php'">Sign In</button>
    </header>

    <section class="hero">
        <div class="sign-in-overlay">
            <h2>Welcome Back!</h2>
            <p>Sign in to continue to your account</p>
        </div>
    </section>

    <div class="signin-container-overlay">
        <main class="signin-card">
            <h2>Sign In</h2>

            <?php if (!empty($error_message)): ?>
                <div class="error-message-alert">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            
            <form action="sign-in.php" method="POST">
                <div class="form-group">
                    <label for="email">Email</label>
                    <div class="input-with-icon">
                        <img src="images/email.png" class="input-icon" alt="Email Icon">
                        <input type="email" id="email" name="email" placeholder="Enter your PUP webmail" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-with-icon">
                        <img src="images/password.png" class="input-icon" alt="Password Icon">
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                        <img src="images/hide.png" class="password-toggle" id="passwordToggle" alt="Hide Password">
                    </div>
                </div>

                <button type="submit" class="signin-submit-btn">Sign In</button>
            </form>
        </main>
    </div>
    
    <script>
        // DOM Handles for Visibility Toggle Logic
        const passwordToggle = document.getElementById('passwordToggle');
        const passwordInput = document.getElementById('password');

        // Toggle visibility state between hidden text and readable text
        passwordToggle.addEventListener('click', function () {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
            this.src = isPassword ? 'images/show.png' : 'images/hide.png';
            this.alt = isPassword ? 'Show Password' : 'Hide Password';
        });
    </script>
</body>
</html>