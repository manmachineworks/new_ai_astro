<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Phone Login | {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-auth-compat.js"></script>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="text-center mb-4">
                    <h1 class="h3 fw-bold">Sign in with your phone</h1>
                    <p class="text-muted mb-0">We’ll send a one-time code via SMS to verify it’s you.</p>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <strong>Couldn’t verify:</strong> {{ $errors->first() }}
                    </div>
                @endif

                <div class="card shadow-sm">
                    <div class="card-body">
                        <form id="phone-form" novalidate>
                            <div class="mb-3">
                                <label for="name" class="form-label">Name (optional)</label>
                                <input type="text" class="form-control" id="name" name="name" maxlength="100" placeholder="Only for first-time sign-up">
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone number (E.164)</label>
                                <input type="tel" class="form-control" id="phone" name="phone" placeholder="+15555550123" required>
                                <div class="form-text">Use full country code. Example: +15555550123</div>
                            </div>

                            <div class="d-grid mb-3">
                                <button id="send-otp" class="btn btn-primary" type="button">Send OTP</button>
                            </div>

                            <div class="mb-3">
                                <label for="otp" class="form-label">OTP code</label>
                                <input type="text" inputmode="numeric" pattern="[0-9]*" class="form-control" id="otp" name="otp" placeholder="6-digit code" minlength="4" maxlength="8" autocomplete="one-time-code">
                            </div>

                            <div class="d-grid">
                                <button id="verify-otp" class="btn btn-success" type="button">Verify &amp; Continue</button>
                            </div>
                        </form>

                        <div id="alert-area" class="mt-3" style="display:none;">
                            <div class="alert mb-0" role="alert"></div>
                        </div>
                    </div>
                </div>

                <div id="recaptcha-container"></div>
            </div>
        </div>
    </div>

    <script type="module">
        const firebaseConfig = @json(config('firebase.web'));
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        if (!firebaseConfig.api_key) {
            console.warn('Firebase web config is missing. Update your .env values.');
        }

        firebase.initializeApp({
            apiKey: firebaseConfig.api_key,
            authDomain: firebaseConfig.auth_domain,
            projectId: firebaseConfig.project_id,
            appId: firebaseConfig.app_id,
            messagingSenderId: firebaseConfig.messaging_sender_id,
            measurementId: firebaseConfig.measurement_id,
        });

        const auth = firebase.auth();
        let confirmationResult = null;
        let recaptchaVerifier = null;

        const alertArea = document.getElementById('alert-area');
        const sendBtn = document.getElementById('send-otp');
        const verifyBtn = document.getElementById('verify-otp');
        const phoneInput = document.getElementById('phone');
        const nameInput = document.getElementById('name');
        const otpInput = document.getElementById('otp');

        const setAlert = (message, type = 'info') => {
            const alertBox = alertArea.querySelector('.alert');
            alertBox.textContent = message;
            alertBox.className = `alert alert-${type} mb-0`;
            alertArea.style.display = 'block';
        };

        const clearAlert = () => {
            alertArea.style.display = 'none';
            alertArea.querySelector('.alert').textContent = '';
        };

        const initRecaptcha = () => {
            if (recaptchaVerifier) return recaptchaVerifier;
            recaptchaVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
                size: 'invisible',
            });
            recaptchaVerifier.render();
            return recaptchaVerifier;
        };

        const disableButtons = (disabled) => {
            sendBtn.disabled = disabled;
            verifyBtn.disabled = disabled;
        };

        const sendOtp = async () => {
            clearAlert();
            const phone = phoneInput.value.trim();
            if (!phone.startsWith('+')) {
                setAlert('Please include the country code, e.g. +15555550123.', 'warning');
                return;
            }

            disableButtons(true);
            try {
                await initRecaptcha();
                confirmationResult = await auth.signInWithPhoneNumber(phone, recaptchaVerifier);
                setAlert('OTP sent. Please enter the code you received.', 'success');
                otpInput.focus();
            } catch (error) {
                console.error(error);
                const code = error.code || '';
                let message = 'Could not send OTP. Please try again.';
                if (code === 'auth/invalid-phone-number') message = 'That phone number is invalid.';
                if (code === 'auth/too-many-requests') message = 'Too many attempts. Please wait and try again later.';
                if (code === 'auth/network-request-failed') message = 'Network issue. Check your connection.';
                if (code === 'auth/missing-phone-number') message = 'Phone number is required.';
                setAlert(message, 'danger');
            } finally {
                disableButtons(false);
            }
        };

        const verifyOtp = async () => {
            clearAlert();
            if (!confirmationResult) {
                setAlert('Please request an OTP first.', 'warning');
                return;
            }
            const code = otpInput.value.trim();
            if (!code) {
                setAlert('Enter the OTP you received.', 'warning');
                return;
            }

            disableButtons(true);
            try {
                const result = await confirmationResult.confirm(code);
                const idToken = await result.user.getIdToken();
                const payload = {
                    firebase_id_token: idToken,
                    name: nameInput.value || null,
                };

                const response = await fetch('{{ route('auth.phone.verify') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify(payload),
                });

                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data?.message || 'Verification failed.');
                }

                window.location.href = data.redirect || '{{ route('user.dashboard') }}';
            } catch (error) {
                console.error(error);
                const message = error.message || 'Verification failed. Please try again.';
                setAlert(message, 'danger');
            } finally {
                disableButtons(false);
            }
        };

        sendBtn.addEventListener('click', sendOtp);
        verifyBtn.addEventListener('click', verifyOtp);
    </script>
</body>
</html>
