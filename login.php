<?php
session_start();
$conn = new mysqli("localhost", "root", "", "tour");

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_email'] = $row['email'];
            header("Location: tampil.php"); // Redirect to dashboard
        } else {
            echo "<script>alert('Password salah');</script>";
        }
    } else {
        echo "<script>alert('Email tidak ditemukan');</script>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* General Body and Background */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f6f9;
            color: #333;
            margin: 0;
            padding: 0;
        }

        /* Container for the form */
        .login-container {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            max-width: 400px;
            margin: 50px auto;
        }

        /* Form Heading */
        h2 {
            font-size: 28px;
            color: #2C3E50;
            margin-bottom: 20px;
        }

        /* Form Controls */
        .form-label {
            font-weight: bold;
            color: #2C3E50;
        }

        input[type="email"], input[type="password"] {
            border-radius: 10px;
            padding: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 15px;
        }

        input[type="email"]:focus, input[type="password"]:focus {
            border-color: #1ABC9C;
            box-shadow: 0 0 8px rgba(26, 188, 156, 0.5);
        }

        /* Button */
        .btn-primary {
            background-color: #1ABC9C;
            border-color: #1ABC9C;
            border-radius: 5px;
            padding: 10px 20px;
            font-size: 16px;
            width: 100%;
        }

        .btn-primary:hover {
            background-color: #16a085;
            border-color: #16a085;
        }

        /* Responsive Design */
        @media (max-width: 576px) {
            .login-container {
                padding: 20px;
            }
            h2 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>

    <div class="container login-container">
        <h2 class="text-center mb-4">Login Admin</h2>
        <form action="" method="post">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>

</body>
</html>
