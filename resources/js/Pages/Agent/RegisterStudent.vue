<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';
import { watchEffect } from 'vue';
import { useConfirm } from '@/Composables/useConfirm';

const props = defineProps({ sponsor: Object });
const user = usePage().props.auth.user;

const { confirmAction } = useConfirm();
const page = usePage();

// --- Accordion State ---
const expandedAccount = ref('Main'); // Default to Main Account expanded
const expandedSub = ref(null); // 'Left' or 'Right'

const toggleSub = (subName) => {
    expandedSub.value = expandedSub.value === subName ? null : subName;
};

// Helper to format ID like '0004'
const formatId = (id) => String(id).padStart(4, '0');
const agentId = formatId(user.member_id);

// Sri Lanka Location Data
const locations = {
    'Ampara': [
        'Addalaichenai', 'Akkaraipattu', 'Ampara', 'Dehiattakandiya', 'Kalmunai',
        'Maha Oya', 'Nintavur', 'Pottuvil', 'Sainthamaruthu', 'Sammanthurai', 'Uhana'
    ],

    'Anuradhapura': [
        'Anuradhapura', 'Eppawala', 'Galenbindunuwewa', 'Horowpathana', 'Kekirawa',
        'Medawachchiya', 'Mihintale', 'Nochchiyagama', 'Talawa', 'Tambuttegama'
    ],

    'Badulla': [
        'Badulla', 'Bandarawela', 'Diyatalawa', 'Ella', 'Hali-Ela', 'Haputale',
        'Lunugala', 'Mahiyanganaya', 'Meegahakiula', 'Passara', 'Welimada'
    ],

    'Batticaloa': [
        'Batticaloa', 'Chenkalady', 'Eravur', 'Kaluwanchikudy', 'Kattankudy',
        'Oddamavadi', 'Vakarai', 'Valaichenai'
    ],

    'Colombo': [
        'Athurugiriya', 'Avissawella', 'Battaramulla', 'Boralesgamuwa',
        'Colombo 01 - Fort', 'Colombo 02 - Slave Island', 'Colombo 03 - Kollupitiya',
        'Colombo 04 - Bambalapitiya', 'Colombo 05 - Havelock Town',
        'Colombo 06 - Wellawatte', 'Colombo 07 - Cinnamon Gardens', 'Colombo 08 - Borella',
        'Colombo 09 - Dematagoda', 'Colombo 10 - Maradana', 'Colombo 11 - Pettah',
        'Colombo 12 - Hulftsdorp', 'Colombo 13 - Kotahena', 'Colombo 14 - Grandpass',
        'Colombo 15 - Mattakkuliya', 'Dehiwala', 'Hanwella', 'Homagama', 'Kaduwela',
        'Kesbewa', 'Kolonnawa', 'Kosgama', 'Maharagama', 'Malabe', 'Moratuwa',
        'Mount Lavinia', 'Nugegoda', 'Padukka', 'Piliyandala', 'Ratmalana',
        'Sri Jayawardenepura Kotte', 'Wellampitiya'
    ],

    'Galle': [
        'Ahungalla', 'Akmeemana', 'Ambalangoda', 'Baddegama', 'Bentota', 'Elpitiya',
        'Galle', 'Hikkaduwa', 'Imaduwa', 'Karandeniya', 'Karapitiya', 'Unawatuna',
        'Yakkalamulla'
    ],

    'Gampaha': [
        'Biyagama', 'Divulapitiya', 'Ekala', 'Gampaha', 'Ganemulla', 'Ja-Ela',
        'Kadawatha', 'Kandana', 'Katunayake', 'Kelaniya', 'Kiribathgoda', 'Kochchikade',
        'Mabole', 'Minuwangoda', 'Mirigama', 'Negombo', 'Nittambuwa', 'Ragama', 'Seeduwa',
        'Veyangoda', 'Wattala'
    ],

    'Hambantota': [
        'Ambalantota', 'Angunakolapelessa', 'Beliatta', 'Hambantota', 'Kataragama',
        'Lunugamvehera', 'Sooriyawewa', 'Tangalle', 'Tissamaharama', 'Weeraketiya'
    ],

    'Jaffna': [
        'Chavakachcheri', 'Delft', 'Jaffna', 'Karainagar', 'Kopay', 'Manipay', 'Nallur',
        'Point Pedro', 'Uduvil', 'Valvettithurai', 'Velanai'
    ],

    'Kalutara': [
        'Agalawatta', 'Aluthgama', 'Bandaragama', 'Beruwala', 'Bulathsinhala',
        'Dharga Town', 'Horana', 'Ingiriya', 'Kalutara', 'Matugama', 'Millaniya',
        'Palindanuwara', 'Panadura', 'Payagala', 'Wadduwa'
    ],

    'Kandy': [
        'Akurana', 'Digana', 'Galagedara', 'Gampola', 'Kadugannawa', 'Kandy',
        'Katugastota', 'Kundasale', 'Menikhinna', 'Peradeniya', 'Poojapitiya', 'Teldeniya',
        'Wattegama'
    ],

    'Kegalle': [
        'Aranayaka', 'Bulathkohupitiya', 'Dehiowita', 'Deraniyagala', 'Galigamuwa',
        'Kegalle', 'Mawanella', 'Rambukkana', 'Ruwanwella', 'Warakapola', 'Yatiyantota'
    ],

    'Kilinochchi': [
        'Akkarayankulam', 'Kilinochchi', 'Pallai', 'Paranthan', 'Poonakary'
    ],

    'Kurunegala': [
        'Alawwa', 'Bingiriya', 'Galgamuwa', 'Ibbagamuwa', 'Kobeigane', 'Kuliyapitiya',
        'Kurunegala', 'Mawathagama', 'Melsiripura', 'Narammala', 'Nikaweratiya', 'Pannala',
        'Polgahawela', 'Wariyapola'
    ],

    'Mannar': [
        'Madhu', 'Mannar', 'Murunkan', 'Pesalai', 'Talaimannar'
    ],

    'Matale': [
        'Dambulla', 'Galewela', 'Laggala', 'Matale', 'Naula', 'Pallepola', 'Rattota',
        'Sigiriya', 'Ukuwela', 'Wilgamuwa', 'Yatawatta'
    ],

    'Matara': [
        'Akuressa', 'Deniyaya', 'Devinuwara', 'Dikwella', 'Hakmana', 'Kamburupitiya',
        'Kirinda Puhulwella', 'Malimbada', 'Matara', 'Weligama'
    ],

    'Monaragala': [
        'Bibile', 'Buttala', 'Kataragama', 'Madulla', 'Monaragala', 'Siyambalanduwa',
        'Thanamalwila', 'Wellawaya'
    ],

    'Mullaitivu': [
        'Mankulam', 'Mullaitivu', 'Mulliyawalai', 'Oddusuddan', 'Puthukkudiyiruppu'
    ],

    'Nuwara Eliya': [
        'Ambewela', 'Ginigathhena', 'Hatton', 'Kotagala', 'Lindula', 'Maskeliya',
        'Nanu Oya', 'Nuwara Eliya', 'Pattipola', 'Ragala', 'Talawakelle', 'Walapane'
    ],

    'Polonnaruwa': [
        'Bakamuna', 'Dimbulagala', 'Hingurakgoda', 'Kaduruwela', 'Medirigiriya',
        'Polonnaruwa', 'Welikanda'
    ],

    'Puttalam': [
        'Anamaduwa', 'Chilaw', 'Dankotuwa', 'Kalpitiya', 'Madampe', 'Marawila', 'Mundel',
        'Nattandiya', 'Norochcholai', 'Puttalam', 'Wennappuwa'
    ],

    'Ratnapura': [
        'Ayagama', 'Balangoda', 'Eheliyagoda', 'Elapatha', 'Embilipitiya', 'Godakawela',
        'Kahawatta', 'Kalawana', 'Kolonna', 'Kuruwita', 'Nivithigala', 'Opanayaka',
        'Pelmadulla', 'Rakwana', 'Ratnapura', 'Udawalawe', 'Weligepola'
    ],

    'Trincomalee': [
        'Kantale', 'Kinniya', 'Kuchchaveli', 'Muttur', 'Nilaveli', 'Seruwila',
        'Thampalakamam', 'Trincomalee'
    ],

    'Vavuniya': [
        'Cheddikulam', 'Nedunkeni', 'Omanthai', 'Vavuniya'
    ]
};

