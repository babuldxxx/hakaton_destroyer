<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: Boolean,
    status: String,
});
</script>

<template>
    <Head title="Профиль" />

    <AuthenticatedLayout>
        <div class="p-6 md:p-10 max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold text-white mb-8">Профиль</h1>

            <div class="space-y-6">
                <div class="rounded-xl border p-6" style="background-color: #1A1F2B; border-color: #2D3748;">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                    />
                </div>

                <div class="rounded-xl border p-6" style="background-color: #1A1F2B; border-color: #2D3748;">
                    <UpdatePasswordForm />
                </div>

                <div class="rounded-xl border p-6" style="background-color: #1A1F2B; border-color: #2D3748;">
                    <DeleteUserForm />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
