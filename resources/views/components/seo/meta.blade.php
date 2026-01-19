@props([
    'title' => config('app.name'),
    'description' => 'Your trusted AI Astrologer platform.',
    'keywords' => 'astrology, horoscope, ai astrologer, kundli',
    'image' => asset('images/og-default.jpg'),
    'type' => 'website',
    'url' => url()->current(),
    'locale' => app()->getLocale(),
    'alternates' => []
])

<title>{{ $title }} | {{ config('app.name') }}</title>
<meta name="description" content="{{ $description }}">
<meta name="keywords" content="{{ $keywords }}">
<link rel="canonical" href="{{ $url }}">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="{{ $type }}">
<meta property="og:url" content="{{ $url }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:image" content="{{ $image }}">
<meta property="og:locale" content="{{ $locale }}">

<!-- Twitter -->
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:url" content="{{ $url }}">
<meta property="twitter:title" content="{{ $title }}">
<meta property="twitter:description" content="{{ $description }}">
<meta property="twitter:image" content="{{ $image }}">

<!-- Alternates (Multi-language) -->
@foreach($alternates as $lang => $link)
<link rel="alternate" hreflang="{{ $lang }}" href="{{ $link }}" />
@endforeach