const districts = Object.keys(locations).sort();

// Detect Manual Tree Selection from URL
const isManualPlacement = ref(false);
const manualLineDisplay = ref('');

const form = useForm({
    name: '', email: '', nic: '', nic_type: 'Own', phone: '',
    district: '', city: '', address: '',
    line: 'G1',
    // Hidden Manual Fields
    manual_parent_id: null,
    manual_position: null
});

onMounted(() => {
    const params = new URLSearchParams(window.location.search);
    if (params.has('manual_parent_id') && params.has('manual_position')) {
        isManualPlacement.value = true;
        form.manual_parent_id = params.get('manual_parent_id');
        form.manual_position = params.get('manual_position');
        manualLineDisplay.value = params.get('manual_line') || 'Custom Node';
        
        // Infer the "Line" string just for validation passing based on the name if possible
        if(manualLineDisplay.value.includes('G2')) form.line = 'G2';
        else if(manualLineDisplay.value.includes('G3')) form.line = 'G3';
        else if(manualLineDisplay.value.includes('G4')) form.line = 'G4';
        else form.line = 'G1';
    }
});

const availableCities = computed(() => {
    return form.district ? [...locations[form.district]].sort() : [];
});

const handleDistrictChange = () => {
    form.city = ''; 
};

const updateAmount = () => {
    if (form.payment_type === 'Full') form.admission_fee_paid = 15000;
    else if (form.payment_type === 'Partial') form.admission_fee_paid = 10000;
    else if (form.admission_fee_paid > 15000) form.admission_fee_paid = 15000;
};

