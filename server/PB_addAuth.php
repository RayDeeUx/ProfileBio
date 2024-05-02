<?php
include("db.php");

function addAuth($AuthCode, $accountID, $conn) {
    // just in case
    $accountID = trim($accountID);
    $authCode = trim($authCode);

    $accountID = mysqli_real_escape_string($conn, $accountID);
    $authCode = mysqli_real_escape_string($conn, $authCode);

    $hashedCode = hash('sha256', $authCode);

    $sql = "INSERT INTO auth (accountID, Auth) VALUES ('$accountID', '$hashedCode')";
    $result = $conn->query($sql);

    if ($result === TRUE) {
        echo "0";
    } else {
        echo "1";
    }
}
?>