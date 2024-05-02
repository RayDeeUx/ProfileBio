<?php
include("db.php");
include("PB_addAuth.php");

if (isset($_GET["accountID"])) {

    $accountID = $_GET["accountID"];

    $HasAuthStrSQL = "SELECT accountID FROM auth WHERE accountID = '$accountID'";
    $stmt = $conn->prepare($HasAuthStrSQL);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "0";
    } else {
        echo "1";
        addAuth($AuthCode, $accountID, $conn);
    }

    $stmt->close();
}
?>
