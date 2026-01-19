<?php

namespace App\Http\Controllers\Astrologer;

use App\Http\Requests\Astrologer\StoreScheduleRequest;
use App\Http\Requests\Astrologer\UpdateScheduleRequest;
use App\Models\AstrologerSchedule;
use App\Models\AstrologerTimeOff;
use Inertia\Inertia;

class ScheduleController extends AstrologerBaseController
{
    public function index()
    {
        $astrologer = $this->resolveAstrologer();

        return Inertia::render('Astrologer/Schedule', [
            'schedules' => AstrologerSchedule::where('astrologer_id', $astrologer->id)->get(),
            'timeOff' => AstrologerTimeOff::where('astrologer_id', $astrologer->id)->get(),
        ]);
    }

    public function store(StoreScheduleRequest $request)
    {
        $astrologer = $this->resolveAstrologer();
        AstrologerSchedule::create(array_merge($request->validated(), [
            'astrologer_id' => $astrologer->id,
        ]));

        return back()->with('success', 'Schedule added.');
    }

    public function update(UpdateScheduleRequest $request, AstrologerSchedule $schedule)
    {
        $this->assertOwnSchedule($schedule);
        $schedule->update($request->validated());

        return back()->with('success', 'Schedule updated.');
    }

    public function destroy(AstrologerSchedule $schedule)
    {
        $this->assertOwnSchedule($schedule);
        $schedule->delete();

        return back()->with('success', 'Schedule removed.');
    }

    protected function assertOwnSchedule(AstrologerSchedule $schedule): void
    {
        $astrologer = $this->resolveAstrologer();
        abort_unless($schedule->astrologer_id === $astrologer->id, 403);
    }
}
