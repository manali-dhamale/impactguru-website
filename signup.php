<?php
// signup.php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

   
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);


    $host = 'localhost';
    $dbName = 'impactguru_db';
    $dbUser = 'root';
    $dbPass = 'manu@1234';

    try {
        
        $pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8", $dbUser, $dbPass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

       
        $sql = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)";
        $stmt = $pdo->prepare($sql);

   
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => $hashed_password
        ]);

       
        header("Location: login_page.html");
        exit();

    } catch (PDOException $e) {
       
        if ($e->getCode() == 23000) {
            echo "<h3>❌ Error: This email address is already registered.</h3>";
        } else {
            echo "<h3>❌ Database Error: " . $e->getMessage() . "</h3>";
        }
    }
} else {
    echo "Access denied.";
}
?>