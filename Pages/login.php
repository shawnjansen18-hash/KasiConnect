<?php
session_start();
include("../Includes/database.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST["email"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user["password"])) {

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user"] = $user["name"];

            if (isset($_SESSION["redirect_after_login"])) {
                $redirect = $_SESSION["redirect_after_login"];
                unset($_SESSION["redirect_after_login"]);
                header("Location: $redirect");
            } else {
                header("Location: dashboard.php");
            }
            exit();
        } else {
            $error = "Incorrect Password!";
        }
    } else {
        $error = "User not found";
    }
}
?>

<?php include("../Includes/header.php"); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KasiConnect</title>
    <link rel="stylesheet" href="../KasiConnect/CSS/style.css">
</head>

<body>
    <h2>Login</h2>

    <?php if (isset($error)) echo "<p>$error</p>"; ?>

    <form method="POST">
        <label>Enter Your Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Enter Your Password:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Login</button>
    </form>

</body>

</html>

<?php
include("../Includes/footer.php");
?>