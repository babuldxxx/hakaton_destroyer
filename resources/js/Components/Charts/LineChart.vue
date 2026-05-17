<script setup>
import { Line } from 'vue-chartjs'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Tooltip,
  Filler
} from 'chart.js'

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Tooltip, Filler)

defineProps({
  chartData: Object
})

const options = {
  responsive: true,
  maintainAspectRatio: false,
  interaction: { mode: 'index', intersect: false },
  plugins: {
    legend: { display: false },
    tooltip: {
      backgroundColor: '#1A1F2B',
      titleColor: '#F8FAFC',
      bodyColor: '#F8FAFC',
      borderColor: '#2D3748',
      borderWidth: 1,
      padding: 12,
      displayColors: false,
      callbacks: {
        title: (items) => items[0].label,
        label: (ctx) => `revenue : ${ctx.parsed.y}`
      }
    }
  },
  scales: {
    x: {
      grid: { color: 'rgba(45, 55, 72, 0.4)', borderDash: [5, 5] },
      ticks: { color: '#94A3B8', font: { family: 'Inter', size: 12 } },
      border: { display: false }
    },
    y: {
      grid: { color: 'rgba(45, 55, 72, 0.4)', borderDash: [5, 5] },
      ticks: { color: '#94A3B8', stepSize: 100000, font: { family: 'Inter', size: 12 } },
      border: { display: false }
    }
  }
}
</script>

<template>
  <Line :data="chartData" :options="options" />
</template>