

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('campaign-creation-form');
    const fileInput = document.getElementById('campaign-image');
    const dropzone = document.getElementById('image-dropzone');
    const previewContainer = document.getElementById('preview-container');
    const previewImg = document.getElementById('upload-preview');
    const removeImgBtn = document.getElementById('remove-preview-btn');

    
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
        e.stopPropagation(); 
        fileInput.value = '';
        previewContainer.style.display = 'none';
        previewImg.src = '';
    });


    form.addEventListener('submit', (e) => {
        e.preventDefault();
        let isFormValid = true;

      
        const patientName = document.getElementById('patient-name');
        const medicalCause = document.getElementById('medical-cause');
        const campaignTitle = document.getElementById('campaign-title');
        const targetAmount = document.getElementById('target-amount');

        
        document.querySelectorAll('.validation-error-text').forEach(errorSpan => {
    errorSpan.style.display = 'none';
});
       
        if (patientName.value.trim() === "") {
            showError('patient-name');
            isFormValid = false;
        }

        
        if (medicalCause.value === "") {
            showError('medical-cause');
            isFormValid = false;
        }

       
        if (campaignTitle.value.trim().length < 15) {
            showError('campaign-title');
            isFormValid = false;
        }

        
        if (targetAmount.value === "" || parseInt(targetAmount.value) < 1000) {
            showError('target-amount');
            isFormValid = false;
        }

       
        if (!fileInput.files[0]) {
            showError('campaign-image');
            isFormValid = false;
        }

       
        if (isFormValid) {
           form.submit();
        }
    });

    function showError(fieldId) {
        document.getElementById(`${fieldId}-error`).style.display = 'block';
    }
});