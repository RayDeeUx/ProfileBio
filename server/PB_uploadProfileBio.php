<?php
include("db.php");

// Function to decode URL parameters
function decodeURLParams($param) {
	return urldecode(str_replace("+", " ", $param));
}

// Check if accountID and bio parameters are set in the URL
if(isset($_GET['accountID']) && isset($_GET['bio'])) {
	// Get the values of accountID and bio parameters from the URL
	$accountID = $_GET['accountID'];
	$bio = $_GET['bio'];

	// Decode URL parameters
	$accountID = decodeURLParams($accountID);
	$bio = decodeURLParams($bio);

	// Trim input
	$accountID = trim($accountID);
	$bio = trim($bio);

	// Sanitize inputs
	$accountID = mysqli_real_escape_string($conn, $accountID);
	$bio = mysqli_real_escape_string($conn, $bio);

	$check = "SELECT accountID FROM bios WHERE accountID = '$accountID'";
	$userHasBio = $conn->query($check);
	
	if (empty($bio)) {
		echo "Looks like your bio is empty! you cannot have an empty bio."; // Indicates empty bio
	} elseif ($userHasBio && $userHasBio->num_rows > 0) {
		// Update bio
		$sql = "UPDATE bios SET bio = '$bio' WHERE accountID = '$accountID'";
		if ($conn->query($sql) === TRUE) {
			echo "Bio updated successfully";
		} else {
			echo "Error updating bio: " . $conn->error;
		}
	} else {
		// Insert new bio
		$sql = "INSERT INTO bios (accountID, bio) VALUES ('$accountID', '$bio')";
		if ($conn->query($sql) === TRUE) {
			echo "Bio uploaded successfully";
		} else {
			echo "Error inserting bio: " . $conn->error;
		}
	}
}
?>
