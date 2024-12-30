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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $position = $_POST['position'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $hire_date = $_POST['hire_date'];
    $status = $_POST['status'];

    $sql = "INSERT INTO karyawan (name, position, email, phone, hire_date, status) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $name, $position, $email, $phone, $hire_date, $status);

    if ($stmt->execute()) {
        header("Location: tampil.php?tab=karyawan");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error adding employee.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Karyawan</title>
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
            max-width: 800px;
            margin-top: 50px;
        }
        h3 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
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
    <h3>Tambah Karyawan</h3>
    <form method="POST">
        <div class="row mb-3">
            <label for="name" class="col-sm-3 col-form-label">Nama</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
        </div>
        <div class="row mb-3">
            <label for="position" class="col-sm-3 col-form-label">Posisi</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" id="position" name="position" required>
            </div>
        </div>
        <div class="row mb-3">
            <label for="email" class="col-sm-3 col-form-label">Email</label>
            <div class="col-sm-9">
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
        </div>
        <div class="row mb-3">
            <label for="phone" class="col-sm-3 col-form-label">Telepon</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" id="phone" name="phone">
            </div>
        </div>
        <div class="row mb-3">
            <label for="hire_date" class="col-sm-3 col-form-label">Tanggal Bergabung</label>
            <div class="col-sm-9">
                <input type="date" class="form-control" id="hire_date" name="hire_date" required>
            </div>
        </div>
        <div class="row mb-3">
            <label for="status" class="col-sm-3 col-form-label">Status</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" id="status" name="status" required>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Tambah Karyawan</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
