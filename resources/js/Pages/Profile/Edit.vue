<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { useConfirm } from '@/Composables/useConfirm';
const { confirmAction } = useConfirm();

const props = defineProps({
    mustVerifyEmail: Boolean,
    status: String,
});

const user = usePage().props.auth.user;

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

const form = useForm({
    _method: 'PATCH',
    name: user.name,
    phone: user.phone,
    district: user.district || '',
    city: user.city || '',
    address: user.address || '',
    beneficiary_name: user.beneficiary_details?.name || '',
    beneficiary_relationship: user.beneficiary_details?.relationship || '',
    beneficiary_nic: user.beneficiary_details?.nic || '',
    beneficiary_phone: user.beneficiary_details?.phone || '',
    bank_account_name: user.bank_details?.account_name || '',
    bank_account_no: user.bank_details?.account_no || '',
    bank_name: user.bank_details?.bank_name || '',
    bank_branch: user.bank_details?.branch || '',
    profile_image: null,
});

const imagePreview = ref(user.profile_image_path ? `/storage/${user.profile_image_path}` : null);
    
const availableCities = computed(() => form.district ? [...locations[form.district]].sort() : []);

const handleDistrictChange = () => form.city = '';

const handleImageChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.profile_image = file;
        imagePreview.value = URL.createObjectURL(file);
    }
};

const submitProfileUpdate = async () => {
    const confirmed = await confirmAction({
        title: 'Save Profile Changes',
        message: 'Are you sure you want to update your profile information? Please ensure your contact and beneficiary details are accurate.',
        confirmText: 'Save Changes',
        confirmButtonTheme: 'primary'
    });

    if (confirmed) {
        form.post(route('profile.update'), {
            preserveScroll: true,
            onSuccess: () => {
                // The global flash message watcher will handle the success notification!
            }
        });
    }
};
</script>

