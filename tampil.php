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

$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'messages'; // Default to messages

// Fetch data for the dashboard
$sql_total_users = "SELECT COUNT(*) AS total_users FROM users";
$result_users = $conn->query($sql_total_users);
$row_users = $result_users->fetch_assoc();

$sql_total_messages = "SELECT COUNT(*) AS total_messages FROM contact";
$result_messages = $conn->query($sql_total_messages);
$row_messages = $result_messages->fetch_assoc();

$sql_total_karyawan = "SELECT COUNT(*) AS total_karyawan FROM karyawan";
$result_karyawan = $conn->query($sql_total_karyawan);
$row_karyawan = $result_karyawan->fetch_assoc();

// Fetch admin settings
$sql_admin = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql_admin);
$stmt->bind_param("i", $_SESSION['admin_id']);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

// Fetch contact messages
$sql_messages = "SELECT * FROM contact ORDER BY created_at DESC";
$result_messages = $conn->query($sql_messages);

// Fetch users
$sql_users = "SELECT * FROM users ORDER BY username ASC";
$result_users = $conn->query($sql_users);

// Fetch karyawan
$sql_karyawan = "SELECT * FROM karyawan ORDER BY hire_date DESC";
$result_karyawan = $conn->query($sql_karyawan);

// Handle form submission for updating settings
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $active_tab == 'settings') {
    $site_name = $_POST['site_name'];
    $admin_email = $_POST['admin_email'];
    $password = $_POST['password'];

    // Update query for admin settings
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $update_sql = "UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("sssi", $site_name, $admin_email, $hashed_password, $_SESSION['admin_id']);
    } else {
        $update_sql = "UPDATE users SET username = ?, email = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ssi", $site_name, $admin_email, $_SESSION['admin_id']);
    }

    if ($stmt->execute()) {
        $_SESSION['admin_email'] = $admin_email;
        $_SESSION['admin_username'] = $site_name;

        // Redirect to settings page after update
        header("Location: tampil.php?tab=settings");
        exit();
    } else {
        echo "Error updating settings: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dasbor Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
        }
        .sidebar {
            height: 100vh;
            background-color: #007bff;
            color: white;
            padding-top: 20px;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px 20px;
        }
        .sidebar a:hover {
            background-color: #0056b3;
        }
        .sidebar .active {
            background-color: #0056b3;
        }
        .content {
            padding: 20px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        .card:hover {
            transform: scale(1.05);
        }
        .card h5 {
            font-size: 1.5rem;
            color: #fff;
        }
        .card p {
            font-size: 1rem;
            margin-bottom: 0;
        }
        .card.bg-danger {
            background-color: #dc3545 !important;
        }
        .card.bg-primary {
            background-color: #007bff !important;
        }
        .card.bg-success {
            background-color: #28a745 !important;
        }
        .logout-btn {
            position: absolute;
            top: 20px;
            right: 20px;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2 sidebar">
            <h5 class="text-center">Admin Panel</h5>
            <a href="tampil.php?tab=messages" class="<?php echo $active_tab == 'messages' ? 'active' : ''; ?>">Pesan Kontak</a>
            <a href="tampil.php?tab=users" class="<?php echo $active_tab == 'users' ? 'active' : ''; ?>">Manage Users</a>
            <a href="tampil.php?tab=karyawan" class="<?php echo $active_tab == 'karyawan' ? 'active' : ''; ?>">Karyawan</a>
            <a href="tampil.php?tab=settings" class="<?php echo $active_tab == 'settings' ? 'active' : ''; ?>">Settings</a>
        </div>

        <!-- Main Content -->
        <div class="col-md-10 content">
            <!-- Logout Button -->
            <a href="logout.php" class="btn btn-danger logout-btn">Logout</a>

            <!-- Dashboard Section -->
            <h2 class="text-right">Dasbor Admin</h2>
            <p class="text-right">Selamat datang, <?php echo htmlspecialchars($_SESSION['admin_email']); ?>!</p>

            <!-- Dashboard Cards -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card p-3 bg-danger">
                        <h5><?php echo $row_users['total_users']; ?></h5>
                        <p>Total Admin</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card p-3 bg-primary">
                        <h5><?php echo $row_messages['total_messages']; ?></h5>
                        <p>Total Pesan Kontak</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card p-3 bg-success">
                        <h5><?php echo $row_karyawan['total_karyawan']; ?></h5>
                        <p>Total Karyawan</p>
                    </div>
                </div>
            </div>

            <?php
            // Display the appropriate tab content
            if ($active_tab == 'messages') {
                // Messages Section
                echo '<h4>Pesan Kontak</h4>';
                echo '<table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Pesan</th>
                                <th>Waktu</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>';
                while ($row = $result_messages->fetch_assoc()) {
                    echo '<tr>
                            <td>' . htmlspecialchars($row['Name']) . '</td>
                            <td>' . htmlspecialchars($row['Email']) . '</td>
                            <td>' . htmlspecialchars($row['Message']) . '</td>
                            <td>' . $row['created_at'] . '</td>
                            <td><a href="delete_message.php?id=' . $row['id'] . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this message?\');">Delete</a></td>
                          </tr>';
                }
                echo '</tbody></table>';
            } elseif ($active_tab == 'users') {
                // Users Section
                echo '<h4>Manage Users</h4>';
                echo '<a href="register.php" class="btn btn-success mb-3">Tambah Admin</a>';
                echo '<table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>';
                while ($row = $result_users->fetch_assoc()) {
                    echo '<tr>
                            <td>' . htmlspecialchars($row['username']) . '</td>
                            <td>' . htmlspecialchars($row['email']) . '</td>
                            <td>
                                <a href="edit_user.php?id=' . $row['id'] . '" class="btn btn-warning btn-sm">Edit</a>
                                <a href="delete_user.php?id=' . $row['id'] . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this user?\');">Delete</a>
                            </td>
                          </tr>';
                }
                echo '</tbody></table>';
            } elseif ($active_tab == 'karyawan') {
                // Karyawan Section
                echo '<h4>Data Karyawan</h4>';
                echo '<a href="add_karyawan.php" class="btn btn-success mb-3">Tambah Karyawan</a>';
                echo '<table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Posisi</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Hire Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>';
                while ($row = $result_karyawan->fetch_assoc()) {
                    echo '<tr>
                            <td>' . htmlspecialchars($row['name']) . '</td>
                            <td>' . htmlspecialchars($row['position']) . '</td>
                            <td>' . htmlspecialchars($row['email']) . '</td>
                            <td>' . htmlspecialchars($row['phone']) . '</td>
                            <td>' . $row['hire_date'] . '</td>
                            <td>' . htmlspecialchars($row['status']) . '</td>
                            <td>
                                <a href="edit_karyawan.php?id=' . $row['id'] . '" class="btn btn-warning btn-sm">Edit</a>
                                <a href="delete_karyawan.php?id=' . $row['id'] . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this employee?\');">Delete</a>
                            </td>
                          </tr>';
                }
                echo '</tbody></table>';
            } elseif ($active_tab == 'settings') {
                // Settings Section
                echo '<h4>Settings</h4>';
                echo '<form method="post" action="tampil.php?tab=settings">
                        <div class="form-group">
                            <label for="site_name">Site Name</label>
                            <input type="text" class="form-control" id="site_name" name="site_name" value="' . htmlspecialchars($admin['username']) . '" required>
                        </div>
                        <div class="form-group">
                            <label for="admin_email">Admin Email</label>
                            <input type="email" class="form-control" id="admin_email" name="admin_email" value="' . htmlspecialchars($admin['email']) . '" required>
                        </div>
                        <div class="form-group">
                            <label for="password">New Password (leave blank if not changing)</label>
                            <input type="password" class="form-control" id="password" name="password">
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Update Settings</button>
                    </form>';
            }
            ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
