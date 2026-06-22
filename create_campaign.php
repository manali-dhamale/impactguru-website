<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patientName        = $_POST['patient_name'];
    $campaignerName     = $_POST['campaigner_name'];
    $campaignerLocation = $_POST['campaigner_location'];
    $relationship       = $_POST['relationship'];
    $hospitalName       = $_POST['hospital_name'];
    $medicalCause       = $_POST['medical_cause'];
    $title              = $_POST['title'];
    $targetAmount       = $_POST['target_amount'];
    $campaignStory      = $_POST['campaign_story'];
    
    $dbImageName = 'default_banner.jpg'; 


    if (isset($_FILES['banner_image']) && $_FILES['banner_image']['error'] == 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        $fileType = $_FILES['banner_image']['type'];
        if (in_array($fileType, $allowedTypes)) {
            $filename = time() . '_' . basename($_FILES['banner_image']['name']);
            $targetDirectory = "uploads/";
            if (!is_dir($targetDirectory)) { mkdir($targetDirectory, 0755, true); }
            $targetFilePath = $targetDirectory . $filename;
            if (move_uploaded_file($_FILES['banner_image']['tmp_name'], $targetFilePath)) {
                $dbImageName = $filename;
            }
        }
    }

    $host = 'localhost'; $dbName = 'impactguru_db'; $dbUser = 'root'; $dbPass = 'manu@1234';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8", $dbUser, $dbPass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "INSERT INTO campaigns (patient_name, campaigner_name, campaigner_location, relationship, hospital_name, medical_cause, title, target_amount, banner_image, campaign_story, shares_count) 
                VALUES (:patient_name, :campaigner_name, :campaigner_location, :relationship, :hospital_name, :medical_cause, :title, :target_amount, :banner_image, :campaign_story, :shares_count)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':patient_name'        => $patientName,
            ':campaigner_name'     => $campaignerName,
            ':campaigner_location' => $campaignerLocation,
            ':relationship'       => $relationship,
            ':hospital_name'       => $hospitalName,
            ':medical_cause'       => $medicalCause,
            ':title'               => $title,
            ':target_amount'       => $targetAmount,
            ':banner_image'        => $dbImageName,
            ':campaign_story'      => $campaignStory,
            ':shares_count'        => rand(50, 600) 
        ]);

        $newCampaignId = $pdo->lastInsertId();
        header("Location: index.php?id=" . $newCampaignId);
        exit();
    } catch (PDOException $e) {
        echo "<h3>Database Error: " . $e->getMessage() . "</h3>";
    }
}
?>