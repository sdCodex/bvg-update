// Admin functionality
document.addEventListener('DOMContentLoaded', function() {
    initAdmin();
});

function initAdmin() {
    // Initialize navigation dropdowns
    initNavDropdowns();
    
    // Initialize data tables
    initDataTables();
    
    // Initialize form validations
    initFormValidations();
}

function initNavDropdowns() {
    const navToggles = document.querySelectorAll('.nav-toggle');
    
    navToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const dropdown = this.closest('.nav-dropdown');
            dropdown.classList.toggle('active');
        });
    });
}

function initDataTables() {
    // You can integrate DataTables library here
    // For now, basic sorting and filtering
    const tables = document.querySelectorAll('.data-table');
    
    tables.forEach(table => {
        const headers = table.querySelectorAll('th');
        headers.forEach((header, index) => {
            header.style.cursor = 'pointer';
            header.addEventListener('click', () => {
                sortTable(table, index);
            });
        });
    });
}

function sortTable(table, columnIndex) {
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    rows.sort((a, b) => {
        const aText = a.cells[columnIndex].textContent.trim();
        const bText = b.cells[columnIndex].textContent.trim();
        
        // Simple numeric comparison for numbers
        if(!isNaN(aText) && !isNaN(bText)) {
            return aText - bText;
        }
        
        return aText.localeCompare(bText);
    });
    
    // Clear and re-append sorted rows
    tbody.innerHTML = '';
    rows.forEach(row => tbody.appendChild(row));
}

function initFormValidations() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = this.querySelectorAll('[required]');
            let valid = true;
            
            requiredFields.forEach(field => {
                if(!field.value.trim()) {
                    valid = false;
                    field.style.borderColor = '#dc2626';
                } else {
                    field.style.borderColor = '';
                }
            });
            
            if(!valid) {
                e.preventDefault();
                alert('Please fill all required fields.');
            }
        });
    });
}

// File upload preview
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    const file = input.files[0];
    
    if(file) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview" style="max-width: 200px; max-height: 200px;">`;
        }
        
        reader.readAsDataURL(file);
    } else {
        preview.innerHTML = '';
    }
}

// Export functions
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        initAdmin,
        sortTable,
        previewImage
    };
}