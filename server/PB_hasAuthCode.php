<?php
include("db.php");
include("PB_addAuth.php");

if (isset($_GET["accountID"]) && isset($_GET["AuthCode"])) {

    $accountID = $_GET["accountID"];
    $AuthCode = $_GET["AuthCode"];

    $HasAuthStrSQL = "SELECT accountID FROM auth WHERE accountID = '$accountID'";
    $stmt = $conn->prepare($HasAuthStrSQL);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "0";
    } else {
        addAuth($AuthCode, $accountID, $conn);
        echo "1";
    }

    $stmt->close();
}
?>
