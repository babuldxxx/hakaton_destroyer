<script setup>
import { useForm } from "@inertiajs/vue3";

const form = useForm({
    track: null,
});

function submit() {
    form.post("/test-upload", {
        forceFormData: true,

        onSuccess: () => {
            form.reset("track");
        },
    });
}
</script>

<template>
    <div class="max-w-md space-y-4">
        <form @submit.prevent="submit" class="space-y-4">
            <input
                type="file"
                @input="form.track = $event.target.files[0]"
            />

            <div
                v-if="form.errors.track"
                class="text-sm text-red-500"
            >
                {{ form.errors.track }}
            </div>

            <progress
                v-if="form.progress"
                :value="form.progress.percentage"
                max="100"
                class="w-full"
            >
                {{ form.progress.percentage }}%
            </progress>

            <button
                type="submit"
                :disabled="form.processing"
                class="px-4 py-2 text-white bg-blue-500 rounded disabled:opacity-50"
            >
                {{ form.processing ? "Загрузка..." : "Загрузить трек" }}
            </button>
        </form>

        <div
            v-if="$page.props.flash?.success"
            class="text-green-500"
        >
            {{ $page.props.flash.success }}
        </div>

        <div
            v-if="$page.props.flash?.error"
            class="text-red-500"
        >
            {{ $page.props.flash.error }}
        </div>
    </div>
</template>
