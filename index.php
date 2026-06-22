<?php

$host = 'localhost';
$dbName = 'impactguru_db';
$dbUser = 'root';
$dbPass = 'manu@1234';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

   
    $campaignId = isset($_GET['id']) ? intval($_GET['id']) : 1;


    $campaignSql = "SELECT * FROM campaigns WHERE id = :id  LIMIT 1";
    $campaignStmt = $pdo->prepare($campaignSql);
    $campaignStmt->execute([':id' => $campaignId]);
    $campaign = $campaignStmt->fetch(PDO::FETCH_ASSOC);

    
    if (!$campaign) {
       $campaignTitle = "A brain hemorrhage stole my brother's future overnight";
        $targetAmount  = 2000000;
        $bannerImage   = "default_banner.jpg";
        $patientName   = "Hitesh Naresh Bathija";
        $campaignerName = "Karishma Naresh Bathija";
        $location      = "Kalyan, Maharashtra";
        $relation      = "Sibling";
        $hospital      = "Aayush Hospital, Khadakpada, Kalyan";
        $story         = "19-year-old Hitesh remains admitted under close medical observation following a brain hemorrhage...";
        $shares        = 557;
    } else {
        $campaignTitle  = $campaign['title'];
        $targetAmount   = $campaign['target_amount'];
        $bannerImage    = $campaign['banner_image'];
        $patientName    = $campaign['patient_name'];
        $campaignerName = $campaign['campaigner_name'];
        $location       = $campaign['campaigner_location'];
        $relation       = $campaign['relationship'];
        $hospital       = $campaign['hospital_name'];
        $story          = $campaign['campaign_story'];
        $shares         = $campaign['shares_count'];
    }

  
    $paymentSql = "SELECT SUM(amount) AS total_raised FROM payments WHERE campaign_id = :campaign_id";
    $paymentStmt = $pdo->prepare($paymentSql);
    $paymentStmt->execute([':campaign_id' => $campaignId]);
    $paymentResult = $paymentStmt->fetch(PDO::FETCH_ASSOC);

    $totalRaised = $paymentResult['total_raised'] ? $paymentResult['total_raised'] : 0;

    $percentage = ($totalRaised / $targetAmount) * 100;
    $displayPercentage = min(round($percentage), 100);

} catch (PDOException $e) {
    
    $campaignTitle = "Campaign Loading Error";
    $targetAmount  = 1200000;
    $totalRaised   = 0;
    $displayPercentage = 0;
    $bannerImage   = "default_banner.jpg";
}

$patientFirstName = explode(' ',$patientName)[0];
?>


 


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ImpactGuru</title>
    <link rel="icon" type="image/x-icon" href="./images/icon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    
