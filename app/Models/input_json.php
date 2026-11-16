<?php
// file: input_json.php
require 'koneksi.php';
$nim = '12345'; // Contoh NIM
$detail_data = [
 'aksi' => 'Login',
 'perangkat' => 'Web Browser',
 'ip_address' => '192.168.1.1'
];
$detail_json = json_encode($detail_data);
// Gunakan prepared statement untuk keamanan
$stmt = $conn->prepare("INSERT INTO log_aktivitas
(nim_mahasiswa, detail) VALUES (?, ?)");
$stmt->bind_param("ss", $nim, $detail_json);
if ($stmt->execute()) {
 echo "Log JSON berhasil disimpan!";
} else {
 echo "Error: " . $stmt->error;
}
$stmt->close();
$conn->close();
?>
