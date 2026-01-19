@props(['tabs' => [], 'active' => ''])

<div x-data="{ activeTab: '{{ $active }}' }">
    <!-- Mobile Dropdown -->
    <div class="d-sm-none mb-3">
        <label for="tabs" class="visually-hidden">Select a tab</label>
        <select id="tabs" name="tabs" x-model="activeTab" class="form-select">
            @foreach($tabs as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <!-- Desktop Tabs -->
    <div class="d-none d-sm-block">
        <ul class="nav nav-tabs mb-4">
            @foreach($tabs as $key => $label)
                <li class="nav-item">
                    <a href="#" @click.prevent="activeTab = '{{ $key }}'" class="nav-link"
                        :class="activeTab === '{{ $key }}' ? 'active' : ''">
                        {{ $label }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    <div>
        {{ $slot }}
    </div>
</div>