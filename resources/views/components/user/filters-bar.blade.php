@props(['filters' => []])

<form action="" method="GET" class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-3">
            {{-- Search --}}
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="form-control border-start-0 ps-0" placeholder="Search astrologers...">
                </div>
            </div>

            {{-- Speciality --}}
            <div class="col-md-3">
                <select name="speciality" class="form-select">
                    <option value="">All Specialties</option>
                    <option value="vedic" {{ request('speciality') == 'vedic' ? 'selected' : '' }}>Vedic</option>
                    <option value="tarot" {{ request('speciality') == 'tarot' ? 'selected' : '' }}>Tarot</option>
                    <option value="numerology" {{ request('speciality') == 'numerology' ? 'selected' : '' }}>Numerology
                    </option>
                    <option value="vastu" {{ request('speciality') == 'vastu' ? 'selected' : '' }}>Vastu</option>
                </select>
            </div>

            {{-- Language --}}
            <div class="col-md-3">
                <select name="language" class="form-select">
                    <option value="">All Languages</option>
                    <option value="english" {{ request('language') == 'english' ? 'selected' : '' }}>English</option>
                    <option value="hindi" {{ request('language') == 'hindi' ? 'selected' : '' }}>Hindi</option>
                    <option value="tamil" {{ request('language') == 'tamil' ? 'selected' : '' }}>Tamil</option>
                </select>
            </div>

            {{-- Sort --}}
            <div class="col-md-3">
                <select name="sort" class="form-select" onchange="this.form.submit()">
                    <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Popularity</option>
                    <option value="rating_high" {{ request('sort') == 'rating_high' ? 'selected' : '' }}>Rating (High to
                        Low)</option>
                    <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price (Low to High)
                    </option>
                    <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price (High to Low)
                    </option>
                </select>
            </div>
        </div>
    </div>
</form>