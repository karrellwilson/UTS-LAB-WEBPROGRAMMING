<?php
include('config.php');
session_start();
$message = ''; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);

    // Cek apakah username atau email sudah ada
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['message'] = "<p class='bg-red-100 text-red-800 p-3 rounded text-center'>Username or Email already exists!</p>";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "<p class='bg-green-100 text-green-800 p-3 rounded text-center'>Registration successful! You can now <a href='login.php' class='text-blue-500 hover:underline'>login</a>.</p>";
        } else {
            $_SESSION['message'] = "<p class='bg-red-100 text-red-800 p-3 rounded text-center'>Registration failed. Please try again.</p>";
        }
    }

    $stmt->close();

    header("Location: register.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="../css/output.css" rel="stylesheet">
</head>
<body class="bg-blue-50 min-h-screen flex items-center justify-center bg-cover bg-center font-mono">
    <div class="bg-white bg-opacity-90 backdrop-blur-lg rounded-3xl shadow-lg p-8 max-w-md w-full">

        <!-- Pesan Alert -->
        <?php
        if (isset($_SESSION['message'])) {
            echo "<div class='mb-4'>" . $_SESSION['message'] . "</div>";
            unset($_SESSION['message']); 
        }
        ?>

        <h2 class="text-3xl font-bold text-center text-gray-800 mb-4">Sign Up</h2>
        <p class="text-center text-gray-500 mb-6">Create your account to manage your to-do list efficiently.</p>
        
        <!-- Form Register -->
        <form method="POST" action="register.php" class="space-y-4">
            <div>
                <input class="w-full mt-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" 
                       type="text" name="username" placeholder="Username" required>
            </div>
            <div>
                <input class="w-full mt-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" 
                       type="email" name="email" placeholder="Email" required>
            </div>
            <div class="relative">
                <input class="w-full mt-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" 
                       type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="w-full py-2 bg-black text-white font-semibold rounded-lg hover:bg-gray-500 transition duration-300">Register</button>
        </form>

        <div class="text-center mt-4">
            <p class="text-xs text-gray-500">Already have an account? <a href="login.php" class="text-blue-500 hover:underline">Log In</a></p>
        </div>
    </div>
</body>
</html>
