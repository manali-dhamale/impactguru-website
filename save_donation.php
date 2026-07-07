<?php


$jsonInput = file_get_contents('php://input');
$data = json_decode($jsonInput, true);


if ($data) {
    $paymentId  = $data['payment_id'];
    $amount     = $data['amount'];
    $campaignId = $data['campaign_id'];
    $donorName  = $data['donor_name'];
    $donorEmail = $data['donor_email'];
    $donorPhone = $data['donor_phone'];

   
    $host = 'localhost';
    $dbName = 'impactguru_db';
    $dbUser = 'root';
    $dbPass = 'manu@1234';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8", $dbUser, $dbPass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


        $sql = "INSERT INTO payments (payment_id, amount, campaign_id, donor_name, donor_email, donor_phone, status) 
                VALUES (:payment_id, :amount, :campaign_id, :donor_name, :donor_email, :donor_phone , :status)";
        
        $stmt = $pdo->prepare($sql);

       
        $stmt->execute([
            ':payment_id'  => $paymentId,
            ':amount'      => $amount,
            ':campaign_id' => $campaignId,
            ':donor_name'  => $donorName,
            ':donor_email' => $donorEmail,
            ':donor_phone' => $donorPhone,
            ':status'      => 1
        ]);

      
        echo "Donation successfully logged to MySQL database.";

    } catch (PDOException $e) {
        echo "Database Error: " . $e->getMessage();
    }
} else {
    echo "No valid transaction payload received.";
}
?>