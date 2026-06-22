<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ReferralTree from '@/Components/ReferralTree.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Network Tree" />
    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="font-bold text-2xl text-slate-800 tracking-tight">Referral Tree</h2>
                <p class="text-sm text-slate-500 mt-1">Visualize your referral network.</p>
            </div>
        </template>
        
        <div class="py-8 bg-slate-50 min-h-screen">
            <div class="max-w-[100rem] w-full mx-auto sm:px-6 lg:px-8">
                <div class="bg-white rounded-[2rem] p-2 border border-slate-200/60 shadow-lg relative">
                    
                    <div class="absolute top-8 left-8 z-10 pointer-events-none">
                        <div class="inline-block px-3 py-1 rounded-full bg-teal-50/80 backdrop-blur-md border border-teal-100 text-teal-700 font-bold text-[10px] uppercase tracking-widest mb-2 shadow-sm">
                            Live Visualization
                        </div>
                        <h3 class="text-xl font-black text-slate-800 tracking-tight">Interactive Tree</h3>
                        <p class="text-sm font-medium text-slate-500 mt-1">Drag to pan. Scroll to zoom. Click nodes to inspect.</p>
                    </div>

                    <div class="h-[80vh] min-h-[700px] w-full rounded-[1.5rem] border border-slate-200 overflow-hidden relative">    
                        <ReferralTree />
                    </div>
                    
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>