<?php

namespace App\Http\Controllers\Astrologer;

use App\Http\Requests\Astrologer\UpdateProfileRequest;
use App\Http\Requests\Astrologer\UploadVerificationRequest;
use Inertia\Inertia;

class ProfileController extends AstrologerBaseController
{
    public function edit()
    {
        $astrologer = $this->resolveAstrologer();

        return Inertia::render('Astrologer/Profile', [
            'astrologer' => $astrologer,
        ]);
    }

    public function update(UpdateProfileRequest $request)
    {
        $astrologer = $this->resolveAstrologer();
        $astrologer->update(array_filter($request->validated(), fn ($value) => $value !== null));

        return back()->with('success', 'Profile updated.');
    }

    public function uploadVerification(UploadVerificationRequest $request)
    {
        $astrologer = $this->resolveAstrologer();
        $astrologer->update([
            'verification_status' => $request->verification_status,
            'verification_remark' => $request->verification_remark,
            'is_verified' => $request->verification_status === 'approved',
        ]);

        return back()->with('success', 'Verification status updated.');
    }
}
