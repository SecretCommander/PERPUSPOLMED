<?php
require 'conn.php';
require('function.php');
session_start();


if (isset($_SESSION['id_user'])) {
    header('Location: index.php');
    exit();
}

// Check apakah Remember
if (isset($_COOKIE['nim']) && isset($_COOKIE['password'])) {
    $cookieNIM = mysqli_real_escape_string($conn, $_COOKIE['nim']);
    $cookiePassword = mysqli_real_escape_string($conn, $_COOKIE['password']);

    $query = "SELECT * FROM pengguna WHERE nim='$cookieNIM' AND password='$cookiePassword'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['id_user'] = $user['nim'];
        $_SESSION['password'] = $user['password'];

        header('Location: index.php');
        exit();
    }
}





outer_login($conn);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: poppins, sans-serif;
            background: #532bcc;
            height: 100vh;

            display: flex;
            justify-content: center;
            align-items: center;

            transform: translateY(-40px);
            animation: fadeIn 0.5s ease-in forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .card {
            background: #fff;
            width: 350px;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            color: #532bcc;
        }

        /* Generic Label Style (for NIM/Password) */
        label {
            display: block;
            text-align: left;
            margin-left: 5%;
            margin-bottom: 5px;
            font-weight: bold;
            color: #000;
        }

        /* Text Input Style */
        input:not([type="checkbox"]) {
            width: 90%;
            padding: 10px;
            border: 1px solid #532bcc;
            border-radius: 6px;
            margin-bottom: 15px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        /* Checkbox specific style */
        input[type="checkbox"] {
            width: auto;
            display: inline-block;
            margin-right: 5px;
            transform: translateY(1px); /* Slight vertical alignment fix */
        }

        /* Specific style for the Remember Me Wrapper */
        #remember {
            display: block;       /* Allows it to take full width */
            text-align: left;     /* Aligns it to the left */
            margin-left: 5%;      /* Aligns with other inputs */
            margin-bottom: 15px;
            font-weight: normal;  /* Often looks better not bold */
            cursor: pointer;
        }

        input[type="submit"] {
            width: 95%;
            padding: 10px;
            background: #532bcc;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background: #3d1fa1;
        }

        a {
            color: #532bcc;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="card">
        <h2>Login</h2>

        <form method="POST">
            <label>NIM:</label>
            <input type="text" name="nim" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <label id="remember">
                <input type="checkbox" name="remember"> Remember Me
            </label>
            
            <input type="submit" value="Login"></input>
        </form>

        <p style="margin-top: 15px;">Belum punya akun?
            <a href="sign-up.php">Daftar sekarang</a>
        </p>
    </div>

</body>

</html>