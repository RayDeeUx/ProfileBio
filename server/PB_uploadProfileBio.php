<?php
include("db.php");

// fix bad words
$bannedwords = ["nigga", "nigger", "faggot", "retard", "tranny", "fag", "dyke"];

// Check if accountID and bio parameters are set in the POST request
if(isset($_POST['accountID']) && isset($_POST['bio'])) {
    // Get the values of accountID and bio parameters from the POST data
    $accountID = $_POST['accountID'];
    $bio = $_POST['bio'];

    // Trim input
    $accountID = trim($accountID);
    $bio = trim($bio);

    // Sanitize inputs
    $accountID = mysqli_real_escape_string($conn, $accountID);
    $bio = mysqli_real_escape_string($conn, $bio);

    $check = "SELECT accountID FROM bios WHERE accountID = '$accountID'";
    $userHasBio = $conn->query($check);

    $hasBadWord = false;

    foreach($bannedwords as $badword) {
        if (strpos($bio, $badword) !== false) {
            echo "Looks like your bio contains a banned word, bad words are not allowed.";
            $hasBadWord = true;
            break;
        }
    }

    if (!$hasBadWord) {
        if (empty($bio)) {
            echo "Looks like your bio is empty, you cannot have an empty bio.";
        } elseif ($userHasBio && $userHasBio->num_rows > 0) {
            $updateBioQuery = "UPDATE bios SET bio = '$bio' WHERE accountID = '$accountID'";
            if ($conn->query($updateBioQuery) === TRUE) {
                echo "Bio updated successfully!";
            } else {
                echo "There has been an error updating your bio.";
            }
        } else {
            $uploadBioQuery = "UPDATE bios SET bio = '$bio' WHERE accountID = '$accountID'";
            if ($conn->query($uploadBioQuery) === TRUE) {
                echo "Your bio is uploaded successfully to the servers!";
            } else {
                echo "There has been an error uploading your bio.";
            }
        }
    }
} else {
    echo "Missing accountID or bio parameters in the request.";
}
?>
