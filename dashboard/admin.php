
<?php 
require '../conn.php';
require '../function.php';
session_start();
if(isset($_SESSION['petugas_id']) && $_SESSION['adminloggedin'] === true){
    header('Location: dashboardkunjungan.php');
    exit();
}

outer_login_petugas($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Login</title>
<link rel="stylesheet" href="style.css">
</head>

<body class="login-page">
<div class="login-card">
    <h2>Admin Login</h2>
    <form method="POST">
        <label>Username</label>
        <input type="text" name="username" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>
