<?php
include '../conn.php';
header('Content-Type: application/json');

// Fetch Users (NIM & Name)
$query = "SELECT nim, nama FROM pengguna ORDER BY nama ASC";
$result = mysqli_query($conn, $query);

$users = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = [
            'nim' => $row['nim'],
            'nama' => $row['nama'],
            'label' => $row['nim'] . ' - ' . $row['nama'] // Combined Label
        ];
    }
    echo json_encode(['success' => true, 'data' => $users]);
} else {
    echo json_encode(['success' => false, 'message' => 'No users found']);
}