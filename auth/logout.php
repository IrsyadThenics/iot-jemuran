<?php
session_start();
session_unset();
session_destroy();

// arahkan kembali ke index
header("Location: ../index.php");
exit;
?>
