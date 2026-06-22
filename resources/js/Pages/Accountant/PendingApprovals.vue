<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { useConfirm } from '@/Composables/useConfirm';

const { confirmAction } = useConfirm();

const props = defineProps({ pendingStudents: Array });
const searchQuery = ref('');

// INSTANT SEARCH FILTER
const filteredStudents = computed(() => {
    if (!searchQuery.value) return props.pendingStudents;
    const term = searchQuery.value.toLowerCase();
    
    return props.pendingStudents.filter(student => {
        const nameMatch = student.name.toLowerCase().includes(term);
        const nicMatch = student.nic.toLowerCase().includes(term);
        const idMatch = String(student.member_id).padStart(4, '0').includes(term);
        return nameMatch || nicMatch || idMatch;
    });
});

const form = useForm({});

const approveStudent = async (studentId) => {
    const confirmed = await confirmAction({
        title: 'Approve Registration',
        message: 'Are you sure you want to approve this student? This will generate their tree nodes and pay the sponsor Rs 2000.',
        confirmText: 'Approve Student',
        confirmButtonTheme: 'success'
    });
    if (confirmed) form.post(`/accountant/approve-student/${studentId}`);
};

const logRemainder = async (studentId) => {
    const confirmed = await confirmAction({
        title: 'Confirm Final Payment',
        message: 'Confirm full payment received? This will mark the student as paid in full and send the final invoice.',
        confirmText: 'Mark Balance as Paid',
        confirmButtonTheme: 'success'
    });
    if (confirmed) form.post(`/accountant/mark-remainder-paid/${studentId}`);
};

const rejectStudent = async (studentId) => {
    const confirmed = await confirmAction({
        title: 'Reject Registration',
        message: 'Reject this payment? The student will be sent back to the agent for resubmission.',
        confirmText: 'Reject Payment',
        confirmButtonTheme: 'danger'
    });
    if (confirmed) form.post(`/accountant/reject-student/${studentId}`);
};

const voidRegistration = async (studentId) => {
    const confirmed = await confirmAction({
        title: 'Void & Delete Registration',
        message: 'WARNING: This will permanently delete the pending registration and free up the tree slot.',
        confirmText: 'Delete Registration',
        confirmButtonTheme: 'danger'
    });
    if (confirmed) form.delete(`/accountant/void-registration/${studentId}`);
};

const formatId = (id) => id ? String(id).padStart(4, '0') : '';

const getPlacementDisplay = (student) => {
    if (!student.intended_parent_node || !student.intended_parent_node.user) return `Node #${student.intended_parent_node_id}`;
    
    const pNode = student.intended_parent_node;
    const pId = formatId(pNode.user.member_id);
    let subType = 'Main';
    if (pNode.account_type === 'Sub_A') subType = 'L';
    if (pNode.account_type === 'Sub_B') subType = 'R';
    
    return `${pId} ${subType} (${student.intended_position} Leg)`;
};
</script>

