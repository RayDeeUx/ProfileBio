<?php
$dbHost = "not telling";
$dbUsername = "not telling";
$dbPassword = "not telling";
$dbName = "not telling";

// Create connection
$conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

// Check connection
if ($conn->connect_error) {
	echo(-1);
}
?>