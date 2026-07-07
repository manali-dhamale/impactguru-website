<?php
session_start();
date_default_timezone_set('Asia/Kolkata');

$host = 'localhost';
$dbName = 'impactguru_db';
$dbUser = 'root';
$dbPass = 'manu@1234';


try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $campaignUuid = isset($_GET['id']) ? trim($_GET['id']) : '';
    
    $campaignSql = "SELECT * FROM campaigns WHERE uuid = :uuid AND status = 1 LIMIT 1";
    $campaignStmt = $pdo->prepare($campaignSql);
    $campaignStmt->execute([':uuid' => $campaignUuid]);
    $campaign = $campaignStmt->fetch(PDO::FETCH_ASSOC);

    if (!$campaign) {
        $fallbackSql = "SELECT * FROM campaigns WHERE status = 1 ORDER BY id DESC LIMIT 1";
        $fallbackStmt = $pdo->query($fallbackSql);
        $campaign = $fallbackStmt->fetch(PDO::FETCH_ASSOC);
    }

    if (!$campaign) {
        $campaignTitle  = "A brain hemorrhage stole my brother's future overnight";
        $targetAmount   = 2000000;
        $bannerImage    = "default_banner.jpg";
        $patientName    = "Hitesh Naresh Bathija";
        $campaignerName = "Karishma Naresh Bathija";
        $location       = "Kalyan, Maharashtra";
        $relation       = "Sibling";
        $hospital       = "Aayush Hospital, Khadakpada, Kalyan";
        $story          = "19-year-old Hitesh remains admitted under close medical observation following a brain hemorrhage...";
        $shares         = 557;
        $updatedAt      = date('Y-m-d H:i:s'); 
        $campaignId     = 1;                  
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
        $updatedAt      = $campaign['updated_at'];
        $campaignId     = $campaign['id'];

        $bannerImage    = !empty($campaign['banner_image']) ? $campaign['banner_image'] : 'default_banner.jpg';
        $thumbnailImage = $bannerImage;

        $relCode = intval($campaign['relationship']);
        if ($relCode === 1) {
            $relation = "Father";
        } elseif ($relCode === 2) {
            $relation = "Mother";
        } elseif ($relCode === 3) {
            $relation = "Sibling";
        } elseif ($relCode === 4) {
            $relation = "Spouse";
        } elseif ($relCode === 5) {
            $relation = "Child";
        } else {
            $relation = "Family/Friend";
        }
    }

    $shareCountSql = "SELECT COUNT(*) FROM shares WHERE campaign_id = :campaign_id";
    $shareCountStmt = $pdo->prepare($shareCountSql);
    $shareCountStmt->execute([':campaign_id' => $campaignId]);
    $shares = $shareCountStmt->fetchColumn();

    $paymentSql = "SELECT SUM(amount) AS total_raised FROM payments WHERE campaign_id = :campaign_id AND status = 1";
    $paymentStmt = $pdo->prepare($paymentSql);
    $paymentStmt->execute([':campaign_id' => $campaignId]);
    $paymentResult = $paymentStmt->fetch(PDO::FETCH_ASSOC);

    $totalRaised = $paymentResult['total_raised'] ? $paymentResult['total_raised'] : 0;
    $percentage = ($targetAmount > 0) ? ($totalRaised / $targetAmount) * 100 : 0;
    $displayPercentage = min(round($percentage), 100);

} catch (PDOException $e) {
    $campaignTitle = "Campaign Loading Error";
    $targetAmount  = 1200000;
    $totalRaised   = 0;
    $displayPercentage = 0;
    $bannerImage   = "default_banner.jpg";
    $relation      = "N/A";
}

