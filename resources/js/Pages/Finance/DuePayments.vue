<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useConfirm } from '@/Composables/useConfirm';
import { watchEffect } from 'vue';

const { confirmAction } = useConfirm();
const props = defineProps({ initialPayments: Array, balancePayments: Array });
const authUser = usePage().props.auth.user;

const paymentModal = ref(false);
const selectedStudent = ref(null);
const paymentForm = useForm({
    amount: 15000,
    transaction_id: ''
});

const openPaymentModal = (student, isBalance = false) => {
    selectedStudent.value = student;
    paymentForm.amount = isBalance ? student.owed : 15000;
    paymentForm.transaction_id = '';
    paymentModal.value = true;
};

const submitAgentPayment = () => {
    paymentForm.post(`/payments/submit/${selectedStudent.value.id}`, {
        preserveScroll: true,
        onSuccess: () => { 
            paymentModal.value = false; 
            paymentForm.reset(); // Clear the form
            selectedStudent.value = null; // Clear the selected student
        }
    });
};

const logRemainder = async (studentId, studentName) => {
    const confirmed = await confirmAction({
        title: 'Verify Final Balance',
        message: `Confirm receipt of the remaining balance for ${studentName}?`,
        confirmText: 'Verify Received',
        confirmButtonTheme: 'success'
    });
    if (confirmed) {
        useForm({}).post(`/accountant/mark-remainder-paid/${studentId}`);
    }
};

// Automatically trigger the custom modal if the server sends a flash_modal response
watchEffect(() => {
    if (authUser && usePage().props.flash?.flash_modal) {
        confirmAction({
            title: usePage().props.flash.title,
            message: usePage().props.flash.message,
            confirmText: 'Okay',
            cancelText: null, // Hide cancel button
            confirmButtonTheme: usePage().props.flash.theme || 'primary'
        });
        usePage().props.flash.flash_modal = false;
    }
});
</script>

