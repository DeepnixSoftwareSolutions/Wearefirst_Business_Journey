<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
</script>

<template>
    <Head title="Welcome to Wearefirst" />
    
    <div class="min-h-screen bg-gradient-to-br from-slate-100 via-slate-50 to-teal-50/50 flex flex-col relative overflow-hidden">
        
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#8080800d_1px,transparent_1px),linear-gradient(to_bottom,#8080800d_1px,transparent_1px)] bg-[size:40px_40px] pointer-events-none z-0"></div>

        <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none z-0">
            <div class="absolute top-[0%] left-[10%] w-[60%] h-[60%] bg-teal-400/20 rounded-full blur-[140px] mix-blend-multiply animate-pulse-faint"></div>
            <div class="absolute bottom-[0%] right-[10%] w-[50%] h-[50%] bg-emerald-400/20 rounded-full blur-[120px] mix-blend-multiply opacity-60"></div>
        </div>

        <main class="flex-1 flex flex-col items-center justify-center p-6 sm:p-10 relative z-10 text-center">
            
            <div class="mb-10 sm:mb-12 transform transition duration-500 hover:scale-105">
                <ApplicationLogo class="h-32 sm:h-40 w-auto drop-shadow-md mx-auto" />
            </div>

            <div class="max-w-4xl mx-auto">
                <h1 class="text-4xl sm:text-6xl md:text-7xl font-black text-slate-900 tracking-tight leading-tight mb-8">
                    Start Your Business <br> 
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-500 to-emerald-600 drop-shadow-sm">Journey Today.</span>
                </h1>
                
                <p class="text-md sm:text-lg text-slate-500 mb-12 max-w-2xl mx-auto leading-relaxed font-medium">
                    Join our elite network of agents and students. Learn professional trading strategies, build your referral network, and unlock unlimited earning potential.
                </p>

                <div>
                    <Link :href="route('login')" class="inline-flex items-center justify-center px-12 py-4 bg-gradient-to-r from-teal-500 to-emerald-600 text-white font-black text-xl rounded-2xl shadow-xl shadow-teal-500/25 hover:from-teal-400 hover:to-emerald-500 transition-all transform hover:-translate-y-1">
                        Log In
                    </Link>
                </div>
            </div>
        </main>

        <footer class="py-8 text-center text-xs font-bold tracking-widest uppercase text-slate-400 relative z-10">
            &copy; {{ new Date().getFullYear() }} Wearefirst Business Journey. All rights reserved.
        </footer>
    </div>
</template>

<style scoped>
/* Animated pulse imported from GuestLayout to ensure visual consistency */
@keyframes pulse-faint {
  0%, 100% { opacity: 0.8; transform: scale(1); }
  50% { opacity: 0.95; transform: scale(1.02); }
}
.animate-pulse-faint {
  animation: pulse-faint 10s ease-in-out infinite;
}
</style>