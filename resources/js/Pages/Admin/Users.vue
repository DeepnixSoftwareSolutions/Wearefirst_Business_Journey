<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import { Head, useForm, router, Link, usePage } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import { useConfirm } from '@/Composables/useConfirm';

const { confirmAction } = useConfirm();

const props = defineProps({ users: Object, filters: Object });
const authUser = usePage().props.auth.user;

// --- LOCATION DATA ---
const locations = {
    'Ampara': [ 'Addalaichenai', 'Akkaraipattu', 'Ampara', 'Dehiattakandiya', 'Kalmunai', 'Maha Oya', 'Nintavur', 'Pottuvil', 'Sainthamaruthu', 'Sammanthurai', 'Uhana' ],
    'Anuradhapura': [ 'Anuradhapura', 'Eppawala', 'Galenbindunuwewa', 'Horowpathana', 'Kekirawa', 'Medawachchiya', 'Mihintale', 'Nochchiyagama', 'Talawa', 'Tambuttegama' ],
    'Badulla': [ 'Badulla', 'Bandarawela', 'Diyatalawa', 'Ella', 'Hali-Ela', 'Haputale', 'Lunugala', 'Mahiyanganaya', 'Meegahakiula', 'Passara', 'Welimada' ],
    'Batticaloa': [ 'Batticaloa', 'Chenkalady', 'Eravur', 'Kaluwanchikudy', 'Kattankudy', 'Oddamavadi', 'Vakarai', 'Valaichenai' ],
    'Colombo': [ 'Athurugiriya', 'Avissawella', 'Battaramulla', 'Boralesgamuwa', 'Colombo 01 - Fort', 'Colombo 02 - Slave Island', 'Colombo 03 - Kollupitiya', 'Colombo 04 - Bambalapitiya', 'Colombo 05 - Havelock Town', 'Colombo 06 - Wellawatte', 'Colombo 07 - Cinnamon Gardens', 'Colombo 08 - Borella', 'Colombo 09 - Dematagoda', 'Colombo 10 - Maradana', 'Colombo 11 - Pettah', 'Colombo 12 - Hulftsdorp', 'Colombo 13 - Kotahena', 'Colombo 14 - Grandpass', 'Colombo 15 - Mattakkuliya', 'Dehiwala', 'Hanwella', 'Homagama', 'Kaduwela', 'Kesbewa', 'Kolonnawa', 'Kosgama', 'Maharagama', 'Malabe', 'Moratuwa', 'Mount Lavinia', 'Nugegoda', 'Padukka', 'Piliyandala', 'Ratmalana', 'Sri Jayawardenepura Kotte', 'Wellampitiya' ],
    'Galle': [ 'Ahungalla', 'Akmeemana', 'Ambalangoda', 'Baddegama', 'Bentota', 'Elpitiya', 'Galle', 'Hikkaduwa', 'Imaduwa', 'Karandeniya', 'Karapitiya', 'Unawatuna', 'Yakkalamulla' ],
    'Gampaha': [ 'Biyagama', 'Divulapitiya', 'Ekala', 'Gampaha', 'Ganemulla', 'Ja-Ela', 'Kadawatha', 'Kandana', 'Katunayake', 'Kelaniya', 'Kiribathgoda', 'Kochchikade', 'Mabole', 'Minuwangoda', 'Mirigama', 'Negombo', 'Nittambuwa', 'Ragama', 'Seeduwa', 'Veyangoda', 'Wattala' ],
    'Hambantota': [ 'Ambalantota', 'Angunakolapelessa', 'Beliatta', 'Hambantota', 'Kataragama', 'Lunugamvehera', 'Sooriyawewa', 'Tangalle', 'Tissamaharama', 'Weeraketiya' ],
    'Jaffna': [ 'Chavakachcheri', 'Delft', 'Jaffna', 'Karainagar', 'Kopay', 'Manipay', 'Nallur', 'Point Pedro', 'Uduvil', 'Valvettithurai', 'Velanai' ],
    'Kalutara': [ 'Agalawatta', 'Aluthgama', 'Bandaragama', 'Beruwala', 'Bulathsinhala', 'Dharga Town', 'Horana', 'Ingiriya', 'Kalutara', 'Matugama', 'Millaniya', 'Palindanuwara', 'Panadura', 'Payagala', 'Wadduwa' ],
    'Kandy': [ 'Akurana', 'Digana', 'Galagedara', 'Gampola', 'Kadugannawa', 'Kandy', 'Katugastota', 'Kundasale', 'Menikhinna', 'Peradeniya', 'Poojapitiya', 'Teldeniya', 'Wattegama' ],
    'Kegalle': [ 'Aranayaka', 'Bulathkohupitiya', 'Dehiowita', 'Deraniyagala', 'Galigamuwa', 'Kegalle', 'Mawanella', 'Rambukkana', 'Ruwanwella', 'Warakapola', 'Yatiyantota' ],
    'Kilinochchi': [ 'Akkarayankulam', 'Kilinochchi', 'Pallai', 'Paranthan', 'Poonakary' ],
    'Kurunegala': [ 'Alawwa', 'Bingiriya', 'Galgamuwa', 'Ibbagamuwa', 'Kobeigane', 'Kuliyapitiya', 'Kurunegala', 'Mawathagama', 'Melsiripura', 'Narammala', 'Nikaweratiya', 'Pannala', 'Polgahawela', 'Wariyapola' ],
    'Mannar': [ 'Madhu', 'Mannar', 'Murunkan', 'Pesalai', 'Talaimannar' ],
    'Matale': [ 'Dambulla', 'Galewela', 'Laggala', 'Matale', 'Naula', 'Pallepola', 'Rattota', 'Sigiriya', 'Ukuwela', 'Wilgamuwa', 'Yatawatta' ],
    'Matara': [ 'Akuressa', 'Deniyaya', 'Devinuwara', 'Dikwella', 'Hakmana', 'Kamburupitiya', 'Kirinda Puhulwella', 'Malimbada', 'Matara', 'Weligama' ],
    'Monaragala': [ 'Bibile', 'Buttala', 'Kataragama', 'Madulla', 'Monaragala', 'Siyambalanduwa', 'Thanamalwila', 'Wellawaya' ],
    'Mullaitivu': [ 'Mankulam', 'Mullaitivu', 'Mulliyawalai', 'Oddusuddan', 'Puthukkudiyiruppu' ],
    'Nuwara Eliya': [ 'Ambewela', 'Ginigathhena', 'Hatton', 'Kotagala', 'Lindula', 'Maskeliya', 'Nanu Oya', 'Nuwara Eliya', 'Pattipola', 'Ragala', 'Talawakelle', 'Walapane' ],
    'Polonnaruwa': [ 'Bakamuna', 'Dimbulagala', 'Hingurakgoda', 'Kaduruwela', 'Medirigiriya', 'Polonnaruwa', 'Welikanda' ],
    'Puttalam': [ 'Anamaduwa', 'Chilaw', 'Dankotuwa', 'Kalpitiya', 'Madampe', 'Marawila', 'Mundel', 'Nattandiya', 'Norochcholai', 'Puttalam', 'Wennappuwa' ],
    'Ratnapura': [ 'Ayagama', 'Balangoda', 'Eheliyagoda', 'Elapatha', 'Embilipitiya', 'Godakawela', 'Kahawatta', 'Kalawana', 'Kolonna', 'Kuruwita', 'Nivithigala', 'Opanayaka', 'Pelmadulla', 'Rakwana', 'Ratnapura', 'Udawalawe', 'Weligepola' ],
    'Trincomalee': [ 'Kantale', 'Kinniya', 'Kuchchaveli', 'Muttur', 'Nilaveli', 'Seruwila', 'Thampalakamam', 'Trincomalee' ],
    'Vavuniya': [ 'Cheddikulam', 'Nedunkeni', 'Omanthai', 'Vavuniya' ]
};
const districtsList = Object.keys(locations).sort();

