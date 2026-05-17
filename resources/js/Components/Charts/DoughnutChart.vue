<script setup>
import { Doughnut } from 'vue-chartjs'
import { Chart as ChartJS, ArcElement, Tooltip } from 'chart.js'

ChartJS.register(ArcElement, Tooltip)

defineProps({
  chartData: Object
})

const options = {
  responsive: true,
  maintainAspectRatio: false,
  cutout: '75%',
  plugins: {
    legend: { display: false },
    tooltip: {
      backgroundColor: '#1A1F2B',
      titleColor: '#F8FAFC',
      bodyColor: '#F8FAFC',
      borderColor: '#2D3748',
      borderWidth: 1,
      padding: 12
    }
  }
}
</script>

<template>
  <Doughnut :data="chartData" :options="options" />
</template>