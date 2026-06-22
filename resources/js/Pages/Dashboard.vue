<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';

const { dashboardData } = usePage().props;
const user = dashboardData.my_profile;
const myStats = dashboardData.my_stats || null;
const topEarners = dashboardData.top_earners;
const topRecruiters = dashboardData.top_recruiters;
const analytics = dashboardData.analytics || null; 
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center w-full pr-4">
                <div>
                    <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
                        Welcome back, {{ user.name }}
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">Your business operations overview.</p>
                </div>
            </div>
        </template>

        <div class="py-10 bg-slate-50 min-h-screen relative overflow-hidden">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8 relative z-10">
                
                <div v-if="analytics" class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 rounded-[2rem] shadow-2xl p-8 text-white relative overflow-hidden border border-slate-700/80">
                    <div class="absolute top-0 right-0 -mt-16 -mr-16 w-80 h-80 bg-teal-500 rounded-full opacity-20 blur-[80px] pointer-events-none"></div>
                    <div class="absolute bottom-0 left-0 -mb-16 -ml-16 w-72 h-72 bg-blue-600 rounded-full opacity-20 blur-[80px] pointer-events-none"></div>
                    
                    <h3 class="text-xl font-bold mb-6 flex items-center text-teal-400 tracking-wide">
                        <svg class="w-6 h-6 mr-3 opacity-90 drop-shadow-md" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        Company Analytics <span class="text-slate-400 font-medium ml-2 text-sm">({{ analytics.period }})</span>
                    </h3>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 relative z-10">
                        <div class="bg-white/5 backdrop-blur-xl rounded-2xl p-6 border border-white/10 hover:bg-white/10 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-black/20">
                            <div class="text-xs text-slate-400 font-semibold uppercase tracking-widest mb-2">Total Network</div>
                            <div class="text-3xl font-bold">{{ analytics.total_users }} <span class="text-sm font-normal text-slate-400">Users</span></div>
                        </div>
                        <div class="bg-white/5 backdrop-blur-xl rounded-2xl p-6 border border-white/10 hover:bg-white/10 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-black/20">
                            <div class="text-xs text-slate-400 font-semibold uppercase tracking-widest mb-2">Weekly Revenue</div>
                            <div class="text-3xl font-bold text-teal-400 drop-shadow-md">Rs {{ analytics.weekly_revenue }}</div>
                        </div>
                        <div class="bg-white/5 backdrop-blur-xl rounded-2xl p-6 border border-white/10 hover:bg-white/10 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-black/20">
                            <div class="text-xs text-slate-400 font-semibold uppercase tracking-widest mb-2">Weekly Payouts</div>
                            <div class="text-3xl font-bold text-rose-400 drop-shadow-md">Rs {{ analytics.weekly_expenses }}</div>
                        </div>
                        <div class="bg-white/5 backdrop-blur-xl rounded-2xl p-6 border border-white/10 hover:bg-white/10 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-black/20">
                            <div class="text-xs text-slate-400 font-semibold uppercase tracking-widest mb-2">Net Profit</div>
                            <div class="text-3xl font-bold text-white drop-shadow-md">Rs {{ analytics.weekly_profit }}</div>
                        </div>
                    </div>
                </div>

                <div v-if="user.role !== 'Accountant'" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                    
                    <div class="bg-white/80 backdrop-blur-md rounded-[2rem] p-6 border border-slate-200/80 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 relative overflow-hidden group flex flex-col">
                        <div class="absolute top-0 right-0 p-6 opacity-5 group-hover:opacity-10 transition-opacity transform group-hover:scale-110 duration-500">
                            <svg class="w-24 h-24 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path></svg>
                        </div>
                        <div class="flex-1">
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Network Growth</div>
                            <div class="text-4xl font-black text-slate-800 tracking-tight">{{ myStats.weekly_recruits }} <span class="text-sm font-bold text-slate-400">Students</span></div>
                            <div class="mt-2 text-[11px] font-bold tracking-wide uppercase text-indigo-700 bg-indigo-50 border border-indigo-100 inline-block px-3 py-1.5 rounded-lg shadow-sm">Recruited This Week</div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-slate-100/80 flex justify-between items-center z-10">
                            <span class="text-xs font-bold text-slate-400">Lifetime Recruits</span>
                            <span class="text-sm font-black text-slate-700">{{ myStats.lifetime_recruits }}</span>
                        </div>
                    </div>

                    <div class="bg-white/80 backdrop-blur-md rounded-[2rem] p-6 border border-slate-200/80 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 relative overflow-hidden group flex flex-col">
                        <div class="absolute top-0 right-0 p-6 opacity-5 group-hover:opacity-10 transition-opacity transform group-hover:scale-110 duration-500">
                            <svg class="w-24 h-24 text-teal-600" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"></path><path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"></path></svg>
                        </div>
                        <div class="flex-1">
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Pending Payout (Friday)</div>
                            <div class="text-4xl font-black text-slate-800 tracking-tight">Rs {{ user.pending_payout_balance }}</div>
                            <div class="mt-2 text-[11px] font-bold tracking-wide uppercase text-teal-700 bg-teal-50 border border-teal-100 inline-block px-3 py-1.5 rounded-lg shadow-sm">Current Balance</div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-slate-100/80 space-y-2 z-10">
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-bold text-slate-400">Generated Direct (Week)</span>
                                <span class="text-xs font-black text-slate-700">Rs {{ myStats.weekly_direct }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-bold text-slate-400">Generated Pair (Week)</span>
                                <span class="text-xs font-black text-slate-700">Rs {{ myStats.weekly_pair }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white/80 backdrop-blur-md rounded-[2rem] p-6 border border-slate-200/80 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 relative overflow-hidden group flex flex-col">
                        <div class="absolute top-0 right-0 p-6 opacity-5 group-hover:opacity-10 transition-opacity transform group-hover:scale-110 duration-500">
                            <svg class="w-24 h-24 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.736 6.979C9.208 6.193 9.696 6 10 6c.304 0 .792.193 1.264.979a1 1 0 001.715-1.029C12.279 4.784 11.232 4 10 4s-2.279.784-2.979 1.95c-.285.475-.507 1-.67 1.55H6a1 1 0 000 2h.013a9.358 9.358 0 000 1H6a1 1 0 100 2h.343c.163.55.385 1.075.67 1.55C7.721 15.216 8.768 16 10 16s2.279-.784 2.979-1.95a1 1 0 10-1.715-1.029c-.472.786-.96 .979-1.264.979-.304 0-.792-.193-1.264-.979a4.265 4.265 0 01-.264-.521H10a1 1 0 100-2H8.017a7.36 7.36 0 010-1H10a1 1 0 100-2H8.017c.035-.187.083-.367.14-.542.062-.19.135-.373.22-.549h.359z" clip-rule="evenodd"></path></svg>
                        </div>
                        <div class="flex-1">
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Total Historical Earned</div>
                            <div class="text-4xl font-black text-slate-800 tracking-tight">Rs {{ user.total_historical_earned }}</div>
                            <div class="mt-2 text-[11px] font-bold tracking-wide uppercase text-blue-700 bg-blue-50 border border-blue-100 inline-block px-3 py-1.5 rounded-lg shadow-sm">Lifetime Earnings</div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-slate-100/80 space-y-2 z-10">
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-bold text-slate-400">Lifetime Direct</span>
                                <span class="text-xs font-black text-slate-700">Rs {{ myStats.lifetime_direct }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-bold text-slate-400">Lifetime Pair</span>
                                <span class="text-xs font-black text-slate-700">Rs {{ myStats.lifetime_pair }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white/80 backdrop-blur-md rounded-[2rem] p-6 border border-slate-200/80 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 relative overflow-hidden group flex flex-col justify-between">
                        <div class="absolute top-0 right-0 p-6 opacity-5 group-hover:opacity-10 transition-opacity transform group-hover:scale-110 duration-500">
                            <svg class="w-24 h-24" :class="user.is_held ? 'text-rose-600' : 'text-emerald-600'" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        </div>
                        <div>
                            <div class="flex justify-between items-start mb-2 relative z-10">
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Account Status</div>
                                <div v-if="user.badge && user.badge !== 'None'" class="text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-lg bg-gradient-to-r from-teal-400 to-emerald-500 text-white shadow-md shadow-teal-500/20 border border-teal-400/50">
                                    {{ user.badge }}
                                </div>
                            </div>
                            <div class="text-3xl font-black tracking-tight mt-4 relative z-10" :class="user.is_held ? 'text-rose-600' : 'text-emerald-600'">
                                {{ user.is_held ? 'ACCOUNT ON HOLD' : 'Active & Verified' }}
                            </div>
                        </div>
                        <div class="mt-4 text-[11px] font-bold tracking-wide uppercase shadow-sm inline-block px-3 py-1.5 rounded-lg border self-start relative z-10" :class="user.is_held ? 'text-rose-700 bg-rose-50 border-rose-200' : 'text-emerald-700 bg-emerald-50 border-emerald-200'">
                            {{ user.is_held ? 'Please contact admin immediately.' : 'Fully Operational' }}
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-2 gap-8 pt-4">
                    
                    <div class="bg-white rounded-[2rem] p-8 border border-slate-200/80 shadow-sm relative">
                        <div class="flex items-center justify-between mb-8">
                            <h3 class="text-lg font-black text-slate-800 flex items-center tracking-tight">
                                <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center mr-4 shadow-inner border border-teal-100">💸</div>
                                Top 3 Earners
                            </h3>
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest bg-slate-100 px-3 py-1.5 rounded-full border border-slate-200 shadow-sm">This Week</span>
                        </div>
                        
                        <ul class="space-y-4">
                            <li v-for="(earner, index) in topEarners.slice(0, 3)" :key="index" 
                                class="relative flex items-center justify-between p-4 rounded-2xl transition-all duration-300 border"
                                :class="{
                                    'bg-gradient-to-br from-slate-900 to-slate-800 border-slate-700 text-white shadow-xl shadow-slate-900/20 transform hover:-translate-y-1 hover:shadow-2xl group overflow-hidden': index === 0,
                                    'bg-gradient-to-r from-slate-50 to-white border-slate-200 hover:shadow-md hover:-translate-y-0.5': index > 0
                                }">
                                
                                <div v-if="index === 0" class="absolute inset-0 bg-teal-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                                <div v-if="index === 0" class="absolute -right-4 -top-4 w-24 h-24 bg-amber-400 rounded-full blur-2xl opacity-20 pointer-events-none"></div>

                                <div class="flex items-center relative z-10">
                                    <div class="w-12 h-12 rounded-xl flex items-center justify-center font-black mr-4 text-base shadow-sm border shrink-0"
                                         :class="{ 
                                            'bg-gradient-to-br from-amber-300 to-amber-500 text-amber-900 border-amber-200 shadow-amber-500/30': index === 0, 
                                            'bg-gradient-to-br from-slate-200 to-slate-300 text-slate-700 border-slate-100': index === 1,
                                            'bg-gradient-to-br from-orange-200 to-orange-300 text-orange-900 border-orange-100': index === 2
                                         }">
                                        {{ index === 0 ? '👑' : index + 1 }}
                                    </div>
                                    
                                    <div class="w-12 h-12 rounded-full overflow-hidden border-2 bg-slate-200 flex items-center justify-center mr-4 shrink-0"
                                         :class="index === 0 ? 'border-amber-300' : 'border-slate-200'">
                                        <img v-if="earner.user.profile_image_path" :src="'/storage/' + earner.user.profile_image_path" class="w-full h-full object-cover" />
                                        <span v-else class="text-slate-400 font-bold">{{ earner.user.name.charAt(0) }}</span>
                                    </div>

                                    <div>
                                        <p class="font-bold text-base" :class="index === 0 ? 'text-white' : 'text-slate-800'">{{ earner.user.name }}</p>
                                        <p class="text-xs font-bold uppercase tracking-widest mt-0.5" :class="index === 0 ? 'text-amber-400' : 'text-slate-500'">{{ earner.user.badge }}</p>
                                        
                                        <div v-if="earner.user.special_offers" class="mt-2">
                                            <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md border shadow-sm"
                                                 :class="index === 0 ? 'bg-amber-400/20 border-amber-400/30 text-amber-300' : 'bg-gradient-to-r from-amber-100 to-yellow-100 border-amber-200 text-amber-800'">
                                                <span class="text-[10px]">🏆</span>
                                                <span class="text-[9px] font-black uppercase tracking-widest">
                                                    {{ earner.user.special_offers }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            
                            <li v-if="!topEarners.length" class="flex flex-col items-center justify-center py-12 bg-slate-50 rounded-3xl border-2 border-dashed border-slate-200">
                                <span class="text-3xl mb-3">📈</span>
                                <span class="text-slate-500 text-sm font-bold">The leaderboard is wide open.</span>
                                <span class="text-slate-400 text-xs mt-1">Make your first move to secure the #1 spot!</span>
                            </li>
                        </ul>
                    </div>

                    <div class="bg-white rounded-[2rem] p-8 border border-slate-200/80 shadow-sm relative">
                        <div class="flex items-center justify-between mb-8">
                            <h3 class="text-lg font-black text-slate-800 flex items-center tracking-tight">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mr-4 shadow-inner border border-blue-100">🚀</div>
                                Top 3 Recruiters
                            </h3>
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest bg-slate-100 px-3 py-1.5 rounded-full border border-slate-200 shadow-sm">This Week</span>
                        </div>
                        
                        <ul class="space-y-4">
                            <li v-for="(recruiter, index) in topRecruiters.slice(0, 3)" :key="index" 
                                class="relative flex items-center justify-between p-4 rounded-2xl transition-all duration-300 border"
                                :class="{
                                    'bg-gradient-to-br from-slate-900 to-slate-800 border-slate-700 text-white shadow-xl shadow-slate-900/20 transform hover:-translate-y-1 hover:shadow-2xl group overflow-hidden': index === 0,
                                    'bg-gradient-to-r from-slate-50 to-white border-slate-200 hover:shadow-md hover:-translate-y-0.5': index > 0
                                }">
                                
                                <div v-if="index === 0" class="absolute inset-0 bg-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                                <div v-if="index === 0" class="absolute -right-4 -top-4 w-24 h-24 bg-blue-400 rounded-full blur-2xl opacity-20 pointer-events-none"></div>

                                <div class="flex items-center relative z-10">
                                    <div class="w-12 h-12 rounded-xl flex items-center justify-center font-black mr-4 text-base shadow-sm border shrink-0"
                                         :class="{ 
                                            'bg-gradient-to-br from-amber-300 to-amber-500 text-amber-900 border-amber-200 shadow-amber-500/30': index === 0, 
                                            'bg-gradient-to-br from-slate-200 to-slate-300 text-slate-700 border-slate-100': index === 1,
                                            'bg-gradient-to-br from-orange-200 to-orange-300 text-orange-900 border-orange-100': index === 2
                                         }">
                                        {{ index === 0 ? '👑' : index + 1 }}
                                    </div>

                                    <div class="w-12 h-12 rounded-full overflow-hidden border-2 bg-slate-200 flex items-center justify-center mr-4 shrink-0"
                                         :class="index === 0 ? 'border-amber-300' : 'border-slate-200'">
                                        <img v-if="recruiter.sponsor.profile_image_path" :src="'/storage/' + recruiter.sponsor.profile_image_path" class="w-full h-full object-cover" />
                                        <span v-else class="text-slate-400 font-bold">{{ recruiter.sponsor.name.charAt(0) }}</span>
                                    </div>

                                    <div>
                                        <p class="font-bold text-base" :class="index === 0 ? 'text-white' : 'text-slate-800'">{{ recruiter.sponsor.name }}</p>
                                        <p class="text-xs font-bold uppercase tracking-widest mt-0.5" :class="index === 0 ? 'text-amber-400' : 'text-slate-500'">{{ recruiter.sponsor.badge }}</p>
                                        
                                        <div v-if="recruiter.sponsor.special_offers" class="mt-2">
                                            <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md border shadow-sm"
                                                 :class="index === 0 ? 'bg-amber-400/20 border-amber-400/30 text-amber-300' : 'bg-gradient-to-r from-amber-100 to-yellow-100 border-amber-200 text-amber-800'">
                                                <span class="text-[10px]">🏆</span>
                                                <span class="text-[9px] font-black uppercase tracking-widest">
                                                    {{ recruiter.sponsor.special_offers }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>

                            <li v-if="!topRecruiters.length" class="flex flex-col items-center justify-center py-12 bg-slate-50 rounded-3xl border-2 border-dashed border-slate-200">
                                <span class="text-3xl mb-3">🌱</span>
                                <span class="text-slate-500 text-sm font-bold">No network growth detected this week.</span>
                                <span class="text-slate-400 text-xs mt-1">Start registering students to climb the ranks!</span>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>