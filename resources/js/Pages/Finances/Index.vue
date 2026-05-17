<script setup>
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const page = usePage()

// Моковые данные
const user = computed(() => page.props.auth?.user ?? { name: 'Мария Светлова' })

const stats = {
  balance: '87 500 ₽',
  total_income: '524 300 ₽',
  paid_out: '436 800 ₽'
}

const history = [
  { id: 1, date: '2025-11-15', desc: 'Доход с VK Music - Летняя Ночь', amount: 15400, type: 'credit' },
  { id: 2, date: '2025-11-15', desc: 'Выплата', amount: -180000, type: 'debit' },
  { id: 3, date: '2025-11-10', desc: 'Доход с Apple Music - Летняя Ночь', amount: 12800, type: 'credit' },
  { id: 4, date: '2025-11-05', desc: 'Доход с Яндекс.Музыка - Летняя Ночь', amount: 9200, type: 'credit' },
]

// Модалка
const showModal = ref(false)
const payoutAmount = ref('')
const error = ref('')
const success = ref(false)

const numericBalance = 87500

function openModal() {
  showModal.value = true
  error.value = ''
  success.value = false
  payoutAmount.value = ''
}

function closeModal() {
  showModal.value = false
}

function submitPayout() {
  const val = parseInt(String(payoutAmount.value).replace(/\s/g, ''), 10)

  if (!val || val <= 0) {
    error.value = 'Введите корректную сумму'
    return
  }

  if (val < 10000) {
    error.value = 'Минимальная сумма вывода 10 000 ₽'
    return
  }

  if (val > numericBalance) {
    error.value = 'Недостаточно средств на балансе'
    return
  }

  // Здесь позже будет axios.post('/payout')
  error.value = ''
  success.value = true
  setTimeout(() => {
    window.location.reload()
  }, 1200)
}
</script>

