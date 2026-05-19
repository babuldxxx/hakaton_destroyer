<script setup>
import { Head, useForm, usePage, router } from '@inertiajs/vue3'
import { computed } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  artists: Array,
  payouts: Object,
  stats: Object,
  transactions: Object,
})

const page = usePage()
const role = computed(() => {
  const r = page.props.auth?.user?.role
  return typeof r === 'string' ? r : r?.value
})

const isLabel = computed(() => role.value === 'label')

const form = useForm({
  artist_id: null,
  method: 'bank',
  details: '',
})

function createPayout(artist) {
  form.artist_id = artist.id
  form.post(route('payouts.store'), {
    preserveScroll: true,
    onSuccess: () => form.reset(),
  })
}

function confirmPaid(payout) {
  if (!confirm('Подтвердить выплату ' + Number(payout.amount).toLocaleString('ru-RU') + ' ₽?')) return
  router.patch(route('payouts.pay', payout.id), {}, { preserveScroll: true })
}
</script>

<<template>
  <Head title="Выплаты" />
  <AuthenticatedLayout>
    <div class="min-h-screen p-8" style="background-color: #0B0E14; color: #F8FAFC;">
      
      <template v-if="isLabel">
        <h1 class="mb-8 text-3xl font-bold">Выплаты артистам</h1>

        <div class="mb-10 space-y-3">
          <h2 class="mb-2 text-sm font-semibold uppercase tracking-wider" style="color: #64748B;">
            Доступно для выплаты
          </h2>

          <div
            v-for="artist in artists"
            :key="artist.id"
            class="flex items-center justify-between rounded-xl px-6 py-4"
            style="background-color: #0F1117; border: 1px solid #1e2330;"
          >
            <div>
              <p class="text-lg font-semibold text-white">{{ artist.artist_name }}</p>
              <p class="text-xs" style="color: #64748B;">{{ artist.pending_count }} начислений</p>
            </div>
            <div class="flex items-center gap-6">
              <div class="text-right">
                <p class="text-2xl font-bold text-white">
                  {{ Number(artist.pending_amount).toLocaleString('ru-RU') }} ₽
                </p>
                <p class="text-[11px]" style="color: #64748B;">доступно</p>
              </div>
              <button
                @click="createPayout(artist)"
                :disabled="form.processing && form.artist_id === artist.id"
                class="rounded-lg px-4 py-2 text-sm font-medium text-white transition-opacity hover:opacity-90 disabled:opacity-50"
                style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);"
              >
                Сформировать выплату
              </button>
            </div>
          </div>

          <div
            v-if="!artists || artists.length === 0"
            class="rounded-xl p-6 text-sm"
            style="background-color: #0F1117; border: 1px solid #1e2330; color: #64748B;"
          >
            Нет начислений в статусе «Ожидает».
          </div>
        </div>

        <h2 class="mb-4 text-sm font-semibold uppercase tracking-wider" style="color: #64748B;">
          История выплат
        </h2>
        <div class="rounded-xl overflow-hidden" style="background-color: #0F1117; border: 1px solid #1e2330;">
          <table class="w-full text-left text-sm">
            <thead style="background-color: #1A1F2B;">
              <tr>
                <th class="px-6 py-3 font-medium" style="color: #94A3B8;">Дата</th>
                <th class="px-6 py-3 font-medium" style="color: #94A3B8;">Артист</th>
                <th class="px-6 py-3 font-medium" style="color: #94A3B8;">Сумма</th>
                <th class="px-6 py-3 font-medium" style="color: #94A3B8;">Статус</th>
                <th class="px-6 py-3 font-medium" style="color: #94A3B8;"></th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="p in payouts.data"
                :key="p.id"
                class="border-t"
                style="border-color: #1e2330;"
              >
                <td class="px-6 py-4 text-white whitespace-nowrap">
                  {{ new Date(p.created_at).toLocaleDateString('ru-RU') }}
                </td>
                <td class="px-6 py-4 text-white">
                  {{ p.artist?.user?.name ?? '-' }}
                </td>
                <td class="px-6 py-4 font-semibold text-white whitespace-nowrap">
                  {{ Number(p.amount).toLocaleString('ru-RU') }} ₽
                </td>
                <td class="px-6 py-4">
                  <span
                    v-if="p.status === 'pending'"
                    class="rounded-full bg-yellow-500/10 px-2.5 py-1 text-xs font-medium text-yellow-500"
                  >
                    Ожидает
                  </span>
                  <span
                    v-else-if="p.status === 'paid'"
                    class="rounded-full bg-green-500/10 px-2.5 py-1 text-xs font-medium text-green-500"
                  >
                    Выплачено
                  </span>
                </td>
                <td class="px-6 py-4 text-right">
                  <button
                    v-if="p.status === 'pending'"
                    @click="confirmPaid(p)"
                    class="rounded-lg px-3 py-1.5 text-xs font-medium text-white transition-opacity hover:opacity-90"
                    style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);"
                  >
                    Подтвердить перевод
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
          <div v-if="!payouts.data.length" class="px-6 py-8 text-center text-sm" style="color: #64748B;">
            Пока нет выплат.
          </div>
        </div>
      </template>

      <template v-else>
        <h1 class="mb-8 text-3xl font-bold">Финансы</h1>

        <div class="mb-10 grid grid-cols-1 gap-6 md:grid-cols-3">
          <div class="rounded-xl p-6" style="background-color: #0F1117; border: 1px solid #1e2330;">
            <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-lg" style="background-color: #1A1F2B;">
              <svg class="h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <p class="text-sm font-medium" style="color: #94A3B8;">Текущий баланс</p>
            <p class="mt-1 text-3xl font-bold text-white">
              {{ Number(stats?.balance ?? 0).toLocaleString('ru-RU') }} ₽
            </p>
            <p class="mt-2 text-xs" style="color: #10B981;">Доступно для вывода</p>
          </div>

          <div class="rounded-xl p-6" style="background-color: #0F1117; border: 1px solid #1e2330;">
            <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-lg" style="background-color: #1A1F2B;">
              <svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
              </svg>
            </div>
            <p class="text-sm font-medium" style="color: #94A3B8;">Общий доход</p>
            <p class="mt-1 text-3xl font-bold text-white">
              {{ Number(stats?.total_earned ?? 0).toLocaleString('ru-RU') }} ₽
            </p>
            <p class="mt-2 text-xs" style="color: #64748B;">За всё время</p>
          </div>

          <div class="rounded-xl p-6" style="background-color: #0F1117; border: 1px solid #1e2330;">
            <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-lg" style="background-color: #1A1F2B;">
              <svg class="h-5 w-5 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
              </svg>
            </div>
            <p class="text-sm font-medium" style="color: #94A3B8;">Выплачено</p>
            <p class="mt-1 text-3xl font-bold text-white">
              {{ Number(stats?.total_paid ?? 0).toLocaleString('ru-RU') }} ₽
            </p>
            <p class="mt-2 text-xs" style="color: #64748B;">Всего получено</p>
          </div>
        </div>

        <h2 class="mb-4 text-xl font-semibold">История начислений и выплат</h2>
        <div class="rounded-xl overflow-hidden" style="background-color: #0F1117; border: 1px solid #1e2330;">
          <table class="w-full text-left text-sm">
            <thead style="background-color: #1A1F2B;">
              <tr>
                <th class="px-6 py-3 font-medium" style="color: #94A3B8;">Дата</th>
                <th class="px-6 py-3 font-medium" style="color: #94A3B8;">Описание</th>
                <th class="px-6 py-3 font-medium" style="color: #94A3B8;">Сумма</th>
                <th class="px-6 py-3 font-medium" style="color: #94A3B8;">Статус</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="tx in transactions.data"
                :key="tx.id"
                class="border-t"
                style="border-color: #1e2330;"
              >
                <td class="px-6 py-4 text-white whitespace-nowrap">
                  {{ new Date(tx.created_at).toLocaleDateString('ru-RU') }}
                </td>
                <td class="px-6 py-4 text-white">
                  {{ tx.earning?.song?.title ?? 'Начисление роялти' }}
                </td>
                <td class="px-6 py-4 font-semibold whitespace-nowrap" :class="tx.amount > 0 ? 'text-green-400' : 'text-red-400'">
                  {{ (tx.amount > 0 ? '+' : '') + Number(tx.amount).toLocaleString('ru-RU') }} ₽
                </td>
                <td class="px-6 py-4">
                  <span
                    v-if="tx.status === 'pending'"
                    class="rounded-full bg-yellow-500/10 px-2.5 py-1 text-xs font-medium text-yellow-500"
                  >
                    Ожидает
                  </span>
                  <span
                    v-else-if="tx.status === 'on_hold'"
                    class="rounded-full bg-blue-500/10 px-2.5 py-1 text-xs font-medium text-blue-500"
                  >
                    В выплате
                  </span>
                  <span
                    v-else-if="tx.status === 'paid'"
                    class="rounded-full bg-green-500/10 px-2.5 py-1 text-xs font-medium text-green-500"
                  >
                    Выплачено
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
          <div v-if="!transactions?.data?.length" class="px-6 py-8 text-center text-sm" style="color: #64748B;">
            Пока нет операций.
          </div>
        </div>
      </template>
    </div>
  </AuthenticatedLayout>
</template>