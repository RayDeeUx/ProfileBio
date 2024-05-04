<?php
include("db.php");

function addAuth($AuthCode, $accountID, $conn) {
    // just in case
    $accountID = trim($accountID);
    $AuthCode = trim($AuthCode);

    $accountID = mysqli_real_escape_string($conn, $accountID);
    $AuthCode = mysqli_real_escape_string($conn, $AuthCode);

    $hashedCode = hash('sha256', $AuthCode);

    $sql = "INSERT INTO auth (accountID, Auth) VALUES ('$accountID', '$hashedCode')";
    $result = $conn->query($sql);
}
?>