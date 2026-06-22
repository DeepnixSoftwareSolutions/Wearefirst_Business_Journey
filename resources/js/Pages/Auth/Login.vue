<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import Checkbox from '@/Components/Checkbox.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: { type: Boolean },
    status: { type: String },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Secure Login" />

        <div class="text-center mb-8">
            <h2 class="text-3xl font-black text-slate-900 tracking-tight">Welcome Back</h2>
            <p class="text-sm text-slate-500 mt-2 font-medium">Please enter your agent credentials</p>
        </div>

        <div v-if="status" class="mb-6 p-4 rounded-2xl bg-teal-50 border border-teal-200 text-sm font-bold text-teal-700 text-center shadow-sm">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <label for="email" class="block text-xs font-bold text-slate-600 uppercase tracking-widest mb-2">Email Address</label>
                <input id="email" type="email" v-model="form.email" required autofocus autocomplete="username"
                       class="w-full bg-slate-100/70 border border-slate-200 rounded-2xl px-4 py-3.5 text-slate-900 placeholder-slate-400 focus:border-teal-500 focus:ring focus:ring-teal-500/20 transition-all outline-none shadow-inner"
                       placeholder="agent@wearefirst.lk" />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <div class="flex justify-between items-center mb-2">
                    <label for="password" class="block text-xs font-bold text-slate-600 uppercase tracking-widest">Password</label>
                    <Link v-if="canResetPassword" :href="route('password.request')" class="text-xs font-bold text-teal-600 hover:text-teal-700 transition-colors">
                        Forgot Password?
                    </Link>
                </div>
                <input id="password" type="password" v-model="form.password" required autocomplete="current-password"
                       class="w-full bg-slate-100/70 border border-slate-200 rounded-2xl px-4 py-3.5 text-slate-900 placeholder-slate-400 focus:border-teal-500 focus:ring focus:ring-teal-500/20 transition-all outline-none shadow-inner"
                       placeholder="••••••••" />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="flex items-center pt-2">
                <label class="flex items-center cursor-pointer group">
                    <Checkbox name="remember" v-model:checked="form.remember" class="!bg-slate-100 !border-slate-300 text-teal-600 focus:ring-teal-500/30 rounded" />
                    <span class="ms-3 text-sm font-medium text-slate-600 group-hover:text-slate-900 transition-colors">Remember this device</span>
                </label>
            </div>

            <div class="pt-4">
                <button type="submit" :disabled="form.processing"
                        class="w-full flex justify-center items-center gap-2 bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-400 hover:to-emerald-500 text-white font-black text-lg py-4 px-6 rounded-2xl shadow-lg shadow-teal-500/25 transition-all transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                    <span v-if="form.processing">Authenticating...</span>
                    <span v-else>Log In</span>
                </button>
            </div>
        </form>
    </GuestLayout>
</template>