$patientFirstName = !empty($patientName) ? explode(' ', $patientName)[0] : 'Patient';
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
            
            <a href="create-campaign.html" class="link">Start a free fundraiser</a>
            <a href="#" class="link">How it works</a>
            <a href="browse.php" class="link">Browse Fundraisers</a>
            <button class="help-btn" id="help-trigger">Help <?php echo htmlspecialchars($patientFirstName); ?></button>
            
            <div class="nav-right-icons">
                    <div class="dropdown profile-dropdown">
                    <a href="#" class="dropdown-trigger">
                        <i class="fa-solid fa-circle-user"></i>
                        <?php echo isset($_SESSION['user_name']) ? '<span style="margin-left:5px; font-weight:bold;">' . htmlspecialchars($_SESSION['user_name']) . '</span>' : ''; ?>
                         <i class="fa-solid fa-angle-down arrow-icon"></i>
                    </a>
                    <div class="dropdown-menu">
                        <?php if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])): ?>
                            <a href="logout.php" class="dropdown-item"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
                        <?php else: ?>
                            <a href="login_page.html" class="dropdown-item"><i class="fa-solid fa-right-to-bracket"></i> Login</a>
                            <a href="signup.html" class="dropdown-item"><i class="fa-solid fa-user-plus"></i> Sign up</a>
                        <?php endif; ?>
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
                        
                        
                       <div style="text-align: right; margin-bottom: 10px;">
                            <strong id="share-counter-display" style="color: #4267B2;">
                                <i class="fa-solid fa-share-nodes"></i> <?php echo $shares; ?> SHARES
                            </strong>
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

                <div class="card">
                    <h3 style="margin-bottom: 10px; border-bottom: 2px solid #eee; padding-bottom: 8px;">Latest Update</h3>
                    
    
                    <p style="font-size: 12px; color: #777; margin-bottom: 15px;">
                       <i class="fa-solid fa-clock"></i> Last Updated: <?php echo date('d M Y, h:i A', strtotime($updatedAt)); ?>
                    </p>
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
                    
                    <button type="button" onclick="openShareModal()" style="cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; width: 100%; padding: 12px; background: #0da2e3; color: white; border: none; border-radius: 25px; font-weight: bold; font-size: 16px; margin-bottom: 15px;">
    <i class="fa-solid fa-share-nodes"></i> SHARE THIS CAMPAIGN
</button>

<div id="customShareModal" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); align-items: center; justify-content: center;">
    
    <div style="background: white; padding: 25px; border-radius: 15px; width: 90%; max-width: 400px; text-align: center; position: relative; font-family: sans-serif; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
        
        <span onclick="closeShareModal()" style="position: absolute; right: 15px; top: 10px; font-size: 24px; cursor: pointer; color: #aaa;">&times;</span>
        
        <h3 style="margin-top: 5px; margin-bottom: 20px; color: #333;">Share this Fundraiser</h3>
        
        <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 10px; margin-bottom: 10px;">
    
            <div onclick="recordShareAction(<?php echo $campaignId; ?>, 'whatsapp')" style="cursor: pointer;">
                <div style="background: #25D366; color: white; width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 5px; font-size: 20px;">
                    <i class="fa-brands fa-whatsapp"></i>
                </div>
                <span style="font-size: 11px; color: #555;">WhatsApp</span>
            </div>
            
            <div onclick="recordShareAction(<?php echo $campaignId; ?>, 'facebook')" style="cursor: pointer;">
                <div style="background: #4267B2; color: white; width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 5px; font-size: 20px;">
                    <i class="fa-brands fa-facebook-f"></i>
                </div>
                <span style="font-size: 11px; color: #555;">Facebook</span>
            </div>
            
            <div onclick="recordShareAction(<?php echo $campaignId; ?>, 'linkedin')" style="cursor: pointer;">
                <div style="background: #0077B5; color: white; width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 5px; font-size: 20px;">
                    <i class="fa-brands fa-linkedin-in"></i>
                </div>
                <span style="font-size: 11px; color: #555;">LinkedIn</span>
            </div>
            
            <div onclick="recordShareAction(<?php echo $campaignId; ?>, 'twitter')" style="cursor: pointer;">
                <div style="background: #000000; color: white; width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 5px; font-size: 18px;">
                    <i class="fa-brands fa-x-twitter"></i>
                </div>
                <span style="font-size: 11px; color: #555;">Twitter</span>
            </div>

            <div onclick="recordShareAction(<?php echo $campaignId; ?>, 'link_copy')" style="cursor: pointer;">
                <div id="copyIconContainer" style="background: #7f8c8d; color: white; width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 5px; font-size: 18px; transition: background 0.3s;">
                    <i class="fa-solid fa-link"></i>
                </div>
                <span id="copyTextLabel" style="font-size: 11px; color: #555;">Copy Link</span>
            </div>

        </div>
    </div>
