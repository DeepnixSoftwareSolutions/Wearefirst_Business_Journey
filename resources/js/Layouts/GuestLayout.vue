<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen bg-slate-50 flex flex-col justify-center items-center px-4 sm:px-6 relative overflow-hidden">
        
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none z-0">
            <div class="absolute -top-[10%] left-[10%] w-[80%] h-[80%] bg-teal-500/10 rounded-full blur-[140px] mix-blend-soft-light animate-pulse-faint"></div>
            <div class="absolute bottom-[0%] right-[0%] w-[60%] h-[60%] bg-indigo-500/10 rounded-full blur-[120px] mix-blend-soft-light opacity-60"></div>
        </div>

        <div class="relative z-10 mb-2 text-center transform transition duration-500 hover:scale-105">
            <Link href="/" class="inline-block">
                <ApplicationLogo class="h-28 sm:h-32 w-auto drop-shadow-md mx-auto" />
            </Link>
        </div>

        <div class="relative z-10 w-full max-w-md bg-white/80 backdrop-blur-2xl border border-slate-200/50 shadow-2xl rounded-[2.5rem] p-8 transition-all hover:shadow-2xl">
            <slot />
        </div>

        <div class="absolute bottom-6 w-full text-center z-10 text-slate-400 text-[10px] sm:text-xs font-semibold tracking-widest uppercase pointer-events-none">
            &copy; {{ new Date().getFullYear() }} Wearefirst Business Journey. All rights reserved.
        </div>
    </div>
</template>

<style>
/* Custom keyframes for a faint, slow pulse */
@keyframes pulse-faint {
  0%, 100% { opacity: 0.8; transform: scale(1); }
  50% { opacity: 0.95; transform: scale(1.02); }
}
.animate-pulse-faint {
  animation: pulse-faint 10s ease-in-out infinite;
}
</style>