<?php
session_start();
include("../includes/connect.php");
if (!isset($_SESSION['admin_name'])) {
    header("Location: index.php");
    exit;
}

$db = new database();
$conn = $db->connect();

// Count reservations per date
$sql = "SELECT reservation_date, COUNT(*) as total FROM reservations GROUP BY reservation_date ORDER BY reservation_date DESC";
$res = $conn->query($sql);

$dates = [];
$totals = [];

while ($row = $res->fetch_assoc()) {
    $dates[] = $row['reservation_date'];
    $totals[] = $row['total'];
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Reports</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #5499cdff, #eef3f7);
            padding: 20px;
            margin: 0;
        }

        h2 {
            color: #124170;
            text-align: center;
            margin-bottom: 20px;
        }

        .chart-container {
            width: 90%;
            max-width: 800px;
            margin: 0 auto 40px;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        table {
            width: 90%;
            max-width: 800px;
            border-collapse: collapse;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            text-align: center;
            padding: 12px;
        }

        th {
            background: #26667F;
            color: white;
        }

        tr:nth-child(even) {
            background: #f8f8f8;
        }

        tr:hover {
            background: #d0ebff;
        }

        @media(max-width:600px) {

            th,
            td {
                padding: 8px;
                font-size: 14px;
            }
        }
    </style>
</head>

<body>

    <?php include("admin-header.php"); ?>

    <h2>Seat Usage Reports</h2>

    <div class="chart-container">
        <canvas id="reservationsChart"></canvas>
    </div>

    <table>
        <tr>
            <th>Date</th>
            <th>Total Reservations</th>
        </tr>
        <?php foreach ($dates as $i => $date): ?>
            <tr>
                <td><?= $date ?></td>
                <td><?= $totals[$i] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <script>
        const ctx = document.getElementById('reservationsChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($dates) ?>,
                datasets: [{
                    label: 'Reservations',
                    data: <?= json_encode($totals) ?>,
                    backgroundColor: 'rgba(38, 102, 127, 0.7)',
                    borderColor: '#124170',
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: true
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>

</body>

</html>