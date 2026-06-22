<script setup>
defineProps({
    class: String
});
</script>

<template>
    <div class="flex items-center justify-center h-full w-full">
        <img 
            src="/images/wearefirst-logo.png" 
            alt="Wearefirst Logo" 
            class="w-auto object-contain transition-transform duration-500 hover:scale-105" 
            :class="$props.class"
        />
    </div>
</template>