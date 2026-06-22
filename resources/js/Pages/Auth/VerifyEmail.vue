<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: { type: String },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <GuestLayout>
        <Head title="Email Verification" />

        <div class="text-center mb-8">
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">Verify Your Email</h2>
            <p class="text-sm text-slate-500 mt-3 font-medium leading-relaxed">
                Before accessing the portal, please verify your email address by clicking the secure link we just emailed to you. If you didn't receive it, we can send another.
            </p>
        </div>

        <div v-if="verificationLinkSent" class="mb-6 p-4 rounded-2xl bg-teal-50 border border-teal-200 text-sm font-bold text-teal-700 text-center shadow-sm">
            A new verification link has been sent to your registered email address.
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div class="pt-2">
                <button type="submit" :disabled="form.processing"
                        class="w-full bg-slate-900 hover:bg-slate-800 text-white font-black text-lg py-4 px-6 rounded-2xl shadow-lg shadow-slate-900/20 transition-all transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                    <span v-if="form.processing">Sending...</span>
                    <span v-else>Resend Verification Email</span>
                </button>
            </div>

            <div class="text-center pt-2">
                <Link :href="route('logout')" method="post" as="button"
                      class="text-sm font-bold text-rose-500 hover:text-rose-600 transition-colors uppercase tracking-widest">
                    Log Out
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>