<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\MedicalCenter;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class AppointmentController extends Controller
{
    public function index(): Response
    {
        $appointments = Appointment::with('medicalCenter')
            ->orderBy('scheduled_at')
            ->get()
            ->map(fn ($appointment) => [
                'id' => $appointment->id,
                'patient' => $appointment->first_name.' '.$appointment->last_name,
                'center' => $appointment->medicalCenter?->name,
                'scheduled_at' => $appointment->scheduled_at?->toIso8601String(),
                'phone' => $appointment->phone,
                'notes' => $appointment->notes,
            ]);

        return Inertia::render('Dashboard', [
            'centers' => MedicalCenter::orderBy('name')->get(),
            'appointments' => $appointments,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('appointments/Request', [
            'centers' => MedicalCenter::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'medical_center_id' => ['required', Rule::exists('medical_centers', 'id')],
            'scheduled_at' => ['required', 'date', 'after:now'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $scheduledAt = Carbon::parse($data['scheduled_at'])->setSecond(0);

        $data['scheduled_at'] = $scheduledAt;

        $isSlotTaken = Appointment::where('medical_center_id', $data['medical_center_id'])
            ->where('scheduled_at', $scheduledAt)
            ->exists();

        if ($isSlotTaken) {
            return Redirect::back()
                ->withInput()
                ->withErrors([
                    'scheduled_at' => 'Ya existe una cita en ese centro para la fecha y hora seleccionadas.',
                ]);
        }

        Appointment::create($data);

        return Redirect::route('appointments.create')
            ->with('success', 'Tu cita se ha registrado correctamente.');
    }
}
