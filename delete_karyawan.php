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
    $id = $_GET['id'];

    // Delete the employee
    $sql = "DELETE FROM karyawan WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: tampil.php?tab=karyawan");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error deleting employee.</div>";
    }
} else {
    echo "<div class='alert alert-danger'>Employee ID not found.</div>";
    exit();
}
?>
