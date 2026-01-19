<footer class="bg-dark text-light py-5 mt-auto">
    <div class="container">
        <div class="row gy-4">
            <div class="col-lg-4 col-md-6">
                <h5 class="fw-bold mb-3"><i class="bi bi-stars"></i> AI Astro</h5>
                <p class="text-secondary">Connect with verification expert astrologers for instant guidance.
                    Compassionate, private, and secure.</p>
                <div class="d-flex gap-3">
                    <a href="#" class="text-secondary hover-text-white fs-5"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-secondary hover-text-white fs-5"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" class="text-secondary hover-text-white fs-5"><i class="bi bi-instagram"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <h6 class="text-white mb-3">Quick Links</h6>
                <ul class="list-unstyled text-secondary">
                    <li><a href="{{ url('/') }}" class="text-decoration-none text-secondary hover-text-white">Home</a>
                    </li>
                    <li><a href="{{ route('user.astrologers.index') }}"
                            class="text-decoration-none text-secondary hover-text-white">Astrologers</a></li>
                    <li><a href="{{ route('blogs.index') }}"
                            class="text-decoration-none text-secondary hover-text-white">Blogs</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-6">
                <h6 class="text-white mb-3">Support</h6>
                <ul class="list-unstyled text-secondary">
                    <li><a href="#" class="text-decoration-none text-secondary hover-text-white">Contact Us</a></li>
                    <li><a href="#" class="text-decoration-none text-secondary hover-text-white">FAQs</a></li>
                    <li><a href="#" class="text-decoration-none text-secondary hover-text-white">Privacy Policy</a></li>
                    <li><a href="#" class="text-decoration-none text-secondary hover-text-white">Terms of Service</a>
                    </li>
                </ul>
            </div>
            <div class="col-lg-4 col-md-6">
                <h6 class="text-white mb-3">Stay Updated</h6>
                <p class="text-secondary small">Subscribe to our newsletter for daily horoscopes and offers.</p>
                <form class="d-flex gap-2">
                    <input type="email" class="form-control form-control-sm" placeholder="Your email">
                    <button class="btn btn-primary btn-sm" type="button">Subscribe</button>
                </form>
            </div>
        </div>
        <hr class="border-secondary my-4">
        <div class="text-center text-secondary small">
            &copy; {{ date('Y') }} AI Astro. All rights reserved.
        </div>
    </div>
</footer>