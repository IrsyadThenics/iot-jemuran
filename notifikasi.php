<?php
include "api/db.php";
$result = mysqli_query($conn, "SELECT * FROM sensor_data ORDER BY id DESC LIMIT 20");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>iot-jemuran</title>
<link rel="stylesheet" href="assets/notifikasi.css">
<style>
</style>
</head>
<body>
  <div class="page-wrapper">
    <div class="container">
      <!-- Header -->
      <header>
        <h1>🌤️ Jemuran Pintar</h1>
        <nav>
          <a href="dashboard.php">Dashboard</a>
        </nav>
      </header>

      <!-- Konten Dinamis -->
      <div class="content-grid">
          <section class="card">
              <h2>📊 Notifikasi Terbaru</h2>
              <div class="table-wrapper">
              <table>
                  <thead>
                  <tr>
                      <th>ID</th>
                      <th>Device ID</th>
                      <th>Rain Value</th>
                      <th>Status</th>
                      <th>Motor Action</th>
                      <th>Created At</th>
                  </tr>
                  </thead>
                  <tbody id="table-body">
                      <!-- akan diisi otomatis oleh JS -->
                  </tbody>
              </table>
              </div>
          </section>
      </div>
    </div>
    
    
  </div>

<script>
function loadData() {
    // untuk card realtime
    fetch("get_data.php")
        .then(res => res.json())
        .then(data => {
            document.getElementById("data").innerHTML =
                "Rain: " + data.rain_value +
                " | Status: " + data.status +
                " | Motor: " + data.motor_action;
        });

    // untuk tabel riwayat
    fetch("get_table.php")
        .then(res => res.text())
        .then(html => {
            document.getElementById("table-body").innerHTML = html;
        });
}

setInterval(loadData, 2000);
window.onload = loadData;
</script>

</body>
</html>