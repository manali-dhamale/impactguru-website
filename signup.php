<?php
$name = $_POST['name'];
$email=$_POST['email'];
$password=$_POST['password'];

$data= $name."|". $email . "|" . $password . "\n";

file_put_contents(
    "users.txt",
    $data,
    FILE_APPEND
);

header("Location: index.html");
exit();
?>