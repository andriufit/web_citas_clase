<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { computed, reactive, watch } from 'vue';

interface Appointment {
    id: number;
    patient: string;
    center: string;
    scheduled_at: string;
    phone: string;
    notes?: string | null;
}

interface MedicalCenter {
    id: number;
    name: string;
    address: string;
    phone: string;
}

const props = defineProps<{
    appointments: Appointment[];
    centers: MedicalCenter[];
}>();

const page = usePage();
const flashMessage = computed(() => page.props.flash?.success || page.props.flash?.error);

const createForm = useForm({
    name: '',
    address: '',
    phone: '',
});

const editableCenters = reactive(props.centers.map((center) => ({ ...center })));

watch(
    () => props.centers,
    (centers) => {
        editableCenters.splice(0, editableCenters.length, ...centers.map((center) => ({ ...center })));
    },
);

const submitCenter = () => {
    createForm.post('/centers', {
        preserveScroll: true,
        onSuccess: () => createForm.reset(),
    });
};

const updateCenter = (center: MedicalCenter) => {
    router.put(`/centers/${center.id}`, center, {
        preserveScroll: true,
    });
};

const deleteCenter = (center: MedicalCenter) => {
    router.delete(`/centers/${center.id}`, {
        preserveScroll: true,
    });
};

const formatDate = (value: string) =>
    new Intl.DateTimeFormat('es-ES', {
        dateStyle: 'short',
        timeStyle: 'short',
    }).format(new Date(value));
</script>

<template>
    <Head title="Panel" />

    <AppLayout>
        <div class="space-y-6 p-4 md:p-8">
            <div class="flex flex-col gap-2 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-neutral-900 dark:text-white">Panel de gestión</h1>
                    <p class="text-neutral-600 dark:text-neutral-300">
                        Revisa las citas solicitadas y mantén actualizada la lista de centros médicos.
                    </p>
                </div>
                <p v-if="flashMessage" class="text-sm font-medium text-green-700 dark:text-green-400">
                    {{ flashMessage }}
                </p>
            </div>

            <div class="grid gap-6 lg:grid-cols-[2fr,1fr]">
                <Card>
                    <CardHeader>
                        <CardTitle>Citas registradas</CardTitle>
                        <CardDescription>Listado de peticiones con control de horario por centro.</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-left text-sm">
                                <thead>
                                    <tr class="border-b border-neutral-200 text-xs uppercase text-neutral-500 dark:border-neutral-700 dark:text-neutral-400">
                                        <th class="px-3 py-2">Paciente</th>
                                        <th class="px-3 py-2">Centro</th>
                                        <th class="px-3 py-2">Fecha</th>
                                        <th class="px-3 py-2">Teléfono</th>
                                        <th class="px-3 py-2">Observaciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="appointment in appointments"
                                        :key="appointment.id"
                                        class="border-b border-neutral-100 last:border-0 dark:border-neutral-800"
                                    >
                                        <td class="px-3 py-2 font-medium text-neutral-900 dark:text-white">
                                            {{ appointment.patient }}
                                        </td>
                                        <td class="px-3 py-2">{{ appointment.center }}</td>
                                        <td class="px-3 py-2">{{ formatDate(appointment.scheduled_at) }}</td>
                                        <td class="px-3 py-2">{{ appointment.phone }}</td>
                                        <td class="px-3 py-2 text-neutral-600 dark:text-neutral-300">
                                            {{ appointment.notes || '—' }}
                                        </td>
                                    </tr>
                                    <tr v-if="appointments.length === 0">
                                        <td colspan="5" class="px-3 py-4 text-center text-neutral-500">
                                            Aún no hay citas registradas.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Centros médicos</CardTitle>
                        <CardDescription>Gestiona los centros disponibles para las citas.</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <form class="space-y-3" @submit.prevent="submitCenter">
                            <div class="space-y-2">
                                <Label for="name">Nombre</Label>
                                <Input
                                    id="name"
                                    v-model="createForm.name"
                                    name="name"
                                    type="text"
                                    :aria-invalid="!!createForm.errors.name"
                                />
                                <p v-if="createForm.errors.name" class="text-sm text-red-500">{{ createForm.errors.name }}</p>
                            </div>
                            <div class="space-y-2">
                                <Label for="address">Dirección</Label>
                                <Input
                                    id="address"
                                    v-model="createForm.address"
                                    name="address"
                                    type="text"
                                    :aria-invalid="!!createForm.errors.address"
                                />
                                <p v-if="createForm.errors.address" class="text-sm text-red-500">
                                    {{ createForm.errors.address }}
                                </p>
                            </div>
                            <div class="space-y-2">
                                <Label for="phone">Teléfono</Label>
                                <Input
                                    id="phone"
                                    v-model="createForm.phone"
                                    name="phone"
                                    type="text"
                                    :aria-invalid="!!createForm.errors.phone"
                                />
                                <p v-if="createForm.errors.phone" class="text-sm text-red-500">{{ createForm.errors.phone }}</p>
                            </div>
                            <Button type="submit" class="w-full" :disabled="createForm.processing">Añadir centro</Button>
                        </form>

                        <div class="space-y-3">
                            <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-200">Centros creados</h3>
                            <div
                                v-for="center in editableCenters"
                                :key="center.id"
                                class="rounded-lg border border-neutral-200 p-3 shadow-xs dark:border-neutral-800"
                            >
                                <div class="grid gap-3">
                                    <div class="space-y-1">
                                        <Label :for="`center-name-${center.id}`">Nombre</Label>
                                        <Input
                                            :id="`center-name-${center.id}`"
                                            v-model="center.name"
                                            type="text"
                                        />
                                    </div>
                                    <div class="space-y-1">
                                        <Label :for="`center-address-${center.id}`">Dirección</Label>
                                        <Input
                                            :id="`center-address-${center.id}`"
                                            v-model="center.address"
                                            type="text"
                                        />
                                    </div>
                                    <div class="space-y-1">
                                        <Label :for="`center-phone-${center.id}`">Teléfono</Label>
                                        <Input
                                            :id="`center-phone-${center.id}`"
                                            v-model="center.phone"
                                            type="text"
                                        />
                                    </div>
                                    <div class="flex gap-2">
                                        <Button
                                            type="button"
                                            variant="outline"
                                            class="flex-1"
                                            @click="updateCenter(center)"
                                        >
                                            Guardar cambios
                                        </Button>
                                        <Button
                                            type="button"
                                            variant="destructive"
                                            class="flex-1"
                                            @click="deleteCenter(center)"
                                        >
                                            Eliminar
                                        </Button>
                                    </div>
                                </div>
                            </div>
                            <p v-if="editableCenters.length === 0" class="text-sm text-neutral-500">
                                Todavía no hay centros creados.
                            </p>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
