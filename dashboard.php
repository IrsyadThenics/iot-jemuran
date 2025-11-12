<?php
// Ambil data dari API BMKG
$api_url = "https://api.bmkg.go.id/publik/prakiraan-cuaca?adm4=35.24.22.1020";
$response_body = @file_get_contents($api_url);

if ($response_body === false) {
    die("ERROR: Gagal mengambil data.");
}

$data = json_decode($response_body, true);
if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
    die("ERROR: Data bukan format JSON yang valid. " . htmlspecialchars(json_last_error_msg()));
}

header("Content-Type: text/html; charset=utf-8");

// Ambil lokasi
$lokasi = $data["lokasi"] ?? [];
$prakiraan = $data["data"][0]["cuaca"] ?? [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Cuaca BMKG</title>
    <link rel="stylesheet" href="assets/dashboard.css">
    <style>
        /* ===== Styling tombol header ===== */
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        /* Tombol Notifikasi */
        .notif-btn {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #7b68ee;
            color: white;
            width: 42px;
            height: 42px;
            border-radius: 10px;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .notif-btn svg {
            width: 22px;
            height: 22px;
        }

        .notif-btn:hover {
            background: #6a5acd;
            transform: scale(1.05);
        }

        .notif-badge {
            position: absolute;
            top: 4px;
            right: 5px;
            background: #ff3b30;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 11px;
            font-weight: bold;
        }

        /* Tombol Logout */
        .logout-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            background: #f44336;
            color: white;
            text-decoration: none;
            font-weight: 600;
            padding: 8px 14px;
            border-radius: 10px;
            font-size: 15px;
            line-height: 1;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background: #d32f2f;
            transform: scale(1.05);
        }

        .logout-icon {
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>

<div class="page-wrapper">
    <h1 class="dashboard-header">
        🌤️ Dashboard Cuaca BMKG
        <div class="header-actions">
            <!-- Tombol Notifikasi -->
            <a href="notifikasi.php" class="notif-btn" title="Lihat Notifikasi">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14V11a6 6 0 
                    00-5-5.916V5a2 2 0 10-4 0v.084A6 6 0 
                    004 11v3c0 .379-.214.725-.553.895L2 17h5m8 
                    0a3 3 0 01-6 0" />
                </svg>
                <span class="notif-badge"><?= count($prakiraan[0] ?? []) ?></span>
            </a>

            <!-- Tombol Logout -->
            <a href="auth/logout.php" class="logout-btn" title="Keluar dari Dashboard">
                <span class="logout-icon">🚪</span>
                <span class="logout-text">Logout</span>
            </a>
        </div>
    </h1>

    <div class="container">
        <!-- Lokasi -->
        <div class="card">
            <h2>📍 Lokasi</h2>
            <p>Desa/Kel: <?= htmlspecialchars($lokasi["desa"] ?? "-") ?></p>
            <p>Kecamatan: <?= htmlspecialchars($lokasi["kecamatan"] ?? "-") ?></p>
            <p>Kota/Kab: <?= htmlspecialchars($lokasi["kotkab"] ?? "-") ?></p>
            <p>Provinsi: <?= htmlspecialchars($lokasi["provinsi"] ?? "-") ?></p>
            <p>Koordinat: <?= htmlspecialchars($lokasi["lat"] ?? "-") ?>, <?= htmlspecialchars($lokasi["lon"] ?? "-") ?></p>
        </div>

        <!-- Cuaca Sekarang -->
        <?php 
        $cuaca_now = $prakiraan[0][0] ?? [];
        ?>
        <div class="card">
            <h2>☀️ Cuaca Saat Ini</h2>
            <p><strong><?= htmlspecialchars($cuaca_now["weather_desc"] ?? "-") ?></strong></p>
            <p>Suhu: <?= htmlspecialchars($cuaca_now["t"] ?? "-") ?> °C</p>
            <p>Kelembapan: <?= htmlspecialchars($cuaca_now["hu"] ?? "-") ?>%</p>
            <p>Angin: <?= htmlspecialchars($cuaca_now["ws"] ?? "-") ?> km/j dari <?= htmlspecialchars($cuaca_now["wd"] ?? "-") ?></p>
        </div>
    </div>

    <!-- Tabel Prakiraan -->
    <div class="card forecast">
        <h2>📅 Prakiraan Cuaca</h2>
        <table>
            <thead>
                <tr>
                    <th>Jam</th>
                    <th>Cuaca</th>
                    <th>Suhu</th>
                    <th>Kelembapan</th>
                    <th>Angin</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prakiraan as $hari): ?>
                    <?php foreach ($hari as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p["local_datetime"] ?? "-") ?></td>
                        <td>
                            <?= htmlspecialchars($p["weather_desc"] ?? "-") ?>
                            <?php if (!empty($p["image"])): ?>
                                <img src="<?= str_replace(" ", "%20", $p["image"]) ?>" class="weather-icon" alt="icon">
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($p["t"] ?? "-") ?> °C</td>
                        <td><?= htmlspecialchars($p["hu"] ?? "-") ?>%</td>
                        <td><?= htmlspecialchars($p["ws"] ?? "-") ?> km/j</td>
                    </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function toggleNotif() {
    const popup = document.getElementById("notifPopup");
    popup.style.display = (popup.style.display === "block") ? "none" : "block";
}
</script>
</body>
</html>
