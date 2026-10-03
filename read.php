<?php
require_once 'config.php';

try {
    $result = $conn->query("SELECT id, name, email, role, created_at FROM users ORDER BY id DESC");
    $users = $result->fetch_all(MYSQLI_ASSOC); 
    $result->free();
} catch (mysqli_sql_exception $e) {
    die("Could not retrieve users from the database.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registered Users</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f6;
            margin: 0; padding: 40px;
            display: flex; justify-content: center;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 900px;
        }
        h2 { margin-top: 0; color: #333; display: inline-block; }
        .add-btn {
            float: right; padding: 10px 15px; background-color: #28a745;
            color: white; text-decoration: none; border-radius: 4px; font-weight: bold;
        }
        .add-btn:hover { background-color: #218838; }
        table {
            width: 100%; border-collapse: collapse; margin-top: 20px;
        }
        th, td {
            padding: 12px 15px; text-align: left; border-bottom: 1px solid #ddd;
        }
        th { background-color: #f8f9fa; color: #333; font-weight: bold; }
        tr:hover { background-color: #f1f1f1; }
        .action-link {
            text-decoration: none; padding: 5px 10px; border-radius: 3px; font-size: 14px;
        }
        .edit-link { background-color: #ffc107; color: #212529; }
        .edit-link:hover { background-color: #e0a800; }
        .delete-link { background-color: #dc3545; color: white; margin-left: 5px; }
        .delete-link:hover { background-color: #c82333; }
        .role-badge {
            padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: bold;
        }
        .role-admin { background-color: #6f42c1; color: white; }
        .role-user { background-color: #17a2b8; color: white; }
    </style>
</head>
<body>

<div class="container">
    <h2>Registered Users</h2>
    <a href="create.php" class="add-btn">+ Add New User</a>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($users)): ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['id']) ?></td>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                            <span class="role-badge <?= $user['role'] === 'Admin' ? 'role-admin' : 'role-user' ?>">
                                <?= htmlspecialchars($user['role']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($user['created_at']) ?></td>
                        <td>
                            <a href="update.php?id=<?= $user['id'] ?>" class="action-link edit-link">Edit</a>
                            <a href="delete.php?id=<?= $user['id'] ?>" class="action-link delete-link" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: #777;">No users found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>