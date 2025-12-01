<?php

namespace App\Http\Controllers;

use App\Models\MedicalCenter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Response;

class MedicalCenterController extends Controller
{
    public function index(): Response
    {
        return Redirect::route('dashboard');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
        ]);

        MedicalCenter::create($data);

        return Redirect::route('dashboard')->with('success', 'Centro médico creado correctamente.');
    }

    public function update(Request $request, MedicalCenter $medicalCenter): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
        ]);

        $medicalCenter->update($data);

        return Redirect::route('dashboard')->with('success', 'Centro médico actualizado.');
    }

    public function destroy(MedicalCenter $medicalCenter): RedirectResponse
    {
        $medicalCenter->delete();

        return Redirect::route('dashboard')->with('success', 'Centro médico eliminado.');
    }
}
