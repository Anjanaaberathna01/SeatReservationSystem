<?php
session_start();
include("../includes/connect.php");

$db = new database();
$conn = $db->connect();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../log/index.php");

    die("You must be logged in to view reservations.");

}


$user_id = intval($_SESSION['user_id']);
$date = isset($_GET['date']) ? $_GET['date'] : date("Y-m-d");
$today = date("Y-m-d");

// Fetch all seats for this date
$sql = "
    SELECT s.seat_id, s.table_number, s.seat_number, r.user_id AS reserved_by, r.reservation_id
    FROM seats s
    LEFT JOIN reservations r 
      ON s.seat_id = r.seat_id AND r.reservation_date = ?
    ORDER BY s.table_number, s.seat_number
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $date);
$stmt->execute();
$result = $stmt->get_result();

// Organize seats by table
$tables = [];
while ($row = $result->fetch_assoc()) {
    $tables[$row['table_number']][] = $row;
}

// Fetch user's reservations for today
$sqlToday = "
    SELECT s.table_number, s.seat_number
    FROM reservations r
    JOIN seats s ON r.seat_id = s.seat_id
    WHERE r.user_id = ? AND r.reservation_date = ?
    ORDER BY s.table_number, s.seat_number
";
$stmtToday = $conn->prepare($sqlToday);
$stmtToday->bind_param("is", $user_id, $today);
$stmtToday->execute();
$resultToday = $stmtToday->get_result();

