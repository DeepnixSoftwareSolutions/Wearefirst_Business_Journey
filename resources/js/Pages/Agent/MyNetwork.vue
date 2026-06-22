<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, Link } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';

const props = defineProps({ users: Object, filters: Object });
const searchQuery = ref(props.filters.search || '');

// UI State
const viewingUser = ref(null);
const activeTab = ref('All'); // 'All', 'G1', 'G2', 'G3', 'G4'

watch(searchQuery, (value) => {
    router.get('/agent/my-network', { search: value }, { preserveState: true, replace: true });
});

const closeSlideOver = () => {
    viewingUser.value = null;
};

// --- FORMATTING HELPERS ---
const formatId = (id) => id ? String(id).padStart(4, '0') : '';

// Determines which Line (G1-G4) a user belongs to
const determineLine = (user) => {
    if (!user.intended_parent_node) return 'Root';
    
    const pNode = user.intended_parent_node;
    
    if (pNode.account_type === 'Sub_A') {
        return user.intended_position === 'Left' ? 'G1' : 'G2';
    } else if (pNode.account_type === 'Sub_B') {
        return user.intended_position === 'Left' ? 'G3' : 'G4';
    } else if (pNode.account_type === 'Main') {
        // Fallback for Admin/Root direct placements
        return user.intended_position === 'Left' ? 'G1' : 'G2'; 
    }
    return 'Unknown';
};

const getPlacementString = (user) => {
    if (!user.intended_parent_node || !user.intended_parent_node.user) return 'N/A (Root)';
    
    const pUser = user.intended_parent_node.user;
    const pId = formatId(pUser.member_id);
    const firstName = pUser.name.split(' ')[0];
    
    let subLeg = '';
    if (user.intended_parent_node.account_type === 'Sub_A') subLeg = 'L';
    if (user.intended_parent_node.account_type === 'Sub_B') subLeg = 'R';

    const gLine = determineLine(user);

    return `${pId} ${firstName} ${subLeg} ${gLine}`.replace(/\s+/g, ' ').trim();
};

// --- INSTANT IN-MEMORY FILTERING ---
const filteredUsers = computed(() => {
    if (activeTab.value === 'All') return props.users.data;
    
    return props.users.data.filter(user => {
        return determineLine(user) === activeTab.value;
    });
});

// Calculate counts for the tabs dynamically
const tabCounts = computed(() => {
    const counts = { 'All': props.users.data.length, 'G1': 0, 'G2': 0, 'G3': 0, 'G4': 0 };
    props.users.data.forEach(user => {
        const line = determineLine(user);
        if (counts[line] !== undefined) counts[line]++;
    });
    return counts;
});

</script>