const searchQuery = ref(props.filters.search || '');
const selectedDistrict = ref(props.filters.district || '');
const selectedCity = ref(props.filters.city || '');

const availableCities = computed(() => selectedDistrict.value ? [...locations[selectedDistrict.value]].sort() : []);

const handleDistrictChange = () => {
    selectedCity.value = '';
};

watch([searchQuery, selectedDistrict, selectedCity], ([search, district, city]) => {
    router.get('/admin/users', { search, district, city }, { preserveState: true, replace: true });
}, { deep: true });

// --- STATE ---
const selectedUserId = ref(null);
const offerModal = ref(false);
const offerForm = useForm({ offer_name: '' });

// MODAL STATE
const masterModal = ref(false);
const editTab = ref('general'); // 'general', 'location', 'finance'
const masterForm = useForm({
    // General
    name: '', email: '', phone: '',
    // Identity & Location
    nic: '', nic_type: 'Own', district: '', city: '', address: '',
    // Beneficiary
    beneficiary_name: '', beneficiary_relationship: '', beneficiary_nic: '', beneficiary_phone: '',
    // Bank
    bank_account_name: '', bank_account_no: '', bank_name: '', bank_branch: ''
});

const viewingUser = computed(() => {
    return props.users.data.find(u => u.id === selectedUserId.value) || null;
});

const availableEditCities = computed(() => masterForm.district ? [...locations[masterForm.district]].sort() : []);
const handleEditDistrictChange = () => masterForm.city = '';

