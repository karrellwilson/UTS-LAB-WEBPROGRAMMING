<?php
session_start();
include('config.php');
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit();
}

$editAssignment = [];

if (isset($_SESSION['successMessage'])) {
    $successMessage = $_SESSION['successMessage'];
    unset($_SESSION['successMessage']);  
} else {
    $successMessage = '';
}

if (isset($_SESSION['errorMessage'])) {
    $errorMessage = $_SESSION['errorMessage'];
    unset($_SESSION['errorMessage']);  
} else {
    $errorMessage = '';
}

if (isset($_GET['edit'])) {
    $assignment_id = $_GET['edit'];
    $query = "SELECT * FROM assignments WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $assignment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $editAssignment = $result->fetch_assoc();
    } else {
        $_SESSION['errorMessage'] = "Assignment not found.";
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $deadline = $_POST['deadline'];
    
    if (isset($_POST['assignment_id'])) {
        $assignment_id = $_POST['assignment_id'];
        $query = "UPDATE assignments SET title = ?, name = ?, description = ?, deadline = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssi", $title, $name, $description, $deadline, $assignment_id);
    } else {
        $query = "INSERT INTO assignments (title, name, description, deadline) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssss", $title, $name, $description, $deadline);
    }

    if ($stmt->execute()) {
        $_SESSION['successMessage'] = isset($_POST['assignment_id']) ? "Assignment updated successfully!" : "Assignment added successfully!";
    } else {
        $_SESSION['errorMessage'] = "Failed to save assignment. Please try again.";
    }
    $stmt->close();

    header('Location: assignment.php');  
    exit();
}

if (isset($_GET['delete'])) {
    $assignment_id = $_GET['delete'];
    $query = "DELETE FROM assignments WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $assignment_id);
    if ($stmt->execute()) {
        $_SESSION['successMessage'] = "Assignment deleted successfully!";
    } else {
        $_SESSION['errorMessage'] = "Failed to delete assignment. Please try again.";
    }
    $stmt->close();

    header('Location: assignment.php');
    exit();
}

$query = "SELECT * FROM assignments";
$result = $conn->query($query);
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment</title>
    <link href="../css/output.css" rel="stylesheet">
</head>
<body class="bg-blue-50 font-mono">
    <?php include 'navbar.php'; ?>

    <div class="container mx-auto max-w-7xl p-">
        <!-- Alert Message -->
        <?php if (!empty($successMessage)): ?>
            <div id="alert" class="bg-green-200 text-green-800 p-4 rounded absolute top-5 right-5 shadow-lg z-50">
                <?= $successMessage ?>
            </div>
        <?php elseif (!empty($errorMessage)): ?>
            <div id="alert" class="bg-red-200 text-red-800 p-4 rounded absolute top-5 right-5 shadow-lg z-50">
                <?= $errorMessage ?>
            </div>
        <?php endif; ?>

        <!-- Grid container for Assignment Form and To-Do List -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 m-10">
            <!-- Assignment Form -->
            <div class="lg:col-span-1 w-full lg:w-3/4 mx-auto">
                <h3 class="text-xl font-bold mb-4 text-center text-green-800">Create To-Do List</h3>
                <form action="assignment.php" method="POST" class="mb-6">
                    <div class="mb-4">
                        <label for="title" class="block mb-2">Title</label>
                        <input type="text" id="title" name="title" placeholder="Enter Title" value="<?= isset($_GET['edit']) ? htmlspecialchars($editAssignment['title']) : '' ?>" required class="border border-gray-300 p-2 w-full rounded">
                    </div>
                    <div class="mb-4">
                        <label for="name" class="block mb-2">Name</label>
                        <input type="text" id="name" name="name" placeholder="Enter Name" value="<?= isset($_GET['edit']) ? htmlspecialchars($editAssignment['name']) : '' ?>" required class="border border-gray-300 p-2 w-full rounded">
                    </div>
                    <div class="mb-4">
                        <label for="description" class="block mb-2">Description</label>
                        <textarea id="description" name="description" placeholder="Description..." rows="5" required class="border border-gray-300 p-2 w-full rounded"><?= isset($_GET['edit']) ? htmlspecialchars($editAssignment['description']) : '' ?></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="deadline" class="block mb-2">Deadline</label>
                        <input type="date" id="deadline" name="deadline" value="<?= isset($_GET['edit']) ? htmlspecialchars($editAssignment['deadline']) : '' ?>" required class="border border-gray-300 p-2 w-full rounded">
                    </div>
                    <?php if (isset($_GET['edit'])): ?>
                        <input type="hidden" name="assignment_id" value="<?= $_GET['edit'] ?>">
                    <?php endif; ?>

                    <div class="flex justify-start">
                        <button type="submit" class="bg-green-800 hover:bg-green-700 text-white px-4 py-2 w-48 p-2 rounded-ee-full text-start">
                            <?= isset($_GET['edit']) ? 'Update Assignment' : 'Submit' ?>
                        </button>
                    </div>
                </form>
            </div>

            <!-- To-Do List Management -->
            <div class="lg:col-span-2 w-full mx-auto">
                <h3 class="text-xl font-bold mb-4 text-center text-green-800">To-Do List Management</h3>
                <table class="min-w-full border-collapse bg-white border border-gray-200 mt-4 text-sm w-full">
                    <thead>
                        <tr>
                            <th class="border border-gray-300 px-6 py-2">Title</th>
                            <th class="border border-gray-300 px-6 py-2">Name</th>
                            <th class="border border-gray-300 px-6 py-2">Description</th>
                            <th class="border border-gray-300 px-6 py-2">Deadline</th>
                            <th class="border border-gray-300 px-6 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="border border-gray-300 px-6 py-2"><?= htmlspecialchars($row['title']) ?></td>
                                    <td class="border border-gray-300 px-6 py-2"><?= htmlspecialchars($row['name']) ?></td>
                                    <td class="border border-gray-300 px-6 py-2"><?= htmlspecialchars($row['description']) ?></td>
                                    <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($row['deadline']) ?></td>
                                    <td class="border border-gray-300 px-2 py-2">
                                    <div class="flex space-x-2">
                                        <a href="assignment.php?edit=<?= $row['id'] ?>" class="bg-yellow-500 text-white text-xs px-2 py-1 rounded">Edit</a>
                                        <a href="assignment.php?delete=<?= $row['id'] ?>" class="bg-red-500 text-white text-xs px-2 py-1 rounded">Delete</a>
                                    </div>
                                </td>

                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center border border-gray-300 px-6 py-2">No assignments found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        setTimeout(function() {
            var alert = document.getElementById('alert');
            if (alert) {
                alert.style.display = 'none';
            }
        }, 2000);
    </script>
</body>
</html>
