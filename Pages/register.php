<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function registerWithApi($name, $email, $password)
{
    $apiUrl = "https://localhost:7223/api/auth/register";

    $payload = json_encode([
        "name" => $name,
        "email" => $email,
        "password" => $password
    ]);

    $ch = curl_init($apiUrl);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $response = curl_exec($ch);

    if ($response === false) {
        curl_close($ch);

        return [
            "success" => false,
            "message" => "Could not connect to API."
        ];
    }

    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($statusCode !== 201) {
        return [
            "success" => false,
            "message" => $response ?: "Registration failed."
        ];
    }

    return [
        "success" => true,
        "data" => json_decode($response, true)
    ];
}

$error = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    $result = registerWithApi($name, $email, $password);

    if ($result["success"]) {
        $data = $result["data"];

        $_SESSION["user_id"] = $data["userId"];
        $_SESSION["user"] = $data["name"];
        $_SESSION["api_token"] = $data["token"];

        header("Location: dashboard.php");
        exit();
    }

    $error = $result["message"];
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

    <h2>Register</h2>

    <?php if ($error !== null): ?>
        <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="post">
        <label>Enter Your Full Name:</label><br>
        <input type="text" name="name" placeholder="John Dickson" required><br>

        <label>Enter Your Email:</label><br>
        <input type="email" name="email" placeholder="johnDickson@gmail.com" required><br>

        <label>Enter Your Password:</label><br>
        <input type="password" name="password" placeholder="Password" required><br><br>

        <button type="submit">
            Register
        </button>
    </form>

</body>

</html>

<?php include("../Includes/footer.php"); ?>