// Automatically trigger the custom modal if the server sends a flash_modal response
watchEffect(() => {
    if (page.props.flash?.flash_modal) {
        confirmAction({
            title: page.props.flash.title,
            message: page.props.flash.message,
            confirmText: 'Understood',
            cancelText: null, // Hide cancel button for a simple "OK" alert
            confirmButtonTheme: page.props.flash.theme || 'primary'
        });
        // Clear it so it doesn't fire again on hot-reload
        page.props.flash.flash_modal = false; 
    }
});

const submit = () => form.post('/agent/register-student');
</script>

<template>
    <Head title="Register Student" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center w-full pr-4">
                <h2 class="font-bold text-2xl text-slate-800 tracking-tight">New Student Registration</h2>
                <Link :href="route('agent.pending')" class="text-sm font-bold text-teal-600 bg-teal-50 px-4 py-2 rounded-xl hover:bg-teal-100 transition shadow-sm border border-teal-100">
                    View Pending Approvals
                </Link>
            </div>
        </template>

        <div class="py-12 bg-slate-50 min-h-screen">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white rounded-[2rem] shadow-xl border border-slate-200/60 overflow-hidden flex flex-col lg:flex-row">
                    
                    <div class="lg:w-1/3 bg-slate-900 text-white p-10 flex flex-col justify-between relative overflow-hidden">
                        <div class="absolute top-0 right-0 -mt-16 -mr-16 w-64 h-64 bg-teal-500 rounded-full opacity-10 blur-3xl"></div>
                        
                        <div class="relative z-10 h-full flex flex-col">
                            
                            <div v-if="isManualPlacement" class="flex-1 flex flex-col justify-center">
                                <div class="w-16 h-16 bg-emerald-500/20 text-emerald-400 rounded-2xl flex items-center justify-center mb-6 shadow-inner border border-emerald-500/30">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                </div>
                                <h3 class="text-2xl font-black text-emerald-400 mb-2">Direct Placement</h3>
                                <p class="text-sm text-slate-400 mb-6">You have selected a specific empty slot directly from the Network Tree.</p>
                                
                                <div class="bg-slate-800/50 rounded-2xl p-5 border border-slate-700 space-y-3">
                                    <div>
                                        <span class="block text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-1">Target Line</span>
                                        <span class="text-lg font-bold text-white">{{ manualLineDisplay }}</span>
                                    </div>
                                    <div class="flex justify-between border-t border-slate-700/50 pt-3">
                                        <div>
                                            <span class="block text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-1">Parent Node ID</span>
                                            <span class="font-mono text-slate-300">{{ form.manual_parent_id }}</span>
                                        </div>
                                        <div>
                                            <span class="block text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-1">Leg Position</span>
                                            <span class="font-bold" :class="form.manual_position === 'Left' ? 'text-blue-400' : 'text-amber-400'">{{ form.manual_position }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-8">
                                    <Link :href="route('agent.register')" class="text-sm font-bold text-slate-400 hover:text-white transition underline underline-offset-4 decoration-slate-600">
                                        Cancel & Use Auto-Placement
                                    </Link>
                                </div>
                            </div>

                            <div v-else class="flex-1 flex flex-col">
                                <h3 class="text-xl font-bold text-teal-400 mb-2">Select Placement</h3>
                                <p class="text-xs text-slate-400 mb-6 leading-relaxed">System will auto-calculate Extreme Outer Leg spillover placement based on your selection.</p>
                                
                                <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden shadow-lg shadow-black/20">
                                    
                                    <div class="px-5 py-4 flex items-center gap-3 bg-white/5 border-b border-white/5 relative">
                                        <div class="w-8 h-8 rounded-full bg-teal-500/20 text-teal-400 flex items-center justify-center font-black border border-teal-500/30">M</div>
                                        <div>
                                            <div class="font-black text-sm text-white">Main Account</div>
                                            <div class="text-[10px] text-teal-400 font-mono tracking-widest uppercase">ID: {{ agentId }}</div>
                                        </div>
                                    </div>

                                    <div class="p-3 space-y-3">
                                        
                                        <div class="border border-slate-700 rounded-xl overflow-hidden bg-slate-800/50 transition-all duration-300"
                                             :class="expandedSub === 'Left' ? 'border-blue-500/50 shadow-md shadow-blue-500/10' : 'hover:border-slate-600'">
                                            
                                            <button @click="toggleSub('Left')" type="button" class="w-full px-4 py-3 flex items-center justify-between focus:outline-none">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-6 h-6 rounded-md bg-blue-500/20 text-blue-400 flex items-center justify-center font-bold text-xs">L</div>
                                                    <div class="text-left">
                                                        <div class="font-bold text-sm text-slate-200">{{ agentId }} Left Sub</div>
                                                    </div>
                                                </div>
                                                <svg class="w-4 h-4 text-slate-400 transition-transform duration-300" :class="{'rotate-180 text-blue-400': expandedSub === 'Left'}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                            </button>

                                            <transition enter-active-class="transition-all duration-300 ease-out" enter-from-class="max-h-0 opacity-0" enter-to-class="max-h-[500px] opacity-100" leave-active-class="transition-all duration-200 ease-in" leave-from-class="max-h-[500px] opacity-100" leave-to-class="max-h-0 opacity-0">
                                                <div v-show="expandedSub === 'Left'" class="px-3 pb-3 space-y-2 overflow-hidden">
                                                    
                                                    <label class="flex items-center p-3 rounded-lg cursor-pointer transition-colors border"
                                                           :class="form.line === 'G1' ? 'bg-blue-500/20 border-blue-500' : 'bg-slate-900 border-slate-700 hover:bg-slate-700'">
                                                        <input type="radio" v-model="form.line" value="G1" class="text-blue-500 bg-slate-900 border-slate-600 focus:ring-blue-500 focus:ring-offset-slate-900 h-4 w-4 mr-3">
                                                        <div>
                                                            <div class="font-black text-sm text-white">Line G1</div>
                                                            <div class="text-[10px] text-slate-400">Extreme Left Leg</div>
                                                        </div>
                                                    </label>

                                                    <label class="flex items-center p-3 rounded-lg transition-colors border"
                                                           :class="[!sponsor.middle_legs_unlocked ? 'opacity-50 cursor-not-allowed bg-slate-900 border-slate-800' : (form.line === 'G2' ? 'bg-blue-500/20 border-blue-500 cursor-pointer' : 'bg-slate-900 border-slate-700 hover:bg-slate-700 cursor-pointer')]">
                                                        <input type="radio" v-model="form.line" value="G2" :disabled="!sponsor.middle_legs_unlocked" class="text-blue-500 bg-slate-900 border-slate-600 focus:ring-blue-500 focus:ring-offset-slate-900 h-4 w-4 mr-3">
                                                        <div class="flex-1 flex justify-between items-center">
                                                            <div>
                                                                <div class="font-black text-sm" :class="!sponsor.middle_legs_unlocked ? 'text-slate-500' : 'text-white'">Line G2</div>
                                                                <div class="text-[10px] text-slate-400">Inner Right Leg</div>
                                                            </div>
                                                            <svg v-if="!sponsor.middle_legs_unlocked" class="w-4 h-4 text-rose-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                                                        </div>
                                                    </label>

                                                </div>
                                            </transition>
                                        </div>

                                        <div class="border border-slate-700 rounded-xl overflow-hidden bg-slate-800/50 transition-all duration-300"
                                             :class="expandedSub === 'Right' ? 'border-amber-500/50 shadow-md shadow-amber-500/10' : 'hover:border-slate-600'">
                                            
                                            <button @click="toggleSub('Right')" type="button" class="w-full px-4 py-3 flex items-center justify-between focus:outline-none">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-6 h-6 rounded-md bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold text-xs">R</div>
                                                    <div class="text-left">
                                                        <div class="font-bold text-sm text-slate-200">{{ agentId }} Right Sub</div>
                                                    </div>
                                                </div>
                                                <svg class="w-4 h-4 text-slate-400 transition-transform duration-300" :class="{'rotate-180 text-amber-400': expandedSub === 'Right'}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                            </button>

                                            <transition enter-active-class="transition-all duration-300 ease-out" enter-from-class="max-h-0 opacity-0" enter-to-class="max-h-[500px] opacity-100" leave-active-class="transition-all duration-200 ease-in" leave-from-class="max-h-[500px] opacity-100" leave-to-class="max-h-0 opacity-0">
                                                <div v-show="expandedSub === 'Right'" class="px-3 pb-3 space-y-2 overflow-hidden">
                                                    
                                                    <label class="flex items-center p-3 rounded-lg transition-colors border"
                                                           :class="[!sponsor.middle_legs_unlocked ? 'opacity-50 cursor-not-allowed bg-slate-900 border-slate-800' : (form.line === 'G3' ? 'bg-amber-500/20 border-amber-500 cursor-pointer' : 'bg-slate-900 border-slate-700 hover:bg-slate-700 cursor-pointer')]">
                                                        <input type="radio" v-model="form.line" value="G3" :disabled="!sponsor.middle_legs_unlocked" class="text-amber-500 bg-slate-900 border-slate-600 focus:ring-amber-500 focus:ring-offset-slate-900 h-4 w-4 mr-3">
                                                        <div class="flex-1 flex justify-between items-center">
                                                            <div>
                                                                <div class="font-black text-sm" :class="!sponsor.middle_legs_unlocked ? 'text-slate-500' : 'text-white'">Line G3</div>
                                                                <div class="text-[10px] text-slate-400">Inner Left Leg</div>
                                                            </div>
                                                            <svg v-if="!sponsor.middle_legs_unlocked" class="w-4 h-4 text-rose-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                                                        </div>
                                                    </label>

                                                    <label class="flex items-center p-3 rounded-lg cursor-pointer transition-colors border"
                                                           :class="form.line === 'G4' ? 'bg-amber-500/20 border-amber-500' : 'bg-slate-900 border-slate-700 hover:bg-slate-700'">
                                                        <input type="radio" v-model="form.line" value="G4" class="text-amber-500 bg-slate-900 border-slate-600 focus:ring-amber-500 focus:ring-offset-slate-900 h-4 w-4 mr-3">
                                                        <div>
                                                            <div class="font-black text-sm text-white">Line G4</div>
                                                            <div class="text-[10px] text-slate-400">Extreme Right Leg</div>
                                                        </div>
                                                    </label>
                                                </div>
                                            </transition>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="lg:w-2/3 p-10 lg:p-14">
                        <div v-if="form.errors.line" class="mb-6 p-4 bg-rose-50 border border-rose-200 rounded-2xl text-rose-600 font-bold text-sm flex items-center shadow-sm">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                            {{ form.errors.line }}
                        </div>

                        <form @submit.prevent="submit" class="space-y-6">
                            
                            <h3 class="text-lg font-black text-slate-800 border-b border-slate-100 pb-2">Personal Information</h3>
                            <div>
                                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Student Full Name</label>
                                <input v-model="form.name" type="text" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-teal-500 focus:border-teal-500" required>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Email Address</label>
                                    <input v-model="form.email" type="email" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-teal-500 focus:border-teal-500" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Phone Number</label>
                                    <input v-model="form.phone" type="text" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-teal-500 focus:border-teal-500" required>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">NIC Belongs To</label>
                                    <select v-model="form.nic_type" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-teal-500 focus:border-teal-500" required>
                                        <option value="Own">Student's Own NIC</option>
                                        <option value="Mother">Mother's NIC</option>
                                        <option value="Father">Father's NIC</option>
                                        <option value="Guardian">Guardian's NIC</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">NIC Number</label>
                                    <input v-model="form.nic" type="text" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-teal-500 focus:border-teal-500" required>
                                </div>
                            </div>

                            <h3 class="text-lg font-black text-slate-800 border-b border-slate-100 pb-2 pt-4">Location Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">District</label>
                                    <select v-model="form.district" @change="handleDistrictChange" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-teal-500 focus:border-teal-500" required>
                                        <option value="" disabled>Select District</option>
                                        <option v-for="dist in districts" :key="dist" :value="dist">{{ dist }}</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">City</label>
                                    <select v-model="form.city" :disabled="!form.district" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 disabled:opacity-50 focus:ring-teal-500 focus:border-teal-500" required>
                                        <option value="" disabled>Select City</option>
                                        <option v-for="city in availableCities" :key="city" :value="city">{{ city }}</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Full Residential Address</label>
                                <textarea v-model="form.address" rows="2" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-teal-500 focus:border-teal-500" required></textarea>
                            </div>

                            <button type="submit" :disabled="form.processing" class="w-full px-6 py-4 bg-slate-900 text-white font-black rounded-xl hover:bg-teal-600 transition shadow-lg shadow-slate-900/20 active:scale-[0.98]">
                                {{ form.processing ? 'Processing Registration...' : 'Submit Registration' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>