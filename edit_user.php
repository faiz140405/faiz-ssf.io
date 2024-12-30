<?php
session_start();
$conn = new mysqli("localhost", "root", "", "tour");

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Fetch user details
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        echo "User not found!";
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Update user details
        $username = $_POST['username'];
        $email = $_POST['email'];

        // Update query
        $update_sql = "UPDATE users SET username = ?, email = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssi", $username, $email, $user_id);
        if ($update_stmt->execute()) {
            header("Location: tampil.php?tab=users");
            exit();
        } else {
            echo "Error updating user details.";
        }
    }
} else {
    echo "Invalid user ID!";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            margin-top: 50px;
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        .form-label {
            font-weight: 600;
        }
        .form-control {
            border-radius: 8px;
            font-size: 1rem;
            padding: 12px;
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(38, 143, 255, 0.25);
        }
        .btn-primary {
            width: 100%;
            border-radius: 8px;
            padding: 12px;
            font-size: 1.1rem;
        }
        .alert {
            margin-top: 20px;
            font-size: 1rem;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit User</h2>
    <form method="post">
        <div class="mb-3 row">
            <label for="username" class="col-sm-3 col-form-label">Username</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
        </div>
        <div class="mb-3 row">
            <label for="email" class="col-sm-3 col-form-label">Email</label>
            <div class="col-sm-9">
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Update User</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
