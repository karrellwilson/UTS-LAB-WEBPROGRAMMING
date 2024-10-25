<?php
session_start();
include('config.php');

// Cek apakah pengguna sudah login
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header('Location: dashboard.php');
    exit();
}

$error = "";

if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']); 
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if ($username && $password) {
        $stmt = $conn->prepare("SELECT id, username, email, password FROM users WHERE username = ?");
        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows == 1) {
                $stmt->bind_result($id, $db_username, $email, $db_password);
                $stmt->fetch();
                if (password_verify($password, $db_password)) {
                    // Set session variables
                    $_SESSION['loggedin'] = true;
                    $_SESSION['id'] = $id;
                    $_SESSION['username'] = $db_username;
                    $_SESSION['email'] = $email;

                    // Redirect ke dashboard setelah login
                    header('Location: dashboard.php');
                    exit();
                } else {
                    $_SESSION['error'] = "Invalid username or password!";
                    header('Location: login.php'); 
                    exit();
                }
            } else {
                $_SESSION['error'] = "Invalid username or password!";
                header('Location: login.php');
                exit();
            }
            $stmt->close();
        } else {
            $_SESSION['error'] = "Something went wrong. Please try again later.";
            header('Location: login.php'); 
            exit();
        }
    } else {
        $_SESSION['error'] = "Please enter both username and password.";
        header('Location: login.php'); 
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="/css/output.css" rel="stylesheet">
</head>
<body class="bg-blue-50 min-h-screen flex items-center justify-center bg-cover bg-center font-mono">
    <div class="bg-white bg-opacity-90 backdrop-blur-lg rounded-3xl shadow-lg p-8 max-w-md w-full">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-4">Log In</h2>
        <p class="text-center text-gray-500 mb-6">Creating your to-do lists that help manage tasks efficiently.</p>
        
        <!-- Alert jika ada pesan error -->
        <?php if (!empty($error)) : ?>
            <div class="bg-red-100 text-red-800 p-3 rounded mb-4 text-center">
                <strong>Error!</strong> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <!-- Login Form -->
        <form action="login.php" method="POST" class="space-y-4">
            <div>
                <input class="w-full mt-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" 
                       type="text" name="username" placeholder="Username" required>
            </div>
            <div class="relative">
                <input class="w-full mt-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" 
                       type="password" name="password" placeholder="Password" required>
            </div>
            
            <button type="submit" class="w-full py-2 bg-black text-white font-semibold rounded-lg hover:bg-gray-500 transition duration-300">Get Started</button>
        </form>

        <div class="text-center mt-4">
            <p class="text-xs text-gray-500">Don't have an account yet? <a href="register.php" class="text-blue-500 hover:underline">Sign Up</a></p>
        </div>
    </div>
</body>
</html>
