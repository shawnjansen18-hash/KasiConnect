<?php
include("../Includes/header.php");


if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KasiConnect</title>
    <link rel="stylesheet" href="../KasiConnect/CSS/style.css">
</head>

<body>
    <h2>Dashboard</h2>
    <p>Welcome, <?php echo $_SESSION["user"]; ?> </p>

    <?php
    if (isset($_SESSION["api_token"])) {
        echo "<p>API token stored</p>";
    } else {
        echo "<p>No API token found</p>";
    }
    ?>

    <a href="../logout.php">Logout</a>
</body>

</html>

<?php
include("../Includes/footer.php");
?>