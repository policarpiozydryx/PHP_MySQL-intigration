<?php
require_once 'config.php';

$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
if (!$id) {
    die("Invalid or missing User ID.");
}

$message = '';

// 1. Handle the update submission first, so changes reflect immediately
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] ?? 'User';

    if (!empty($name) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        try {
            $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?");
            $stmt->bind_param("sssi", $name, $email, $role, $id);
            $stmt->execute();
            $stmt->close();
            
            // Redirect back to the read page after successful update
            header("Location: read.php");
            exit;
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) { 
                $message = "<div class='alert error'>Error: That email address is already in use by another user.</div>";
            } else {
                $message = "<div class='alert error'>Failed to update record.</div>";
            }
        }
    } else {
        $message = "<div class='alert error'>Please provide a valid name and email address.</div>";
    }
}

// 2. Fetch current user data to populate the form
try {
    $stmt = $conn->prepare("SELECT name, email, role FROM users WHERE id = ?");
    $stmt->bind_param("i", $id); 
    $stmt->execute();
    
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    
    if (!$user) {
        die("User record not found.");
    }
} catch (mysqli_sql_exception $e) {
    die("Database error occurred.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        h2 { margin-top: 0; color: #333; }
        label { font-weight: bold; color: #555; display: block; margin-bottom: 5px; }
        input[type="text"], input[type="email"], select {
            width: 100%; padding: 10px; margin-bottom: 15px;
            border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;
        }
        button {
            width: 100%; padding: 10px; background-color: #ffc107;
            color: #212529; border: none; border-radius: 4px; font-weight: bold;
            font-size: 16px; cursor: pointer; margin-bottom: 10px;
        }
        button:hover { background-color: #e0a800; }
        .cancel-btn {
            display: block; width: 100%; padding: 10px; background-color: #6c757d;
            color: white; text-align: center; text-decoration: none; border-radius: 4px;
            box-sizing: border-box; font-size: 16px;
        }
        .cancel-btn:hover { background-color: #5a6268; }
        .alert { padding: 10px; margin-bottom: 15px; border-radius: 4px; text-align: center; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>

<div class="container">
    <h2>Update User Profile</h2>
    
    <?= $message ?>

    <form method="POST">
        <label>Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
        
        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
        
        <label>Role</label>
        <select name="role" required>
            <option value="User" <?= $user['role'] === 'User' ? 'selected' : '' ?>>User</option>
            <option value="Admin" <?= $user['role'] === 'Admin' ? 'selected' : '' ?>>Admin</option>
        </select>
        
        <button type="submit">Save Changes</button>
    </form>

    <a href="read.php" class="cancel-btn">Cancel</a>
</div>

</body>
</html>