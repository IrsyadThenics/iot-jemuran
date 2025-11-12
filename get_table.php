<?php
include "api/db.php";
$result = mysqli_query($conn, "SELECT * FROM sensor_data ORDER BY id DESC LIMIT 20");

while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr class='hover:bg-purple-50'>
        <td class='py-2 px-4 border-b border-gray-200 text-sm text-gray-700'>".htmlspecialchars($row['id'])."</td>
        <td class='py-2 px-4 border-b border-gray-200 text-sm text-gray-700'>".htmlspecialchars($row['device_id'])."</td>
        <td class='py-2 px-4 border-b border-gray-200 text-sm text-gray-700'>".htmlspecialchars($row['rain_value'])."</td>
        <td class='py-2 px-4 border-b border-gray-200 text-sm text-gray-700'>".htmlspecialchars($row['status'])."</td>
        <td class='py-2 px-4 border-b border-gray-200 text-sm text-gray-700'>".htmlspecialchars($row['motor_action'])."</td>
        <td class='py-2 px-4 border-b border-gray-200 text-sm text-gray-700'>".htmlspecialchars($row['created_at'])."</td>
    </tr>";
}
?>
