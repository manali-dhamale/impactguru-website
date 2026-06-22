<?php

$loginInput = $_POST['login_input'];
$passwordInput = $_POST['password'];


$users = file("users.txt");

foreach ($users as $line) {
   
    $userData = explode("|", $line);
    
   
    if ($loginInput == $userData[0] || $loginInput == $userData[1]) {
      
       
        if (password_verify($passwordInput, $userData[2])) {
            
          
            header("location:index.php");
            exit();
            
        }
    }
}


header("location:index.html");
?>