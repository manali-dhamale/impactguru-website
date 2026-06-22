<?php
// Capture parameters sent via Razorpay's redirect string
$amount = isset($_GET['amount']) ? htmlspecialchars($_GET['amount']) : '0';
$payment_id = isset($_GET['payment_id']) ? htmlspecialchars($_GET['payment_id']) : 'N/A';
$donor_name = isset($_GET['name']) ? htmlspecialchars($_GET['name']) : 'Generous Donor';
$campaign_id = isset($_GET['campaign_id']) ? intval($_GET['campaign_id']) : 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful | ImpactGuru</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .success-container {
            max-width: 500px;
            margin: 80px auto;
            padding: 40px;
            text-align: center;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            font-family: Arial, sans-serif;
        }
        .success-icon {
            font-size: 64px;
            color: #4CAF50;
            margin-bottom: 20px;
        }
        .amount-display {
            font-size: 32px;
            font-weight: bold;
            color: #333;
            margin: 15px 0;
        }
        .details-box {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            text-align: left;
            font-size: 14px;
            color: #555;
            line-height: 1.6;
        }
        .return-btn {
            display: inline-block;
            background-color: #ff8502;
            color: white;
            text-decoration: none;
            padding: 14px 28px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 5px;
            margin-top: 15px;
            transition: background 0.2s;
        }
        .return-btn:hover {
            background-color: #e07401;
        }
    </style>
</head>
<body>

    <div class="success-container">
        <div class="success-icon">✓</div>
        <h2>Thank You, <?php echo $donor_name; ?>!</h2>
        <p style="color: #666;">Your donation has been received successfully.</p>
        
        <div class="amount-display">₹ <?php echo number_format($amount); ?></div>
        
        <div class="details-box">
            <strong>Payment ID:</strong> <?php echo $payment_id; ?><br>
            <strong>Status:</strong> Completed Successfully<br>
            <strong>Purpose:</strong> Medical Fund Contribution
        </div>

        <!-- 🌟 DYNAMIC LINK: Routes them back to the exact fundraiser page using the dynamic id query -->
        <a href="index.php?id=<?php echo $campaign_id; ?>" class="return-btn">Return to Fundraiser Profile</a>
    </div>

</body>
</html>