<script setup>
import { Head, useForm } from '@inertiajs/vue3'

const props = defineProps({
  invitation: Object,
  message: String,
})

const form = useForm({})

function accept() {
  form.post(route('artists.invitations.accept', props.invitation.token))
}
</script>

<template>
  <Head title="Приглашение" />
  <div class="flex min-h-screen items-center justify-center p-6" style="background-color: #0B0E14; color: #F8FAFC;">
    <div class="w-full max-w-md rounded-xl p-8" style="background-color: #0F1117; border: 1px solid #1e2330;">
      <h1 class="mb-2 text-2xl font-bold text-white">Приглашение в лейбл</h1>

      <div v-if="message" class="mb-6 rounded-lg p-4 text-sm" style="background-color: #1A1F2B; color: #94A3B8;">
        {{ message }}
      </div>

      <template v-else>
        <p class="mb-6 text-sm" style="color: #94A3B8;">
          Лейбл <strong class="text-white">{{ invitation.label?.name ?? '—' }}</strong> приглашает вас присоединиться к ростеру.
        </p>

        <div class="mb-6 flex items-center justify-between rounded-lg p-4 text-sm" style="background-color: #1A1F2B;">
          <span style="color: #64748B;">Email</span>
          <span class="font-medium text-white">{{ invitation.email }}</span>
        </div>

        <button
          v-if="$page.props.auth?.user"
          @click="accept"
          :disabled="form.processing"
          class="w-full rounded-lg py-2.5 text-sm font-medium text-white transition-opacity hover:opacity-90 disabled:opacity-50"
          style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);"
        >
          <span v-if="form.processing">Обработка...</span>
          <span v-else>Принять приглашение</span>
        </button>

        <div v-else class="text-center text-xs" style="color: #64748B;">
          <a :href="route('login')" class="text-blue-400 hover:underline">Войдите</a>, чтобы принять приглашение.
        </div>
      </template>
    </div>
  </div>
</template>