</div>
                    
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

    
    const helpTriggerBtn = document.getElementById('help-trigger');
    const helpModal = document.getElementById('help-modal');
    const modalCloseBtn = document.getElementById('modal-close-btn');

    if(helpTriggerBtn) {
        helpTriggerBtn.addEventListener('click', (e) => {
            e.preventDefault();
            helpModal.classList.add('show');
        });
    }

    if(modalCloseBtn) {
        modalCloseBtn.addEventListener('click', () => {
            helpModal.classList.remove('show');
        });
    }

   
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

  
    function openShareModal() {
        document.getElementById('customShareModal').style.display = 'flex';
    }

    function closeShareModal() {
        document.getElementById('customShareModal').style.display = 'none';
    }

  
    window.addEventListener('click', (event) => {
        if (event.target === helpModal) {
            helpModal.classList.remove('show');
        }
        
        let shareModal = document.getElementById('customShareModal');
        if (event.target === shareModal) {
            shareModal.style.display = 'none';
        }
    });

    
    function recordShareAction(campaignId, platformName) {
        let formData = new FormData();
        formData.append('campaign_id', campaignId);
        formData.append('platform', platformName);
        console.log(formData);

        fetch('log_share.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
      
            let currentUrl = window.location.href;
            let encodedUrl = encodeURIComponent(currentUrl);

            if (data.success && document.getElementById('share-counter-display')) {
                document.getElementById('share-counter-display').innerHTML = 
                    '<i class="fa-solid fa-share-nodes"></i> ' + data.total_shares + ' SHARES';
            }

            
            if (platformName === 'link_copy') {
                navigator.clipboard.writeText(currentUrl).then(() => {
                    let iconBg = document.getElementById('copyIconContainer');
                    let textLabel = document.getElementById('copyTextLabel');
                    console.log(textLabel);
                    
                    if (iconBg && textLabel) {
                        iconBg.style.background = '#27ae60';
                        textLabel.style.color = '#27ae60';
                        textLabel.innerText = 'Copied!';
                    }
                    
                    setTimeout(() => {
                        if (iconBg && textLabel) {
                            iconBg.style.background = '#7f8c8d';
                            textLabel.style.color = '#555';
                            textLabel.innerText = 'Copy Link';
                        }
                        closeShareModal();
                    }, 1000);
                }).catch(err => console.error('Clipboard copy failed:', err));
            } 
            else {
                closeShareModal();
                if (platformName === 'facebook') {
                    window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodedUrl, '_blank', 'width=600,height=400');
                } else if (platformName === 'whatsapp') {
                    window.open('https://api.whatsapp.com/send?text=Please support this fundraiser: ' + encodedUrl, '_blank');
                } else if (platformName === 'linkedin') {
                    window.open('https://www.linkedin.com/sharing/share-offsite/?url=' + encodedUrl, '_blank', 'width=600,height=500');
                } else if (platformName === 'twitter') {
                    window.open('https://twitter.com/intent/tweet?url=' + encodedUrl, '_blank', 'width=550,height=420');
                }
            }
        })
        .catch(error => {
            console.error('Share Tracking System Error:', error);
         
            let currentUrl = window.location.href;
            let encodedUrl = encodeURIComponent(currentUrl);
            closeShareModal();
            if (platformName === 'whatsapp') {
                window.open('https://api.whatsapp.com/send?text=Please support this fundraiser: ' + encodedUrl, '_blank');
            }
        });
    }
</script>