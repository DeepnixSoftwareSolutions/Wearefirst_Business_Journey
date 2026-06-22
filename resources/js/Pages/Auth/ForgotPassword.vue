<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    status: { type: String },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Forgot Password" />

        <div class="text-center mb-8">
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">Account Recovery</h2>
            <p class="text-sm text-slate-500 mt-3 font-medium leading-relaxed">
                Enter your registered email address and we will send you a secure link to choose a new password.
            </p>
        </div>

        <div v-if="status" class="mb-6 p-4 rounded-2xl bg-teal-50 border border-teal-200 text-sm font-bold text-teal-700 text-center shadow-sm">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <label for="email" class="block text-xs font-bold text-slate-600 uppercase tracking-widest mb-2">Email Address</label>
                <input id="email" type="email" v-model="form.email" required autofocus autocomplete="username"
                       class="w-full bg-slate-100/70 border border-slate-200 rounded-2xl px-4 py-3.5 text-slate-900 placeholder-slate-400 focus:border-teal-500 focus:ring focus:ring-teal-500/20 transition-all outline-none shadow-inner"
                       placeholder="agent@wearefirst.lk" />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <button type="submit" :disabled="form.processing"
                        class="w-full bg-slate-900 hover:bg-slate-800 text-white font-black py-4 px-6 rounded-2xl shadow-lg shadow-slate-900/20 transition-all transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                    Send Recovery Link
                </button>
            </div>
            
            <div class="text-center pt-2">
                <Link :href="route('login')" class="text-sm font-bold text-slate-500 hover:text-slate-900 transition-colors">
                    &larr; Back to Login
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>