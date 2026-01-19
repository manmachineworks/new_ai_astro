<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AppointmentPolicy
{
    public function view(User $user, Appointment $appointment): Response
    {
        return $user->id === $appointment->astrologer_user_id
            ? Response::allow()
            : Response::deny('You do not own this appointment.');
    }

    public function update(User $user, Appointment $appointment): Response
    {
        return $this->view($user, $appointment);
    }
}
