<?php

date_default_timezone_set('Asia/Kolkata');

$host = 'localhost'; $dbName = 'impactguru_db'; $dbUser = 'root'; $dbPass = 'manu@1234';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

   
    $selectedCity = isset($_GET['city']) ? trim($_GET['city']) : '';

    
    if (!empty($selectedCity)) {
        $query = "SELECT c.*, COALESCE(SUM(p.amount), 0) AS total_raised 
                  FROM campaigns c 
                  LEFT JOIN payments p ON c.id = p.campaign_id 
                  WHERE c.city = :city AND c.status = 1
                  GROUP BY c.id 
                  ORDER BY c.id DESC";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute([':city' => $selectedCity]);
    } else {
        
        $query = "SELECT c.*, COALESCE(SUM(p.amount), 0) AS total_raised 
                  FROM campaigns c 
                  LEFT JOIN payments p ON c.id = p.campaign_id 
                  WHERE c.status = 1
                  GROUP BY c.id 
                  ORDER BY c.id DESC";
        
        $stmt = $pdo->query($query);
    }

    $campaigns = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Browse Engine Error: " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Fundraisers | ImpactGuru</title>
    <link rel="icon" type="image/x-icon" href="./images/icon.png">
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
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            padding: 20px;
            align-items: stretch;
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
            justify-content: space-between; /* 🌟 Ensures button stays perfectly at the bottom */
        }
        .card-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
            line-height: 1.4;
            /* 🌟 Clamps the title to max 2 lines so long headers don't push layouts unevenly */
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
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
            margin-top: auto; /* 🌟 Pushes button to base line automatically */
            text-transform: uppercase;
            font-size: 14px;
        }
        .view-btn:hover {
            background-color: #0c8fc9;
        }
    </style>
</head>
<body>

<div class="filter-navigation" style="text-align: center; margin: 30px 0; font-family: sans-serif;">
    <span style="font-weight: bold; margin-right: 12px; color: #555;">📍 Explore Campaigns by City:</span>
    
    <a href="browse.php" style="padding: 8px 16px; background: <?php echo empty($selectedCity) ? '#0da2e3' : '#eee'; ?>; color: <?php echo empty($selectedCity) ? 'white' : '#333'; ?>; text-decoration: none; border-radius: 20px; margin: 0 5px; font-size: 14px; font-weight: bold; display: inline-block;">All Cities</a>
    
    <a href="browse.php?city=Mumbai" style="padding: 8px 16px; background: <?php echo ($selectedCity == 'Mumbai') ? '#0da2e3' : '#eee'; ?>; color: <?php echo ($selectedCity == 'Mumbai') ? 'white' : '#333'; ?>; text-decoration: none; border-radius: 20px; margin: 0 5px; font-size: 14px; font-weight: bold; display: inline-block;">Mumbai</a>
    
    <a href="browse.php?city=Thane" style="padding: 8px 16px; background: <?php echo ($selectedCity == 'Thane') ? '#0da2e3' : '#eee'; ?>; color: <?php echo ($selectedCity == 'Thane') ? 'white' : '#333'; ?>; text-decoration: none; border-radius: 20px; margin: 0 5px; font-size: 14px; font-weight: bold; display: inline-block;">Thane</a>
    
    <a href="browse.php?city=Kalyan" style="padding: 8px 16px; background: <?php echo ($selectedCity == 'Kalyan') ? '#0da2e3' : '#eee'; ?>; color: <?php echo ($selectedCity == 'Kalyan') ? 'white' : '#333'; ?>; text-decoration: none; border-radius: 20px; margin: 0 5px; font-size: 14px; font-weight: bold; display: inline-block;">Kalyan</a>

    <a href="browse.php?city=Pune" style="padding: 8px 16px; background: <?php echo ($selectedCity == 'Pune') ? '#0da2e3' : '#eee'; ?>; color: <?php echo ($selectedCity == 'Pune') ? 'white' : '#333'; ?>; text-decoration: none; border-radius: 20px; margin: 0 5px; font-size: 14px; font-weight: bold; display: inline-block;">Pune</a>
</div>

<div class="browse-container">
    <h1 class="page-title">Active Medical Fundraisers</h1>
    
    <div class="campaign-grid">
        <?php foreach ($campaigns as $row): ?>
            <div class="campaign-card">
                <a href="index.php?id=<?php echo htmlspecialchars($row['uuid']); ?>">
                    <img src="uploads/<?php echo htmlspecialchars($row['banner_image']); ?>" alt="Banner" class="card-image">
                </a>
                
                <div class="card-content">
                    <h3 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h3>
                    
                    <a href="index.php?id=<?php echo htmlspecialchars($row['uuid']); ?>" class="view-btn">
                        View Fundraiser
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>