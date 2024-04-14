<?php
// Database configuration
include("db.php");

if(isset($_GET['accountID'])) {
	// Get the accountId parameter from the URL
	$accountID = $_GET['accountID'];
} else {
	// If accountId parameter is not provided, return an error message
	die("Error: accountId parameter is missing.");
}

// SQL query to retrieve data from the bios table for the specified account ID
$sql = "SELECT id, accountID, bio FROM bios WHERE accountID = $accountID";
$result = $conn->query($sql);

// Check if the SQL query was successful
if (!$result) {
	// Query failed, output the error message
	die("Error executing the query: " . $conn->error);
}

// Check if there are any rows returned
if ($result->num_rows > 0) {

	$row = $result->fetch_assoc();

	$data = array(
		"id" => $row["id"],
		"accountID" => $row["accountID"],
		"bio" => $row["bio"]
	);
	$jsonString = json_encode($data, JSON_PRETTY_PRINT);

	echo $jsonString;
} else {
	echo("-1");
}

// Close connection
$conn->close();
?>