// --- ACTIONS ---
const toggleHold = async (id) => {
    const isCurrentlyHeld = viewingUser.value?.is_held;
    const confirmed = await confirmAction({
        title: isCurrentlyHeld ? 'Reactivate Account' : 'Place Account on Hold',
        message: isCurrentlyHeld 
            ? 'Are you sure you want to remove the financial hold and reactivate this account?' 
            : 'Are you sure you want to freeze this account? They will not be able to recruit or earn payouts until the hold is lifted.',
        confirmText: isCurrentlyHeld ? 'Reactivate Account' : 'Place on Hold',
        confirmButtonTheme: isCurrentlyHeld ? 'success' : 'danger'
    });

    if (confirmed) {
        useForm({}).post(`/admin/agents/${id}/toggle-hold`, { preserveScroll: true });
    }
};

const unlockLegs = async (id) => {
    const confirmed = await confirmAction({
        title: 'Unlock Middle Legs',
        message: 'Permanently unlock the G2 and G3 lines for this agent? This will allow them to recruit into their inner network.',
        confirmText: 'Unlock G2/G3',
        confirmButtonTheme: 'primary'
    });

    if (confirmed) {
        useForm({}).post(`/admin/agents/${id}/unlock-legs`, { preserveScroll: true });
    }
};

const deleteUser = async (id) => {
    const confirmed = await confirmAction({
        title: 'Terminate Account',
        message: 'WARNING: This will anonymize the user and freeze their nodes permanently. Proceed?',
        confirmText: 'Terminate Permanently',
        confirmButtonTheme: 'danger'
    });

    if (confirmed) {
        useForm({}).delete(`/admin/users/${id}/safe-delete`, { 
            preserveScroll: true,
            onSuccess: () => selectedUserId.value = null 
        });
    }
};

const openOfferModal = () => { offerModal.value = true; };
const submitOffer = () => {
    offerForm.post(`/admin/users/${selectedUserId.value}/award-offer`, {
        preserveScroll: true,
        onSuccess: () => { offerModal.value = false; offerForm.reset(); }
    });
};
const removeOffer = async () => {
    const confirmed = await confirmAction({ title: 'Revoke Reward', message: 'Remove active reward?', confirmText: 'Revoke Reward', confirmButtonTheme: 'danger' });
    if (confirmed) router.post(`/admin/users/${selectedUserId.value}/award-offer`, { offer_name: null }, { preserveScroll: true });
};

const openMasterEdit = () => {
    const u = viewingUser.value;
    
    // General & Location
    masterForm.name = u.name || '';
    masterForm.email = u.email || '';
    masterForm.phone = u.phone || '';
    masterForm.district = u.district || '';
    masterForm.city = u.city || '';
    masterForm.address = u.address || '';
    
    // Identity
    masterForm.nic = u.nic || '';
    masterForm.nic_type = u.nic_type || 'Own';
    
    // Beneficiary
    masterForm.beneficiary_name = u.beneficiary_details?.name || '';
    masterForm.beneficiary_relationship = u.beneficiary_details?.relationship || '';
    masterForm.beneficiary_nic = u.beneficiary_details?.nic || '';
    masterForm.beneficiary_phone = u.beneficiary_details?.phone || '';
    
    // Bank
    masterForm.bank_account_name = u.bank_details?.account_name || '';
    masterForm.bank_account_no = u.bank_details?.account_no || '';
    masterForm.bank_name = u.bank_details?.bank_name || '';
    masterForm.bank_branch = u.bank_details?.branch || '';
    
    masterForm.clearErrors();
    editTab.value = 'general';
    masterModal.value = true;
};

const submitMasterUpdate = () => {
    masterForm.post(`/admin/users/${selectedUserId.value}/update-master-profile`, {
        preserveScroll: true,
        onSuccess: () => { masterModal.value = false; masterForm.reset(); }
    });
};

// --- Formatting ---
const formatId = (id) => id ? String(id).padStart(4, '0') : '';

const getPlacementString = (user) => {
    if (!user.intended_parent_node || !user.intended_parent_node.user) return 'N/A (Root)';
    const pNode = user.intended_parent_node;
    const pUser = pNode.user;
    const pId = formatId(pUser.member_id);
    const firstName = pUser.name.split(' ')[0];
    let subLeg = pNode.account_type === 'Sub_A' ? 'L' : (pNode.account_type === 'Sub_B' ? 'R' : '');
    let gLine = '';
    if (pNode.account_type === 'Sub_A') gLine = user.intended_position === 'Left' ? 'G1' : 'G2';
    else if (pNode.account_type === 'Sub_B') gLine = user.intended_position === 'Left' ? 'G3' : 'G4';
    else gLine = user.intended_position === 'Left' ? 'G1' : 'G2';
    return `${pId} ${firstName} ${subLeg} ${gLine}`.replace(/\s+/g, ' ').trim();
};
</script>

