document.addEventListener('DOMContentLoaded', function() {
    const sendOtpBtn = document.getElementById('sendOtpBtn');
    const verifyOtpBtn = document.getElementById('verifyOtpBtn');
    const otpInputSection = document.getElementById('otpInputSection');
    const otpStatus = document.getElementById('otpStatus');
    const submitBtn = document.getElementById('submitBtn');
    const mobileNumber = document.getElementById('mobileNumber');
    const contactInput = document.querySelector('input[name="contact"]');

    console.log('OTP System Loaded');

    // Auto-fill mobile number for OTP when contact input changes
    contactInput.addEventListener('input', function() {
        console.log('Contact input changed:', this.value);
        mobileNumber.value = this.value;
        resetOtpSection();
    });

    sendOtpBtn.addEventListener('click', function() {
        const mobile = mobileNumber.value.trim();
        console.log('Send OTP clicked for:', mobile);
        
        if (!mobile || !/^[0-9]{10}$/.test(mobile)) {
            showOtpStatus('Please enter a valid 10-digit mobile number', 'error');
            return;
        }

        // Check if mobile matches contact number
        const contactValue = contactInput.value.trim();
        if (mobile !== contactValue) {
            showOtpStatus('Mobile number must match contact number', 'error');
            return;
        }

        // Disable button and show loading
        sendOtpBtn.disabled = true;
        sendOtpBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

        console.log('Sending OTP request for:', mobile);

        // Send OTP via AJAX
        fetch('verify_otp.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=send_otp&mobile=${mobile}`
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('OTP Response:', data);
            if (data.success) {
                showOtpStatus('OTP sent successfully! Check your mobile.', 'success');
                otpInputSection.classList.remove('hidden');
                verifyOtpBtn.disabled = false;
                
                // Debug - show OTP in console
                if (data.debug_otp) {
                    console.log('DEBUG OTP:', data.debug_otp);
                    showOtpStatus(`OTP sent successfully! Debug OTP: ${data.debug_otp}`, 'success');
                }
            } else {
                showOtpStatus(data.message || 'Failed to send OTP', 'error');
            }
        })
        .catch(error => {
            console.error('Fetch Error:', error);
            showOtpStatus('Error sending OTP: ' + error.message, 'error');
        })
        .finally(() => {
            sendOtpBtn.disabled = false;
            sendOtpBtn.innerHTML = 'Send OTP';
        });
    });

    verifyOtpBtn.addEventListener('click', function() {
        const otp = document.getElementById('otpInput').value.trim();
        const mobile = mobileNumber.value.trim();

        console.log('Verify OTP clicked - Mobile:', mobile, 'OTP:', otp);

        if (!otp || !/^[0-9]{6}$/.test(otp)) {
            showOtpStatus('Please enter a valid 6-digit OTP', 'error');
            return;
        }

        verifyOtpBtn.disabled = true;
        verifyOtpBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';

        // Verify OTP via AJAX
        fetch('verify_otp.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=verify_otp&mobile=${mobile}&otp=${otp}`
        })
        .then(response => {
            console.log('Verification response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Verification Response:', data);
            if (data.success) {
                showOtpStatus('OTP verified successfully!', 'success');
                submitBtn.disabled = false;
                verifyOtpBtn.innerHTML = 'Verified ✓';
                verifyOtpBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
                verifyOtpBtn.classList.add('bg-green-700');
                verifyOtpBtn.disabled = true;
            } else {
                showOtpStatus(data.message || 'Invalid OTP', 'error');
                verifyOtpBtn.disabled = false;
                verifyOtpBtn.innerHTML = 'Verify OTP';
            }
        })
        .catch(error => {
            console.error('Verification Error:', error);
            showOtpStatus('Error verifying OTP: ' + error.message, 'error');
            verifyOtpBtn.disabled = false;
            verifyOtpBtn.innerHTML = 'Verify OTP';
        });
    });

    function showOtpStatus(message, type) {
        console.log('OTP Status:', type, message);
        otpStatus.textContent = message;
        otpStatus.className = 'text-sm mt-2 ' + (type === 'success' ? 'text-green-600' : 'text-red-600');
    }

    function resetOtpSection() {
        otpInputSection.classList.add('hidden');
        document.getElementById('otpInput').value = '';
        submitBtn.disabled = true;
        verifyOtpBtn.disabled = false;
        verifyOtpBtn.innerHTML = 'Verify OTP';
        verifyOtpBtn.classList.remove('bg-green-700');
        verifyOtpBtn.classList.add('bg-green-600', 'hover:bg-green-700');
        otpStatus.textContent = '';
    }

    // Prevent form submission if OTP not verified
    document.getElementById('registrationForm').addEventListener('submit', function(e) {
        if (submitBtn.disabled) {
            e.preventDefault();
            showOtpStatus('Please verify your mobile number first', 'error');
        }
    });
});