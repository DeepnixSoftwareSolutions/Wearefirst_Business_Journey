<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import { Link, usePage } from '@inertiajs/vue3';

const showingSidebar = ref(false);
const authUser = usePage().props.auth.user;

const sidebarNav = ref(null);

// Restore scroll position when the new page loads
onMounted(() => {
    if (sidebarNav.value) {
        const savedScroll = sessionStorage.getItem('sidebar-scroll-position');
        if (savedScroll) {
            sidebarNav.value.scrollTop = parseInt(savedScroll, 10);
        }
    }
});

// Save the scroll position right before navigating away
onBeforeUnmount(() => {
    if (sidebarNav.value) {
        sessionStorage.setItem('sidebar-scroll-position', sidebarNav.value.scrollTop);
    }
});
</script>

<template>
    <div class="flex h-screen bg-slate-50 overflow-hidden font-sans">
        <div v-if="showingSidebar" @click="showingSidebar = false" class="fixed inset-0 z-20 bg-slate-900/50 backdrop-blur-sm lg:hidden transition-opacity"></div>
        <aside :class="showingSidebar ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-30 w-72 bg-slate-900 text-slate-300 transition-transform duration-300 lg:static lg:translate-x-0 flex flex-col shadow-2xl lg:shadow-none border-r border-slate-800">
                
            <div class="flex items-center justify-center h-20 border-b border-slate-800 px-6 shrink-0">
                <Link :href="route('dashboard')" class="flex items-center w-full">
                    <ApplicationLogo class="h-14 w-auto text-white drop-shadow-lg" />
                </Link>
            </div>

            <nav ref="sidebarNav" class="flex-1 overflow-y-auto py-6 px-4 space-y-1 scrollbar-hide">
                
                <template v-if="authUser.role !== 'Student'">
                        
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-4 px-3 mt-4">Main Menu</div>
                    <Link :href="route('dashboard')" :class="route().current('dashboard') ? 'bg-teal-500/10 text-teal-400 border-teal-500/50' : 'hover:bg-slate-800 hover:text-white border-transparent'" class="flex items-center gap-3 px-3 py-3 rounded-xl border transition-colors group">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        <span class="font-semibold text-sm">Dashboard</span>
                    </Link>

                    <template v-if="['Admin', 'Manager'].includes(authUser.role)">
                        <div class="text-[10px] font-black text-slate-600 uppercase tracking-widest mb-3 px-3 pt-6">Management Controls</div>
                        
                        <Link :href="route('network.tree')" :class="route().current('network.tree') ? 'bg-indigo-500/10 text-indigo-400 border-indigo-500/50 shadow-inner' : 'hover:bg-slate-800 hover:text-white border-transparent'" class="flex items-center gap-3 px-3 py-3 rounded-xl border transition-all duration-300 group">
                            <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                            <span class="font-semibold text-sm">Referral Tree</span>
                        </Link>
                        <Link :href="route('manager.promotions')" :class="route().current('manager.promotions') ? 'bg-indigo-500/10 text-indigo-400 border-indigo-500/50 shadow-inner' : 'hover:bg-slate-800 hover:text-white border-transparent'" class="flex items-center gap-3 px-3 py-3 rounded-xl border transition-all duration-300 group">
                            <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            <span class="font-semibold text-sm">Promote Students</span>
                        </Link>
                        <Link :href="route('manager.pending')" :class="route().current('manager.pending') ? 'bg-indigo-500/10 text-indigo-400 border-indigo-500/50 shadow-inner' : 'hover:bg-slate-800 hover:text-white border-transparent'" class="flex items-center gap-3 px-3 py-3 rounded-xl border transition-all duration-300 group">
                            <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            <span class="font-semibold text-sm">Approve Salaries</span>
                        </Link>
                        <Link :href="route('admin.users')" :class="route().current('admin.users') ? 'bg-indigo-500/10 text-indigo-400 border-indigo-500/50 shadow-inner' : 'hover:bg-slate-800 hover:text-white border-transparent'" class="flex items-center gap-3 px-3 py-3 rounded-xl border transition-all duration-300 group">
                            <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            <span class="font-semibold text-sm">Manage Users</span>
                        </Link>
                    </template>

                    <template v-if="['Admin', 'Manager', 'Accountant'].includes(authUser.role)">
                        <div class="text-[10px] font-black text-slate-600 uppercase tracking-widest mb-3 px-3 pt-6">Finance & Accounting</div>
                        
                        <Link :href="route('accountant.pending')" :class="route().current('accountant.pending') ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/50' : 'hover:bg-slate-800 hover:text-white border-transparent'" class="flex items-center gap-3 px-3 py-3 rounded-xl border transition-colors group">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                            <span class="font-semibold text-sm">Pending Student Approvals</span>
                        </Link>
                        <Link :href="route('payments.due')" :class="route().current('payments.due') ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/50' : 'hover:bg-slate-800 hover:text-white border-transparent'" class="flex items-center gap-3 px-3 py-3 rounded-xl border transition-colors group">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="font-semibold text-sm">Due Payments</span>
                        </Link>
                        <Link :href="route('accountant.payouts')" :class="route().current('accountant.payouts') ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/50' : 'hover:bg-slate-800 hover:text-white border-transparent'" class="flex items-center gap-3 px-3 py-3 rounded-xl border transition-colors group">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="font-semibold text-sm">Request Salary Approvals</span>
                        </Link>
                        <Link :href="route('accountant.disburse')" :class="route().current('accountant.disburse') ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/50' : 'hover:bg-slate-800 hover:text-white border-transparent'" class="flex items-center gap-3 px-3 py-3 rounded-xl border transition-colors group">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            <span class="font-semibold text-sm">Disburse Salaries</span>
                        </Link>
                        <Link :href="route('company.ledger')" :class="route().current('company.ledger') ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/50' : 'hover:bg-slate-800 hover:text-white border-transparent'" class="flex items-center gap-3 px-3 py-3 rounded-xl border transition-colors group">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span class="font-semibold text-sm">Company Ledger</span>
                        </Link>
                    </template>

                    <template v-if="['Admin', 'Agent'].includes(authUser.role)">
                        <div class="text-[10px] font-black text-slate-600 uppercase tracking-widest mb-3 px-3 pt-6">Agent Tools</div>
                            
                        <Link v-if="authUser.role === 'Agent'" :href="route('agent.group-service')" :class="route().current('agent.group-service') ? 'bg-blue-500/10 text-blue-400 border-blue-500/50 shadow-inner' : 'hover:bg-slate-800 hover:text-white border-transparent'" class="flex items-center gap-3 px-3 py-3 rounded-xl border transition-all duration-300 group">
                            <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            <span class="font-semibold text-sm">Group Service</span>
                        </Link>
                        <Link :href="route('agent.register')" :class="route().current('agent.register') ? 'bg-blue-500/10 text-blue-400 border-blue-500/50 shadow-inner' : 'hover:bg-slate-800 hover:text-white border-transparent'" class="flex items-center gap-3 px-3 py-3 rounded-xl border transition-all duration-300 group">
                            <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                            <span class="font-semibold text-sm">Register Student</span>
                        </Link>
                        <Link :href="route('agent.pending')" :class="route().current('agent.pending') ? 'bg-blue-500/10 text-blue-400 border-blue-500/50 shadow-inner' : 'hover:bg-slate-800 hover:text-white border-transparent'" class="flex items-center gap-3 px-3 py-3 rounded-xl border transition-all duration-300 group">
                            <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="font-semibold text-sm">Pending Registrations</span>
                        </Link>
                        <Link v-if="authUser.role === 'Agent'" :href="route('payments.due')" :class="route().current('payments.due') ? 'bg-blue-500/10 text-blue-400 border-blue-500/50 shadow-inner' : 'hover:bg-slate-800 hover:text-white border-transparent'" class="flex items-center gap-3 px-3 py-3 rounded-xl border transition-all duration-300 group">
                            <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="font-semibold text-sm">Submit Payments</span>
                        </Link>
                        <Link :href="route('agent.network')" :class="route().current('agent.network') ? 'bg-blue-500/10 text-blue-400 border-blue-500/50 shadow-inner' : 'hover:bg-slate-800 hover:text-white border-transparent'" class="flex items-center gap-3 px-3 py-3 rounded-xl border transition-all duration-300 group">
                            <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            <span class="font-semibold text-sm">My Recruits List</span>
                        </Link>
                        <Link :href="route('ledger')" :class="route().current('ledger') ? 'bg-blue-500/10 text-blue-400 border-blue-500/50' : 'hover:bg-slate-800 hover:text-white border-transparent'" class="flex items-center gap-3 px-3 py-3 rounded-xl border transition-colors group">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span class="font-semibold text-sm">My Ledger</span>
                        </Link>
                    </template>
                </template>

                <template v-else>
                    <div class="text-[10px] font-black text-slate-600 uppercase tracking-widest mb-3 px-3 mt-4">Student Portal</div>
                    <Link :href="route('student.welcome')" :class="route().current('student.welcome') ? 'bg-teal-500/10 text-teal-400 border-teal-500/50' : 'hover:bg-slate-800 hover:text-white border-transparent'" class="flex items-center gap-3 px-3 py-3 rounded-xl border transition-colors group">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                        <span class="font-semibold text-sm">Welcome</span>
                    </Link>
                </template>

                <div class="text-[10px] font-black text-slate-600 uppercase tracking-widest mb-3 px-3 mt-4 border-t border-slate-800 pt-6">Account</div>
                <Link :href="route('profile.edit')" :class="route().current('profile.edit') ? 'bg-teal-500/10 text-teal-400 border-teal-500/50' : 'hover:bg-slate-800 hover:text-white border-transparent'" class="flex items-center gap-3 px-3 py-3 rounded-xl border transition-colors group">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <span class="font-semibold text-sm">My Profile</span>
                </Link>
            </nav>

            <div class="p-4 border-t border-slate-800 shrink-0">
                <div class="flex items-center gap-3 px-3 py-2 bg-slate-800/50 rounded-xl border border-slate-700/50">
                    <div class="w-10 h-10 rounded-full bg-slate-700 flex items-center justify-center text-white font-bold text-sm border border-slate-600 overflow-hidden shrink-0">
                        <img v-if="authUser.profile_image_path" :src="'/storage/' + authUser.profile_image_path" class="w-full h-full object-cover" />
                        <span v-else>{{ authUser.name.charAt(0) }}</span>
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-sm font-bold text-white truncate">{{ authUser.name }}</p>
                        <p class="text-[10px] font-bold text-teal-400 uppercase tracking-widest truncate">{{ authUser.role }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0">
            
            <header class="relative min-h-[5rem] py-3 md:py-0 md:h-20 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 lg:px-8 z-20 shadow-sm shrink-0">
                
                <div class="flex items-center gap-3 min-w-0 flex-1">
                    <button @click="showingSidebar = true" class="lg:hidden text-slate-500 hover:text-slate-800 focus:outline-none bg-slate-100 p-2 rounded-xl shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    
                    <div class="min-w-0 flex-1 [&>h2]:text-base sm:[&>h2]:text-lg md:[&>h2]:text-2xl [&>h2]:leading-tight [&>h2]:whitespace-normal">
                        <slot name="header" />
                    </div>
                </div>

                <div class="flex items-center gap-4 shrink-0 ml-4">
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button type="button" class="flex items-center gap-2 px-2 md:px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 transition focus:outline-none">
                                <div class="w-8 h-8 md:w-6 md:h-6 rounded-full bg-teal-100 flex items-center justify-center text-teal-700 font-bold text-xs border border-teal-200 overflow-hidden shrink-0">
                                    <img v-if="authUser.profile_image_path" :src="'/storage/' + authUser.profile_image_path" class="w-full h-full object-cover" />
                                    <span v-else>{{ authUser.name.charAt(0) }}</span>
                                </div>
                                <span class="text-sm font-bold text-slate-700 hidden sm:block truncate max-w-[120px]">{{ authUser.name }}</span>
                                <svg class="w-4 h-4 text-slate-400 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </template>

                        <template #content>
                            <div class="px-4 py-3 border-b border-slate-100 sm:hidden">
                                <p class="text-sm font-bold text-slate-800 truncate">{{ authUser.name }}</p>
                                <p class="text-xs text-slate-500 truncate">{{ authUser.email }}</p>
                            </div>
                            <DropdownLink :href="route('logout')" method="post" as="button" class="font-semibold text-rose-600">Log Out</DropdownLink>
                        </template>
                    </Dropdown>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto bg-slate-50/50">
                <slot />
            </main>
        </div>
        <ConfirmationModal />
    </div>
</template>

<style scoped>
/* Hide scrollbar for Chrome, Safari and Opera */
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
/* Hide scrollbar for IE, Edge and Firefox */
.scrollbar-hide {
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
}
</style>