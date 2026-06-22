<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Line } from 'vue-chartjs';
import { Chart as ChartJS, CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend } from 'chart.js';

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend);

const props = defineProps(['revenueData', 'expenseData', 'labels', 'stats']);

const chartData = {
    labels: props.labels,
    datasets: [
        { label: 'Revenue (Rs)', data: props.revenueData, borderColor: '#10b981', backgroundColor: '#10b981' },
        { label: 'Payouts (Rs)', data: props.expenseData, borderColor: '#f43f5e', backgroundColor: '#f43f5e' }
    ]
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="py-12 max-w-7xl mx-auto px-6">
            <h2 class="text-2xl font-bold mb-6">Company Analytics (Last 7 Days)</h2>
            <div class="grid grid-cols-4 gap-6 mb-8">
                <div v-for="(val, key) in stats" :key="key" class="bg-white p-6 rounded-2xl shadow-sm border">
                    <div class="text-xs uppercase text-slate-400 font-bold">{{ key.replace('_', ' ') }}</div>
                    <div class="text-2xl font-black mt-2">{{ val }}</div>
                </div>
            </div>
            <div class="bg-white p-8 rounded-3xl border shadow-sm">
                <Line :data="chartData" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>