<template>
  <AuthenticatedLayout>
    <div class="p-6 md:p-10">
      <div class="mx-auto max-w-6xl">

        <!-- Хедер -->
        <div class="mb-8 flex items-center justify-between">
          <h1 class="text-[32px] font-bold" style="color: #F8FAFC;">Финансы</h1>
          <button 
            @click="openModal"
            class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold text-white transition-opacity hover:opacity-90"
            style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);"
          >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round"  d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
              <path d="M12 2v20"/>
            </svg>
            Запросить выплату
          </button>
        </div>

        <!-- KPI -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
          
          <!-- Баланс -->
          <div class="rounded-[12px] p-6" style="background-color: #1A1F2B; box-shadow: 0px 4px 6px rgba(0,0,0,0.3);">
            <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-500 text-white">
              <span class="text-xl font-bold">$</span>
            </div>
            <p class="text-sm font-medium" style="color: #94A3B8;">Текущий баланс</p>
            <p class="mt-2 text-[28px] font-bold text-white">{{ stats.balance }}</p>
            <p class="mt-2 text-sm font-medium" style="color: #10B981;">Доступно для вывода</p>
          </div>

          <!-- Общий доход -->
          <div class="rounded-[12px] p-6" style="background-color: #1A1F2B; box-shadow: 0px 4px 6px rgba(0,0,0,0.3);">
            <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-violet-500 text-white">
              <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
              </svg>
            </div>
            <p class="text-sm font-medium" style="color: #94A3B8;">Общий доход</p>
            <p class="mt-2 text-[28px] font-bold text-white">{{ stats.total_income }}</p>
            <p class="mt-2 text-sm" style="color: #64748B;">За всё время</p>
          </div>

          <!-- Выплачено -->
          <div class="rounded-[12px] p-6" style="background-color: #1A1F2B; box-shadow: 0px 4px 6px rgba(0,0,0,0.3);">
            <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-orange-500 text-white">
              <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m0 0l6.75-6.75M12 19.5l-6.75-6.75" />
              </svg>
            </div>
            <p class="text-sm font-medium" style="color: #94A3B8;">Выплачено</p>
            <p class="mt-2 text-[28px] font-bold text-white">{{ stats.paid_out }}</p>
            <p class="mt-2 text-sm" style="color: #64748B;">Всего получено</p>
          </div>
        </div>

        <!-- История -->
        <div class="mt-8 rounded-[12px] overflow-hidden" style="background-color: #1A1F2B; box-shadow: 0px 4px 6px rgba(0,0,0,0.3);">
          <div class="px-6 py-5">
            <h2 class="text-lg font-semibold" style="color: #F8FAFC;">История начислений и выплат</h2>
          </div>
          
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="border-b text-left text-sm font-medium" style="border-color: #2D3748; color: #94A3B8;">
                  <th class="px-6 py-3">Дата</th>
                  <th class="px-6 py-3">Описание</th>
                  <th class="px-6 py-3 text-right">Сумма</th>
                  <th class="px-6 py-3 text-right">Тип</th>
                </tr>
              </thead>
              <tbody>
                <tr 
                  v-for="(item, idx) in history" 
                  :key="item.id"
                  class="border-b transition-colors hover:bg-white/5"
                  style="border-color: #2D3748;"
                >
                  <td class="px-6 py-4 text-sm whitespace-nowrap" style="color: #F8FAFC;">{{ item.date }}</td>
                  <td class="px-6 py-4 text-sm" style="color: #F8FAFC;">{{ item.desc }}</td>
                  <td 
                    class="px-6 py-4 text-right text-sm font-semibold whitespace-nowrap"
                    :style="item.type === 'credit' ? { color: '#10B981' } : { color: '#EF4444' }"
                  >
                    {{ item.type === 'credit' ? '+' : '' }}{{ item.amount.toLocaleString('ru-RU') }} ₽
                  </td>
                  <td class="px-6 py-4 text-right">
                    <span 
                      class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium"
                      :style="item.type === 'credit' 
                        ? { backgroundColor: 'rgba(16, 185, 129, 0.15)', color: '#10B981' }
                        : { backgroundColor: 'rgba(239, 68, 68, 0.15)', color: '#EF4444' }"
                    >
                      <template v-if="item.type === 'credit'">
                        <svg class="mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                        </svg>
                        Начисление
                      </template>
                      <template v-else>
                        <svg class="mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15m0 0l6.75 6.75M4.5 12l6.75-6.75" />
                        </svg>
                        Выплата
                      </template>
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- О выплатах -->
        <div class="mt-8 rounded-[12px] p-6" style="background-color: #1A1F2B; box-shadow: 0px 4px 6px rgba(0,0,0,0.3);">
          <div class="flex items-start gap-5">
            <div 
              class="rounded-xl flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl text-white"
              style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);"
            >
              <span class=" text-2xl font-bold">$</span>
            </div>
            <div>
              <h3 class="text-lg font-semibold text-white">О выплатах</h3>
              <p class="mt-2 text-sm leading-relaxed" style="color: #94A3B8;">
                Выплаты производятся дважды в месяц: 1-го и 15-го числа.<br>
                Минимальная сумма для вывода составляет 10,000 ₽. Запросить выплату можно в любое время, она будет обработана в ближайшую дату выплат.
              </p>
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- МОДАЛКА: Запросить выплату -->
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="transform opacity-0"
      enter-to-class="transform opacity-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="transform opacity-100"
      leave-to-class="transform opacity-0"
    >
      <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center px-4" style="background-color: rgba(0,0,0,0.7);">
        <div 
          class="w-full max-w-md rounded-[16px] p-6"
          style="background-color: #1A1F2B; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); border: 1px solid #2D3748;"
        >
          <div class="mb-6 flex items-center justify-between">
            <h3 class="text-xl font-bold text-white">Запросить выплату</h3>
            <button @click="closeModal" class="rounded-lg p-1 transition-colors hover:bg-white/10">
              <svg class="h-6 w-6" style="color: #94A3B8;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <div v-if="!success">
            <p class="mb-4 text-sm" style="color: #94A3B8;">
              Доступно для вывода: <span class="font-semibold text-white">{{ stats.balance }}</span>
            </p>

            <label class="mb-2 block text-sm font-medium text-white">Сумма</label>
            <div 
              class="flex items-center gap-2 rounded-xl border px-4 py-3"
              :style="error ? 'border-color: #EF4444; background-color: #0B0E14;' : 'border-color: #2D3748; background-color: #0B0E14;'"
            >
              <input 
                v-model="payoutAmount"
                type="number" 
                placeholder="Введите сумму" 
                class="w-full bg-transparent text-sm text-white placeholder-gray-500 outline-none"
              />
              <span class="text-sm font-medium" style="color: #64748B;">₽</span>
            </div>
            <p v-if="error" class="mt-2 text-xs" style="color: #EF4444;">{{ error }}</p>

            <button 
              @click="submitPayout"
              class="mt-6 w-full rounded-xl py-3 text-sm font-semibold text-white transition-opacity hover:opacity-90"
              style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);"
            >
              Отправить запрос
            </button>
          </div>

          <div v-else class="py-8 text-center">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full" style="background-color: rgba(16, 185, 129, 0.15);">
              <svg class="h-8 w-8" style="color: #10B981;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
              </svg>
            </div>
            <h4 class="text-lg font-semibold text-white">Запрос отправлен!</h4>
            <p class="mt-2 text-sm" style="color: #94A3B8;">Обновление баланса после подтверждения</p>
          </div>
        </div>
      </div>
    </Transition>
  </AuthenticatedLayout>
</template>