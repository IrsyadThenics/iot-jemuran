<?php
include "../api/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username   = $_POST['username'];
    $email      = $_POST['email'];
    $password   = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $created_at = date('Y-m-d H:i:s');

    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
$stmt->bind_param("ss", $username, $password);
$stmt->execute();
if ($stmt->affected_rows > 0) {
        $success = "Registrasi berhasil. Silakan <a href='login.php'>login</a>.";
    } else {
        $error = "Terjadi kesalahan. Silakan coba lagi.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - IoT Jemuran</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            position: fixed;
            width: 500px;
            height: 500px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            top: -250px;
            right: -250px;
            animation: float 6s ease-in-out infinite;
            z-index: 0;
        }
        
        body::after {
            content: '';
            position: fixed;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            bottom: -150px;
            left: -150px;
            animation: float 8s ease-in-out infinite;
            z-index: 0;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 25px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 50px 45px;
            width: 100%;
            max-width: 450px;
            position: relative;
            z-index: 1;
            backdrop-filter: blur(10px);
            animation: fadeInUp 0.8s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .logo {
            text-align: center;
            margin-bottom: 35px;
        }
        
        .logo h2 {
            font-size: 2.2em;
            color: #667eea;
            font-weight: 800;
            margin-bottom: 8px;
        }
        
        .logo p {
            color: #666;
            font-size: 14px;
        }
        
        h2 {
            font-size: 2.2em;
            color: #667eea;
            font-weight: 800;
            margin-bottom: 8px;
            text-align: center;
        }
        
        .success {
            padding: 12px 18px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
            animation: slideDown 0.3s ease-out;
            text-align: center;
        }
        
        .success a {
            color: #065f46;
            font-weight: 700;
            text-decoration: underline;
        }
        
        .error {
            padding: 12px 18px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
            animation: slideDown 0.3s ease-out;
            text-align: center;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .form-group {
            margin-bottom: 25px;
            text-align: left;
        }
        
        .form-group label {
            display: block;
            color: #333;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .form-group input {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #e0e7ff;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: #f9fafb;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }
        
        .form-group input::placeholder {
            color: #999;
        }
        
        .password-wrapper {
            position: relative;
        }
        
        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            padding: 5px;
            transition: color 0.3s ease;
            font-size: 18px;
        }
        
        .toggle-password:hover {
            color: #667eea;
        }
        
        .btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .login-link {
            text-align: center;
            margin-top: 25px;
            font-size: 14px;
            color: #555;
        }
        
        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        
        .login-link a:hover {
            color: #764ba2;
            text-decoration: underline;
        }
        
        @media (max-width: 480px) {
            .container {
                padding: 40px 30px;
            }
            
            h2 {
                font-size: 1.8em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <h2>🌤️ Register Admin</h2>
            <p>IoT Jemuran Pintar</p>
        </div>
        
        <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Masukkan username" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Masukkan email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" placeholder="Buat password" required>
                    <button type="button" class="toggle-password" onclick="togglePassword()">👁️</button>
                </div>
            </div>
            
            <button type="submit" class="btn">Daftar</button>
        </form>
        
        <p class="login-link">Sudah punya akun? <a href="login.php">Login disini</a></p>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const button = event.target;
            
            if (input.type === 'password') {
                input.type = 'text';
                button.textContent = '🙈';
            } else {
                input.type = 'password';
                button.textContent = '👁️';
            }
        }
    </script>
</body>
</html>