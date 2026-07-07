<?php
session_start();
header('Content-Type: application/json');

$host = 'localhost';
$dbName = 'impactguru_db';
$dbUser = 'root';
$dbPass = 'manu@1234';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['platform'], $_POST['campaign_id'])) {
        
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        $campaignId = intval($_POST['campaign_id']);
        $platform = trim($_POST['platform']);


        $sql = "INSERT INTO shares (campaign_id, user_id, platform, shared_at, updated_at, status) 
                VALUES (:campaign_id, :user_id, :platform, NOW(), NOW(), 1)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':campaign_id' => $campaignId,
            ':user_id'     => $userId, 
            ':platform'    => $platform
        ]);

        
        $countSql = "SELECT COUNT(*) FROM shares WHERE campaign_id = :campaign_id AND status = 1";
        $countStmt = $pdo->prepare($countSql);
        $countStmt->execute([':campaign_id' => $campaignId]);
        $totalShares = $countStmt->fetchColumn();

        echo json_encode([
            'success' => true,
            'total_shares' => $totalShares
        ]);
        exit();
    }

    echo json_encode(['success' => false, 'error' => 'Invalid parameters passed']);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database failure: ' . $e->getMessage()
    ]);
}
?>