<template>
    <Head title="Pending Approvals" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="font-bold text-2xl text-slate-800 tracking-tight">Approve Student Registrations</h2>
                <p class="text-sm text-slate-500 mt-1">Confirm students admission fees</p>
            </div>
        </template>

        <div class="py-12 bg-slate-50 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex flex-col md:flex-row md:justify-between md:items-center bg-slate-900 text-white gap-4">
                        <div class="flex items-center gap-4">
                            <h3 class="font-bold text-lg">Awaiting Payment Verification</h3>
                            <span class="bg-amber-500/20 text-amber-300 text-xs font-bold px-3 py-1 rounded-full border border-amber-500/30">
                                {{ pendingStudents.length }} Pending
                            </span>
                        </div>
                        
                        <div class="relative w-full md:w-80 text-slate-800">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                            </div>
                            <input v-model="searchQuery" type="text" class="block w-full pl-10 pr-3 py-2 border border-slate-700 rounded-xl leading-5 bg-slate-800 text-white placeholder-slate-400 focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm transition" placeholder="Search ID, Name, or NIC...">
                        </div>
                    </div>

                    <div v-if="filteredStudents.length === 0" class="p-12 text-center text-slate-500 font-medium">
                        <svg class="w-16 h-16 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span v-if="searchQuery">No registrations found matching "{{ searchQuery }}".</span>
                        <span v-else>No pending registrations. You are all caught up!</span>
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="w-full text-left border-collapse whitespace-nowrap">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase tracking-widest text-slate-500">
                                    <th class="p-4 font-bold pl-6">Student Details</th>
                                    <th class="p-4 font-bold">Placement (Tree)</th>
                                    <th class="p-4 font-bold">Financials</th>
                                    <th class="p-4 font-bold text-right pr-6">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="student in filteredStudents" :key="student.id" class="hover:bg-slate-50 transition">
                                    <td class="p-4 pl-6 align-top">
                                        <div class="font-bold text-slate-800">{{ student.name }} <span class="text-[10px] text-slate-400 ml-1 font-mono">#{{ formatId(student.member_id) }}</span></div>
                                        <div class="text-xs text-slate-500">NIC: {{ student.nic }}</div>
                                        <div class="text-[10px] text-teal-600 font-bold mt-1 uppercase tracking-widest">Sponsor: {{ student.sponsor?.name }}</div>
                                    </td>

                                    <td class="p-4 align-top">
                                        <div class="font-bold text-slate-800">{{ getPlacementDisplay(student) }}</div>
                                        <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-1">Reserved Slot</div>
                                    </td>

                                    <td class="p-4 align-top">
                                        <template v-if="student.payment_transaction_id?.includes('(BALANCE)')">
                                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Verifying Balance</div>
                                            <div class="font-black text-emerald-600 text-lg">Rs {{ 15000 - student.admission_fee_paid }}</div>
                                        </template>

                                        <template v-else>
                                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Verifying Initial</div>
                                            <div class="font-black text-emerald-600 text-lg">Rs {{ student.admission_fee_paid }}</div>
                                            
                                            <div v-if="student.admission_fee_paid < 15000" class="text-[10px] text-rose-500 font-bold bg-rose-50 px-2 py-0.5 rounded inline-block mt-1 border border-rose-100">
                                                PARTIAL PAYMENT
                                            </div>
                                        </template>

                                        <div class="text-[10px] text-slate-500 uppercase font-bold tracking-wider mt-2">
                                            Trx: <span class="text-slate-800">{{ student.payment_transaction_id?.replace(' (BALANCE)', '') }}</span>
                                        </div>
                                    </td>

                                    <td class="p-4 align-top text-right space-y-2 pr-6">
                                        <template v-if="student.admission_status === 'Pending Approval'">
                                            <div v-if="!student.payment_transaction_id?.includes('(BALANCE)')">
                                                <button @click="approveStudent(student.id)" 
                                                        class="inline-block bg-slate-900 hover:bg-teal-600 text-white font-bold text-xs py-2 px-4 rounded-xl shadow-sm transition whitespace-nowrap">
                                                    Approve Initial Payment
                                                </button>
                                            </div>
                                            <div v-else>
                                                <button @click="logRemainder(student.id)" 
                                                        class="inline-block bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs py-2 px-4 rounded-xl shadow-sm transition whitespace-nowrap">
                                                    Verify Final Balance
                                                </button>
                                            </div>                                           
                                            <div>
                                                <button @click="rejectStudent(student.id)" 
                                                        class="inline-block bg-rose-50 hover:bg-rose-500 hover:text-white text-rose-600 border border-rose-200 font-bold text-xs py-2 px-4 rounded-xl transition whitespace-nowrap">
                                                    Reject Payment
                                                </button>
                                            </div>
                                        </template>
                                        <div v-if="student.admission_status !== 'Active'">
                                            <button @click="voidRegistration(student.id)" 
                                                    class="inline-block bg-white hover:bg-slate-800 text-slate-500 hover:text-white border border-slate-200 font-bold text-xs py-2 px-4 rounded-xl transition whitespace-nowrap">
                                                Void & Delete Slot
                                            </button>
                                        </div>
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