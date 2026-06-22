<script setup>
import { useConfirm } from '@/Composables/useConfirm';
import { computed } from 'vue';

const { state } = useConfirm();

const confirmClass = computed(() => {
    switch (state.confirmButtonTheme) {
        case 'danger': return 'bg-rose-600 hover:bg-rose-700 text-white shadow-rose-600/20';
        case 'success': return 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-emerald-600/20';
        default: return 'bg-teal-600 hover:bg-teal-700 text-white shadow-teal-600/20';
    }
});

const iconClass = computed(() => {
    switch (state.confirmButtonTheme) {
        case 'danger': return 'bg-rose-100 text-rose-600';
        case 'success': return 'bg-emerald-100 text-emerald-600';
        default: return 'bg-teal-100 text-teal-600';
    }
});
</script>

<template>
    <Transition name="fade">
        <div v-if="state.isOpen" class="fixed inset-0 z-[9999] flex items-center justify-center px-4 pt-4 pb-20 text-center sm:p-0">
            
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="state.onCancel"></div>

            <Transition name="slide-up">
                <div v-if="state.isOpen" class="relative inline-block w-full max-w-md transform overflow-hidden rounded-[2rem] bg-white text-left align-bottom shadow-2xl transition-all sm:my-8 sm:align-middle">
                    
                    <div class="bg-white px-6 pb-6 pt-8 sm:p-8 sm:pb-6">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-12 sm:w-12 shadow-sm border border-slate-100" :class="iconClass">
                                <svg v-if="state.confirmButtonTheme === 'danger'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                <svg v-else-if="state.confirmButtonTheme === 'success'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                <svg v-else class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <div class="mt-4 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <h3 class="text-xl font-black text-slate-800 tracking-tight" id="modal-title">{{ state.title }}</h3>
                                <div class="mt-3">
                                    <p class="text-sm text-slate-500 leading-relaxed">{{ state.message }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 sm:flex sm:flex-row-reverse sm:px-8">
                        <button type="button" class="inline-flex w-full justify-center rounded-xl px-5 py-3 text-sm font-bold shadow-lg transition-all sm:ml-3 sm:w-auto" :class="confirmClass" @click="state.onConfirm">
                            {{ state.confirmText }}
                        </button>
                        <button type="button" class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-5 py-3 text-sm font-bold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 transition-all sm:mt-0 sm:w-auto" @click="state.onCancel">
                            {{ state.cancelText }}
                        </button>
                    </div>

                </div>
            </Transition>
        </div>
    </Transition>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
.slide-up-enter-active, .slide-up-leave-active { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
.slide-up-enter-from, .slide-up-leave-to { opacity: 0; transform: translateY(20px) scale(0.95); }
</style>