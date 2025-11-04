// Blog functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize blog features
    initBlog();
});

function initBlog() {
    // Add loading states to download buttons
    document.querySelectorAll('.download-btn, .download-link').forEach(btn => {
        btn.addEventListener('click', function(e) {
            // Track download in analytics (you can integrate with Google Analytics)
            trackDownload(this.getAttribute('href'));
            
            // Show loading state
            const originalText = this.textContent;
            this.textContent = 'Downloading...';
            this.style.opacity = '0.7';
            
            setTimeout(() => {
                this.textContent = originalText;
                this.style.opacity = '1';
            }, 2000);
        });
    });
    
    // Add search functionality to tables
    const tables = document.querySelectorAll('table');
    tables.forEach(table => {
        addTableSearch(table);
    });
    
    // Initialize filter forms
    initFilters();
}

function trackDownload(fileUrl) {
    // Here you can integrate with your analytics service
    console.log('Download tracked:', fileUrl);
    
    // Example: Send to Google Analytics
    if (typeof gtag !== 'undefined') {
        gtag('event', 'download', {
            'event_category': 'File Download',
            'event_label': fileUrl
        });
    }
}

function addTableSearch(table) {
    const container = table.closest('.papers-table');
    if (!container) return;
    
    // Create search input
    const searchHtml = `
        <div class="table-search">
            <input type="text" placeholder="Search in table..." class="search-input">
        </div>
    `;
    
    container.insertAdjacentHTML('afterbegin', searchHtml);
    
    const searchInput = container.querySelector('.search-input');
    const rows = table.querySelectorAll('tbody tr');
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
}

function initFilters() {
    const filterForms = document.querySelectorAll('.filters-form');
    
    filterForms.forEach(form => {
        form.addEventListener('change', function() {
            // Auto-submit form when filters change (optional)
            // this.submit();
        });
    });
}

// View count tracking for blog posts
function incrementViewCount(postId) {
    fetch('/api/increment-views.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ postId: postId })
    })
    .then(response => response.json())
    .then(data => {
        console.log('View count updated');
    })
    .catch(error => {
        console.error('Error updating view count:', error);
    });
}

// Utility function for formatting file sizes
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Export functions for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        initBlog,
        trackDownload,
        formatFileSize
    };
}