<template>
    <Head title="Manage Network" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-bold text-2xl text-slate-800 tracking-tight">Manage All Users In The System</h2>
        </template>

        <div class="py-12 max-w-[90rem] mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-4 rounded-[2rem] border border-slate-200 shadow-sm mb-6 flex flex-col lg:flex-row justify-between items-center gap-4">
                <div class="flex flex-col md:flex-row w-full lg:w-auto gap-3 flex-1">
                    <div class="relative w-full md:max-w-xs">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </div>
                        <input v-model="searchQuery" type="text" placeholder="Search by ID, Name, or NIC..." class="w-full pl-9 rounded-xl border-slate-300 shadow-sm focus:ring-teal-500 focus:border-teal-500 text-sm">
                    </div>

                    <select v-model="selectedDistrict" @change="handleDistrictChange" class="w-full md:w-48 rounded-xl border-slate-300 shadow-sm focus:ring-teal-500 focus:border-teal-500 text-sm">
                        <option value="">All Districts</option>
                        <option v-for="dist in districtsList" :key="dist" :value="dist">{{ dist }}</option>
                    </select>

                    <select v-model="selectedCity" :disabled="!selectedDistrict" class="w-full md:w-48 rounded-xl border-slate-300 shadow-sm focus:ring-teal-500 focus:border-teal-500 text-sm disabled:bg-slate-50 disabled:text-slate-400">
                        <option value="">All Cities</option>
                        <option v-for="city in availableCities" :key="city" :value="city">{{ city }}</option>
                    </select>
                </div>
                <div class="text-sm font-bold text-slate-500 shrink-0 bg-slate-100 px-4 py-2 rounded-xl">
                    Records: {{ users.total }}
                </div>
            </div>
            
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-x-auto">
                <table class="w-full text-left whitespace-nowrap">
                    <thead class="bg-slate-50 border-b text-xs uppercase text-slate-500 font-bold tracking-widest">
                        <tr>
                            <th class="p-5 pl-8">Identity</th>
                            <th class="p-5">Contact Details</th>
                            <th class="p-5">System Role</th>
                            <th class="p-5 text-right pr-8">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="user in users.data" :key="user.id" class="transition-colors hover:bg-slate-50" :class="{'bg-rose-50/30': user.is_held}">
                            <td class="p-5 pl-8 flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-slate-200 border border-slate-300 overflow-hidden flex items-center justify-center shrink-0">
                                    <img v-if="user.profile_image_path" :src="'/storage/' + user.profile_image_path" class="w-full h-full object-cover">
                                    <span v-else class="font-bold text-slate-500">{{ user.name.charAt(0) }}</span>
                                </div>
                                <div>
                                    <div class="font-black text-slate-800">{{ user.name }} <span class="text-[10px] text-slate-400 ml-1 font-mono">#{{ formatId(user.member_id) }}</span></div>
                                    <div class="text-xs font-bold text-slate-500 mt-0.5">NIC: {{ user.nic }}</div>
                                </div>
                            </td>
                            <td class="p-5">
                                <div class="text-sm font-bold text-slate-700">{{ user.phone }}</div>
                                <div class="text-xs text-slate-500">{{ user.email }}</div>
                            </td>
                            <td class="p-5">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold" :class="user.role === 'Admin' ? 'text-indigo-600' : 'text-teal-600'">{{ user.role }}</span>
                                    <span v-if="user.badge !== 'None'" class="px-2 py-0.5 bg-amber-100 text-amber-800 text-[10px] font-black rounded uppercase tracking-widest">{{ user.badge }}</span>
                                </div>
                                <div v-if="user.is_held" class="text-[10px] font-black text-rose-600 uppercase mt-1">Status: On Hold</div>
                                <div v-else-if="user.admission_status === 'Pending Approval'" class="text-[10px] font-black text-amber-600 uppercase tracking-widest mt-1">Pending Approval</div>
                                <div v-else class="text-[10px] font-black text-emerald-600 uppercase mt-1">Status: Active</div>
                            </td>
                            <td class="p-5 text-right pr-8">
                                <button @click="selectedUserId = user.id" class="px-6 py-2 bg-slate-900 text-white rounded-xl font-bold text-xs hover:bg-teal-600 transition shadow-md shadow-slate-900/10">
                                    View Details & Controls
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="mt-6 flex justify-center gap-2">
                <button v-for="link in users.links" :key="link.label" 
                        @click="link.url ? router.get(link.url, { search: searchQuery, district: selectedDistrict, city: selectedCity }, { preserveState: true }) : null"
                        v-html="link.label"
                        class="px-4 py-2 rounded-lg text-sm font-bold transition"
                        :class="[
                            link.active ? 'bg-teal-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200',
                            !link.url ? 'opacity-50 cursor-not-allowed' : ''
                        ]"></button>
            </div>
        </div>

        <transition enter-active-class="transform transition ease-in-out duration-300" enter-from-class="translate-x-full" enter-to-class="translate-x-0" leave-active-class="transform transition ease-in-out duration-300" leave-from-class="translate-x-0" leave-to-class="translate-x-full">
            <div v-if="viewingUser" class="fixed inset-y-0 right-0 w-full md:w-[32rem] bg-white shadow-2xl z-40 flex flex-col overflow-hidden border-l border-slate-200">
                
                <div class="p-6 bg-slate-900 text-white flex justify-between items-start shrink-0 relative overflow-hidden">
                    <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-teal-500 rounded-full opacity-20 blur-2xl pointer-events-none"></div>
                    <div class="relative z-10 flex gap-4 items-center">
                        <div class="w-16 h-16 rounded-full bg-slate-800 border-2 border-slate-600 overflow-hidden flex items-center justify-center shrink-0">
                            <img v-if="viewingUser.profile_image_path" :src="'/storage/' + viewingUser.profile_image_path" class="w-full h-full object-cover">
                            <span v-else class="font-black text-2xl text-slate-400">{{ viewingUser.name.charAt(0) }}</span>
                        </div>
                        <div>
                            <h2 class="text-xl font-black tracking-tight">{{ viewingUser.name }}</h2>
                            <p class="text-teal-400 text-xs font-bold uppercase tracking-widest">ID: {{ formatId(viewingUser.member_id) }} • {{ viewingUser.role }}</p>
                        </div>
                    </div>
                    <button @click="selectedUserId = null" class="relative z-10 text-slate-400 hover:text-white bg-slate-800 p-2 rounded-full transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6 flex-1 overflow-y-auto space-y-8 bg-slate-50">
                    
                    <div>
                        <div class="flex justify-between items-end mb-3 border-b border-slate-200 pb-2">
                            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Management Commands</h4>
                            <button v-if="authUser.role === 'Admin' && viewingUser.role !== 'Admin'" @click="openMasterEdit" class="text-xs font-bold text-blue-600 hover:text-blue-700 transition flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                Update User
                            </button>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <button v-if="['Agent', 'Student'].includes(viewingUser.role) && !viewingUser.middle_legs_unlocked" 
                                    @click="unlockLegs(viewingUser.id)" class="py-3 bg-teal-600 text-white rounded-xl font-bold text-xs hover:bg-teal-700 transition shadow-sm">
                                Unlock G2/G3
                            </button>
                            <div v-else-if="['Agent', 'Student'].includes(viewingUser.role)" class="py-3 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-xl font-black text-[10px] text-center uppercase tracking-widest">
                                G2/G3 Unlocked
                            </div>

                            <button v-if="viewingUser.role === 'Agent'" 
                                    @click="toggleHold(viewingUser.id)" 
                                    class="py-3 font-bold text-xs rounded-xl transition border shadow-sm"
                                    :class="viewingUser.is_held ? 'bg-amber-100 text-amber-800 border-amber-200 hover:bg-amber-200' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'">
                                {{ viewingUser.is_held ? 'Reactivate Account' : 'Place on Hold' }}
                            </button>

                            <button v-if="viewingUser.role === 'Agent'" 
                                    @click="openOfferModal" class="py-3 bg-violet-600 text-white rounded-xl font-bold text-xs hover:bg-violet-700 transition shadow-sm">
                                Award Reward
                            </button>
                            
                            <Link :href="route('network.tree')" class="py-3 bg-slate-200 text-slate-700 rounded-xl font-bold text-xs text-center hover:bg-slate-300 transition flex items-center justify-center">
                                View in Tree
                            </Link>
                        </div>
                    </div>

                    <div v-if="viewingUser.special_offers">
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 border-b border-slate-200 pb-2">Active Special Reward</h4>
                        <div class="bg-gradient-to-r from-violet-50 to-fuchsia-50 border border-violet-100 rounded-xl p-4 flex justify-between items-center shadow-sm">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-violet-200 text-violet-700 flex items-center justify-center text-lg">🎁</div>
                                <span class="text-sm font-black text-violet-900">{{ viewingUser.special_offers }}</span>
                            </div>
                            <button @click="removeOffer" title="Revoke Reward" class="w-8 h-8 rounded-full bg-white border border-rose-200 text-rose-500 flex items-center justify-center hover:bg-rose-500 hover:text-white transition shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 border-b border-slate-200 pb-2">Network Placement</h4>
                        <div class="bg-white rounded-2xl border border-slate-200 p-4 space-y-3 shadow-sm">
                            <div class="flex justify-between">
                                <span class="text-xs font-bold text-slate-500">Sponsor Agent</span>
                                <span class="text-sm font-black text-slate-800">{{ viewingUser.sponsor ? `${formatId(viewingUser.sponsor.member_id)} ${viewingUser.sponsor.name.split(' ')[0]}` : 'N/A (Root)' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-xs font-bold text-slate-500">Parent Node</span>
                                <span class="text-sm font-black text-slate-800">{{ getPlacementString(viewingUser) }}</span>
                            </div>
                            <div class="flex justify-between border-t border-slate-100 pt-3">
                                <span class="text-xs font-bold text-slate-500">G2/G3 Status</span>
                                <span class="text-[10px] font-black uppercase tracking-widest px-2 py-0.5 rounded" 
                                      :class="viewingUser.middle_legs_unlocked ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-rose-100 text-rose-700 border border-rose-200'">
                                    {{ viewingUser.middle_legs_unlocked ? 'Unlocked' : 'Locked' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 border-b border-slate-200 pb-2">Business Financials</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm text-center">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Lifetime Earnings</span>
                                <div class="text-xl font-black text-slate-800 mt-1">Rs {{ viewingUser.total_historical_earned }}</div>
                            </div>
                            <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm text-center">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Pending Balance</span>
                                <div class="text-xl font-black text-teal-600 mt-1">Rs {{ viewingUser.pending_payout_balance }}</div>
                            </div>
                            <div class="col-span-2 bg-white rounded-2xl border border-slate-200 p-4 shadow-sm flex justify-between items-center">
                                <span class="text-xs font-bold text-slate-500">Admission Fee Paid</span>
                                <span class="text-sm font-black" :class="viewingUser.admission_fee_paid >= 15000 ? 'text-emerald-600' : 'text-amber-600'">
                                    Rs {{ viewingUser.admission_fee_paid }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 border-b border-slate-200 pb-2">Identity & Contact</h4>
                        <div class="bg-white rounded-2xl border border-slate-200 p-4 space-y-3 shadow-sm">
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-bold text-slate-500">NIC Number</span>
                                <div class="text-right">
                                    <span class="text-sm font-black text-slate-800">{{ viewingUser.nic }}</span>
                                    <span class="ml-2 text-[9px] font-bold text-slate-500 uppercase tracking-widest bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200">
                                        {{ viewingUser.nic_type || 'Own' }}
                                    </span>
                                </div>
                            </div>
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

                    <div>
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 border-b border-slate-200 pb-2">Legal Beneficiary</h4>
                        <div v-if="viewingUser.beneficiary_details" class="bg-indigo-50 rounded-2xl border border-indigo-100 p-4 space-y-3 shadow-sm">
                            <div class="flex justify-between">
                                <span class="text-xs font-bold text-indigo-400">Full Name</span>
                                <span class="text-sm font-black text-indigo-900">{{ viewingUser.beneficiary_details.name || 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-xs font-bold text-indigo-400">Relationship</span>
                                <span class="text-sm font-black text-indigo-900">{{ viewingUser.beneficiary_details.relationship || 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-xs font-bold text-indigo-400">NIC Number</span>
                                <span class="text-sm font-black text-indigo-900">{{ viewingUser.beneficiary_details.nic || 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-xs font-bold text-indigo-400">Phone Number</span>
                                <span class="text-sm font-black text-indigo-900">{{ viewingUser.beneficiary_details.phone || 'N/A' }}</span>
                            </div>
                        </div>
                        <div v-else class="bg-white rounded-2xl border border-slate-200 p-6 text-center shadow-sm">
                            <span class="text-sm font-bold text-slate-400">No beneficiary configured yet.</span>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 border-b border-slate-200 pb-2">Bank Details for Payouts</h4>
                        <div v-if="viewingUser.bank_details" class="bg-emerald-50 rounded-2xl border border-emerald-100 p-4 shadow-sm">
                            <div class="flex justify-between mb-3">
                                <span class="text-xs font-bold text-emerald-600">Bank & Branch</span>
                                <span class="text-sm font-black text-emerald-900">{{ viewingUser.bank_details.bank_name }} ({{ viewingUser.bank_details.branch }})</span>
                            </div>
                            <div class="flex justify-between mb-3">
                                <span class="text-xs font-bold text-emerald-600">Account No</span>
                                <span class="text-sm font-black text-emerald-900 font-mono">{{ viewingUser.bank_details.account_no }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-xs font-bold text-emerald-600">Account Holder</span>
                                <span class="text-sm font-black text-emerald-900">{{ viewingUser.bank_details.account_name }}</span>
                            </div>
                        </div>
                        <div v-else class="bg-white rounded-2xl border border-slate-200 p-6 text-center shadow-sm">
                            <span class="text-sm font-bold text-slate-400">No bank details configured.</span>
                        </div>
                    </div>

                    <div class="pt-4 pb-8 border-t border-slate-200">
                        <button @click="deleteUser(viewingUser.id)" class="w-full py-4 bg-rose-50 text-rose-600 border border-rose-100 rounded-2xl font-bold text-sm hover:bg-rose-600 hover:text-white transition">
                            Terminate Account Permanently
                        </button>
                        <p class="text-[10px] text-center text-slate-400 mt-2 italic">This action will anonymize the user and freeze the node.</p>
                    </div>

                </div>
            </div>
        </transition>

        <div v-if="viewingUser" @click="selectedUserId = null" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-30 transition-opacity"></div>
        
        <Modal :show="offerModal" @close="offerModal = false">
             <div class="p-6">
                <h2 class="text-lg font-bold text-slate-800">Award Special Reward</h2>
                <input v-model="offerForm.offer_name" class="w-full mt-4 rounded-xl border-slate-300" placeholder="e.g. Dubai Trip 2026">
                <div class="mt-6 flex justify-end gap-3">
                    <button @click="offerModal = false" class="px-4 py-2 font-bold">Cancel</button>
                    <button @click="submitOffer" class="bg-violet-600 text-white px-6 py-2 rounded-xl font-bold">Award Now</button>
                </div>
            </div>
        </Modal>

        <Modal :show="masterModal" @close="masterModal = false">
            <div class="bg-slate-50 flex flex-col h-full max-h-[90vh]">
                
                <div class="p-6 bg-white border-b border-slate-200 shrink-0">
                    <h2 class="text-xl font-black text-slate-800 tracking-tight">User Profile Edit</h2>
                    <p class="text-xs font-bold text-slate-500 mt-1">Modifying data for {{ viewingUser?.name }}</p>
                    
                    <div class="flex gap-2 mt-4 overflow-x-auto pb-1 hide-scrollbar">
                        <button @click="editTab = 'general'" :class="editTab === 'general' ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-lg text-xs font-bold transition whitespace-nowrap">
                            General & Contact
                        </button>
                        <button @click="editTab = 'identity'" :class="editTab === 'identity' ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-lg text-xs font-bold transition whitespace-nowrap">
                            Identity & Location
                        </button>
                        <button @click="editTab = 'finance'" :class="editTab === 'finance' ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-lg text-xs font-bold transition whitespace-nowrap">
                            Bank & Beneficiary
                        </button>
                    </div>
                </div>

                <div class="p-6 overflow-y-auto flex-1">
                    <form @submit.prevent="submitMasterUpdate" id="masterForm">
                        
                        <div v-show="editTab === 'general'" class="space-y-4">
                            <div>
                                <label class="block text-xs font-black text-slate-500 uppercase mb-2">Full Name</label>
                                <input v-model="masterForm.name" type="text" class="w-full bg-white border-slate-200 rounded-xl p-3 focus:ring-blue-500" required>
                                <div v-if="masterForm.errors.name" class="text-[10px] text-rose-500 font-bold mt-1">{{ masterForm.errors.name }}</div>
                            </div>
                            <div>
                                <label class="block text-xs font-black text-slate-500 uppercase mb-2">Email Address</label>
                                <input v-model="masterForm.email" type="email" class="w-full bg-white border-slate-200 rounded-xl p-3 focus:ring-blue-500" required>
                                <div v-if="masterForm.errors.email" class="text-[10px] text-rose-500 font-bold mt-1">{{ masterForm.errors.email }}</div>
                            </div>
                            <div>
                                <label class="block text-xs font-black text-slate-500 uppercase mb-2">Phone Number</label>
                                <input v-model="masterForm.phone" type="text" class="w-full bg-white border-slate-200 rounded-xl p-3 focus:ring-blue-500" required>
                                <div v-if="masterForm.errors.phone" class="text-[10px] text-rose-500 font-bold mt-1">{{ masterForm.errors.phone }}</div>
                            </div>
                        </div>

                        <div v-show="editTab === 'identity'" class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-black text-slate-500 uppercase mb-2">NIC Belongs To</label>
                                    <select v-model="masterForm.nic_type" class="w-full bg-white border-slate-200 rounded-xl p-3 focus:ring-blue-500" required>
                                        <option value="Own">Student's Own</option>
                                        <option value="Mother">Mother's</option>
                                        <option value="Father">Father's</option>
                                        <option value="Guardian">Guardian's</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-black text-slate-500 uppercase mb-2">NIC Number</label>
                                    <input v-model="masterForm.nic" type="text" class="w-full bg-white border-slate-200 rounded-xl p-3 focus:ring-blue-500" required>
                                </div>
                                <div v-if="masterForm.errors.nic" class="col-span-2 text-[10px] text-rose-500 font-bold mt-1">{{ masterForm.errors.nic }}</div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mt-6">
                                <div>
                                    <label class="block text-xs font-black text-slate-500 uppercase mb-2">District</label>
                                    <select v-model="masterForm.district" @change="handleEditDistrictChange" class="w-full bg-white border-slate-200 rounded-xl p-3 focus:ring-blue-500">
                                        <option value="">Select</option>
                                        <option v-for="dist in districtsList" :key="dist" :value="dist">{{ dist }}</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-black text-slate-500 uppercase mb-2">City</label>
                                    <select v-model="masterForm.city" :disabled="!masterForm.district" class="w-full bg-white border-slate-200 rounded-xl p-3 focus:ring-blue-500 disabled:opacity-50">
                                        <option value="">Select</option>
                                        <option v-for="city in availableEditCities" :key="city" :value="city">{{ city }}</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-black text-slate-500 uppercase mb-2">Full Address</label>
                                <textarea v-model="masterForm.address" rows="2" class="w-full bg-white border-slate-200 rounded-xl p-3 focus:ring-blue-500"></textarea>
                            </div>
                        </div>

                        <div v-show="editTab === 'finance'" class="space-y-6">
                            
                            <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-2xl space-y-4">
                                <h4 class="text-xs font-black text-emerald-800 uppercase tracking-widest flex items-center gap-2 mb-2">
                                    <span>🏦</span> Bank Details
                                </h4>
                                <div>
                                    <label class="block text-[10px] font-black text-emerald-600 uppercase mb-1">Account Holder Name</label>
                                    <input v-model="masterForm.bank_account_name" type="text" class="w-full bg-white border-emerald-200 rounded-xl p-2.5 text-sm focus:ring-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-emerald-600 uppercase mb-1">Account Number</label>
                                    <input v-model="masterForm.bank_account_no" type="text" class="w-full bg-white border-emerald-200 rounded-xl p-2.5 text-sm font-mono focus:ring-emerald-500">
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[10px] font-black text-emerald-600 uppercase mb-1">Bank Name</label>
                                        <input v-model="masterForm.bank_name" type="text" class="w-full bg-white border-emerald-200 rounded-xl p-2.5 text-sm focus:ring-emerald-500">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-emerald-600 uppercase mb-1">Branch</label>
                                        <input v-model="masterForm.bank_branch" type="text" class="w-full bg-white border-emerald-200 rounded-xl p-2.5 text-sm focus:ring-emerald-500">
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 bg-indigo-50 border border-indigo-100 rounded-2xl space-y-4">
                                <h4 class="text-xs font-black text-indigo-800 uppercase tracking-widest flex items-center gap-2 mb-2">
                                    <span>⚖️</span> Legal Beneficiary
                                </h4>
                                <div>
                                    <label class="block text-[10px] font-black text-indigo-600 uppercase mb-1">Full Name</label>
                                    <input v-model="masterForm.beneficiary_name" type="text" class="w-full bg-white border-indigo-200 rounded-xl p-2.5 text-sm focus:ring-indigo-500">
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[10px] font-black text-indigo-600 uppercase mb-1">Relationship</label>
                                        <input v-model="masterForm.beneficiary_relationship" type="text" class="w-full bg-white border-indigo-200 rounded-xl p-2.5 text-sm focus:ring-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-indigo-600 uppercase mb-1">NIC</label>
                                        <input v-model="masterForm.beneficiary_nic" type="text" class="w-full bg-white border-indigo-200 rounded-xl p-2.5 text-sm focus:ring-indigo-500">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-indigo-600 uppercase mb-1">Phone Number</label>
                                    <input v-model="masterForm.beneficiary_phone" type="text" class="w-full bg-white border-indigo-200 rounded-xl p-2.5 text-sm focus:ring-indigo-500">
                                </div>
                            </div>

                        </div>

                    </form>
                </div>

                <div class="p-6 bg-white border-t border-slate-200 shrink-0 flex justify-end gap-3">
                    <button @click="masterModal = false" class="px-6 py-3 font-bold text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200 transition">Cancel</button>
                    <button form="masterForm" type="submit" :disabled="masterForm.processing" class="bg-blue-600 text-white px-8 py-3 rounded-xl font-black shadow-lg shadow-blue-600/20 hover:bg-blue-700 transition disabled:opacity-50">
                        {{ masterForm.processing ? 'Saving...' : 'Save All Changes' }}
                    </button>
                </div>
            </div>
        </Modal>

    </AuthenticatedLayout>
</template>

<style scoped>
.hide-scrollbar::-webkit-scrollbar { display: none; }
.hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>