<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3'

const props = defineProps({
  invitation: Object,
  message: String,
  expired: Boolean,
})

const acceptForm = useForm({})
const declineForm = useForm({})

function accept() {
  acceptForm.post('/invitations/' + props.invitation.token + '/accept')
}

function decline() {
  if (!confirm('Отклонить приглашение?')) return
  declineForm.post('/invitations/' + props.invitation.token + '/decline')
}
</script>

<<template>
  <Head title="Приглашение в лейбл" />

  <div class="flex min-h-screen items-center justify-center p-6" style="background-color: #0B0E14; color: #F8FAFC;">
    <div class="w-full max-w-md rounded-xl p-8" style="background-color: #0F1117; border: 1px solid #1e2330;">

      <!-- Header -->
      <div class="mb-6 flex items-center gap-3">
        <div
          class="flex h-10 w-10 items-center justify-center rounded-xl text-sm font-bold text-white"
          style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);"
        >
          SE
        </div>
        <div>
          <h1 class="text-lg font-bold text-white">SoundERP</h1>
          <p class="text-xs" style="color: #64748B;">Приглашение в лейбл</p>
        </div>
      </div>

      <!-- Сообщение (приглашение уже принято / истекло / отклонено) -->
      <div v-if="message" class="mb-6 rounded-lg p-4 text-sm" style="background-color: #1A1F2B; color: #94A3B8;">
        {{ message }}
      </div>

      <template v-else>
        <div class="mb-6 space-y-3">
          <div class="flex items-center justify-between rounded-lg p-4" style="background-color: #1A1F2B;">
            <span style="color: #64748B;">Лейбл</span>
            <span class="font-semibold text-white">{{ invitation.label?.name ?? '—' }}</span>
          </div>

          <div class="flex items-center justify-between rounded-lg p-4" style="background-color: #1A1F2B;">
            <span style="color: #64748B;">Email</span>
            <span class="font-medium text-white">{{ invitation.email }}</span>
          </div>

          <div class="flex items-center justify-between rounded-lg p-4" style="background-color: #1A1F2B;">
            <span style="color: #64748B;">Действует до</span>
            <span class="font-medium text-white">
              {{ invitation.expires_at ? new Date(invitation.expires_at).toLocaleDateString('ru-RU') : 'Бессрочно' }}
            </span>
          </div>
        </div>

        <div v-if="$page.props.auth?.user" class="flex flex-col gap-3">
          <button
            @click="accept"
            :disabled="acceptForm.processing"
            class="w-full rounded-lg py-2.5 text-sm font-bold text-white transition-opacity hover:opacity-90 disabled:opacity-50"
            style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);"
          >
            <span v-if="acceptForm.processing">Обработка…</span>
            <span v-else>Принять приглашение</span>
          </button>

          <button
            @click="decline"
            :disabled="declineForm.processing"
            class="w-full rounded-lg py-2.5 text-sm font-medium transition-opacity hover:opacity-90 disabled:opacity-50"
            style="background-color: #1A1F2B; color: #94A3B8;"
          >
            <span v-if="declineForm.processing">Обработка…</span>
            <span v-else>Отклонить</span>
          </button>
        </div>

        <div v-else class="text-center text-xs" style="color: #64748B;">
          Чтобы ответить,
          <Link :href="route('login')" class="text-blue-400 hover:underline">войдите в аккаунт</Link>.
        </div>
      </template>

      <div class="mt-6 text-center">
        <Link :href="route('dashboard')" class="text-xs hover:text-white" style="color: #64748B;">
          ← Вернуться в дашборд
        </Link>
      </div>

    </div>
  </div>
</template>