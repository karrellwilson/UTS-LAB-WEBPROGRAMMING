<?php
session_start();
include('config.php');
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['id'])) {
    header('Location: index.php');
    exit();
}

$success = "";
$error   = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_username = trim($_POST['new_username']);
    $new_email    = trim($_POST['new_email']);
    $new_password = trim($_POST['new_password']);
    
    if ($new_username == "" || $new_email == "") {
        $error = "Username and Email cannot be empty.";
    } else {
        if ($stmt = $conn->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?")) {
            $stmt->bind_param("ssi", $new_username, $new_email, $_SESSION['id']);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $error = "Username or Email already taken by another user.";
            } else {
                if ($new_password != "") {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?");
                    $stmt->bind_param("sssi", $new_username, $new_email, $hashed_password, $_SESSION['id']);
                } else {
                    // If password is not provided, only update username and email
                    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
                    $stmt->bind_param("ssi", $new_username, $new_email, $_SESSION['id']);
                }
                if ($stmt->execute()) {
                    $_SESSION['username'] = $new_username;
                    $_SESSION['email']    = $new_email;
                    $success = "Profile updated successfully!";
                } else {
                    $error = "Failed to update profile. Please try again.";
                }
            }
            $stmt->close();
        } else {
            $error = "Database error: " . $conn->error;
        }
    }
}

if ($stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?")) {
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
    $stmt->bind_result($username, $email);
    $stmt->fetch();
    $stmt->close();
} else {
    $error = "Failed to fetch user data: " . $conn->error;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link href="../css/output.css" rel="stylesheet">
</head>
<body class="bg-blue-50 font-mono">
    <?php include 'navbar.php'; ?>

    <div class="container mx-auto max-w-3xl bg-white p-6 rounded-lg shadow-md mt-6">
        <h2 class="text-2xl font-bold mb-6 text-center">User Profile Management</h2>
        <hr>

        <?php if ($success || $error): ?>
            <div id="alert" class="<?= $success ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' ?> p-4 rounded shadow-lg z-50 mb-4">
                <?= htmlspecialchars($success ?: $error); ?>
            </div>
        <?php endif; ?>

        <div id="viewProfile">
            <p><strong>Username :</strong> <?= htmlspecialchars($username); ?></p>
            <p><strong>Email    :</strong> <?= htmlspecialchars($email); ?></p>
            <button class="bg-blue-500 text-white px-4 py-2 rounded mt-4 hover:bg-blue-600 transition" onclick="showEditProfile()">Edit Profile</button>
        </div>

        <div id="editProfile" style="display: none;">
            <h4 class="text-xl font-bold">Edit Profile</h4>
            <form method="POST" action="profile.php" class="mt-4">
                <div class="mb-4">
                    <label for="username" class="block mb-2">Username</label>
                    <input type="text" class="border border-gray-300 p-2 w-full rounded" id="username" name="new_username" required value="<?= htmlspecialchars($username); ?>">
                </div>
                <div class="mb-4">
                    <label for="email" class="block mb-2">Email</label>
                    <input type="email" class="border border-gray-300 p-2 w-full rounded" id="email" name="new_email" required value="<?= htmlspecialchars($email); ?>">
                </div>
                <div class="mb-4">
                    <label for="password" class="block mb-2">Password <small>(Leave blank to keep current password)</small></label>
                    <input type="password" class="border border-gray-300 p-2 w-full rounded" id="password" name="new_password" placeholder="Enter new password">
                </div>
                <div class="flex justify-between">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition">Save Changes</button>
                    <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition" onclick="cancelEdit()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showEditProfile() {
            document.getElementById('viewProfile').style.display = 'none';
            document.getElementById('editProfile').style.display = 'block';
        }

        function cancelEdit() {
            document.getElementById('editProfile').style.display = 'none';
            document.getElementById('viewProfile').style.display = 'block';
        }

        setTimeout(function() {
            var alert = document.getElementById('alert');
            if (alert) {
                alert.style.display = 'none';
            }
        }, 2000);
    </script>

</body>
</html>
