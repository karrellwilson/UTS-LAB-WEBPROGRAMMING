<?php
session_start();
include('config.php');

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit();
}

if (isset($_POST['mark_complete'])) {
    $assignment_id = $_POST['assignment_id'];
    $new_status = $_POST['status'];
    $query = "UPDATE assignments SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $new_status, $assignment_id);
    $stmt->execute();
}

$searchKeyword = '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

if (isset($_POST['search'])) {
    $searchKeyword = $conn->real_escape_string($_POST['search_keyword']);
}

$query = "SELECT * FROM assignments";

if ($filter == 'completed') {
    $query .= " WHERE status = 'completed'";
} elseif ($filter == 'unfinished') {
    $query .= " WHERE status = 'unfinished'";
}

if (!empty($searchKeyword)) {
    $query .= (strpos($query, 'WHERE') !== false ? ' AND' : ' WHERE') . " title LIKE '%$searchKeyword%' OR description LIKE '%$searchKeyword%'";
}

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="../css/output.css" rel="stylesheet">
</head>
<body class="bg-blue-50 font-serif">
    <?php include 'navbar.php'; ?>
    
    <div class="mx-auto max-w-6xl p-4 font-mono">
        <h2 class="text-2xl text-center text-green-800 font-bold">Dashboard</h2>

        <div class="flex justify-between items-center my-4">
            <div class="flex items-center space-x-2"> 
                <a href="assignment.php">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 transition-transform duration-200 ease-in-out hover:scale-110" viewBox="0 0 448 512">
                        <path fill="#2e511f" d="M64 32C28.7 32 0 60.7 0 96L0 416c0 35.3 28.7 64 64 64l320 0c35.3 0 64-28.7 64-64l0-320c0-35.3-28.7-64-64-64L64 32zM200 344l0-64-64 0c-13.3 0-24-10.7-24-24s10.7-24 24-24l64 0 0-64c0-13.3 10.7-24 24-24s24 10.7 24 24l0 64 64 0c13.3 0 24 10.7 24 24s-10.7 24-24 24l-64 0 0 64c0 13.3-10.7 24-24 24s-24-10.7-24-24z"/>
                    </svg>
                </a>
                <a href="dashboard.php?filter=all" class="text-green-800 px-4 py-2">All</a>
                <a href="dashboard.php?filter=completed" class="text-green-800 px-4 py-2">Completed</a>
                <a href="dashboard.php?filter=unfinished" class="text-green-800 px-4 py-2">Unfinished</a>
            </div>

            <div class="flex items-center justify-end">
                <form method="post" action="dashboard.php" class="flex items-center">
                    <input 
                        type="text" 
                        id="search" 
                        name="search_keyword" 
                        placeholder="Search..." 
                        class="w-full px-4 py-1 border rounded-full focus:outline-none focus:ring-2 focus:ring-green-800"
                    />
                    <button type="submit" name="search" class="ml-2 text-green-800">
                        <svg 
                            xmlns="http://www.w3.org/2000/svg" 
                            class="w-6 h-6 cursor-pointer transition-transform duration-200 ease-in-out hover:scale-110 hover:text-green-600" 
                            viewBox="0 0 460 512"
                        >
                            <path fill="currentColor" d="M220.6 130.3l-67.2 28.2V43.2L98.7 233.5l54.7-24.2v130.3l67.2-209.3zm-83.2-96.7l-1.3 4.7-15.2 52.9C80.6 106.7 52 145.8 52 191.5c0 52.3 34.3 95.9 83.4 105.5v53.6C57.5 340.1 0 272.4 0 191.6c0-80.5 59.8-147.2 137.4-158zm311.4 447.2c-11.2 11.2-23.1 12.3-28.6 10.5-5.4-1.8-27.1-19.9-60.4-44.4-33.3-24.6-33.6-35.7-43-56.7-9.4-20.9-30.4-42.6-57.5-52.4l-9.7-14.7c-24.7 16.9-53 26.9-81.3 28.7l2.1-6.6 15.9-49.5c46.5-11.9 80.9-54 80.9-104.2 0-54.5-38.4-102.1-96-107.1V32.3C254.4 37.4 320 106.8 320 191.6c0 33.6-11.2 64.7-29 90.4l14.6 9.6c9.8 27.1 31.5 48 52.4 57.4s32.2 9.7 56.8 43c24.6 33.2 42.7 54.9 44.5 60.3s.7 17.3-10.5 28.5zm-9.9-17.9c0-4.4-3.6-8-8-8s-8 3.6-8 8 3.6 8 8 8 8-3.6 8-8z"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='bg-white p-4 rounded-b-3xl shadow-lg flex flex-col h-full'>"; 
                    echo "<h3 class='text-2xl font-semibold mb-2'>" . htmlspecialchars($row['title']) . "</h3>";
                    echo "<p class='text-gray-600 text-base mb-4'> " . htmlspecialchars($row['description']) . "</p>";
                    echo "<p class='text-gray-600 text-sm'>Name: " . htmlspecialchars($row['name']) . "</p>";
                    echo "<p class='text-gray-600 text-sm'>Deadline: " . htmlspecialchars($row['deadline']) . "</p>";
                    echo "<p class='text-gray-600 text-sm'>Status: " . htmlspecialchars($row['status']) . "</p>";
                    echo "<div class='mt-auto'>"; 
                    echo "<form method='post' action='dashboard.php'>"; 
                    echo "<input type='hidden' name='assignment_id' value='" . $row['id'] . "'>";
                    if ($row['status'] == 'unfinished') {
                        echo "<input type='hidden' name='status' value='completed'>";
                        echo "<button type='submit' name='mark_complete' class='bg-green-500 text-white text-end px-4 py-2 mt-2 rounded'>Mark as Complete</button>";
                    } else {
                        echo "<input type='hidden' name='status' value='unfinished'>";
                        echo "<button type='submit' name='mark_complete' class='bg-red-500 text-white text-end px-4 py-2 mt-2 rounded'>Mark as Unfinished</button>";
                    }
                    echo "</form></div></div>"; 
                }
            } else {
                echo "<p class='text-center text-gray-500'>No assignments found.</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>