<template>
    <Head title="My Profile" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-bold text-2xl text-slate-800 tracking-tight">Account Settings</h2>
        </template>

        <div class="py-10 bg-slate-50 min-h-screen">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-8">

                <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200 overflow-hidden">
                    <div class="p-8 md:p-12">
                        <form @submit.prevent="submitProfileUpdate" class="space-y-10">
                            
                            <div class="flex flex-col md:flex-row items-center gap-8 pb-8 border-b border-slate-100">
                                <div class="relative group">
                                    <div class="h-32 w-32 rounded-full bg-slate-100 overflow-hidden border-4 border-white shadow-xl flex-shrink-0">
                                        <img v-if="imagePreview" :src="imagePreview" class="h-full w-full object-cover">
                                        <div v-else class="h-full w-full flex items-center justify-center text-slate-400 font-black text-4xl bg-slate-200">
                                            {{ user.name.charAt(0) }}
                                        </div>
                                    </div>
                                    <label class="absolute bottom-0 right-0 bg-teal-600 p-2.5 rounded-full text-white cursor-pointer shadow-lg hover:bg-teal-700 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        <input type="file" @change="handleImageChange" accept="image/*" class="hidden"/>
                                    </label>
                                </div>
                                <div class="text-center md:text-left flex-1">
                                    <h3 class="text-2xl font-black text-slate-800">{{ user.name }}</h3>
                                    <p class="text-teal-600 font-bold uppercase tracking-widest text-xs mt-1">{{ user.role }} • {{ user.badge }}</p>
                                    
                                    <div class="mt-4 flex flex-wrap justify-center md:justify-start gap-4">
                                        <div class="px-4 py-2 bg-slate-50 border border-slate-100 rounded-xl">
                                            <span class="block text-[10px] font-bold text-slate-400 uppercase">Primary Email</span>
                                            <span class="text-sm font-bold text-slate-600">{{ user.email }}</span>
                                        </div>
                                        <div class="px-4 py-2 bg-slate-50 border border-slate-100 rounded-xl">
                                            <span class="block text-[10px] font-bold text-slate-400 uppercase">NIC Number</span>
                                            <span class="text-sm font-bold text-slate-600">{{ user.nic }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-12 gap-y-8">
                                
                                <div class="space-y-6">
                                    <h4 class="font-black text-slate-800 uppercase tracking-tighter text-lg border-l-4 border-teal-500 pl-3">Contact & Residence</h4>
                                    
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-xs font-black text-slate-500 uppercase mb-2">Display Name</label>
                                            <input v-model="form.name" type="text" class="w-full bg-slate-50 border-slate-200 rounded-2xl p-3.5 focus:ring-teal-500 focus:border-teal-500 transition" required>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-black text-slate-500 uppercase mb-2">Phone Number</label>
                                            <input v-model="form.phone" type="text" class="w-full bg-slate-50 border-slate-200 rounded-2xl p-3.5 focus:ring-teal-500 focus:border-teal-500 transition" required>
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-xs font-black text-slate-500 uppercase mb-2">District</label>
                                                <select v-model="form.district" @change="handleDistrictChange" class="w-full bg-slate-50 border-slate-200 rounded-2xl p-3.5" required>
                                                    <option value="" disabled>Select District</option>
                                                    <option v-for="dist in districts" :key="dist" :value="dist">{{ dist }}</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-black text-slate-500 uppercase mb-2">City</label>
                                                <select v-model="form.city" :disabled="!form.district" class="w-full bg-slate-50 border-slate-200 rounded-2xl p-3.5 disabled:opacity-50" required>
                                                    <option value="" disabled>Select City</option>
                                                    <option v-for="city in availableCities" :key="city" :value="city">{{ city }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-black text-slate-500 uppercase mb-2">Postal Address</label>
                                            <textarea v-model="form.address" rows="3" class="w-full bg-slate-50 border-slate-200 rounded-2xl p-3.5" required></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-6">
                                    <h4 class="font-black text-slate-800 uppercase tracking-tighter text-lg border-l-4 border-indigo-500 pl-3">Nominee / Beneficiary</h4>
                                    
                                    <div v-if="user.role === 'Student'" class="p-6 bg-indigo-50 border border-indigo-100 rounded-2xl text-indigo-700 text-sm leading-relaxed">
                                        <p class="font-bold mb-2 italic">Registration Incomplete</p>
                                        Beneficiary details can only be added once you have been officially promoted to an **Agent** and gained full access to your business triangle.
                                    </div>

                                    <div v-else class="space-y-4">
                                        <div>
                                            <label class="block text-xs font-black text-slate-500 uppercase mb-2">Full Name</label>
                                            <input v-model="form.beneficiary_name" type="text" class="w-full bg-slate-50 border-slate-200 rounded-2xl p-3.5">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-black text-slate-500 uppercase mb-2">Relationship</label>
                                            <input v-model="form.beneficiary_relationship" type="text" class="w-full bg-slate-50 border-slate-200 rounded-2xl p-3.5" placeholder="e.g. Spouse, Parent, Child">
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-xs font-black text-slate-500 uppercase mb-2">NIC Number</label>
                                                <input v-model="form.beneficiary_nic" type="text" class="w-full bg-slate-50 border-slate-200 rounded-2xl p-3.5">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-black text-slate-500 uppercase mb-2">Contact Number</label>
                                                <input v-model="form.beneficiary_phone" type="text" class="w-full bg-slate-50 border-slate-200 rounded-2xl p-3.5">
                                            </div>
                                        </div>
                                        <div class="text-[11px] text-slate-400 font-medium bg-slate-50 p-3 rounded-xl border border-slate-100">
                                            Note: The person named above will be the legal inheritor of your network nodes and accrued balances in accordance with company policy.
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-6 lg:col-span-2 border-t border-slate-200 pt-8 mt-4">
                                    <h4 class="font-black text-slate-800 uppercase tracking-tighter text-lg border-l-4 border-emerald-500 pl-3">Bank Details for Payouts</h4>
                                    
                                    <div v-if="user.role === 'Student'" class="p-6 bg-emerald-50 border border-emerald-100 rounded-2xl text-emerald-700 text-sm leading-relaxed">
                                        <p class="font-bold mb-2 italic">Feature Locked</p>
                                        Bank details can only be added once you have been officially promoted to an **Agent**.
                                    </div>

                                    <div v-else>
                                        <div v-if="user.bank_details && user.role !== 'Admin'" class="bg-slate-50 rounded-2xl border border-slate-200 p-6 shadow-sm">
                                            <div class="flex items-center justify-between mb-4 pb-4 border-b border-slate-200">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl">🏦</div>
                                                    <div>
                                                        <h5 class="font-bold text-slate-800">{{ user.bank_details.bank_name }}</h5>
                                                        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">{{ user.bank_details.branch }} Branch</p>
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <div class="text-sm font-black text-slate-800">{{ user.bank_details.account_no }}</div>
                                                    <div class="text-xs font-bold text-slate-500 mt-1">{{ user.bank_details.account_name }}</div>
                                                </div>
                                            </div>
                                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest text-center mt-2">
                                                🔒 These details are locked. To change your bank information, please contact an Administrator.
                                            </p>
                                        </div>

                                        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-white border border-slate-200 p-6 rounded-2xl shadow-sm">
                                            <div class="md:col-span-2 mb-2">
                                                <p v-if="user.role !== 'Admin'" class="text-xs text-rose-500 font-bold bg-rose-50 p-3 rounded-lg border border-rose-100">
                                                    ⚠️ WARNING: Please verify your bank details carefully. Once submitted, you cannot change them without Admin assistance.
                                                </p>
                                                <p v-else class="text-xs text-indigo-600 font-bold bg-indigo-50 p-3 rounded-lg border border-indigo-100 flex items-center gap-2">
                                                    <span>👑</span> Master Control: As an Admin, you can freely update the Company Bank Details at any time.
                                                </p>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-black text-slate-500 uppercase mb-2">Account Holder's Name</label>
                                                <input v-model="form.bank_account_name" type="text" class="w-full bg-slate-50 border-slate-200 rounded-2xl p-3.5 focus:ring-teal-500 focus:border-teal-500 transition" placeholder="As it appears on bank statement" :required="user.role === 'Agent'">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-black text-slate-500 uppercase mb-2">Account Number</label>
                                                <input v-model="form.bank_account_no" type="text" class="w-full bg-slate-50 border-slate-200 rounded-2xl p-3.5 focus:ring-teal-500 focus:border-teal-500 transition" :required="user.role === 'Agent'">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-black text-slate-500 uppercase mb-2">Bank Name</label>
                                                <input v-model="form.bank_name" type="text" class="w-full bg-slate-50 border-slate-200 rounded-2xl p-3.5 focus:ring-teal-500 focus:border-teal-500 transition" placeholder="e.g. Commercial Bank" :required="user.role === 'Agent'">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-black text-slate-500 uppercase mb-2">Branch</label>
                                                <input v-model="form.bank_branch" type="text" class="w-full bg-slate-50 border-slate-200 rounded-2xl p-3.5 focus:ring-teal-500 focus:border-teal-500 transition" :required="user.role === 'Agent'">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-6 flex justify-end">
                                <button type="submit" :disabled="form.processing" class="px-10 py-4 bg-slate-900 hover:bg-teal-600 text-white font-black rounded-2xl shadow-xl shadow-slate-900/10 transition-all transform hover:-translate-y-1 active:scale-95 disabled:opacity-50">
                                    {{ form.processing ? 'Syncing...' : 'Save All Changes' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-200">
                        <h3 class="text-lg font-black text-slate-800 mb-6">Security & Password</h3>
                        <UpdatePasswordForm />
                    </div>

                    <div v-if="['Agent', 'Student'].includes(user.role)" class="bg-white p-8 rounded-[2rem] shadow-sm border border-rose-100">
                        <h3 class="text-lg font-black text-rose-800 mb-6">Danger Zone</h3>
                        <DeleteUserForm />
                    </div>
                    
                    <div v-else class="bg-slate-50 p-8 rounded-[2rem] border border-slate-200 flex flex-col justify-center items-center text-center">
                        <div class="w-12 h-12 bg-slate-200 text-slate-500 rounded-full flex items-center justify-center mb-4 text-xl">🛡️</div>
                        <h3 class="text-slate-700 font-bold mb-2">Staff Account</h3>
                        <p class="text-xs text-slate-500 max-w-xs">System administration and management accounts are permanently secured and cannot be deleted via the portal.</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>