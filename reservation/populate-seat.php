<?php
// populate-seat.php
include("../includes/connect.php");

$db = new database();
$conn = $db->connect();

// Example: 25 tables × 4 seats = 100 seats
for ($table = 1; $table <= 25; $table++) {
    for ($seat = 1; $seat <= 4; $seat++) {
        $stmt = $conn->prepare("INSERT INTO seats (table_number, seat_number) VALUES (?, ?)");
        $stmt->bind_param("ii", $table, $seat);
        $stmt->execute();
    }
}
echo "Seats populated!";