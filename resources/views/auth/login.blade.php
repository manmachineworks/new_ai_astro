<x-app-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <div class="mb-4 text-center">
                <h2 class="text-2xl font-bold text-gray-900">
                    @if(request()->is('admin/*')) Admin Login
                    @elseif(request()->is('astrologer/*')) Astrologer Login
                    @else User Login
                    @endif
                </h2>
            </div>

            <div x-data="{ loginMethod: 'phone' }">
                <div class="flex border-b border-gray-200 mb-6">
                    <button @click="loginMethod = 'phone'"
                        :class="{ 'border-b-2 border-indigo-500 text-indigo-600': loginMethod === 'phone', 'text-gray-500': loginMethod !== 'phone' }"
                        class="w-1/2 py-2 text-center font-medium">
                        Phone Login
                    </button>
                    <button @click="loginMethod = 'email'"
                        :class="{ 'border-b-2 border-indigo-500 text-indigo-600': loginMethod === 'email', 'text-gray-500': loginMethod !== 'email' }"
                        class="w-1/2 py-2 text-center font-medium">
                        Email Login
                    </button>
                </div>

                @if(session('status'))
                    <div class="mb-4 font-medium text-sm text-green-600">
                        {{ session('status') }}
                    </div>
                @endif

                <div id="error-message" class="hidden mb-4 font-medium text-sm text-red-600"></div>

                <!-- Phone Login Form -->
                <div x-show="loginMethod === 'phone'">
                    <form id="phone-login-form" onsubmit="handlePhoneLogin(event)">
                        <div class="mb-4">
                            <label for="phone" class="block font-medium text-sm text-gray-700">Phone Number</label>
                            <input id="phone" type="tel"
                                class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                placeholder="+1234567890" required />
                        </div>

                        <div id="recaptcha-container" class="mb-4"></div>

                        <div id="otp-section" class="hidden mb-4">
                            <label for="otp" class="block font-medium text-sm text-gray-700">Enter OTP</label>
                            <input id="otp" type="text"
                                class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                placeholder="123456" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" id="send-otp-btn"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Send OTP
                            </button>
                            <button type="button" id="verify-otp-btn" onclick="verifyOtp()"
                                class="hidden inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Verify & Login
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Email Login Form -->
                <div x-show="loginMethod === 'email'" style="display: none;">
                    <form method="POST" action="{{ route('auth.login') }}">
                        @csrf
                        <div>
                            <label for="email" class="block font-medium text-sm text-gray-700">Email</label>
                            <input id="email"
                                class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                type="email" name="email" :value="old('email')" required autofocus />
                        </div>

                        <div class="mt-4">
                            <label for="password" class="block font-medium text-sm text-gray-700">Password</label>
                            <input id="password"
                                class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                type="password" name="password" required autocomplete="current-password" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Log in
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Firebase Scripts -->
            <script type="module">
                import { initializeApp } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js";
                import { getAuth, RecaptchaVerifier, signInWithPhoneNumber } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-auth.js";

                const firebaseConfig = @json(config('firebase.web'));
                const errorMessage = document.getElementById('error-message');
                const sendOtpBtn = document.getElementById('send-otp-btn');
                const verifyOtpBtn = document.getElementById('verify-otp-btn');

                if (!firebaseConfig.api_key || !firebaseConfig.auth_domain || !firebaseConfig.project_id) {
                    errorMessage.innerText = 'Firebase configuration is missing. Please update your .env file.';
                    errorMessage.classList.remove('hidden');
                    sendOtpBtn.disabled = true;
                    verifyOtpBtn.disabled = true;
                } else {
                    const app = initializeApp({
                        apiKey: firebaseConfig.api_key,
                        authDomain: firebaseConfig.auth_domain,
                        projectId: firebaseConfig.project_id,
                        appId: firebaseConfig.app_id,
                        messagingSenderId: firebaseConfig.messaging_sender_id,
                        measurementId: firebaseConfig.measurement_id,
                    });
                    const auth = getAuth(app);
                    auth.useDeviceLanguage();

                    window.recaptchaVerifier = new RecaptchaVerifier(auth, 'recaptcha-container', {
                        'size': 'normal',
                        'callback': () => {
                            // reCAPTCHA solved, allow signInWithPhoneNumber.
                        },
                        'expired-callback': () => {
                            // Response expired. Ask user to solve reCAPTCHA again.
                        }
                    });
                }

                window.handlePhoneLogin = function (e) {
                    e.preventDefault();
                    const phoneNumber = document.getElementById('phone').value;
                    if (!window.recaptchaVerifier) {
                        errorMessage.innerText = 'Firebase is not configured. Please contact support.';
                        errorMessage.classList.remove('hidden');
                        return;
                    }
                    const appVerifier = window.recaptchaVerifier;

                    const btn = document.getElementById('send-otp-btn');
                    btn.disabled = true;
                    btn.innerText = "Sending...";

                    signInWithPhoneNumber(auth, phoneNumber, appVerifier)
                        .then((confirmationResult) => {
                            window.confirmationResult = confirmationResult;
                            document.getElementById('otp-section').classList.remove('hidden');
                            document.getElementById('send-otp-btn').classList.add('hidden');
                            document.getElementById('verify-otp-btn').classList.remove('hidden');
                            document.getElementById('recaptcha-container').classList.add('hidden');
                        }).catch((error) => {
                            console.error("SMS Error", error);
                            document.getElementById('error-message').innerText = error.message;
                            document.getElementById('error-message').classList.remove('hidden');
                            btn.disabled = false;
                            btn.innerText = "Send OTP";
                            window.recaptchaVerifier.render().then(function (widgetId) {
                                grecaptcha.reset(widgetId);
                            });
                        });
                }

                window.verifyOtp = function () {
                    const code = document.getElementById('otp').value;
                    if (!window.confirmationResult) {
                        errorMessage.innerText = 'Please request an OTP first.';
                        errorMessage.classList.remove('hidden');
                        return;
                    }
                    if (!code) {
                        errorMessage.innerText = 'Enter the OTP you received.';
                        errorMessage.classList.remove('hidden');
                        return;
                    }
                    window.confirmationResult.confirm(code).then((result) => {
                        // User signed in successfully with Firebase
                        const user = result.user;

                        // Now authenticate with backend
                        user.getIdToken().then((idToken) => {
                            fetch("{{ route('auth.login.firebase') }}", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                },
                                body: JSON.stringify({ id_token: idToken })
                            })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        window.location.href = data.redirect_url;
                                    } else {
                                        alert('Backend Authentication Failed');
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    alert('An error occurred during backend authentication.');
                                });
                        });

                    }).catch((error) => {
                        document.getElementById('error-message').innerText = "Invalid OTP";
                        document.getElementById('error-message').classList.remove('hidden');
                    });
                }
            </script>
        </div>
    </div>
</x-app-layout>
