<?php

namespace App\Http\Controllers;

use App\Services\AstrologyApiClient;
use App\Models\HoroscopeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HoroscopeController extends Controller
{
    protected $client;

    public function __construct(AstrologyApiClient $client)
    {
        $this->client = $client;
    }

    public function index()
    {
        $signs = [
            'aries',
            'taurus',
            'gemini',
            'cancer',
            'leo',
            'virgo',
            'libra',
            'scorpio',
            'sagittarius',
            'capricorn',
            'aquarius',
            'pisces'
        ];
        return view('user.horoscope.index', compact('signs'));
    }

    public function daily(Request $request)
    {
        $sign = strtolower($request->query('sign', 'aries'));
        $date = $request->query('date', now()->format('Y-m-d'));
        $lang = $request->query('lang', 'en');

        $cacheKey = "horoscope_daily_{$sign}_{$date}_{$lang}";

        $prediction = Cache::remember($cacheKey, 3600 * 12, function () use ($sign, $date, $lang) {
            $data = $this->client->getDailyHoroscope($sign, $date, $lang);

            // Log to DB for audit
            HoroscopeRequest::create([
                'user_id' => auth()->id(),
                'type' => 'daily',
                'input_json' => ['sign' => $sign, 'date' => $date, 'lang' => $lang],
                'response_json' => $data,
                'status' => 'success'
            ]);

            return $data;
        });

        return view('user.horoscope.show', [
            'type' => 'Daily',
            'sign' => ucfirst($sign),
            'prediction' => $prediction,
            'date' => $prediction['prediction_date'] ?? $date
        ]);
    }

    public function weekly(Request $request)
    {
        $sign = strtolower($request->query('sign', 'aries'));
        $lang = $request->query('lang', 'en');

        $cacheKey = "horoscope_weekly_{$sign}_{$lang}";

        $prediction = Cache::remember($cacheKey, 3600 * 24, function () use ($sign, $lang) {
            $data = $this->client->getWeeklyHoroscope($sign, $lang);

            HoroscopeRequest::create([
                'user_id' => auth()->id(),
                'type' => 'weekly',
                'input_json' => ['sign' => $sign, 'lang' => $lang],
                'response_json' => $data,
                'status' => 'success'
            ]);

            return $data;
        });

        return view('user.horoscope.show', [
            'type' => 'Weekly',
            'sign' => ucfirst($sign),
            'prediction' => $prediction,
            'date' => $prediction['week'] ?? now()->startOfWeek()->format('d M')
        ]);
    }

    public function kundliForm()
    {
        return view('user.horoscope.kundli_form');
    }

    public function getKundli(Request $request)
    {
        $validated = $request->validate([
            'day' => 'required|integer|min:1|max:31',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:1900|max:2100',
            'hour' => 'required|integer|min:0|max:23',
            'min' => 'required|integer|min:0|max:59',
            'lat' => 'required|numeric',
            'lon' => 'required|numeric',
            'tzone' => 'required|numeric',
        ]);

        try {
            $data = $this->client->getKundli($validated);

            HoroscopeRequest::create([
                'user_id' => auth()->id(),
                'type' => 'kundli',
                'input_json' => $validated,
                'response_json' => $data,
                'status' => 'success'
            ]);

            return view('user.horoscope.kundli_result', compact('data'));
        } catch (\Exception $e) {
            return back()->with('error', 'Could not generate Kundli. Please check details.');
        }
    }
}
