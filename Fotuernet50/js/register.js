 // File size validation
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            const files = document.querySelectorAll('input[type="file"]');
            let hasError = false;

            for (const file of files) {
                if (file.files[0]) {
                    if (file.files[0].size > 2 * 1024 * 1024) {
                        alert('File size must be less than 2MB: ' + file.files[0].name);
                        hasError = true;
                    }

                    // Check file type
                    const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                    if (!validTypes.includes(file.files[0].type)) {
                        alert('Please upload only JPG or PNG images: ' + file.files[0].name);
                        hasError = true;
                    }
                }
            }

            if (hasError) {
                e.preventDefault();
            }
        });

        // Input formatting
        document.querySelector('input[name="contact"]').addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '').slice(0, 10);
        });

        document.querySelector('input[name="alt_contact"]').addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '').slice(0, 10);
        });

        document.querySelector('input[name="aadhaar"]').addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '').slice(0, 12);
        });

        document.querySelector('input[name="pincode"]').addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '').slice(0, 6);
        });