<template>
    <Head title="My Direct Recruits" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-bold text-2xl text-slate-800 tracking-tight">My Direct Recruits</h2>
        </template>

        <div class="py-12 max-w-[90rem] mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <input v-model="searchQuery" type="text" placeholder="Search your recruits by Member ID, Name, or NIC..." class="w-full md:w-1/3 rounded-xl border-slate-300 shadow-sm focus:ring-teal-500 focus:border-teal-500">
                <div class="text-sm font-bold text-slate-500">Total Found: {{ users.total }}</div>
            </div>
            
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden">
                
                <div class="flex overflow-x-auto border-b border-slate-100 bg-slate-50/50 p-2 gap-2 hide-scrollbar">
                    <button v-for="tab in ['All', 'G1', 'G2', 'G3', 'G4']" :key="tab"
                            @click="activeTab = tab"
                            class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all whitespace-nowrap flex items-center gap-2"
                            :class="activeTab === tab ? 'bg-white text-teal-700 shadow-sm border border-slate-200/60' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-700'">
                        {{ tab === 'All' ? 'View All Recruits' : `Line ${tab}` }}
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-black"
                              :class="activeTab === tab ? 'bg-teal-50 text-teal-600' : 'bg-slate-200 text-slate-500'">
                            {{ tabCounts[tab] }}
                        </span>
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left whitespace-nowrap">
                        <thead class="bg-slate-50 border-b text-xs uppercase text-slate-500 font-bold tracking-widest">
                            <tr>
                                <th class="p-5 pl-8">Student Info</th>
                                <th class="p-5">Contact details</th>
                                <th class="p-5">Network Line</th>
                                <th class="p-5">Current Status</th>
                                <th class="p-5 text-right pr-8">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="user in filteredUsers" :key="user.id" class="transition-colors hover:bg-slate-50" :class="{'bg-rose-50/50': user.is_held}">
                                
                                <td class="p-5 pl-8 flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-full bg-slate-200 border border-slate-300 overflow-hidden flex items-center justify-center shrink-0">
                                        <img v-if="user.profile_image_path" :src="'/storage/' + user.profile_image_path" class="w-full h-full object-cover">
                                        <span v-else class="font-bold text-slate-500">{{ user.name.charAt(0) }}</span>
                                    </div>
                                    <div>
                                        <div class="font-black text-slate-800">{{ user.name }} <span class="text-[10px] text-slate-400 font-bold ml-1 bg-slate-100 px-1.5 py-0.5 rounded">ID: {{ formatId(user.member_id) }}</span></div>
                                        <div class="text-xs font-bold text-slate-500 mt-0.5">{{ user.nic }}</div>
                                    </div>
                                </td>
                                
                                <td class="p-5">
                                    <div class="text-sm font-bold text-slate-700">{{ user.phone }}</div>
                                    <div class="text-xs text-slate-500">{{ user.email }}</div>
                                </td>

                                <td class="p-5">
                                    <span class="px-3 py-1 rounded-lg text-xs font-black tracking-widest border"
                                          :class="{
                                              'bg-blue-50 text-blue-700 border-blue-200': determineLine(user) === 'G1',
                                              'bg-indigo-50 text-indigo-700 border-indigo-200': determineLine(user) === 'G2',
                                              'bg-purple-50 text-purple-700 border-purple-200': determineLine(user) === 'G3',
                                              'bg-fuchsia-50 text-fuchsia-700 border-fuchsia-200': determineLine(user) === 'G4',
                                              'bg-slate-50 text-slate-700 border-slate-200': !['G1','G2','G3','G4'].includes(determineLine(user))
                                          }">
                                        {{ determineLine(user) }}
                                    </span>
                                </td>

                                <td class="p-5">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-bold text-teal-600">{{ user.role }}</span>
                                        <span v-if="user.badge !== 'None'" class="px-2 py-0.5 bg-amber-100 text-amber-800 text-[10px] font-black rounded uppercase tracking-widest">{{ user.badge }}</span>
                                    </div>
                                    <div v-if="user.is_held" class="text-[10px] font-black text-rose-600 uppercase tracking-widest mt-1">On Hold</div>
                                    <div v-else-if="user.admission_status === 'Pending Approval'" class="text-[10px] font-black text-amber-600 uppercase tracking-widest mt-1">Pending Approval</div>
                                    <div v-else class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mt-1">Active</div>
                                </td>

                                <td class="p-5 text-right pr-8">
                                    <button @click="viewingUser = user" class="px-4 py-2 bg-slate-100 text-slate-700 hover:bg-slate-200 rounded-lg font-bold text-xs transition shadow-sm border border-slate-200">
                                        View Details
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="filteredUsers.length === 0">
                                <td colspan="5" class="p-10 text-center text-slate-500">
                                    <span v-if="activeTab !== 'All'">No recruits found in Line {{ activeTab }}.</span>
                                    <span v-else>You haven't registered any students yet.</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="mt-6 flex justify-center gap-2">
                <button v-for="link in users.links" :key="link.label" 
                        @click="link.url ? router.get(link.url, { search: searchQuery }, { preserveState: true }) : null"
                        v-html="link.label"
                        class="px-4 py-2 rounded-lg text-sm font-bold transition"
                        :class="[
                            link.active ? 'bg-teal-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200',
                            !link.url ? 'opacity-50 cursor-not-allowed' : ''
                        ]"></button>
            </div>
        </div>

        <transition enter-active-class="transform transition ease-in-out duration-300" enter-from-class="translate-x-full" enter-to-class="translate-x-0" leave-active-class="transform transition ease-in-out duration-300" leave-from-class="translate-x-0" leave-to-class="translate-x-full">
            <div v-if="viewingUser" class="fixed inset-y-0 right-0 w-full md:w-[28rem] bg-white shadow-2xl z-50 flex flex-col overflow-hidden border-l border-slate-200">
                
                <div class="p-6 bg-slate-900 text-white flex justify-between items-start shrink-0 relative overflow-hidden">
                    <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-teal-500 rounded-full opacity-20 blur-2xl pointer-events-none"></div>
                    <div class="relative z-10 flex gap-4 items-center">
                        <div class="w-16 h-16 rounded-full bg-slate-800 border-2 border-slate-600 overflow-hidden flex items-center justify-center shrink-0">
                            <img v-if="viewingUser.profile_image_path" :src="'/storage/' + viewingUser.profile_image_path" class="w-full h-full object-cover">
                            <span v-else class="font-black text-2xl text-slate-400">{{ viewingUser.name.charAt(0) }}</span>
                        </div>
                        <div>
                            <h2 class="text-xl font-black tracking-tight">{{ viewingUser.name }}</h2>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="px-2 py-0.5 bg-teal-500/20 border border-teal-500/30 text-teal-300 text-[10px] font-black rounded uppercase tracking-widest">{{ viewingUser.role }}</span>
                            </div>
                        </div>
                    </div>
                    <button @click="closeSlideOver" class="relative z-10 text-slate-400 hover:text-white bg-slate-800 p-2 rounded-full transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6 flex-1 overflow-y-auto space-y-8 bg-slate-50">
                    
                    <div>
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 border-b border-slate-200 pb-2">Network Placement</h4>
                        <div class="bg-white rounded-2xl border border-slate-200 p-4 space-y-3 shadow-sm">
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-bold text-slate-500">Unique ID</span>
                                <span class="text-sm font-black text-slate-800 font-mono">{{ formatId(viewingUser.member_id) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-bold text-slate-500">Sponsor Agent</span>
                                <span class="text-sm font-black text-slate-800">
                                    {{ viewingUser.sponsor ? `${formatId(viewingUser.sponsor.member_id)} ${viewingUser.sponsor.name.split(' ')[0]}` : 'N/A (Root)' }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-bold text-slate-500">Parent Node</span>
                                <span class="text-sm font-black text-slate-800">{{ getPlacementString(viewingUser) }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center pt-3 border-t border-slate-100">
                                <span class="text-xs font-bold text-slate-500">G2/G3 Status</span>
                                <span class="text-[10px] font-black uppercase tracking-widest px-2 py-0.5 rounded" 
                                      :class="viewingUser.middle_legs_unlocked ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-rose-100 text-rose-700 border border-rose-200'">
                                    {{ viewingUser.middle_legs_unlocked ? 'Unlocked' : 'Locked' }}
                                </span>
                            </div>
                            
                            <div class="pt-2">
                                <Link :href="route('network.tree')" class="w-full block text-center py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition">
                                    Open in Network Tree
                                </Link>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 border-b border-slate-200 pb-2">Identity & Contact</h4>
                        <div class="bg-white rounded-2xl border border-slate-200 p-4 space-y-3 shadow-sm">
                            <div class="flex justify-between"><span class="text-xs font-bold text-slate-500">NIC Number</span><span class="text-sm font-black text-slate-800">{{ viewingUser.nic }}</span></div>
                            <div class="flex justify-between"><span class="text-xs font-bold text-slate-500">Email Address</span><span class="text-sm font-black text-slate-800">{{ viewingUser.email }}</span></div>
                            <div class="flex justify-between"><span class="text-xs font-bold text-slate-500">Phone Number</span><span class="text-sm font-black text-slate-800">{{ viewingUser.phone }}</span></div>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 border-b border-slate-200 pb-2">Location Information</h4>
                        <div class="bg-white rounded-2xl border border-slate-200 p-4 space-y-3 shadow-sm">
                            <div class="flex justify-between">
                                <span class="text-xs font-bold text-slate-500">District</span>
                                <span class="text-sm font-black text-slate-800">{{ viewingUser.district || 'Not Provided' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-xs font-bold text-slate-500">City</span>
                                <span class="text-sm font-black text-slate-800">{{ viewingUser.city || 'Not Provided' }}</span>
                            </div>
                            <div>
                                <span class="block text-xs font-bold text-slate-500 mb-1">Full Address</span>
                                <p class="text-sm font-bold text-slate-800 leading-snug">{{ viewingUser.address || 'Not Provided' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </transition>

        <div v-if="viewingUser" @click="closeSlideOver" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40 transition-opacity"></div>
    </AuthenticatedLayout>
</template>

<style scoped>
.hide-scrollbar::-webkit-scrollbar {
    display: none;
}
.hide-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>