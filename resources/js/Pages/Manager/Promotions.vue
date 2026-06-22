<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { useConfirm } from '@/Composables/useConfirm';
import { ref, computed } from 'vue'; // Added imports

const { confirmAction } = useConfirm();

const props = defineProps({ students: Object });
const form = useForm({});
const searchQuery = ref('');

// INSTANT SEARCH FILTER
const filteredStudents = computed(() => {
    if (!searchQuery.value) return props.students.data;
    const term = searchQuery.value.toLowerCase();
    
    return props.students.data.filter(student => {
        const nameMatch = student.name.toLowerCase().includes(term);
        const nicMatch = student.nic?.toLowerCase().includes(term);
        const idMatch = String(student.member_id).padStart(4, '0').includes(term);
        return nameMatch || nicMatch || idMatch;
    });
});

const promoteStudent = async (id, name) => {
    const confirmed = await confirmAction({
        title: 'Promote Student',
        message: `Are you sure you want to graduate ${name} to an Agent? This will send their login credentials via email.`,
        confirmText: 'Promote to Agent',
        confirmButtonTheme: 'primary'
    });

    if (confirmed) {
        form.post(`/manager/promote/${id}`);
    }
};

// --- FORMATTING HELPERS ---
const formatId = (id) => id ? String(id).padStart(4, '0') : '';

const getPlacementDisplay = (student) => {
    if (!student.intended_parent_node || !student.intended_parent_node.user) return `N/A (Root)`;
    
    const pNode = student.intended_parent_node;
    const pId = formatId(pNode.user.member_id);
    let subType = 'Main';
    if (pNode.account_type === 'Sub_A') subType = 'L';
    if (pNode.account_type === 'Sub_B') subType = 'R';
    
    return `${pId} ${subType} (${student.intended_position} Leg)`;
};
</script>

<template>
    <Head title="Promote Students" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-bold text-2xl text-slate-800">Promote Students Into Agents</h2>
        </template>
        <div class="py-12 bg-slate-50 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                    
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
                            <thead class="bg-slate-50 border-b border-slate-200 text-xs uppercase text-slate-500">
                                <tr>
                                    <th class="p-4 pl-6">Student Details</th>
                                    <th class="p-4">Payment Status</th>
                                    <th class="p-4 text-right pr-6">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="student in filteredStudents" :key="student.id" class="hover:bg-slate-50">
                                    <td class="p-4 pl-6">
                                        <div class="font-bold text-slate-800">
                                            {{ student.name }}
                                            <span class="text-[10px] text-slate-500 font-bold ml-2 bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200 shadow-sm">
                                                ID: {{ formatId(student.member_id) }}
                                            </span>
                                        </div>
                                        <div class="text-xs text-teal-600 font-bold mt-1">Sponsor: {{ student.sponsor?.name }}</div>
                                        <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">
                                            Placed: {{ getPlacementDisplay(student) }}
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <div class="font-bold text-slate-700">Rs {{ student.admission_fee_paid }} / 15,000</div>
                                        <div v-if="student.admission_fee_paid < 15000" class="text-xs font-bold text-rose-600">
                                            Pending: Rs {{ 15000 - student.admission_fee_paid }}
                                        </div>
                                        <div v-else class="text-xs font-bold text-emerald-600">Paid in Full</div>
                                    </td>
                                    <td class="p-4 text-right pr-6">
                                        <button @click="promoteStudent(student.id, student.name)" 
                                                :disabled="student.admission_fee_paid < 15000 || form.processing" 
                                                class="font-bold py-2 px-4 rounded-xl transition"
                                                :class="student.admission_fee_paid < 15000 ? 'bg-slate-200 text-slate-500 cursor-not-allowed' : 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm shadow-indigo-600/20'">
                                            {{ student.admission_fee_paid < 15000 ? 'Awaiting Payment' : 'Promote to Agent' }}
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="filteredStudents.length === 0">
                                    <td colspan="3" class="p-8 text-center text-slate-500">
                                        <span v-if="searchQuery">No students found matching "{{ searchQuery }}".</span>
                                        <span v-else>No students are currently awaiting promotion.</span>
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