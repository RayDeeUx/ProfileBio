<?php
include("db.php");

function compareAuthCodes($authCode, $accountID, $conn) {
    $hasAuthQuery = "SELECT auth FROM auth WHERE accountID = '$accountID'";
    $stmt = $conn->prepare($hasAuthQuery);
    $stmt->execute();
    $hasAuthResult = $stmt->get_result();

    if ($hasAuthResult->num_rows > 0) {
        $row = $hasAuthResult->fetch_assoc();
        $authString = $row['auth'];
        
        $AuthCodeHASHED = hash('sha256', $authCode);

        return ($authString === $AuthCodeHASHED);
    }
}

?>
