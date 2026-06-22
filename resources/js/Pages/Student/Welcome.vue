<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';

const user = usePage().props.auth.user;
</script>

<template>
    <Head title="Welcome Student" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-bold text-2xl text-slate-800 tracking-tight">Student Portal</h2>
        </template>

        <div class="py-12 bg-slate-50 min-h-screen">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                
                <div class="bg-white rounded-[2.5rem] shadow-xl border border-slate-200 overflow-hidden relative">
                    <div class="absolute top-0 right-0 -mt-16 -mr-16 w-64 h-64 bg-teal-500 rounded-full opacity-10 blur-3xl pointer-events-none"></div>
                    
                    <div class="p-10 md:p-16 relative z-10 text-center">
                        
                        <div class="w-24 h-24 bg-teal-50 text-teal-600 rounded-full flex items-center justify-center mx-auto mb-8 shadow-inner border border-teal-100 text-4xl">
                            🎓
                        </div>

                        <h1 class="text-3xl md:text-4xl font-black text-slate-800 tracking-tight mb-4">
                            Welcome to Wearefirst, {{ user.name.split(' ')[0] }}!
                        </h1>
                        
                        <p class="text-slate-500 text-lg max-w-2xl mx-auto leading-relaxed mb-8">
                            Your account is currently in <span class="font-bold text-slate-700">Student Mode</span>. You have successfully secured your position in the network tree, but your business dashboard and recruitment tools are currently locked.
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-left max-w-3xl mx-auto mb-10">
                            
                            <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100 shadow-sm">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center font-black text-sm">1</div>
                                    <h3 class="font-bold text-slate-800">Complete Your Payment</h3>
                                </div>
                                <p class="text-sm text-slate-600">
                                    To unlock the Agent Dashboard, ensure your total admission fee of <span class="font-bold text-slate-800">Rs 15,000</span> has been submitted to the company Accountant.
                                </p>
                                <div class="mt-4 pt-4 border-t border-slate-200 flex justify-between items-center">
                                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Amount Paid</span>
                                    <span class="text-sm font-black" :class="user.admission_fee_paid >= 15000 ? 'text-emerald-600' : 'text-amber-600'">
                                        Rs {{ user.admission_fee_paid }}
                                    </span>
                                </div>
                            </div>

                            <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100 shadow-sm">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-black text-sm">2</div>
                                    <h3 class="font-bold text-slate-800">Agent Promotion</h3>
                                </div>
                                <p class="text-sm text-slate-600">
                                    Once your payment is verified, your sponsor or a network Manager will officially promote your account to <span class="font-bold text-slate-800">Agent</span> status.
                                </p>
                                <div class="mt-4 pt-4 border-t border-slate-200 flex justify-between items-center">
                                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Current Status</span>
                                    <span class="text-[10px] font-black uppercase tracking-widest px-2 py-1 rounded bg-slate-200 text-slate-600">
                                        Awaiting Promotion
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>