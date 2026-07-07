<?php
session_start();

$host = 'localhost';
$dbName = 'impactguru_db';
$dbUser = 'root';
$dbPass = 'manu@1234';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $loginInput = trim($_POST['login_input']);
    $passwordInput = $_POST['password'];

    $sql = "SELECT id, name, password FROM users WHERE name = :input OR email = :input LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':input' => $loginInput]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($passwordInput, $user['password'])) {
        
        $_SESSION['user_id'] = $user['id']; 
        $_SESSION['user_name'] = $user['name'];
        
        header("Location: index.php");
        exit();
    } else {
        header("Location: login_page.html?error=invalid");
        exit();
    }
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>