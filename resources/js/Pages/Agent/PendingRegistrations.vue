<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { useConfirm } from '@/Composables/useConfirm';

const { confirmAction } = useConfirm();

const props = defineProps({ pendingStudents: Array });
const searchQuery = ref('');

// INSTANT SEARCH FILTER
const filteredStudents = computed(() => {
    if (!searchQuery.value) return props.pendingStudents;
    const term = searchQuery.value.toLowerCase();
    
    return props.pendingStudents.filter(student => {
        const nameMatch = student.name.toLowerCase().includes(term);
        const nicMatch = student.nic.toLowerCase().includes(term);
        const idMatch = String(student.member_id).padStart(4, '0').includes(term);
        return nameMatch || nicMatch || idMatch;
    });
});

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

const editModal = ref(false);
const editForm = useForm({
    id: '', name: '', email: '', nic: '', phone: '', district: '', city: '', address: ''
});

const availableCities = computed(() => editForm.district ? [...locations[editForm.district]].sort() : []);

const openEdit = (student) => {
    editForm.id = student.id;
    editForm.name = student.name;
    editForm.email = student.email;
    editForm.nic = student.nic;
    editForm.phone = student.phone;
    editForm.district = student.district;
    editForm.city = student.city;
    editForm.address = student.address;
    editModal.value = true;
};

const handleDistrictChange = () => editForm.city = '';

const submitEdit = () => {
    editForm.put(`/agent/pending-registrations/${editForm.id}`, {
        onSuccess: () => editModal.value = false
    });
};

const voidRegistration = async (studentId, studentName) => {
    const confirmed = await confirmAction({
        title: 'Delete Registration',
        message: `Are you sure you want to permanently delete the registration for ${studentName}? This will instantly free up their reserved slot on your network tree.`,
        confirmText: 'Yes, Delete It',
        confirmButtonTheme: 'danger'
    });
    if (confirmed) {
        useForm({}).delete(`/agent/void-registration/${studentId}`, { preserveScroll: true });
    }
};

const formatId = (id) => id ? String(id).padStart(4, '0') : '';

const getPlacementDisplay = (student) => {
    if (!student.intended_parent_node || !student.intended_parent_node.user) return `Node #${student.intended_parent_node_id}`;
    
    const pNode = student.intended_parent_node;
    const pId = formatId(pNode.user.member_id);
    let subType = 'Main';
    if (pNode.account_type === 'Sub_A') subType = 'L';
    if (pNode.account_type === 'Sub_B') subType = 'R';
    
    return `${pId} ${subType} (${student.intended_position})`;
};
</script>

