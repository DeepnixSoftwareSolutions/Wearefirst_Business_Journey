<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    email: { type: String, required: true },
    token: { type: String, required: true },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Create New Password" />

        <div class="text-center mb-8">
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">Create New Password</h2>
            <p class="text-sm text-slate-500 mt-2 font-medium">Please secure your account with a strong password.</p>
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <label for="email" class="block text-xs font-bold text-slate-600 uppercase tracking-widest mb-2">Email Address</label>
                <input id="email" type="email" v-model="form.email" required readonly
                       class="w-full bg-slate-200/60 border border-slate-200 rounded-2xl px-4 py-3.5 text-slate-500 opacity-80 cursor-not-allowed outline-none shadow-inner" />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <label for="password" class="block text-xs font-bold text-slate-600 uppercase tracking-widest mb-2">New Password</label>
                <input id="password" type="password" v-model="form.password" required autofocus autocomplete="new-password"
                       class="w-full bg-slate-100/70 border border-slate-200 rounded-2xl px-4 py-3.5 text-slate-900 placeholder-slate-400 focus:border-teal-500 focus:ring focus:ring-teal-500/20 transition-all outline-none shadow-inner"
                       placeholder="••••••••" />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-bold text-slate-600 uppercase tracking-widest mb-2">Confirm Password</label>
                <input id="password_confirmation" type="password" v-model="form.password_confirmation" required autocomplete="new-password"
                       class="w-full bg-slate-100/70 border border-slate-200 rounded-2xl px-4 py-3.5 text-slate-900 placeholder-slate-400 focus:border-teal-500 focus:ring focus:ring-teal-500/20 transition-all outline-none shadow-inner"
                       placeholder="••••••••" />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="pt-4">
                <button type="submit" :disabled="form.processing"
                        class="w-full bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-400 hover:to-emerald-500 text-white font-black text-lg py-4 px-6 rounded-2xl shadow-lg shadow-teal-500/25 transition-all transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                    Save New Password
                </button>
            </div>
        </form>
    </GuestLayout>
</template>