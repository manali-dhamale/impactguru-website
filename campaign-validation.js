// --- CAMPAIGN FORM VALIDATION & PROCESSING ---

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('campaign-creation-form');
    const fileInput = document.getElementById('campaign-image');
    const dropzone = document.getElementById('image-dropzone');
    const previewContainer = document.getElementById('preview-container');
    const previewImg = document.getElementById('upload-preview');
    const removeImgBtn = document.getElementById('remove-preview-btn');

    // 1. Image Upload Preview Interactions System Mechanics
    dropzone.addEventListener('click', (e) => {
        if (e.target !== removeImgBtn) {
            fileInput.click();
        }
    });

    fileInput.addEventListener('change', () => {
        handleFileSelection(fileInput.files[0]);
    });

    function handleFileSelection(file) {
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewContainer.style.display = 'block';
                document.getElementById('campaign-image-error').style.display = 'none';
            }
            reader.readAsDataURL(file);
        }
    }

    removeImgBtn.addEventListener('click', (e) => {
        e.stopPropagation(); // Avoid triggering dropzone input element launch click loop
        fileInput.value = '';
        previewContainer.style.display = 'none';
        previewImg.src = '';
    });

    // 2. Comprehensive client-side fields checks inputs submission intercept rules
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        let isFormValid = true;

        // Grab values
        const patientName = document.getElementById('patient-name');
        const medicalCause = document.getElementById('medical-cause');
        const campaignTitle = document.getElementById('campaign-title');
        const targetAmount = document.getElementById('target-amount');

        // Reset past errors state visibility
        document.querySelectorAll('.validation-error-text').style.display = 'none';

        // Validate Patient Name
        if (patientName.value.trim() === "") {
            showError('patient-name');
            isFormValid = false;
        }

        // Validate Dropdown Condition Item Selection 
        if (medicalCause.value === "") {
            showError('medical-cause');
            isFormValid = false;
        }

        // Validate Headline text limits
        if (campaignTitle.value.trim().length < 15) {
            showError('campaign-title');
            isFormValid = false;
        }

        // Validate Numerical Limit boundaries
        if (targetAmount.value === "" || parseInt(targetAmount.value) < 1000) {
            showError('target-amount');
            isFormValid = false;
        }

        // Validate Image Presence Check Field Requirements
        if (!fileInput.files[0]) {
            showError('campaign-image');
            isFormValid = false;
        }

        // Forward processing validation status evaluation block
        if (isFormValid) {
            alert('Success! Form validation complete. Ready for Step 7 (REST API integration).');
            form.reset();
            previewContainer.style.display = 'none';
            window.location.href = 'index.html'; // Points back to home dashboard index web layout view page
        }
    });

    function showError(fieldId) {
        document.getElementById(`${fieldId}-error`).style.display = 'block';
    }
});