</head>
<body>

    <nav class="navbar">
        <a href="#" class="logo"><img src="./images/impactguruLogo.png" alt="ImpactGuru"></a>
        
        <div class="menu-toggle" id="mobile-menu">
            <i class="fa-solid fa-bars"></i>
        </div>

        <div class="nav-menu" id="nav-menu">
            <div class="search-container">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" class="search" placeholder="Search">
            </div>
            
            <a href="#" class="link">Start a free fundraiser</a>
            <a href="#" class="link">How it works</a>
            <a href="browse.php" class="link">Browse Fundraisers</a>
            <button class="help-btn" id="help-trigger">Help <?php echo htmlspecialchars($patientFirstName); ?></button>
            
            <div class="nav-right-icons">
                <div class="dropdown profile-dropdown">
                    <a href="#" class="dropdown-trigger">
                        <i class="fa-solid fa-circle-user"></i> <i class="fa-solid fa-angle-down arrow-icon"></i>
                    </a>
                    <div class="dropdown-menu">
                        <a href="login_page.html" class="dropdown-item"><i class="fa-solid fa-right-to-bracket"></i> Login</a>
                        <a href="signup.html" class="dropdown-item"><i class="fa-solid fa-user-plus"></i> Sign up</a>
                    </div>
                </div>
                
                <div class="dropdown currency-dropdown">
                    <a href="#" class="dropdown-trigger">
                        <i class="fa-solid fa-indian-rupee-sign"></i> <span id="current-currency">INR</span> <i class="fa-solid fa-angle-down arrow-icon"></i>
                    </a>
                    <div class="dropdown-menu scrollable-menu">
                        <a href="#" class="dropdown-item" data-currency="INR"><i class="fa-solid fa-indian-rupee-sign"></i> INR</a>
                        <a href="#" class="dropdown-item" data-currency="USD"><i class="fa-solid fa-dollar-sign"></i> USD</a>
                        <a href="#" class="dropdown-item" data-currency="GBP"><i class="fa-solid fa-sterling-sign"></i> GBP</a>
                        <a href="#" class="dropdown-item" data-currency="SGD"><i class="fa-solid fa-dollar-sign"></i> SGD</a>
                        <a href="#" class="dropdown-item" data-currency="HKD"><i class="fa-solid fa-dollar-sign"></i> HKD</a>
                        <a href="#" class="dropdown-item" data-currency="AED">AED</a>
                        <a href="#" class="dropdown-item" data-currency="AUD"><i class="fa-solid fa-dollar-sign"></i> AUD</a>
                        <a href="#" class="dropdown-item" data-currency="CAD"><i class="fa-solid fa-dollar-sign"></i> CAD</a>
                        <a href="#" class="dropdown-item" data-currency="EUR"><i class="fa-solid fa-euro-sign"></i> EURO</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    
    <div class="promo-banner">
        <i class="fa-solid fa-gift" style="color: #ffca28; margin-right: 5px;"></i>  
        Get Additional Matching Benefits of up to 15%* on your donations, while funds last. T&C Apply. 
        <a href="#">Know More &gt;</a>
    </div>

    <div class="container">
        <h1 class="campaign-title"><?php echo htmlspecialchars($campaignTitle); ?></h1>
        
        <div class="main-grid">
            <div class="left-column">
                <div class="card">
                    <img src="uploads/<?php echo htmlspecialchars($bannerImage); ?>" alt="Campaign Image" style="width: 100%; border-radius: 8px;">
                    
                    <div class="meta-info-section">
                        <div>
                            <h3>GALLERY (3)</h3>
                            <div class="gallery-thumbs">
                                <img src="./images/img1.png" alt="Thumb">   
                                <img src="./images/img1.png" alt="Thumb">
                                <img src="./images/img1.png" alt="Thumb">
                            </div>
                            
                            <div class="info-profile">
                                <h4>Campaigner Details <i class="fa-solid fa-circle-check" style="color: #0da2e3;"></i></h4>
                                <p style="font-weight: bold; margin-top:5px;"><?php echo htmlspecialchars($campaignerName); ?></p>
                                <p><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($location); ?></p>
                            </div>
                        </div>
                        
                        <div>
                        <div style="text-align: right; margin-bottom: 10px;">
                            <strong style="color: #4267B2;"><i class="fa-solid fa-share-nodes"></i> <?php echo $shares; ?> SHARES</strong>
                        </div>
                            
                           <div class="info-profile" style="border-top: none; padding-top: 0; margin-top: 0;">
                                <h4>Beneficiary Details <i class="fa-solid fa-circle-check" style="color: #0da2e3;"></i></h4>
                                <p style="font-weight: bold; margin-top:5px;"><?php echo htmlspecialchars($patientName); ?></p>
                                <p><small><?php echo htmlspecialchars($relation); ?> of <?php echo htmlspecialchars($campaignerName); ?></small></p>
                                <p style="margin-top: 8px;"><i class="fa-solid fa-hospital"></i> Patient Hospitalized at:</p>
                                <p><strong><?php echo htmlspecialchars($hospital); ?></strong></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <h3 style="margin-bottom: 10px; border-bottom: 2px solid #eee; padding-bottom: 8px;">Latest Update</h3>
                    <p><strong>Dear Donors,</strong></p>
                    <p style="margin-top: 5px; line-height: 1.6; white-space: pre-line;"><?php echo htmlspecialchars($story); ?></p>
                    <button class="help-btn" style="display:block; margin: 15px auto 0 auto; background: #555;">Read More</button>
                </div>
            </div>

            <div class="right-column">
               <div class="donation-box">
                    <div class="progress-container">
                        <div class="custom-progress-bar">
                            <div class="progress-fill" style="width: <?php echo $displayPercentage; ?>%; min-width: 5px; height: 100%; background-color: #ff8502; border-radius: 30px;"></div>
                        </div>
                        <p style="font-size: 14px; margin-top: 8px;"><strong><?php echo $displayPercentage; ?>% funded</strong> in 21 days</p>
                    </div>
                    
                    <div class="amount-raised-status" style="margin-top: 15px;">
                        <h4 style="margin: 0; font-size: 18px;">
                            <span>₹ <?php echo number_format($totalRaised); ?> Raised</span> 
                            <br>
                            <span style="font-size: 14px; color: #666; font-weight: normal;">of ₹ <?php echo number_format($targetAmount); ?></span> 
                        </h4>
                    </div>
                    
                    <p style="font-size: 13px; color: #555; margin-top: 10px;">
                        <i class="fa-solid fa-circle-info" style="color:#0da2e3;"></i> Funds will be transferred directly for patient's treatment.
                    </p>
                    
                    <a href="donate.html?campaign_id=<?php echo $campaignId; ?>" class="btn-donate" id="main-donate-btn">
                        DONATE NOW
                        <span>(INDIAN TAX BENEFITS AVAILABLE)</span>
                    </a>
    
                    
                    <p style="text-align: center; font-size: 13px; margin-bottom: 8px; font-weight: bold; color: #d32f2f;">
                        Every social media share can bring ₹5,000
                    </p>
                    
                    <button class="btn-share">
                        <i class="fa-brands fa-facebook"></i> SHARE ON FACEBOOK
                    </button>
                    
                    <hr style="margin: 20px 0; border: 0; border-top: 1px solid #eee;">
                    
                    <div class="qr-box">
                        <h4>Donate via Paytm / GPay / PhonePe</h4>
                        <img src="./images/img3.png" alt="UPI QR Code">
                        <p>Donations made through this fundraiser and UPI ID will be securely deposited into Impact Guru’s bank account for the patient’s treatment. This UPI ID is not associated with personal accounts.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bottom-banner">
        <a href="create-campaign.html" class="btn-footer">Start A Fundraiser</a>
    </div>

    
