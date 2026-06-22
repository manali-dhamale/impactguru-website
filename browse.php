<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = "localhost"; 
$dbname = "impactguru_db"; 
$username = "root"; 
$password = "manu@1234"; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}


$query = "SELECT c.*, COALESCE(SUM(p.amount), 0) AS total_raised 
          FROM campaigns c 
          LEFT JOIN payments p ON c.id = p.campaign_id 
          GROUP BY c.id 
          ORDER BY c.id DESC";

$stmt = $pdo->query($query);

$campaigns = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Fundraisers | ImpactGuru</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .browse-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
            font-family: Arial, sans-serif;
        }
        .page-title {
            font-size: 28px;
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }
        .campaign-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 30px;
        }
        .campaign-card {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            transition: transform 0.2s;
        }
        .campaign-card:hover {
            transform: translateY(-5px);
        }
        .card-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background-color: #f5f5f5;
        }
        .card-content {
            padding: 20px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }
        .card-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
            line-height: 1.4;
        }
        .card-patient {
            font-size: 14px;
            color: #ff8502;
            font-weight: bold;
            margin-bottom: 12px;
        }
        .progress-bar-container {
            background-color: #eee;
            border-radius: 4px;
            height: 8px;
            width: 100%;
            margin-bottom: 15px;
            overflow: hidden;
        }
        .progress-bar-fill {
            background-color: #ff8502;
            height: 100%;
            border-radius: 4px;
        }
        .funding-stats {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            color: #555;
            margin-bottom: 20px;
        }
        .view-btn {
            display: block;
            text-align: center;
            background-color: #0da2e3;
            color: white;
            text-decoration: none;
            padding: 12px;
            border-radius: 4px;
            font-weight: bold;
            margin-top: auto;
            text-transform: uppercase;
            font-size: 14px;
        }
        .view-btn:hover {
            background-color: #0c8fc9;
        }
    </style>
</head>
<body>

    <div class="browse-container">
        <h1 class="page-title">Active Medical Fundraisers</h1>
        
        <div class="campaign-grid">
            <?php foreach ($campaigns as $row): 
               
                $target = $row['target_amount'] > 0 ? $row['target_amount'] : 1;
                $percent = round(($row['total_raised'] / $target) * 100);
                if ($percent > 100) $percent = 100;
                
                
                $imageName = !empty($row['banner_image']) ? $row['banner_image'] : 'default_banner.jpg';
                $imagePath = "uploads/" . $imageName;
            ?>
                <div class="campaign-card">
                    <img src="<?php echo htmlspecialchars($imagePath); ?>" class="card-image" alt="Campaign Image">
                    
                    <div class="card-content">
                        <div class="card-patient">For: <?php echo htmlspecialchars($row['patient_name']); ?></div>
                        <div class="card-title"><?php echo htmlspecialchars($row['title']); ?></div>
                        
                        <div class="progress-bar-container">
                            <div class="progress-bar-fill" style="width: <?php echo $percent; ?>%;"></div>
                        </div>
                        
                        <div class="funding-stats">
                            <div><strong>₹<?php echo number_format($row['total_raised']); ?></strong> <br><span style="font-size:12px; color:#999;">Raised</span></div>
                            <div style="text-align: right;"><strong><?php echo $percent; ?>%</strong> <br><span style="font-size:12px; color:#999;">Funded</span></div>
                        </div>
                        
                        <a href="index.php?id=<?php echo $row['id']; ?>" class="view-btn">View Fundraiser</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</body>
</html>