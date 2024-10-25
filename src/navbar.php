<?php
include 'config.php';

$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'User';
$userId = isset($_SESSION['id']) ? $_SESSION['id'] : ''; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar</title>
    <link href="/css/output.css" rel="stylesheet">
</head>
<body>
<nav class="bg-white shadow font-mono">
        <div class="container mx-auto flex items-center justify-between p-4">
            <div class="flex items-center">
                <a href="dashboard.php" class="text-xl font-bold text-green-800 ml-14 hover:text-green-600">T O D O L I S T.</a>
            </div>
            
            <div class="flex items-center mr-14">
                <span class="text-green-800 text-sm mr-2">Welcome, <?php echo htmlspecialchars($username); ?>!</span>

                <button onclick="window.location.href='profile.php'" class="text-green-500 hover:text-green-700 hover:scale-105 transition-transform duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="w-4 h-4 mr-10">
                        <path fill="#2e511f" d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512l388.6 0c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304l-91.4 0z"/>
                    </svg>
                </button>

                <button onclick="window.location.href='logout.php'" class="text-green-500 hover:text-green-700 hover:scale-105 transition-transform duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="w-4 h-4 mr-4">
                        <path fill="#2e511f" d="M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1-128 0c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96L96 96c-17.7 0-32 14.3-32 32l0 256c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0c-53 0-96-43-96-96L0 128C0 75 43 32 96 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32z"/>
                    </svg>
                </button>
            </div>
        </div>
    </nav>
</body>
</html>