<div class="modal-overlay" id="help-modal">
    <div class="modal-box">
        <span class="modal-close" id="modal-close-btn">&times;</span>
        
        <div class="modal-banner-section">
            
            <img src="uploads/<?php echo htmlspecialchars($bannerImage); ?>" alt="Patient Popup Image" class="modal-img">
            <div class="modal-image-overlay">
                <h2><?php echo htmlspecialchars($campaignTitle); ?></h2>
                <a href="donate.html?campaign_id=<?php echo $campaignId; ?>" class="modal-save-btn" style="text-decoration: none; text-align: center; display: inline-block; line-height: 40px;">SAVE HIM</a>
            </div>
        </div>
        
        <div class="modal-content">
            <p class="modal-heading-text">Your donations will make a world of difference to</p>
            <h3 class="modal-patient-name"><?php echo htmlspecialchars($patientName); ?></h3>
            
            
            <a href="donate.html?campaign_id=<?php echo $campaignId; ?>" class="modal-donate-btn btn-donate">Donate ₹ 5,000</a>
            
            <p class="modal-or">OR</p>
            <a href="donate.html?campaign_id=<?php echo $campaignId; ?>" class="modal-alt-amount">Choose a different amount</a>
        </div>
    </div>
</div>

    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>

    <script>
        // --- MOBILE NAV MENU ---
        const mobileMenuBtn = document.getElementById('mobile-menu');
        const navMenu = document.getElementById('nav-menu');
        const menuIcon = mobileMenuBtn.querySelector('i');

        mobileMenuBtn.addEventListener('click', () => {
            navMenu.classList.toggle('active');
            if (navMenu.classList.contains('active')) {
                menuIcon.classList.replace('fa-bars', 'fa-xmark');
            } else {
                menuIcon.classList.replace('fa-xmark', 'fa-bars');
            }
        });

        // --- POPUP MODAL FUNCTIONALITY ---
        const helpTriggerBtn = document.getElementById('help-trigger');
        const helpModal = document.getElementById('help-modal');
        const modalCloseBtn = document.getElementById('modal-close-btn');

        helpTriggerBtn.addEventListener('click', (e) => {
            e.preventDefault();
            helpModal.classList.add('show');
        });

        modalCloseBtn.addEventListener('click', () => {
            helpModal.classList.remove('show');
        });

        window.addEventListener('click', (event) => {
            if (event.target === helpModal) {
                helpModal.classList.remove('show');
            }
        });

        // --- NAVBAR DROPDOWN CURRENCY ---
        const currencyItems = document.querySelectorAll('.currency-dropdown .dropdown-item');
        const currentCurrencyText = document.getElementById('current-currency');
        const currencyTriggerIcon = document.querySelector('.currency-dropdown .dropdown-trigger i');

        currencyItems.forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                const selectedCurrency = item.getAttribute('data-currency');
                currentCurrencyText.textContent = selectedCurrency;
                
                if(selectedCurrency === 'INR') {
                    currencyTriggerIcon.className = "fa-solid fa-indian-rupee-sign";
                } else if(selectedCurrency === 'EUR') {
                    currencyTriggerIcon.className = "fa-solid fa-euro-sign";
                } else if(selectedCurrency === 'GBP') {
                    currencyTriggerIcon.className = "fa-solid fa-sterling-sign";
                } else {
                    currencyTriggerIcon.className = "fa-solid fa-dollar-sign";
                }
            });
        });

       
        
       
        
    </script>
</body>
</html>