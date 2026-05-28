<?php
include("../Includes/header.php");
?>

<?php
include("../Includes/database.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (name, email, password)
                VALUES ('$name','$email','$hash')";

    if ($conn->query($sql)) {
        echo "Registration successful!";
    } else {
        echo "Error: " . $conn->error;
    }
}
include("../Includes/footer.php");
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

    <h2>Register</h2>
    <form method="post">
        <label>Enter Your Full Name:</label><br>
        <input type="text" name="name" placeholder="John Dickson"><br>
        <label>Enter Your Email:</label><br>
        <input type="email" name="email" placeholder="johnDickson@gmail.com"><br>
        <label>Enter Your Password:</label><br>
        <input type="password" name="password" placeholder="Password"><br><br>

        <button>
            Register
        </button>
    </form>

</body>

</html>