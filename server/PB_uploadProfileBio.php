<?php
include("db.php"); // Assuming this includes your mysqli database connection
include("CheckAuth.php");

// Fetch banned words
$badWordQuery = "SELECT word FROM bannedwords"; // Assuming the column name is 'word'
$result = $conn->query($badWordQuery);
$bannedWords = array();
while ($row = $result->fetch_assoc()) {
    $bannedWords[] = $row['word'];
}

if(isset($_POST['accountID'], $_POST['bio'], $_POST['authCode'])) {
    $accountID = $_POST['accountID'];
    $bio = $_POST['bio'];
    $authCode = $_POST['authCode'];

    $hasBadWord = false;
    foreach($bannedWords as $badWord) {
        if (strpos($bio, $badWord) !== false) {
            echo "Your bio contains a banned word. Remove it.";
            $hasBadWord = true;
            break;
        }
    }

    if (!$hasBadWord) {
        if (!compareAuthCodes($authCode, $accountID, $conn)) {
            exit("Authentication Failed.");
        } elseif (empty($bio)) {
            echo "Your bio is empty. Please provide a bio.";
        } else {
            $accountID = mysqli_real_escape_string($conn, trim($accountID));
            $bio = mysqli_real_escape_string($conn, trim($bio));
            $authCode = mysqli_real_escape_string($conn, trim($authCode));

            $check = "SELECT accountID FROM bios WHERE accountID = '$accountID'";
            $result = $conn->query($check);
            $hasBio = ($result->num_rows > 0);

            if ($hasBio) {
                $updateBioQuery = "UPDATE bios SET bio = '$bio' WHERE accountID = '$accountID'";
            } else {
                $updateBioQuery = "INSERT INTO bios (accountID, bio) VALUES ('$accountID', '$bio')";
            }

            if ($conn->query($updateBioQuery)) {
                if ($hasBio) {
                    echo "Your bio has been successfully updated!";
                } else {
                    echo "Your bio has been uploaded to the servers!";
                }
            } else {
                echo "An error occurred while updating/uploading your bio.";
            }
        }
    }
} else {
    echo "Missing arguments.";
}
?>
