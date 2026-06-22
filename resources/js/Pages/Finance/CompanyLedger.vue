<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    transactions: Object,
    metrics: Object,
    filters: Object
});

const searchQuery = ref(props.filters.search || '');

let searchTimeout;
watch(searchQuery, (value) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get('/company-ledger', { search: value }, {
            preserveState: true,
            replace: true
        });
    }, 300);
});

const formatDate = (dateString) => {
    const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    return new Date(dateString).toLocaleDateString(undefined, options);
};
</script>

<template>
    <Head title="Company Ledger" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-bold text-2xl text-slate-800 tracking-tight">Company Ledger & Treasury</h2>
        </template>

        <div class="py-12 bg-slate-50 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-[2rem] p-8 text-white shadow-xl relative overflow-hidden">
                        <div class="absolute top-0 right-0 -mt-16 -mr-16 w-64 h-64 bg-teal-500 rounded-full opacity-10 blur-3xl pointer-events-none"></div>
                        <div class="text-xs text-slate-400 font-bold uppercase tracking-widest mb-1">Live Bank Treasury</div>
                        <div class="text-4xl font-black text-emerald-400 tracking-tight">Rs {{ metrics.liveCash.toLocaleString() }}</div>
                        <div class="mt-4 text-[10px] font-bold tracking-wide uppercase text-slate-400 border border-slate-600 inline-block px-3 py-1.5 rounded-lg">Available Liquid Cash</div>
                    </div>

                    <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-slate-200 relative overflow-hidden">
                        <div class="absolute top-0 right-0 -mt-16 -mr-16 w-64 h-64 bg-rose-500 rounded-full opacity-5 blur-3xl pointer-events-none"></div>
                        <div class="text-xs text-slate-500 font-bold uppercase tracking-widest mb-1">Pending Salaries Owed</div>
                        <div class="text-4xl font-black text-rose-500 tracking-tight">Rs {{ metrics.pendingSalaries.toLocaleString() }}</div>
                        <div class="mt-4 text-[10px] font-bold tracking-wide uppercase text-slate-500 bg-slate-100 border border-slate-200 inline-block px-3 py-1.5 rounded-lg">Unpaid Agent Commissions</div>
                    </div>

                    <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-slate-200 relative overflow-hidden">
                        <div class="absolute top-0 right-0 -mt-16 -mr-16 w-64 h-64 bg-blue-500 rounded-full opacity-5 blur-3xl pointer-events-none"></div>
                        <div class="text-xs text-slate-500 font-bold uppercase tracking-widest mb-1">All-Time Cash Disbursed</div>
                        <div class="text-4xl font-black text-slate-800 tracking-tight">Rs {{ metrics.allTimeDisbursed.toLocaleString() }}</div>
                        <div class="mt-4 text-[10px] font-bold tracking-wide uppercase text-slate-500 bg-slate-100 border border-slate-200 inline-block px-3 py-1.5 rounded-lg">Total Withdrawals Processed</div>
                    </div>

                </div>

                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden">
                    
                    <div class="p-4 border-b border-slate-100 bg-white">
                        <div class="relative max-w-md">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                            </div>
                            <input v-model="searchQuery" type="text" class="block w-full pl-10 pr-3 py-2 border border-slate-200 rounded-xl leading-5 bg-slate-50 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-teal-500 sm:text-sm transition" placeholder="Search by Agent Name or NIC...">
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse whitespace-nowrap">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-[10px] uppercase tracking-widest text-slate-500">
                                    <th class="p-4 pl-6 font-bold">Transaction Date</th>
                                    <th class="p-4 font-bold">Payee Details</th>
                                    <th class="p-4 font-bold">Description</th>
                                    <th class="p-4 font-bold text-right pr-6">Amount Paid</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="trx in transactions.data" :key="trx.id" class="hover:bg-slate-50 transition">
                                    <td class="p-4 pl-6 text-sm text-slate-500 font-medium">
                                        {{ formatDate(trx.created_at) }}
                                    </td>
                                    <td class="p-4">
                                        <div class="font-bold text-slate-800">{{ trx.user?.name }}</div>
                                        <div class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">{{ trx.user?.nic }}</div>
                                    </td>
                                    <td class="p-4 text-xs text-slate-600">
                                        {{ trx.description }}
                                    </td>
                                    <td class="p-4 text-right pr-6 font-black text-slate-800">
                                        Rs {{ Math.abs(trx.amount).toLocaleString() }}
                                    </td>
                                </tr>
                                <tr v-if="transactions.data.length === 0">
                                    <td colspan="4" class="p-12 text-center text-slate-500 font-medium">No disbursed transactions found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>