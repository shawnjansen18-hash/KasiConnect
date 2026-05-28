<?php
$conn = new mysqli("localhost", "root", "", "kasi_connect");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
