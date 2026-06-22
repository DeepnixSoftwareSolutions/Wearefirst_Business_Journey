<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Confirm Password" />

        <div class="text-center mb-8">
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">Security Check</h2>
            <p class="text-sm text-slate-500 mt-2 font-medium leading-relaxed">
                This is a secure area of the application. Please confirm your password before continuing.
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <label for="password" class="block text-xs font-bold text-slate-600 uppercase tracking-widest mb-2">Password</label>
                <input id="password" type="password" v-model="form.password" required autocomplete="current-password" autofocus
                       class="w-full bg-slate-100/70 border border-slate-200 rounded-2xl px-4 py-3.5 text-slate-900 placeholder-slate-400 focus:border-teal-500 focus:ring focus:ring-teal-500/20 transition-all outline-none shadow-inner"
                       placeholder="••••••••" />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="pt-2">
                <button type="submit" :disabled="form.processing"
                        class="w-full bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-400 hover:to-emerald-500 text-white font-black text-lg py-4 px-6 rounded-2xl shadow-lg shadow-teal-500/25 transition-all transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                    <span v-if="form.processing">Verifying...</span>
                    <span v-else>Confirm Identity</span>
                </button>
            </div>
        </form>
    </GuestLayout>
</template>