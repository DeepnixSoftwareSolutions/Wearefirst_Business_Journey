<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import { useConfirm } from '@/Composables/useConfirm';

const { confirmAction } = useConfirm();

const props = defineProps({
    pendingRequests: Array,
    totalAmount: Number,
    filters: Object
});

const page = usePage();
const selectedRequests = ref([]);
const searchQuery = ref(props.filters.search || '');

let searchTimeout;
watch(searchQuery, (value) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get('/manager/pending-payouts', { search: value }, {
            preserveState: true,
            replace: true
        });
    }, 300);
});

const toggleAll = (event) => {
    if (event.target.checked) {
        selectedRequests.value = props.pendingRequests.map(r => r.id);
    } else {
        selectedRequests.value = [];
    }
};

const form = useForm({
    request_ids: []
});

const approvePayouts = async () => {
    if (selectedRequests.value.length === 0) return;
    
    const confirmed = await confirmAction({
        title: 'Authorize Payouts',
        message: `AUTHORIZATION REQUIRED: You are about to authorize bank transfers for ${selectedRequests.value.length} agents. This action is final and will reflect in the master ledger.`,
        confirmText: 'Authorize Selected',
        confirmButtonTheme: 'primary'
    });

    if (confirmed) {
        form.transform((data) => ({
            ...data,
            request_ids: selectedRequests.value
        })).post('/manager/approve-payouts', {
            preserveScroll: true,
            onSuccess: () => selectedRequests.value = []
        });
    }
};

const rejectPayouts = async () => {
    if (selectedRequests.value.length === 0) return;
    
    const confirmed = await confirmAction({
        title: 'Reject Payouts',
        message: `Are you sure you want to reject ${selectedRequests.value.length} payout request(s)? The locked funds will be safely refunded back to the agents' pending balances.`,
        confirmText: 'Reject & Refund',
        confirmButtonTheme: 'danger'
    });

    if (confirmed) {
        form.transform((data) => ({ ...data, request_ids: selectedRequests.value }))
            .post('/manager/reject-payouts', {
                preserveScroll: true,
                onSuccess: () => selectedRequests.value = []
            });
    }
};

const selectedTotal = computed(() => {
    return props.pendingRequests
        .filter(r => selectedRequests.value.includes(r.id))
        .reduce((sum, req) => sum + Number(req.amount), 0);
});
</script>

<template>
    <Head title="Admin: Approve Payouts" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
                Approve Salary Requests
            </h2>
        </template>

        <div class="py-12 bg-slate-50 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <div v-if="form.errors.request_ids" class="p-4 bg-rose-100 text-rose-800 border border-rose-200 rounded-xl font-bold flex items-center gap-2 shadow-sm">
                    ⚠️ Validation Error: {{ form.errors.request_ids }}
                </div>
                <div v-if="page.props.errors?.error" class="p-4 bg-rose-100 text-rose-800 border border-rose-200 rounded-xl font-bold flex items-center gap-2 shadow-sm">
                    ⚠️ System Error: {{ page.props.errors.error }}
                </div>

                <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 rounded-[2rem] p-8 text-white shadow-2xl flex flex-col md:flex-row justify-between md:items-center relative overflow-hidden border border-slate-700/50 gap-6">
                    <div class="absolute top-0 left-0 -mt-16 -ml-16 w-64 h-64 bg-indigo-500 rounded-full opacity-10 blur-3xl pointer-events-none"></div>
                    
                    <div class="relative z-10">
                        <div class="text-xs text-slate-400 font-bold uppercase tracking-widest mb-1 flex items-center">
                            <svg class="w-4 h-4 mr-1 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            Total Pending Authorization
                        </div>
                        <div class="text-4xl font-black text-white tracking-tight">Rs {{ totalAmount }}</div>
                        <div class="text-sm font-medium text-indigo-300 mt-2 bg-indigo-900/30 inline-block px-3 py-1 rounded-lg border border-indigo-500/20">
                            {{ pendingRequests.length }} Verified Requests
                        </div>
                    </div>
                    
                    <div class="md:text-right relative z-10 border-t md:border-t-0 border-slate-700/50 pt-4 md:pt-0 mt-2 md:mt-0">
                        <div class="text-xs text-slate-400 font-bold uppercase tracking-widest mb-1">Selected Amount</div>
                        <div class="text-3xl font-black text-emerald-400">Rs {{ selectedTotal }}</div>
                        
                        <div class="mt-4 flex flex-wrap md:justify-end gap-3">
                            <button @click="rejectPayouts" :disabled="form.processing || selectedRequests.length === 0" class="bg-rose-500 hover:bg-rose-600 text-white font-bold py-3 px-6 rounded-xl shadow-lg shadow-rose-500/20 transition-all disabled:opacity-50 disabled:bg-slate-800 disabled:text-slate-500 disabled:shadow-none">
                                Reject
                            </button>
                            <button @click="approvePayouts" :disabled="form.processing || selectedRequests.length === 0" class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-indigo-600/30 transition-all disabled:opacity-50 disabled:bg-slate-800 disabled:text-slate-500 disabled:shadow-none">
                                {{ form.processing ? 'Processing...' : 'Authorize Selected' }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden relative">
                    
                    <div class="p-4 border-b border-slate-100 bg-white flex items-center justify-between">
                        <div class="relative w-full max-w-md">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                            </div>
                            <input v-model="searchQuery" type="text" class="block w-full pl-10 pr-3 py-2 border border-slate-200 rounded-xl leading-5 bg-slate-50 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition" placeholder="Search by Member ID, Agent Name, or NIC...">
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse whitespace-nowrap">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-[10px] uppercase tracking-widest text-slate-500">
                                    <th class="p-4 pl-6 w-16">
                                        <input type="checkbox" @change="toggleAll" :checked="selectedRequests.length === pendingRequests.length && pendingRequests.length > 0" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    </th>
                                    <th class="p-4 font-bold">Agent / Payee</th>
                                    <th class="p-4 font-bold">Contact Details</th>
                                    <th class="p-4 font-bold">Batch Origin</th>
                                    <th class="p-4 font-bold text-right pr-6">Amount to Pay</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="request in pendingRequests" :key="request.id" class="hover:bg-slate-50 transition" :class="{'bg-indigo-50/40': selectedRequests.includes(request.id)}">
                                    <td class="p-4 pl-6">
                                        <input type="checkbox" :value="request.id" v-model="selectedRequests" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    </td>
                                    <td class="p-4">
                                        <div class="font-bold text-slate-800">{{ request.user?.name }}</div>
                                        <div class="text-xs font-bold text-indigo-600 uppercase tracking-widest mt-0.5">{{ request.user?.badge }}</div>
                                    </td>
                                    <td class="p-4">
                                        <div class="text-xs text-slate-600 font-medium">NIC: {{ request.user?.nic }}</div>
                                        <div class="text-xs text-slate-400">{{ request.user?.phone }}</div>
                                    </td>
                                    <td class="p-4">
                                        <div class="text-xs text-slate-500">Requested By:</div>
                                        <div class="text-xs font-bold text-slate-700">{{ request.requested_by?.name }}</div>
                                    </td>
                                    <td class="p-4 text-right pr-6 font-black text-lg text-slate-800">
                                        Rs {{ request.amount }}
                                    </td>
                                </tr>
                                <tr v-if="pendingRequests.length === 0">
                                    <td colspan="5" class="p-12 text-center text-slate-500 font-medium">
                                        <span v-if="searchQuery">No requests found matching "{{ searchQuery }}".</span>
                                        <span v-else>All clear! No pending payouts require authorization.</span>
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