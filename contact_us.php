<?php
$db_hostname = "localhost";
$db_username = "root";
$db_password = "";
$db_name = "tour";

$conn = mysqli_connect($db_hostname, $db_username, $db_password, $db_name);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    $sql = "INSERT INTO contact (Name, Email, Phone, Subject, Message) 
            VALUES ('$name', '$email', '$phone', '$subject', '$message')";

    if (mysqli_query($conn, $sql)) {
        $success_message = "Pesan Anda telah diterima. Kami akan menghubungi Anda segera.";
    } else {
        $success_message = "Error: " . $sql . "<br>" . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Diterima</title>
    <style>
        /* CSS dalam file PHP */

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .message-box {
            width: 100%;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: rgba(0, 0, 0, 0.5);
            position: fixed;
            top: 0;
            left: 0;
        }

        .message-box p {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            font-size: 18px;
            color: #333;
            max-width: 500px;
            margin: 0 20px;
        }

        #close-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }

        #close-btn:hover {
            background-color: #45a049;
        }
    </style>
    <script> 
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</head>
<body>
    <div class="message-box" id="message-box">
        <p><?php echo isset($success_message) ? $success_message : ''; ?></p>
        <button id="close-btn">Tutup</button>
    </div>

    <script>
        document.getElementById('close-btn').addEventListener('click', function() {
            window.location.href = 'index.html';
        });
    </script>
</body>
</html>
