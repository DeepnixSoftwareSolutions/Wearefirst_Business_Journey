<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    transactions: Object,
    stats: Object
});

const filters = ref({
    type: ''
});

// Watch for filter changes to refresh the ledger automatically
watch(filters, (newFilters) => {
    router.get(route('ledger'), newFilters, {
        preserveState: true,
        replace: true
    });
}, { deep: true });

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', { 
        year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' 
    });
};

const getBadgeClass = (type) => {
    switch (type) {
        case 'Direct Referral':
        case 'Direct Commission': return 'bg-blue-100 text-blue-700 border-blue-200';
        case 'Pair Bonus':
        case 'Pair Balance': return 'bg-purple-100 text-purple-700 border-purple-200';
        case 'Payout Withdrawal':
        case 'Salary Payout': return 'bg-slate-200 text-slate-700 border-slate-300';
        default: return 'bg-gray-100 text-gray-700 border-gray-200';
    }
};

const formatId = (id) => id ? String(id).padStart(4, '0') : '';

const getNodeDisplay = (node) => {
    if (!node || !node.user) return `#${node.id}`; // Fallback if data is missing
    
    const memId = formatId(node.user.member_id);
    let subLeg = '';
    
    if (node.account_type === 'Sub_A') subLeg = ' L';
    if (node.account_type === 'Sub_B') subLeg = ' R';
    // 'Main' remains blank, just returning the member_id
    return `${memId}${subLeg}`;
};
</script>

<template>
    <Head title="Transaction Ledger" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-bold text-2xl text-slate-800 tracking-tight">Financial Ledger</h2>
        </template>

        <div class="py-12 bg-slate-50 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm flex items-center justify-between">
                        <div>
                            <div class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Lifetime Earned</div>
                            <div class="text-3xl font-black text-emerald-600">Rs {{ stats.total_earned }}</div>
                        </div>
                        <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-500">💰</div>
                    </div>
                    
                    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm flex items-center justify-between">
                        <div>
                            <div class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Total Withdrawn</div>
                            <div class="text-3xl font-black text-slate-800">Rs {{ stats.total_withdrawn }}</div>
                        </div>
                        <div class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-500">🏦</div>
                    </div>

                    <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-3xl p-6 text-white shadow-lg flex items-center justify-between relative overflow-hidden">
                        <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-teal-500 rounded-full opacity-20 blur-2xl"></div>
                        <div class="relative z-10">
                            <div class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Pending Friday Payout</div>
                            <div class="text-3xl font-black text-white">Rs {{ stats.current_pending }}</div>
                        </div>
                        <div class="relative z-10 w-12 h-12 bg-white/10 backdrop-blur-sm rounded-2xl flex items-center justify-center text-teal-400">⏳</div>
                    </div>
                </div>

                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-center gap-4">
                        <h3 class="font-bold text-lg text-slate-800">Transaction History</h3>
                        <select v-model="filters.type" class="text-sm border-slate-200 rounded-xl bg-slate-50 focus:ring-teal-500">
                            <option value="">All Transactions</option>
                            <option value="Direct Referral">Direct Referrals</option>
                            <option value="Pair Balance">Pair Bonuses</option>
                            <option value="Payout Withdrawal">Payouts</option>
                        </select>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse whitespace-nowrap">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase tracking-widest text-slate-500">
                                    <th class="p-4 pl-6 font-bold">Date & Time</th>
                                    <th class="p-4 font-bold">Description</th>
                                    <th class="p-4 font-bold">Type</th>
                                    <th class="p-4 font-bold text-right pr-6">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="transaction in transactions.data" :key="transaction.id" class="hover:bg-slate-50 transition">
                                    <td class="p-4 pl-6 text-sm text-slate-500 font-medium">
                                        {{ formatDate(transaction.created_at) }}
                                    </td>
                                    <td class="p-4">
                                        <div class="text-sm font-bold text-slate-800">{{ transaction.description }}</div>
                                        <div v-if="transaction.node" class="text-xs text-slate-500 font-bold mt-0.5">
                                            Origin Account: <span class="bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200">{{ getNodeDisplay(transaction.node) }}</span>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <span class="px-3 py-1 text-[10px] font-bold uppercase tracking-widest rounded-full border" :class="getBadgeClass(transaction.type)">
                                            {{ transaction.type }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-right pr-6">
                                        <div class="text-lg font-black" :class="transaction.amount > 0 ? 'text-emerald-600' : 'text-slate-800'">
                                            {{ transaction.amount > 0 ? '+' : '' }}Rs {{ Math.abs(transaction.amount) }}
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="p-4 border-t border-slate-100 bg-slate-50 flex justify-center">
                        <div class="flex space-x-1">
                            <Link v-for="(link, key) in transactions.links" :key="key" 
                                  :href="link.url || '#'" v-html="link.label"
                                  class="px-4 py-2 text-sm font-bold rounded-lg transition border"
                                  :class="[
                                    link.active ? 'bg-teal-600 text-white border-teal-600' : 'bg-white text-slate-600 border-slate-200',
                                    !link.url ? 'text-slate-300 cursor-not-allowed' : 'hover:bg-slate-100'
                                  ]">
                            </Link>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>