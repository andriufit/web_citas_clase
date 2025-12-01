<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\MedicalCenter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_request_an_available_appointment(): void
    {
        $center = MedicalCenter::factory()->create();
        $scheduledAt = now()->addDay()->setTime(10, 0)->format('Y-m-d\TH:i');

        $response = $this->post(route('appointments.store'), [
            'first_name' => 'Laura',
            'last_name' => 'García',
            'phone' => '123456789',
            'medical_center_id' => $center->id,
            'scheduled_at' => $scheduledAt,
            'notes' => 'Paciente con alergias',
        ]);

        $response->assertRedirect(route('appointments.create'));

        $this->assertDatabaseHas('appointments', [
            'medical_center_id' => $center->id,
            'first_name' => 'Laura',
            'last_name' => 'García',
            'phone' => '123456789',
        ]);
    }

    public function test_prevents_double_booking_on_same_center_and_time(): void
    {
        $center = MedicalCenter::factory()->create();
        $scheduledAt = now()->addDays(2)->setTime(9, 30);

        Appointment::factory()->create([
            'medical_center_id' => $center->id,
            'scheduled_at' => $scheduledAt,
        ]);

        $response = $this->post(route('appointments.store'), [
            'first_name' => 'María',
            'last_name' => 'López',
            'phone' => '999888777',
            'medical_center_id' => $center->id,
            'scheduled_at' => $scheduledAt->format('Y-m-d\TH:i'),
            'notes' => null,
        ]);

        $response->assertSessionHasErrors('scheduled_at');
        $this->assertDatabaseCount('appointments', 1);
    }

    public function test_authenticated_users_can_manage_medical_centers(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $createResponse = $this->post(route('centers.store'), [
            'name' => 'Centro Norte',
            'address' => 'Av. Principal 123',
            'phone' => '111222333',
        ]);

        $createResponse->assertRedirect(route('dashboard'));
        $center = MedicalCenter::first();

        $updateResponse = $this->put(route('centers.update', $center), [
            'name' => 'Centro Norte Actualizado',
            'address' => 'Av. Principal 123',
            'phone' => '444555666',
        ]);

        $updateResponse->assertRedirect(route('dashboard'));

        $deleteResponse = $this->delete(route('centers.destroy', $center));
        $deleteResponse->assertRedirect(route('dashboard'));

        $this->assertDatabaseMissing('medical_centers', ['id' => $center->id]);
    }
}
