<?php
require 'conn.php';
require('function.php');
session_start();

if (isset($_SESSION['id_user'])) {
    header('Location: index.php');
    exit();
}


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

$data_jurusan = mysqli_query($conn, "SELECT * FROM jurusan ORDER BY nama_jurusan ASC");

$data_prodi_array = [];
$query_prodi = mysqli_query($conn, "SELECT * FROM program_studi ORDER BY nama_prodi ASC");
while ($row = mysqli_fetch_assoc($query_prodi)) {
    $data_prodi_array[] = $row;
}

// Register logic
register_user($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <style>
        /* CSS SAMA SEPERTI SEBELUMNYA */
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
            from {
                opacity: 0;
            }

            to {
                opacity: 100;
            }
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

        label {
            display: block;
            text-align: left;
            margin-bottom: 5px;
            font-weight: bold;
            color: #000;
        }

        input,
        select {
            box-sizing: border-box;
            width: 100%;
            padding: 10px;
            border: 1px solid #532bcc;
            border-radius: 6px;
            margin-bottom: 15px;
            display: block;
            background-color: white;
            font-family: inherit;
        }

        /* Style tambahan untuk state disabled */
        select:disabled {
            background-color: #e0e0e0;
            cursor: not-allowed;
            color: #888;
        }

        button {
            width: 95%;
            padding: 10px;
            background: #532bcc;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }

        button:hover {
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
        <h2>Daftar Akun</h2>

        <form method="POST">
            <label>Nama:</label>
            <input type="text" name="nama" required placeholder="Masukkan Nama Lengkap">

            <label>NIM:</label>
            <input type="text" name="nim" required placeholder="Masukkan NIM">

            <label>Jurusan:</label>
            <select name="jurusan" id="jurusan" required>
                <option value="">-- Pilih Jurusan --</option>
                <?php foreach ($data_jurusan as $j): ?>
                    <option value="<?= $j['jurusan_id'] ?>"><?= $j['nama_jurusan'] ?></option>
                <?php endforeach; ?>
            </select>

            <label>Program Studi:</label>
            <select name="prodi" id="prodi" required disabled>
                <option value="">-- Pilih Jurusan Terlebih Dahulu --</option>
            </select>

            <label>Password:</label>
            <input type="password" name="password" required placeholder="Masukkan Password">

            <label>Kontak:</label>
            <input type="tel" name="kontak" required placeholder="08xxxxxxxxxx">

            <button type="submit" name="register">Daftar</button>
        </form>

        <p style="margin-top: 15px;">Sudah punya akun?
            <a href="login.php">Login disini</a>
        </p>
    </div>

    <script>
        const dataProdi = <?= json_encode($data_prodi_array) ?>;

        const selectJurusan = document.getElementById('jurusan');
        const selectProdi = document.getElementById('prodi');

        selectJurusan.addEventListener('change', function() {
            const idJurusanDipilih = this.value;

            selectProdi.innerHTML = '<option value="">-- Pilih Prodi --</option>';

            // Jika user memilih "Pilih Jurusan" (kosong), matikan lagi dropdown prodi
            if (idJurusanDipilih === "") {
                selectProdi.disabled = true;
                selectProdi.innerHTML = '<option value="">-- Pilih Jurusan Terlebih Dahulu --</option>';
                return;
            }

            selectProdi.disabled = false;

            // 3. Filter data prodi sesuai id_jurusan
            const prodiSesuai = dataProdi.filter(function(prodi) {
                return prodi.jurusan_id == idJurusanDipilih;
            });

            // 4. Masukkan prodi yang sudah difilter ke dalam dropdown HTML
            prodiSesuai.forEach(function(prodi) {
                const option = document.createElement('option');
                option.value = prodi.prodi_id;
                option.textContent = prodi.nama_prodi;
                selectProdi.appendChild(option);
            });
        });
    </script>

</body>

</html>