$yourSeatsToday = [];
while ($row = $resultToday->fetch_assoc()) {
    $yourSeatsToday[] = "Table {$row['table_number']} - Seat {$row['seat_number']}";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Seat Reservation</title>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;400&display=swap');

    body {
        font-family: 'Poppins', sans-serif;
        background: #C4E1E6;
        margin: 0;
        padding: 20px;
        text-align: center;
    }

    h2 {
        margin-bottom: 10px;
        font-size: 28px;
        text-align: left;
        margin-left: 20px;
    }

    .date-form {
        display: flex;
        align-items: center;
        margin-left: 20px;
        text-align: left;
        gap: 15px;
        margin-bottom: 80px;
        font-family: 'Poppins', sans-serif;
    }

    .date-form label {
        font-size: 18px;
        font-weight: 600;
        color: #333;
    }

    .date-form input[type="date"] {
        padding: 10px 15px;
        border: 2px solid #ccc;
        border-radius: 8px;
        font-size: 16px;
        transition: all 0.3s ease;
        outline: none;
    }

    .date-form input[type="date"]:focus {
        border-color: #2196F3;
        box-shadow: 0 0 8px rgba(33, 150, 243, 0.5);
    }

    .date-form button {
        padding: 10px 20px;
        background-color: #26667F;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .date-form button:hover {
        background-color: #124170;
        transform: translateY(-2px);
    }

    .tables-wrapper {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        grid-gap: 10px;
        justify-items: center;
        margin: 70px auto;
        max-width: 1500px;
    }

    .bottom-row {
        display: flex;
        justify-content: center;
        gap: 200px;
        margin-top: 150px;
    }

    .table {
        position: relative;
        width: 255px;
        height: 255px;
        border-radius: 50%;
        background: #ddd;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: #333;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
        flex-shrink: 0;
        font-size: 22px;
    }

    .seat-wrapper {
        position: absolute;
        text-align: center;
        width: 60px;
        height: 70px;
        pointer-events: auto;
    }

    .seat {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: transform 0.2s;
        margin: 0 auto;
    }

    .seat:hover {
        transform: scale(1.3);
    }

    .available {
        background: #4CAF50;
        color: white;
    }

    .yours {
        background: #0b81e1ff;
        color: white;
    }

    .booked {
        background: red;
        color: white;
        cursor: not-allowed;
    }

    .cancel-btn {
        margin-top: 5px;
        padding: 4px 8px;
        font-size: 12px;
        border: none;
        border-radius: 6px;
        background-color: #e74c3c;
        color: white;
        cursor: pointer;
        transition: 0.3s;
    }

    .cancel-btn:hover {
        background-color: #c0392b;
    }

    .top-right-btn {
        position: absolute;
        top: 85px;
        right: 30px;
        text-align: right;
        z-index: 100;
    }

    .top-right-btn button {
        padding: 10px 20px;
        background-color: #C4E1E6;
        font-family: 'Poppins', sans-serif;
        color: black;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: bold;
        cursor: default;
        white-space: nowrap;
    }

    .top-right-btn button:hover {
        color: #124170;
    }


    @media (max-width: 1500px) {
        .tables-wrapper {
            grid-template-columns: repeat(1, 1fr);
        }

        .bottom-row {
            gap: 80px;
        }
    }

    @media (max-width: 800px) {
        .tables-wrapper {
            grid-template-columns: 1fr;
        }

        .bottom-row {
            flex-direction: column;
            gap: 40px;
        }

        .top-right-btn {
            position: static;
            display: flex;
            justify-content: center;
            margin: 10px 0;
        }
    }
    </style>
</head>

<body>
    <?php include("reservation_header.php") ?>
    <!-- Top Right Section: Today's Booked Seats -->
    <div class="top-right-btn">
        <button onclick="window.location.href='my-reservations.php'">
            <?php
            if (count($yourSeatsToday) > 0) {
                echo "Today's Bookings:<br>" . implode(", ", $yourSeatsToday);
            } else {
                echo "No bookings for today";
            }
            ?>
        </button>
    </div>


    <h2>Seat Reservation for <?php echo htmlspecialchars($date); ?></h2>

    <form class="date-form" method="get">
        <label for="date">Select date:</label>
        <input type="date" name="date" id="date" value="<?php echo $date; ?>">
        <button type="submit">Go</button>

    </form>

    <div class="tables-wrapper">
        <?php
        $counter = 0;
        foreach ($tables as $table_number => $seats):
            $counter++;
            ?>
        <div class="table">
            Table <?php echo $table_number; ?>
            <?php
                $totalSeats = count($seats);
                $tableWidth = 271;
                $radius = 170;
                $center = $tableWidth / 2;
                $seatSize = 64;
                foreach ($seats as $index => $seat) {
                    $angle = (2 * M_PI * $index) / $totalSeats;
                    $x = cos($angle) * $radius + $center - $seatSize / 2;
                    $y = sin($angle) * $radius + $center - $seatSize / 2;

                    $class = "available";
                    $reservationId = $seat['reservation_id'] ?? 0;
                    $cancelButton = '';

                    if ($seat['reserved_by']) {
                        if ($seat['reserved_by'] == $user_id) {
                            $class = "yours";
                            $cancelButton = "<button class='cancel-btn' onclick='cancelSeat({$reservationId})'>Cancel</button>";
                        } else {
                            $class = "booked";
                        }
                    }

                    echo "<div class='seat-wrapper' style='left:{$x}px; top:{$y}px;'>
        <div class='seat $class' 
             title='Table {$table_number} - Seat {$seat['seat_number']}' 
             onclick='bookSeat({$seat['seat_id']}, \"{$date}\", \"{$class}\")'>
            {$seat['seat_number']}
        </div>
        $cancelButton
      </div>";
                }
                ?>
        </div>
        <?php if ($counter == 3): ?>
    </div>
    <div class="bottom-row">
        <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <script>
    function bookSeat(seatId, date, status) {
        if (status === "booked") {
            alert("This seat is already taken.");
            return;
        }
        if (confirm("Book this seat?")) {
            fetch("book-seat.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "seat_id=" + seatId + "&date=" + date
                })
                .then(res => res.text())
                .then(txt => {
                    if (txt.includes("success")) {
                        location.reload();
                    } else {
                        alert("Failed: " + txt);
                    }
                });
        }
    }

    function cancelSeat(reservationId) {
        if (!reservationId) return;
        if (confirm("Are you sure you want to cancel this reservation?")) {
            fetch("cancel-reservation.php?id=" + reservationId)
                .then(res => res.text())
                .then(txt => {
                    if (txt.includes("success") || txt.includes("✅")) {
                        location.reload();
                    } else {
                        alert("Failed to cancel: " + txt);
                    }
                });
        }
    }
    </script>
</body>

</html>