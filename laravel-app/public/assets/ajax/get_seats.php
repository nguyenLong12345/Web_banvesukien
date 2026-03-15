<?php
require_once "../config.php";

$event_id = $_GET["event_id"];

$stmt = $pdo->prepare("SELECT * FROM seats WHERE event_id = ?");
$stmt->execute([$event_id]);
$seats = $stmt->fetchAll();

$output = "";
foreach ($seats as $seat) {
    $class = ($seat["status"] == "booked") ? "booked" : "available";
    $output .= "<div class='seat $class' data-seat='{$seat["seat_number"]}'>{$seat["seat_number"]}</div>";
}
echo $output;
?>
    