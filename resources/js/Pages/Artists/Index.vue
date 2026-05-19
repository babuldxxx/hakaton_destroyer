<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
  artists: Array,         // одобренные артисты лейбла
  pendingArtists: Array,  // свободные/ожидающие
})

const deleteForm = useForm({})
const approveForm = useForm({})

function destroy(id) {
  if (!confirm('Удалить артиста? Это необратимо.')) return
  deleteForm.delete(route('artists.destroy', id))
}

function approve(id) {
  if (!confirm('Принять артиста в лейбл?')) return
  approveForm.post(route('artists.approve', id))
}
</script>

<template>
  <Head title="Артисты" />

  <AuthenticatedLayout>
    <div class="space-y-8 p-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-white">Артисты</h1>
        <Link
          :href="route('artists.create')"
          class="rounded-lg px-4 py-2 text-sm font-bold text-white transition-opacity hover:opacity-90"
          style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);"
        >
          + Добавить артиста
        </Link>
      </div>

      <!-- Мои артисты -->
      <section>
        <h2 class="mb-3 text-sm font-semibold uppercase tracking-wider" style="color: #64748B;">В лейбле</h2>
        <div class="overflow-hidden rounded-xl border" style="background-color: #0F1117; border-color: #1e2330;">
          <table class="w-full text-left text-sm">
            <thead style="background-color: #1A1F2B;">
              <tr class="text-xs uppercase" style="color: #94A3B8;">
                <th class="px-4 py-3 font-medium">Сценическое имя</th>
                <th class="px-4 py-3 font-medium">Настоящее имя</th>
                <th class="px-4 py-3 font-medium">Email</th>
                <th class="px-4 py-3 font-medium text-right">Действия</th>
              </tr>
            </thead>
            <tbody class="divide-y" style="border-color: #1e2330;">
              <tr v-for="artist in artists" :key="artist.id" class="transition-colors hover:bg-[#151a25]">
                <td class="px-4 py-3 font-medium text-white">{{ artist.stage_name ?? '—' }}</td>
                <td class="px-4 py-3 text-white">{{ artist.real_name ?? '—' }}</td>
                <td class="px-4 py-3" style="color: #94A3B8;">{{ artist.user?.email ?? '—' }}</td>
                <td class="px-4 py-3">
                  <div class="flex items-center justify-end gap-2">
                    <Link
                      :href="route('artists.show', artist.id)"
                      class="rounded-md px-2.5 py-1.5 text-xs font-medium text-white transition-colors hover:bg-white/5"
                      style="background-color: #1A1F2B;"
                    >
                      Открыть
                    </Link>
                    <Link
                      :href="route('artists.edit', artist.id)"
                      class="rounded-md px-2.5 py-1.5 text-xs font-medium text-white transition-colors hover:bg-white/5"
                      style="background-color: #1A1F2B;"
                    >
                      Изменить
                    </Link>
                    <button
                      @click="destroy(artist.id)"
                      class="rounded-md px-2.5 py-1.5 text-xs font-medium text-red-400 transition-colors hover:bg-white/5"
                      style="background-color: #1A1F2B;"
                    >
                      Выгнать из лейбла
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="artists.length === 0">
                <td colspan="4" class="px-4 py-8 text-center text-sm" style="color: #64748B;">
                  В лейбле пока нет артистов
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>

      <!-- Ожидающие заявки -->
      <section>
        <h2 class="mb-3 text-sm font-semibold uppercase tracking-wider" style="color: #64748B;">Заявки / Свободные артисты</h2>
        <div class="overflow-hidden rounded-xl border" style="background-color: #0F1117; border-color: #1e2330;">
          <table class="w-full text-left text-sm">
            <thead style="background-color: #1A1F2B;">
              <tr class="text-xs uppercase" style="color: #94A3B8;">
                <th class="px-4 py-3 font-medium">Сценическое имя</th>
                <th class="px-4 py-3 font-medium">Настоящее имя</th>
                <th class="px-4 py-3 font-medium">Email</th>
                <th class="px-4 py-3 font-medium text-right">Действия</th>
              </tr>
            </thead>
            <tbody class="divide-y" style="border-color: #1e2330;">
              <tr v-for="artist in pendingArtists" :key="artist.id" class="transition-colors hover:bg-[#151a25]">
                <td class="px-4 py-3 font-medium text-white">{{ artist.stage_name ?? '—' }}</td>
                <td class="px-4 py-3 text-white">{{ artist.real_name ?? '—' }}</td>
                <td class="px-4 py-3" style="color: #94A3B8;">{{ artist.user?.email ?? '—' }}</td>
                <td class="px-4 py-3 text-right">
                  <button
                    @click="approve(artist.id)"
                    :disabled="approveForm.processing"
                    class="rounded-lg px-3 py-1.5 text-xs font-bold text-white transition-opacity hover:opacity-90 disabled:opacity-50"
                    style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);"
                  >
                    Принять в лейбл
                  </button>
                </td>
              </tr>
              <tr v-if="pendingArtists.length === 0">
                <td colspan="4" class="px-4 py-8 text-center text-sm" style="color: #64748B;">
                  Нет ожидающих заявок
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </div>
  </AuthenticatedLayout>
</template>