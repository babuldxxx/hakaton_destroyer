<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage()
const user = computed(() => page.props.auth?.user)

const isActive = (href) => {
  if (href === '#') return false
  return page.url.startsWith(href)
}

const menu = [
  {
    name: 'Дашборд',
    href: '/label/dashboard',
    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" /></svg>'
  },
  {
    name: 'Артисты',
    href: '/label/artists',
    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>'
  },
  {
    name: 'Треки',
    href: '#',
    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 9l10.5-3m0 6.553v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 11-.99-3.467l2.31-.66a2.25 2.25 0 001.632-2.163zm0 0V2.25L9 5.25v10.303m0 0v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 01-.99-3.467l2.31-.66A2.25 2.25 0 009 15.553z" /></svg>'
  },
  {
    name: 'Выплаты',
    href: '/label/payouts',
    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" /></svg>'
  },
  {
    name: 'Отчёты',
    href: '/label/reports',
    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>'
  }
]
</script>

<template>
  <div class="flex min-h-screen bg-[#0B0E14]">
    
    <!-- Sidebar -->
    <aside class="w-64 flex flex-col bg-[#0B0E14] border-r border-white/10 sticky top-0 h-screen">
      
      <!-- Logo -->
<div class="p-6 pb-5">
  <div class="flex items-center gap-3">
    <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-lg shadow-violet-500/20"
      style="background: linear-gradient(135deg, #8B5CF6 0%, #3B82F6 100%);">
      
      <!-- Audio-wave / Equalizer icon -->
      <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
        <path d="M12 3a1 1 0 0 1 1 1v16a1 1 0 1 1-2 0V4a1 1 0 0 1 1-1zM7 7a1 1 0 0 1 1 1v8a1 1 0 1 1-2 0V8a1 1 0 0 1 1-1zM17 6a1 1 0 0 1 1 1v10a1 1 0 1 1-2 0V7a1 1 0 0 1 1-1zM2 11a1 1 0 0 1 1 1v2a1 1 0 1 1-2 0v-2a1 1 0 0 1 1-1zM22 10a1 1 0 0 1 1 1v4a1 1 0 1 1-2 0v-4a1 1 0 0 1 1-1z"/>
      </svg>
    </div>
    <div>
      <h1 class="text-xl font-bold leading-none bg-clip-text text-transparent bg-gradient-to-r from-violet-400 to-blue-400">
        SoundERP
      </h1>
      <p class="text-[11px] text-slate-500 mt-1 tracking-wide">Music Label CRM</p>
    </div>
  </div>
</div>

      <!-- Divider -->
      <div class="mx-6 h-px bg-white/10 mb-4" />

      <!-- Navigation -->
      <nav class="flex-1 px-4 space-y-1">
        <Link
          v-for="item in menu"
          :key="item.name"
          :href="item.href"
          class="group flex items-center gap-3.5 px-4 py-3 rounded-2xl text-sm font-medium transition relative overflow-hidden"
          :class="isActive(item.href)
            ? 'bg-[#151b2e] text-white shadow-[inset_0_0_0_1px_rgba(124,58,237,0.08)]'
            : 'text-slate-400 hover:text-white hover:bg-white/5'"
        >
          <!-- Active left accent -->
          <div
            v-if="isActive(item.href)"
            class="absolute left-0 top-2.5 bottom-2.5 w-[3px] rounded-r-full bg-gradient-to-b from-violet-400 to-blue-500"
          />
          
          <div
            class="shrink-0"
            :class="isActive(item.href) ? 'text-violet-400' : 'text-slate-400 group-hover:text-slate-300'"
            v-html="item.icon"
          />
          <span class="relative z-10">{{ item.name }}</span>
        </Link>
      </nav>

      <!-- Bottom user block -->
      <div class="p-4 mx-4 mb-4">
        <div class="flex items-center gap-3 pt-4 border-t border-white/10">
          <div class="w-10 h-10 rounded-full bg-gradient-to-br from-violet-500 to-blue-500 flex items-center justify-center text-white font-bold text-sm shadow-md">
            {{ user?.name?.charAt(0) || 'L' }}
          </div>
          <div class="min-w-0">
            <p class="text-sm font-semibold text-white truncate">SoundERP Label</p>
            <p class="text-xs text-slate-500">Администратор</p>
          </div>
        </div>
      </div>
    </aside>

    <!-- Main content -->
    <main class="flex-1 overflow-y-auto">
      <slot />
    </main>
  </div>
</template>