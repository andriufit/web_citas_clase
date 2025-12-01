<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

interface MedicalCenter {
    id: number;
    name: string;
}

interface PageProps {
    centers: MedicalCenter[];
}

const page = usePage<PageProps>();
const centers = computed(() => page.props.centers ?? []);

const form = useForm({
    first_name: '',
    last_name: '',
    phone: '',
    medical_center_id: centers.value[0]?.id ?? '',
    scheduled_at: '',
    notes: '',
});

const flashMessage = computed(() => page.props.flash?.success);

const submit = () => {
    form.post('/appointments', {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Solicitar cita" />

    <div class="min-h-screen bg-neutral-50 px-4 py-10 dark:bg-neutral-900">
        <div class="mx-auto max-w-4xl space-y-6">
            <header class="flex flex-col gap-2 text-center">
                <p class="text-sm font-semibold uppercase tracking-wide text-orange-500">Gestión de citas</p>
                <h1 class="text-3xl font-bold text-neutral-900 dark:text-white">Solicita tu cita médica</h1>
                <p class="text-neutral-600 dark:text-neutral-300">
                    Completa el formulario para pedir una cita en uno de nuestros centros. Si el horario ya está ocupado,
                    te avisaremos para que elijas otro momento.
                </p>
            </header>

            <div class="grid gap-6 md:grid-cols-[2fr,1fr]">
                <Card>
                    <CardHeader>
                        <CardTitle>Datos de la cita</CardTitle>
                        <CardDescription>Comprueba que el día y la hora elegidos están libres.</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="first_name">Nombre</Label>
                                <Input
                                    id="first_name"
                                    v-model="form.first_name"
                                    name="first_name"
                                    type="text"
                                    autocomplete="given-name"
                                    :aria-invalid="!!form.errors.first_name"
                                />
                                <p v-if="form.errors.first_name" class="text-sm text-red-500">{{ form.errors.first_name }}</p>
                            </div>
                            <div class="space-y-2">
                                <Label for="last_name">Apellidos</Label>
                                <Input
                                    id="last_name"
                                    v-model="form.last_name"
                                    name="last_name"
                                    type="text"
                                    autocomplete="family-name"
                                    :aria-invalid="!!form.errors.last_name"
                                />
                                <p v-if="form.errors.last_name" class="text-sm text-red-500">{{ form.errors.last_name }}</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="phone">Número de teléfono</Label>
                            <Input
                                id="phone"
                                v-model="form.phone"
                                name="phone"
                                type="tel"
                                autocomplete="tel"
                                :aria-invalid="!!form.errors.phone"
                            />
                            <p v-if="form.errors.phone" class="text-sm text-red-500">{{ form.errors.phone }}</p>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="medical_center_id">Centro</Label>
                                <select
                                    id="medical_center_id"
                                    v-model="form.medical_center_id"
                                    name="medical_center_id"
                                    class="h-10 w-full rounded-md border border-input bg-white px-3 text-sm text-neutral-900 shadow-xs dark:bg-neutral-800 dark:text-white"
                                    :aria-invalid="!!form.errors.medical_center_id"
                                >
                                    <option value="" disabled>Selecciona un centro</option>
                                    <option v-for="center in centers" :key="center.id" :value="center.id">
                                        {{ center.name }}
                                    </option>
                                </select>
                                <p v-if="form.errors.medical_center_id" class="text-sm text-red-500">
                                    {{ form.errors.medical_center_id }}
                                </p>
                            </div>
                            <div class="space-y-2">
                                <Label for="scheduled_at">Fecha y hora</Label>
                                <Input
                                    id="scheduled_at"
                                    v-model="form.scheduled_at"
                                    name="scheduled_at"
                                    type="datetime-local"
                                    :aria-invalid="!!form.errors.scheduled_at"
                                />
                                <p v-if="form.errors.scheduled_at" class="text-sm text-red-500">{{ form.errors.scheduled_at }}</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="notes">Observaciones</Label>
                            <textarea
                                id="notes"
                                v-model="form.notes"
                                name="notes"
                                rows="4"
                                class="w-full rounded-md border border-input bg-white px-3 py-2 text-sm text-neutral-900 shadow-xs outline-none transition focus-visible:border-ring focus-visible:ring-4 focus-visible:ring-ring/30 dark:bg-neutral-800 dark:text-white"
                                :aria-invalid="!!form.errors.notes"
                            ></textarea>
                            <p v-if="form.errors.notes" class="text-sm text-red-500">{{ form.errors.notes }}</p>
                        </div>

                        <div class="flex items-center justify-between">
                            <p v-if="flashMessage" class="text-sm font-medium text-green-700 dark:text-green-400">
                                {{ flashMessage }}
                            </p>
                            <Button
                                type="button"
                                class="ml-auto"
                                :disabled="form.processing"
                                @click="submit"
                            >
                                Solicitar cita
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                <Card class="border-orange-200 bg-orange-50 text-neutral-900 dark:border-orange-900 dark:bg-orange-950 dark:text-white">
                    <CardHeader>
                        <CardTitle>Cómo funciona</CardTitle>
                        <CardDescription class="text-neutral-700 dark:text-neutral-200">
                            Recomendaciones para completar la solicitud.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-3 text-sm leading-6">
                        <p>1. Elige el centro donde quieres ser atendido.</p>
                        <p>2. Selecciona una fecha y hora futuras. Solo se permite una cita por centro en cada franja.</p>
                        <p>3. Añade un teléfono de contacto y observaciones para el equipo médico.</p>
                        <p>
                            Si el horario no está disponible, el sistema te avisará inmediatamente para que puedas escoger otro
                            momento.
                        </p>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
