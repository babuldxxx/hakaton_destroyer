<script setup>
defineProps({
  title: String,
  value: String,
  subtitle: String,
  iconBg: {
    type: String,
    default: 'bg-gray-700'
  }
})
</script>

<template>
  <div 
    class="rounded-[12px] p-6"
    style="background-color: #1A1F2B; box-shadow: 0px 4px 6px rgba(0,0,0,0.3);"
  >
    <!-- Icon -->
    <div :class="['inline-flex h-12 w-12 items-center justify-center rounded-xl text-white', iconBg]">
      <slot name="icon" />
    </div>

    <!-- Content -->
    <div class="mt-5">
      <p class="text-sm font-medium" style="color: #94A3B8;">{{ title }}</p>
      <p class="mt-2 text-[30px] font-bold leading-tight" style="color: #F8FAFC;">{{ value }}</p>
      <p v-if="subtitle" class="mt-1 text-sm" style="color: #64748B;">{{ subtitle }}</p>
    </div>
  </div>
</template>