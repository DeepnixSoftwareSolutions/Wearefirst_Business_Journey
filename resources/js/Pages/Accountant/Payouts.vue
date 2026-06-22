<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import { useConfirm } from '@/Composables/useConfirm';

const { confirmAction } = useConfirm();

const props = defineProps({
    eligibleAgents: Array,
    totalLiability: Number,
    pendingRequests: Array,
    filters: Object
});

// State
const selectedAgents = ref([]);
const searchQuery = ref(props.filters.search || '');

// Real-time Search functionality (debounced slightly to prevent API spam)
let searchTimeout;
watch(searchQuery, (value) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get('/accountant/eligible-payouts', { search: value }, {
            preserveState: true,
            replace: true
        });
    }, 300);
});

// Select All Toggle
const toggleAll = (event) => {
    if (event.target.checked) {
        selectedAgents.value = props.eligibleAgents.map(a => a.id);
    } else {
        selectedAgents.value = [];
    }
};

const form = useForm({
    user_ids: [] // Matched to your controller
});

const submitPayouts = async () => { // ADD ASYNC
    if (selectedAgents.value.length === 0) return;

    const confirmed = await confirmAction({
        title: 'Submit Salary Requests',
        message: `Are you sure you want to lock in ${selectedAgents.value.length} payout requests? Their balances will reset to 0, and the Admin will be notified to approve the bank transfers.`,
        confirmText: 'Submit to Admin',
        confirmButtonTheme: 'primary'
    });

    if (confirmed) {
        form.user_ids = selectedAgents.value;
        form.post('/accountant/submit-payouts', {
            onSuccess: () => selectedAgents.value = []
        });
    }
};

const selectedTotal = computed(() => {
    return props.eligibleAgents
        .filter(a => selectedAgents.value.includes(a.id))
        .reduce((sum, agent) => sum + Number(agent.pending_payout_balance), 0);
});
</script>

<template>
    <Head title="Salary Requests" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
                Request Salary Approvals
            </h2>
        </template>

        <div class="py-12 bg-slate-50 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <div class="bg-slate-900 rounded-[2rem] p-8 text-white shadow-xl flex justify-between items-center relative overflow-hidden border border-slate-700">
                    <div class="absolute top-0 right-0 -mt-16 -mr-16 w-64 h-64 bg-teal-500 rounded-full opacity-10 blur-3xl pointer-events-none"></div>
                    
                    <div>
                        <div class="text-sm text-slate-400 font-bold uppercase tracking-widest mb-1">Total System Liability</div>
                        <div class="text-4xl font-black text-white">Rs {{ totalLiability }}</div>
                        <div class="text-sm text-teal-400 mt-2">{{ eligibleAgents.length }} Agents Ready for Submission</div>
                    </div>
                    
                    <div class="text-right z-10">
                        <div class="text-sm text-slate-400 font-bold uppercase tracking-widest mb-1">Selected for Admin Approval</div>
                        <div class="text-3xl font-black text-emerald-400">Rs {{ selectedTotal }}</div>
                        <button @click="submitPayouts" :disabled="form.processing || selectedAgents.length === 0" class="mt-4 bg-teal-500 hover:bg-teal-400 text-slate-900 font-black py-3 px-6 rounded-xl shadow-lg transition disabled:opacity-50 disabled:bg-slate-700 disabled:text-slate-400">
                            {{ form.processing ? 'Submitting...' : 'Submit to Admin' }}
                        </button>
                    </div>
                </div>

                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden">
                    
                    <div class="p-4 border-b border-slate-100 bg-slate-50">
                        <div class="relative max-w-md">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                            </div>
                            <input v-model="searchQuery" type="text" class="block w-full pl-10 pr-3 py-2 border border-slate-300 rounded-xl leading-5 bg-white placeholder-slate-400 focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm transition" placeholder="Search by Member ID, Agent Name, or NIC...">
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse whitespace-nowrap">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase tracking-widest text-slate-500">
                                    <th class="p-4 pl-6 w-16">
                                        <input type="checkbox" @change="toggleAll" :checked="selectedAgents.length === eligibleAgents.length && eligibleAgents.length > 0" class="rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                                    </th>
                                    <th class="p-4 font-bold">Agent Name</th>
                                    <th class="p-4 font-bold">Badge / Contact</th>
                                    <th class="p-4 font-bold text-right pr-6">Pending Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="agent in eligibleAgents" :key="agent.id" class="hover:bg-slate-50 transition" :class="{'bg-teal-50/50': selectedAgents.includes(agent.id)}">
                                    <td class="p-4 pl-6">
                                        <input type="checkbox" :value="agent.id" v-model="selectedAgents" class="rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                                    </td>
                                    <td class="p-4 font-bold text-slate-800">{{ agent.name }}</td>
                                    <td class="p-4">
                                        <div class="text-xs font-bold text-teal-600 uppercase tracking-widest">{{ agent.badge }}</div>
                                        <div class="text-xs text-slate-400">{{ agent.phone || agent.nic }}</div>
                                    </td>
                                    <td class="p-4 text-right pr-6 font-black text-lg text-emerald-600">
                                        Rs {{ agent.pending_payout_balance }}
                                    </td>
                                </tr>
                                <tr v-if="eligibleAgents.length === 0">
                                    <td colspan="4" class="p-12 text-center text-slate-500 font-medium">
                                        <span v-if="searchQuery">No agents found matching "{{ searchQuery }}".</span>
                                        <span v-else>No agents currently have a pending balance.</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div v-if="pendingRequests && pendingRequests.length > 0" class="mt-12 pt-6">
                    <h3 class="text-lg font-black text-slate-800 flex items-center tracking-tight mb-4">
                        <svg class="w-5 h-5 mr-2 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Previously Submitted (Awaiting Admin)
                    </h3>
                    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden opacity-75 grayscale-[20%]">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse whitespace-nowrap">
                                <thead>
                                    <tr class="bg-slate-100 border-b border-slate-200 text-[10px] uppercase tracking-widest text-slate-500">
                                        <th class="p-4 pl-6 font-bold">Agent Name</th>
                                        <th class="p-4 font-bold">Badge / Contact</th>
                                        <th class="p-4 font-bold">Status</th>
                                        <th class="p-4 font-bold text-right pr-6">Locked Amount</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <tr v-for="req in pendingRequests" :key="req.id" class="bg-slate-50">
                                        <td class="p-4 pl-6 font-bold text-slate-700">{{ req.user?.name }}</td>
                                        <td class="p-4">
                                            <div class="text-xs font-bold text-slate-500 uppercase tracking-widest">{{ req.user?.badge }}</div>
                                            <div class="text-[10px] text-slate-400">{{ req.user?.nic }}</div>
                                        </td>
                                        <td class="p-4">
                                            <span class="bg-amber-100 text-amber-800 text-[10px] font-black px-2.5 py-1 rounded-lg uppercase tracking-widest border border-amber-200">
                                                Pending Admin
                                            </span>
                                        </td>
                                        <td class="p-4 text-right pr-6 font-black text-amber-600">
                                            Rs {{ req.amount }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>