<template>
    <Head title="Due Payments" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-bold text-2xl text-slate-800">Payment Submissions & Verification</h2>
        </template>

        <div class="py-12 max-w-7xl mx-auto px-6 space-y-10">
            
            <div>
                <h3 class="text-lg font-black text-slate-800 mb-4 flex items-center">
                    <span class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center mr-3">1</span>
                    Awaiting Initial Payment
                </h3>
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse whitespace-nowrap">
                            <thead class="bg-slate-50 text-xs uppercase text-slate-500 font-bold">
                                <tr>
                                    <th class="p-4 pl-6">Student</th>
                                    <th class="p-4">Status</th>
                                    <th class="p-4 text-right pr-6">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="student in initialPayments" :key="student.id" class="hover:bg-slate-50">
                                    <td class="p-4 pl-6 font-bold text-slate-800">
                                        {{ student.name }}
                                        <div class="text-xs text-slate-400 font-normal mt-0.5">Joined: {{ student.registration_date }}</div>
                                    </td>
                                    <td class="p-4">
                                        <span v-if="student.status === 'Rejected'" class="text-[10px] font-bold uppercase tracking-widest text-rose-600 bg-rose-50 px-2 py-1 rounded border border-rose-200">Rejected (Resubmit)</span>
                                        <span v-else class="text-[10px] font-bold uppercase tracking-widest text-amber-600 bg-amber-50 px-2 py-1 rounded border border-amber-200">Payment Required</span>
                                    </td>
                                    <td class="p-4 text-right pr-6">
                                        <button v-if="['Agent', 'Admin'].includes(authUser.role)" @click="openPaymentModal(student, false)" class="bg-slate-900 hover:bg-teal-600 text-white font-bold py-2 px-4 text-xs rounded-xl shadow-sm transition">
                                            Submit Payment Details
                                        </button>
                                        <span v-else class="text-xs text-slate-400 font-bold italic">Waiting on Agent</span>
                                    </td>
                                </tr>
                                <tr v-if="initialPayments.length === 0">
                                    <td colspan="3" class="p-10 text-center text-slate-500">No students pending initial payment.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-black text-slate-800 mb-4 flex items-center">
                    <span class="w-8 h-8 rounded-lg bg-teal-100 text-teal-600 flex items-center justify-center mr-3">2</span>
                    Awaiting Balance Payment
                </h3>
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse whitespace-nowrap">
                            <thead class="bg-slate-50 text-xs uppercase text-slate-500 font-bold">
                                <tr>
                                    <th class="p-4 pl-6">Student</th>
                                    <th class="p-4">Paid</th>
                                    <th class="p-4 text-rose-600">Balance Due</th>
                                    <th class="p-4 text-right pr-6">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="student in balancePayments" :key="student.id" class="hover:bg-slate-50">
                                    <td class="p-4 pl-6 font-bold text-slate-800">
                                        {{ student.name }}
                                    </td>
                                    <td class="p-4 text-sm font-bold text-emerald-600">Rs {{ student.paid }}</td>
                                    <td class="p-4 font-black text-rose-600">Rs {{ student.owed }}</td>
                                    <td class="p-4 text-right pr-6 space-y-2">
                                        <button v-if="['Agent', 'Admin'].includes(authUser.role) && !student.payment_transaction_id?.includes('(BALANCE)')" 
                                                @click="openPaymentModal(student, true)" 
                                                class="w-full bg-slate-900 hover:bg-teal-600 text-white font-bold py-2 px-4 text-xs rounded-xl shadow-sm transition">
                                            Submit Balance Details
                                        </button>
                                        
                                        <template v-if="['Admin', 'Accountant'].includes(authUser.role) && student.payment_transaction_id?.includes('(BALANCE)')">
                                            <button @click="logRemainder(student.id, student.name)" 
                                                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 text-sm rounded-xl shadow-sm transition">
                                                Verify Balance Received
                                            </button>
                                            <button @click="useForm({}).post(`/accountant/reject-student/${student.id}`)" 
                                                    class="w-full bg-rose-50 hover:bg-rose-500 hover:text-white text-rose-600 border border-rose-200 font-bold text-xs py-2 px-4 rounded-xl transition mt-2">
                                                Reject Balance Payment
                                            </button>
                                        </template>
                                    </td>
                                </tr>
                                <tr v-if="balancePayments.length === 0">
                                    <td colspan="4" class="p-10 text-center text-slate-500">No outstanding balance payments found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <Modal :show="paymentModal" @close="paymentModal = false">
            <div class="p-8">
                <h2 class="text-xl font-black text-slate-800 mb-2">Submit Payment Details</h2>
                <p class="text-sm text-slate-500 mb-6">Enter the bank transaction details for <strong>{{ selectedStudent?.name }}</strong>.</p>
                
                <form @submit.prevent="submitAgentPayment" class="space-y-4">
                    <div v-if="['Pending Payment', 'Rejected'].includes(selectedStudent?.status)">
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Amount Transferred</label>
                        <select v-model="paymentForm.amount" class="w-full bg-slate-50 border-slate-200 rounded-xl p-3 focus:ring-teal-500 focus:border-teal-500" required>
                            <option :value="15000">Full Payment (Rs 15,000)</option>
                            <option :value="10000">Partial Payment (Rs 10,000)</option>
                        </select>
                        <div v-if="paymentForm.errors.amount" class="text-[10px] text-rose-500 font-bold mt-1 uppercase tracking-widest">{{ paymentForm.errors.amount }}</div>
                    </div>
                    <div v-else>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Amount Transferred</label>
                        <div class="w-full bg-slate-100 border border-slate-200 rounded-xl p-3 font-bold text-slate-600">
                            Rs {{ paymentForm.amount }} (Remaining Balance)
                        </div>
                        <div v-if="paymentForm.errors.amount" class="text-[10px] text-rose-500 font-bold mt-1 uppercase tracking-widest">{{ paymentForm.errors.amount }}</div>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Transaction ID</label>
                        <input v-model="paymentForm.transaction_id" type="text" class="w-full bg-slate-50 border-slate-200 rounded-xl p-3 focus:ring-teal-500 focus:border-teal-500" placeholder="e.g. TR-987654321" required>
                        <div v-if="paymentForm.errors.transaction_id" class="text-[10px] text-rose-500 font-bold mt-1 uppercase tracking-widest">{{ paymentForm.errors.transaction_id }}</div>
                    </div>
                    
                    <div v-if="paymentForm.errors.error" class="p-3 bg-rose-50 text-rose-600 border border-rose-200 rounded-xl text-xs font-bold">
                        {{ paymentForm.errors.error }}
                    </div>
                    
                    <div class="flex gap-3 pt-6 border-t border-slate-100">
                        <button type="button" @click="paymentModal = false" class="flex-1 py-3 bg-white border border-slate-200 text-slate-700 font-bold rounded-xl hover:bg-slate-50">Cancel</button>
                        <button type="submit" :disabled="paymentForm.processing" class="flex-1 py-3 bg-teal-600 text-white font-bold rounded-xl shadow-md shadow-teal-600/20 hover:bg-teal-700 transition">
                            {{ paymentForm.processing ? 'Submitting...' : 'Submit to Finance' }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>