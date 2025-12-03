function downloadReceipt() {
    // Create a clone of the receipt element
    const originalElement = document.getElementById('receipt');
    const element = originalElement.cloneNode(true);

    // Remove any no-print or no-pdf classes from the clone
    element.querySelectorAll('.no-print, .no-pdf').forEach(el => {
        el.classList.remove('no-print', 'no-pdf');
    });

    // Create a temporary container for PDF generation
    const tempContainer = document.createElement('div');
    tempContainer.style.position = 'absolute';
    tempContainer.style.left = '-9999px';
    tempContainer.appendChild(element);
    document.body.appendChild(tempContainer);

    // Show loading state
    const originalText = event.target.innerHTML;
    event.target.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Generating PDF...';
    event.target.disabled = true;

    const opt = {
        margin: [0.3, 0.3, 0.3, 0.3],
        filename: 'BVGF50_Registration_<?php echo $unique_id; ?>.pdf',
        image: {
            type: 'jpeg',
            quality: 0.98
        },
        html2canvas: {
            scale: 2,
            useCORS: true,
            logging: false,
            backgroundColor: '#ffffff'
        },
        jsPDF: {
            unit: 'in',
            format: 'a4',
            orientation: 'portrait'
        }
    };

    html2pdf().set(opt).from(element).save().then(() => {
        // Clean up and restore button state
        document.body.removeChild(tempContainer);
        event.target.innerHTML = originalText;
        event.target.disabled = false;
    }).catch((error) => {
        console.error('PDF generation failed:', error);
        document.body.removeChild(tempContainer);
        event.target.innerHTML = originalText;
        event.target.disabled = false;
        alert('Failed to generate PDF. Please try again.');
    });
}

// Auto-print option
const urlParams = new URLSearchParams(window.location.search);
if (urlParams.get('print') === 'true') {
    window.print();
}

// Ensure images are loaded before PDF generation
document.addEventListener('DOMContentLoaded', function () {
    const images = document.querySelectorAll('img');
    images.forEach(img => {
        img.loading = 'eager';
    });
});