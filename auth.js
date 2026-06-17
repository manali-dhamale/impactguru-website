// --- SIGN UP / LOGIN FORM UTILITIES ---

document.addEventListener('DOMContentLoaded', () => {
    
    // Select targets
    const selectTrigger = document.querySelector('.country-code-select');
    const codeDropdown = document.getElementById('code-dropdown');
    const selectedCodeText = document.getElementById('selected-code');
    const phoneInput = document.getElementById('user-phone');
    const phoneError = document.getElementById('phone-error');
    const authForm = document.getElementById('otp-auth-form');
    
    const verifyScreen = document.getElementById('otp-verify-screen');
    const otpDigits = document.querySelectorAll('.otp-digit');
    const confirmLoginBtn = document.getElementById('confirm-login-btn');

    // 1. Toggle custom code options list dropdown menu layer
    selectTrigger.addEventListener('click', (e) => {
        e.stopPropagation();
        codeDropdown.classList.toggle('active');
        document.querySelector('.select-arrow').style.transform = 
            codeDropdown.classList.contains('active') ? 'rotate(180deg)' : 'rotate(0deg)';
    });

    // 2. Select matching data country tag properties value
    document.querySelectorAll('.code-opt').forEach(option => {
        option.addEventListener('click', () => {
            selectedCodeText.textContent = option.getAttribute('data-code');
            codeDropdown.classList.remove('active');
            document.querySelector('.select-arrow').style.transform = 'rotate(0deg)';
        });
    });

    // Close options list instantly if anywhere else on screen window layer clicked
    window.addEventListener('click', () => {
        codeDropdown.classList.remove('active');
    });

    // 3. Form validation checking logic sequence parameters
    authForm.addEventListener('submit', (event) => {
        event.preventDefault();
        
        const rawPhoneValue = phoneInput.value.trim();
        
        // Simple numeric sequence length check validator parameter
        if (rawPhoneValue.length !== 10 || isNaN(rawPhoneValue)) {
            phoneInput.style.borderColor = '#ef4444';
            phoneError.style.display = 'block';
        } else {
            phoneInput.style.borderColor = '#cbd5e1';
            phoneError.style.display = 'none';
            
            // Launch simulation view panel screen layout window
            verifyScreen.classList.add('active');
            // Shift automatic structural input focus to primary initial element digit
            otpDigits[0].focus();
        }
    });

    // 4. Auto-advance workflow sequence mechanics for verification inputs
    otpDigits.forEach((input, index) => {
        input.addEventListener('input', (e) => {
            if (input.value.length === 1 && index < otpDigits.length - 1) {
                otpDigits[index + 1].focus();
            }
        });

        // Step backwards cleanly if typing backspace key action triggered
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && input.value.length === 0 && index > 0) {
                otpDigits[index - 1].focus();
            }
        });
    });

    // 5. Trigger fake final authentication pass simulator confirmation
    confirmLoginBtn.addEventListener('click', () => {
        let codeBuffer = "";
        otpDigits.forEach(field => codeBuffer += field.value);
        
        if (codeBuffer.length === 4) {
            alert('Welcome! Authentication Simulation Complete 🎉');
            window.location.href = 'index.html'; // Points back to your home layout index file page
        } else {
            alert('Please populate all verification field digits.');
        }
    });
});