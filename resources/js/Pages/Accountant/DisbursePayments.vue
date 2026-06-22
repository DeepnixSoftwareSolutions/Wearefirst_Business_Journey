<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { useConfirm } from '@/Composables/useConfirm';

const { confirmAction } = useConfirm();
const props = defineProps({ approvedRequests: Array });

const selectedRequests = ref([]);
const form = useForm({ request_ids: [] });
const searchQuery = ref('');

// INSTANT SEARCH FILTER
const filteredRequests = computed(() => {
    if (!searchQuery.value) return props.approvedRequests;
    const term = searchQuery.value.toLowerCase();
    
    return props.approvedRequests.filter(req => {
        const nameMatch = req.user?.name.toLowerCase().includes(term);
        const nicMatch = req.user?.nic.toLowerCase().includes(term);
        const idMatch = String(req.user?.member_id).padStart(4, '0').includes(term);
        return nameMatch || nicMatch || idMatch;
    });
});

const toggleAll = (event) => {
    selectedRequests.value = event.target.checked ? filteredRequests.value.map(r => r.id) : [];
};

const markAsPaid = async () => {
    if (selectedRequests.value.length === 0) return;
    
    const confirmed = await confirmAction({
        title: 'Confirm Bank Transfers',
        message: `Did you complete the bank transfers? This will mark ${selectedRequests.value.length} requests as PAID and deduct the money from their system balances permanently.`,
        confirmText: 'Confirm Paid',
        confirmButtonTheme: 'success'
    });

    if (confirmed) {
        form.request_ids = selectedRequests.value;
        form.post('/accountant/mark-payouts-paid', {
            onSuccess: () => selectedRequests.value = []
        });
    }
};

const selectedTotal = computed(() => {
    return props.approvedRequests
        .filter(r => selectedRequests.value.includes(r.id))
        .reduce((sum, req) => sum + Number(req.amount), 0);
});

const formatId = (id) => id ? String(id).padStart(4, '0') : '';
</script>

<template>
    <Head title="Disburse Payments" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-bold text-2xl text-slate-800 tracking-tight">Disburse Approved Salaries</h2>
        </template>

        <div class="py-12 bg-slate-50 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <div class="bg-indigo-900 rounded-[2rem] p-8 text-white shadow-xl flex flex-col md:flex-row justify-between md:items-center relative overflow-hidden border border-indigo-700 gap-6">
                    <div class="absolute top-0 right-0 -mt-16 -mr-16 w-64 h-64 bg-emerald-500 rounded-full opacity-20 blur-3xl pointer-events-none"></div>
                    
                    <div class="relative z-10">
                        <div class="text-sm text-indigo-300 font-bold uppercase tracking-widest mb-1">Awaiting Bank Transfer</div>
                        <div class="text-4xl font-black text-white">{{ approvedRequests.length }} Approved Requests</div>
                    </div>
                    
                    <div class="md:text-right relative z-10 border-t md:border-t-0 border-indigo-700 pt-4 md:pt-0 mt-2 md:mt-0">
                        <div class="text-sm text-indigo-300 font-bold uppercase tracking-widest mb-1">Selected for Marking Paid</div>
                        <div class="text-3xl font-black text-emerald-400">Rs {{ selectedTotal }}</div>
                        <button @click="markAsPaid" :disabled="form.processing || selectedRequests.length === 0" class="mt-4 bg-emerald-500 hover:bg-emerald-400 text-slate-900 font-black py-3 px-6 rounded-xl shadow-lg shadow-emerald-500/20 transition-all disabled:opacity-50 disabled:bg-slate-700 disabled:text-slate-400 disabled:shadow-none">
                            {{ form.processing ? 'Processing...' : 'Confirm Bank Transfers Complete' }}
                        </button>
                    </div>
                </div>

                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden">
                    <div class="p-4 border-b border-slate-100 bg-slate-50 flex items-center">
                        <div class="relative w-full max-w-md">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                            </div>
                            <input v-model="searchQuery" type="text" class="block w-full pl-10 pr-3 py-2 border border-slate-300 rounded-xl leading-5 bg-white placeholder-slate-400 focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm transition" placeholder="Search by Member ID, Name, or NIC...">
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse whitespace-nowrap">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase tracking-widest text-slate-500">
                                    <th class="p-4 pl-6 w-16">
                                        <input type="checkbox" @change="toggleAll" :checked="selectedRequests.length === filteredRequests.length && filteredRequests.length > 0" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                                    </th>
                                    <th class="p-4 font-bold">Agent Details</th>
                                    <th class="p-4 font-bold">Contact</th>
                                    <th class="p-4 font-bold">Bank Information</th>
                                    <th class="p-4 font-bold text-right pr-6">Amount to Pay</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="req in filteredRequests" :key="req.id" class="hover:bg-slate-50 transition" :class="{'bg-emerald-50/50': selectedRequests.includes(req.id)}">
                                    
                                    <td class="p-4 pl-6">
                                        <input type="checkbox" :value="req.id" v-model="selectedRequests" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                                    </td>
                                    
                                    <td class="p-4 font-bold text-slate-800">
                                        {{ req.user.name }} <span class="text-[10px] text-slate-400 ml-1 font-mono">#{{ formatId(req.user.member_id) }}</span>
                                        <div class="text-[10px] text-emerald-600 bg-emerald-50 border border-emerald-100 font-bold uppercase mt-1 inline-block px-1.5 py-0.5 rounded">Admin Approved</div>
                                    </td>
                                    
                                    <td class="p-4">
                                        <div class="text-xs font-bold text-indigo-600 uppercase tracking-widest">{{ req.user.badge }}</div>
                                        <div class="text-xs text-slate-500 mt-0.5">{{ req.user.phone || req.user.nic }}</div>
                                    </td>

                                    <td class="p-4">
                                        <div v-if="req.user.bank_details" class="bg-slate-50 border border-slate-100 rounded-xl p-2.5 inline-block min-w-[200px]">
                                            <div class="text-xs font-black text-slate-800 flex items-center gap-1.5 mb-1">
                                                🏦 {{ req.user.bank_details.bank_name }} <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest bg-white px-1 border rounded">{{ req.user.bank_details.branch }}</span>
                                            </div>
                                            <div class="text-[11px] font-mono font-bold text-indigo-600 mb-0.5">Acc: {{ req.user.bank_details.account_no }}</div>
                                            <div class="text-[10px] text-slate-500 font-bold uppercase truncate">{{ req.user.bank_details.account_name }}</div>
                                        </div>
                                        <div v-else class="text-[10px] font-bold text-rose-500 bg-rose-50 px-2.5 py-1.5 rounded-lg inline-flex items-center gap-1.5 border border-rose-100 shadow-sm">
                                            <span>⚠️</span> No Bank Details Provided
                                        </div>
                                    </td>

                                    <td class="p-4 text-right pr-6 font-black text-xl text-emerald-600">
                                        Rs {{ req.amount }}
                                    </td>
                                </tr>
                                <tr v-if="filteredRequests.length === 0">
                                    <td colspan="5" class="p-12 text-center text-slate-500 font-medium">
                                        <span v-if="searchQuery">No payouts found matching "{{ searchQuery }}".</span>
                                        <span v-else>No approved payouts awaiting transfer.</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>