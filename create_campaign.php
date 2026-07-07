<?php

date_default_timezone_set('Asia/Kolkata');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
    $patientName        = isset($_POST['patient_name']) ? $_POST['patient_name'] : '';
    $campaignerName     = isset($_POST['campaigner_name']) ? $_POST['campaigner_name'] : '';
    $campaignerLocation = isset($_POST['campaigner_location']) ? $_POST['campaigner_location'] : '';
    $relationship       = isset($_POST['relationship']) ? $_POST['relationship'] : '';
    $hospitalName       = isset($_POST['hospital_name']) ? $_POST['hospital_name'] : '';
    $medicalCause       = isset($_POST['medical_cause']) ? $_POST['medical_cause'] : '';
    $title              = isset($_POST['title']) ? $_POST['title'] : '';
    $targetAmount       = isset($_POST['target_amount']) ? intval($_POST['target_amount']) : 0;
    $campaignStory      = isset($_POST['campaign_story']) ? $_POST['campaign_story'] : '';
    
    
    $state              = isset($_POST['state']) ? $_POST['state'] : 'Maharashtra';
    $city               = isset($_POST['city']) ? $_POST['city'] : '';
    $regionArea         = isset($_POST['region_area']) ? $_POST['region_area'] : '';

    
    $dbImageName = 'default_banner.jpg'; 

    
    if (isset($_FILES['banner_image']) && $_FILES['banner_image']['error'] == 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        $fileType = $_FILES['banner_image']['type'];
        if (in_array($fileType, $allowedTypes)) {
            $filename = time() . '_' . basename($_FILES['banner_image']['name']);
            $targetDirectory = "uploads/";
            if (!is_dir($targetDirectory)) { 
                mkdir($targetDirectory, 0755, true); 
            }
            $targetFilePath = $targetDirectory . $filename;
            if (move_uploaded_file($_FILES['banner_image']['tmp_name'], $targetFilePath)) {
                $dbImageName = $filename;
            }
        }
    }

    
    $host = 'localhost'; 
    $dbName = 'impactguru_db'; 
    $dbUser = 'root'; 
    $dbPass = 'manu@1234';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8", $dbUser, $dbPass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

       
        $formData = [
            'title'               => $title,
            'target_amount'       => $targetAmount,
            'patient_name'        => $patientName,
            'campaigner_name'     => $campaignerName,
            'hospital_name'       => $hospitalName,
            'medical_cause'       => $medicalCause,
            'campaign_story'      => $campaignStory,
            'banner_image'        => $dbImageName,
            'campaigner_location' => $campaignerLocation,
            'relationship'        => $relationship,
            'shares_count'        => rand(50, 600),
            'state'               => $state,
            'city'                => $city,
            'region_area'         => $regionArea,
            'uuid'                => 'expr_placeholder_marker'
        ];

       
        $schemaStmt = $pdo->query("DESCRIBE campaigns");
        $databaseColumns = $schemaStmt->fetchAll(PDO::FETCH_COLUMN);

        $columnsToInsert = [];
        $placeholders = [];
        $executePayload = [];


        foreach ($formData as $columnKey => $formValue) {
            if (in_array($columnKey, $databaseColumns)) {
                $columnsToInsert[] = $columnKey;

                if ($columnKey === 'uuid') {
                    $placeholders[] = "UUID()";
                } else {
                    $placeholders[] = ":" . $columnKey;
                    $executePayload[":" . $columnKey] = $formValue;
                }

               
            }
        }

        $sql = "INSERT INTO campaigns (" . implode(', ', $columnsToInsert) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";
                
        $stmt = $pdo->prepare($sql);
        $pdo->exec("SET time_zone = '+05:30'");
        $stmt->execute($executePayload);

        
       $lastInsertedId = $pdo->lastInsertId();
        $uuidFetch = $pdo->prepare("SELECT uuid FROM campaigns WHERE id = :id LIMIT 1");
        $uuidFetch->execute([':id' => $lastInsertedId]);
        $newCampaignUuid = $uuidFetch->fetchColumn();

        
        header("Location: index.php?id=" . $newCampaignUuid);
        exit();
    } catch (PDOException $e) {
        echo "<div style='padding: 20px; background: #fff5f5; border: 1px solid #ffc9c9; color: #d93838; border-radius: 4px; font-family: sans-serif; margin: 20px;'>";
        echo "<h3>Database Error</h3>";
        echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
        echo "</div>";
    }
}
?>