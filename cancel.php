<?php

$conn = mysqli_connect("localhost", "root", "", "demo_his");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


if (isset($_GET['id']) && ctype_digit($_GET['id']) && (int) $_GET['id'] > 0) {

    $id = (int) $_GET['id'];


    $stmt = $conn->prepare(
        "UPDATE reservations SET status = 'Cancelled'
         WHERE reservation_id = ? AND status = 'Ongoing'"
    );
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: index.php?cancelled=1");
    exit();

} else {
    header("Location: index.php");
    exit();
}

mysqli_close($conn);
?>