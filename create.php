<?php
require_once 'config.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] ?? 'User';

    if (!empty($name) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        try {
            $stmt = $conn->prepare("INSERT INTO users (name, email, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $role);
            $stmt->execute();
            
            $message = "<div class='alert success'>User registered successfully! ID: " . $conn->insert_id . "</div>";
            $stmt->close();
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) { 
                $message = "<div class='alert error'>Error: That email address is already registered.</div>";
            } else {
                $message = "<div class='alert error'>An error occurred while saving the profile.</div>";
            }
        }
    } else {
        $message = "<div class='alert error'>Please provide a valid name and email address.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New User</title>
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
            width: 100%; padding: 10px; background-color: #28a745;
            color: white; border: none; border-radius: 4px;
            font-size: 16px; cursor: pointer;
        }
        button:hover { background-color: #218838; }
        .view-btn {
            background-color: #007bff; margin-top: 10px;
        }
        .view-btn:hover { background-color: #0069d9; }
        .alert { padding: 10px; margin-bottom: 15px; border-radius: 4px; text-align: center; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        hr { border: 0; height: 1px; background: #eee; margin: 20px 0; }
    </style>
</head>
<body>

<div class="container">
    <h2>Create New User</h2>
    
    <?= $message ?>

    <form method="POST">
        <label>Name</label>
        <input type="text" name="name" placeholder="Full Name" required>
        
        <label>Email</label>
        <input type="email" name="email" placeholder="Email Address" required>
        
        <label>Role</label>
        <select name="role" required>
            <option value="User">User</option>
            <option value="Admin">Admin</option>
        </select>
        
        <button type="submit">Create User</button>
    </form>

    <hr>

    <form action="read.php" method="GET">
        <button type="submit" class="view-btn">View, Edit, or Remove Users</button>
    </form>
</div>

</body>
</html>