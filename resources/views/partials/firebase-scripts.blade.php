<!-- Firebase SDKs -->
<script type="module">
    import { initializeApp } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js";
    import { getAuth, signInWithCustomToken } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-auth.js";
    import { getFirestore, collection, addDoc, onSnapshot, query, where, orderBy } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-firestore.js";
    import { getMessaging, getToken, onMessage } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging.js";

    const firebaseConfig = {
        apiKey: "{{ config('firebase.api_key') }}",
        authDomain: "{{ config('firebase.auth_domain') }}",
        projectId: "{{ config('firebase.project_id') }}",
        storageBucket: "{{ config('firebase.storage_bucket') }}",
        messagingSenderId: "{{ config('firebase.messaging_sender_id') }}",
        appId: "{{ config('firebase.app_id') }}"
    };

    // Initialize Firebase
    const app = initializeApp(firebaseConfig);
    const auth = getAuth(app);
    const db = getFirestore(app);
    const messaging = getMessaging(app);

    window.firebase = { app, auth, db, messaging, collection, addDoc, onSnapshot, query, where, orderBy, signInWithCustomToken };

    console.log("Firebase Initialized");

    // Auto-Login if User is Auth'd in Laravel
    @auth
        {{--
        fetch("{{ route('firebase.token') }}")
        .then(res => res.json())
        .then(data => {
            if (data.firebase_token) {
                signInWithCustomToken(auth, data.firebase_token)
                    .then((userCredential) => {
                        console.log("Firebase Auth Success:", userCredential.user.uid);
                        window.currentUserUid = userCredential.user.uid;
                        // Dispatch event
                        window.dispatchEvent(new CustomEvent('firebase-ready', { detail: userCredential.user }));
                    })
                    .catch((error) => {
                        console.error("Firebase Auth Error", error);
                    });
            }
        });
        --}}
    @endauth
</script>