<template>
    <Head title="My Pending Registrations" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-bold text-2xl text-slate-800">My Pending Registrations</h2>
        </template>

        <div class="py-12 max-w-7xl mx-auto px-6">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                
                <div class="p-4 border-b border-slate-100 bg-slate-50 flex items-center">
                    <div class="relative w-full max-w-md">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </div>
                        <input v-model="searchQuery" type="text" class="block w-full pl-10 pr-3 py-2 border border-slate-300 rounded-xl leading-5 bg-white placeholder-slate-400 focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm transition" placeholder="Search by Member ID, Name, or NIC...">
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left whitespace-nowrap">
                        <thead class="bg-slate-50 text-xs uppercase text-slate-500 font-bold border-b border-slate-200">
                            <tr>
                                <th class="p-4 pl-6">Student Info</th>
                                <th class="p-4">Location</th>
                                <th class="p-4">Placement Intent</th>
                                <th class="p-4 text-right pr-6">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="student in filteredStudents" :key="student.id" class="hover:bg-slate-50">
                                <td class="p-4 pl-6">
                                    <div class="font-bold text-slate-800">{{ student.name }} <span class="text-[10px] text-slate-400 ml-1 font-mono">#{{ formatId(student.member_id) }}</span></div>
                                    <div class="text-xs text-slate-500">{{ student.email }} | {{ student.nic }}</div>
                                </td>
                                <td class="p-4 text-sm text-slate-600">
                                    {{ student.city }}, {{ student.district }}
                                </td>
                                <td class="p-4">
                                    <span class="bg-teal-50 text-teal-700 text-[10px] font-black px-2 py-1 rounded border border-teal-100">
                                        {{ getPlacementDisplay(student) }}
                                    </span>
                                </td>
                                <td class="p-4 text-right pr-6 space-y-2">
                                    <div>
                                        <span v-if="student.admission_status === 'Pending Payment'" class="text-[10px] uppercase font-bold text-amber-600 bg-amber-50 border border-amber-200 px-2 py-1 rounded shadow-sm block text-center mb-2">
                                            Awaiting Payment
                                        </span>
                                        <span v-else-if="student.admission_status === 'Pending Approval'" class="text-[10px] uppercase font-bold text-blue-600 bg-blue-50 border border-blue-200 px-2 py-1 rounded shadow-sm block text-center mb-2">
                                            Under Review
                                        </span>
                                        <span v-else-if="student.admission_status === 'Rejected'" class="text-[10px] uppercase font-bold text-rose-600 bg-rose-50 border border-rose-200 px-2 py-1 rounded shadow-sm block text-center mb-2">
                                            Rejected
                                        </span>
                                    </div>
                                    
                                    <button @click="openEdit(student)" class="w-full bg-slate-900 hover:bg-teal-600 text-white font-bold py-1.5 px-3 text-xs rounded-lg shadow-sm transition">
                                        Edit Details
                                    </button>

                                    <button v-if="['Pending Payment', 'Rejected'].includes(student.admission_status)"
                                            @click="voidRegistration(student.id, student.name)"
                                            class="w-full bg-rose-50 hover:bg-rose-500 hover:text-white text-rose-600 border border-rose-200 font-bold text-xs py-1.5 px-3 rounded-lg transition flex items-center justify-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        Delete
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="filteredStudents.length === 0">
                                <td colspan="4" class="p-10 text-center text-slate-500">
                                    <span v-if="searchQuery">No registrations found matching "{{ searchQuery }}".</span>
                                    <span v-else>You have no pending registrations.</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div v-if="editModal" class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm">
            <div class="bg-white rounded-3xl p-8 max-w-lg w-full shadow-2xl">
                <h3 class="text-xl font-black text-slate-800 mb-4">Edit Pending Student</h3>
                <form @submit.prevent="submitEdit" class="space-y-4">
                    <input v-model="editForm.name" type="text" class="w-full bg-slate-50 border-slate-200 rounded-xl p-3" placeholder="Name" required>
                    <input v-model="editForm.email" type="email" class="w-full bg-slate-50 border-slate-200 rounded-xl p-3" placeholder="Email" required>
                    <div class="grid grid-cols-2 gap-4">
                        <input v-model="editForm.nic" type="text" class="w-full bg-slate-50 border-slate-200 rounded-xl p-3" placeholder="NIC" required>
                        <input v-model="editForm.phone" type="text" class="w-full bg-slate-50 border-slate-200 rounded-xl p-3" placeholder="Phone" required>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <select v-model="editForm.district" @change="handleDistrictChange" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3">
                            <option v-for="dist in districts" :key="dist" :value="dist">{{ dist }}</option>
                        </select>
                        <select v-model="editForm.city" :disabled="!editForm.district" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 disabled:opacity-50">
                            <option v-for="city in availableCities" :key="city" :value="city">{{ city }}</option>
                        </select>
                    </div>
                    <textarea v-model="editForm.address" rows="2" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3" placeholder="Address"></textarea>
                    
                    <div class="flex gap-3 pt-4">
                        <button type="button" @click="editModal = false" class="flex-1 px-4 py-3 bg-white border border-slate-300 text-slate-700 font-bold rounded-xl hover:bg-slate-50">Cancel</button>
                        <button type="submit" :disabled="editForm.processing" class="flex-1 px-4 py-3 bg-teal-600 text-white font-bold rounded-xl hover:bg-teal-700">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>