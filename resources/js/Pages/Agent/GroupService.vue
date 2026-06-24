<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({ networkData: Object });

// Helper to determine what they need to do to get a payout based on Graduation Rules
const getActionableAdvice = (node) => {
    const left = node.left_points;
    const right = node.right_points;
    const pairs = Math.min(left, right);
    const level = node.current_pair_level;

    // 1. Determine Target based strictly on current level
    let targetPairs = 1;
    if (level === 1) targetPairs = 2;
    if (level >= 2) targetPairs = 3; 

    const nextPayout = (targetPairs === 3) ? 7500 : (targetPairs === 2 ? 5000 : 2500);

    // 2. Cap Reached: Stop encouraging them to build this node today
    if (pairs >= targetPairs) {
        if (targetPairs === 3) {
            return `🏆 Max maturity reached! You've secured Rs 7500. ALL points will be hard-flushed tonight.`;
        }
        return `✅ Target reached! You've secured Rs ${nextPayout}. Excess points carry over to tomorrow. Focus on other nodes today!`;
    }

    // 3. Balanced Status
    if (left === right) {
        if (left === 0) {
            if (level === 0) return `Lines are empty. Add 1 student to both sides to trigger your first Rs 2500 match!`;
            if (level === 1) return `Level 1! Add 2 students to both sides to trigger your first Rs 5000 match.`;
            if (level >= 2) return `Matured Level! Add 3 students to both sides to trigger a Rs 7500 match.`;
        }
        const needed = targetPairs - pairs;
        return `Balanced at ${pairs} pair(s). You need ${needed} more pair(s) to trigger a Rs ${nextPayout} payout!`;
    }
    
    // 4. Unbalanced Status
    const isLeftBigger = left > right;
    const strongLeg = isLeftBigger ? node.left_name : node.right_name;
    const weakLeg = isLeftBigger ? node.right_name : node.left_name;
    const strongPoints = isLeftBigger ? left : right;
    const weakPoints = isLeftBigger ? right : left;

    const neededOnWeak = targetPairs - weakPoints;
    const neededOnStrong = Math.max(0, targetPairs - strongPoints);

    if (neededOnStrong > 0) {
        return `🔥 Push for the next level! Add ${neededOnWeak} to ${weakLeg} AND ${neededOnStrong} to ${strongLeg} to reach ${targetPairs} pairs and trigger a Rs ${nextPayout} match.`;
    } else {
        return `🔥 Strong ${strongLeg}! Add ${neededOnWeak} student(s) to the ${weakLeg} to hit ${targetPairs} pairs and trigger a Rs ${nextPayout} match.`;
    }
};
</script>

<template>
    <Head title="Group Service" />
    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="font-bold text-2xl text-slate-800 tracking-tight">Group Service Status</h2>
                <p class="text-sm text-slate-500 mt-1">Monitor your 3 accounts & 4 lines.</p>
            </div>
        </template>

        <div class="py-10 bg-slate-50 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                
                <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-[2rem] p-8 text-white relative overflow-hidden shadow-2xl border border-slate-700">
                    <div class="absolute top-0 right-0 -mt-10 -mr-10 w-64 h-64 bg-teal-500 rounded-full opacity-20 blur-[60px] pointer-events-none"></div>
                    <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
                        <div>
                            <h3 class="text-teal-400 font-bold uppercase tracking-widest text-xs mb-2">Total Projected Payout (Today)</h3>
                            <div class="text-5xl font-black tracking-tight">Rs {{ networkData.total_projected_today }}</div>
                            <p class="text-sm text-slate-400 mt-2">Combined across Main, Sub A, and Sub B accounts. Resets at midnight.</p>
                        </div>
                        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/10 flex gap-6">
                            <div class="text-center">
                                <div class="text-2xl font-black">{{ networkData.nodes.main.pending_matches + networkData.nodes.sub_a.pending_matches + networkData.nodes.sub_b.pending_matches }}</div>
                                <div class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Total Matches</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div v-for="(node, key) in networkData.nodes" :key="key" 
                         class="rounded-[2rem] p-6 relative overflow-hidden group hover:shadow-xl transition-all duration-300 flex flex-col"
                         :class="key === 'main' ? 'bg-white border-2 border-slate-200/60 shadow-lg z-10' : 'bg-slate-100/60 border border-slate-200/80 shadow-sm'">
                        
                        <div class="flex justify-between items-start mb-6">
                            <div class="flex items-center gap-2">
                                <span v-if="key === 'main'" class="flex w-2.5 h-2.5 bg-teal-500 rounded-full shadow-[0_0_8px_rgba(20,184,166,0.8)]"></span>
                                <h3 class="font-black text-lg text-slate-800">{{ node.title }}</h3>
                            </div>
                            <div class="text-right">
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Projected</div>
                                <div class="font-black text-teal-600">Rs {{ node.projected_income }}</div>
                            </div>
                        </div>

                        <div class="mb-5 p-4 rounded-xl relative overflow-hidden transition-colors"
                             :class="key === 'main' ? 'bg-slate-50 border border-slate-100' : 'bg-white border border-slate-100/50'">
                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-indigo-500"></div>
                            <div class="flex justify-between text-xs font-bold mb-3">
                                <span class="text-slate-600">{{ node.left_name }}</span>
                                <span class="text-indigo-600">{{ node.left_points }} Student(s)</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-lg font-black text-slate-800 leading-none mb-1">Rs {{ node.left_value }}</span>
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Unmatched Value</span>
                            </div>
                        </div>

                        <div class="mb-6 p-4 rounded-xl relative overflow-hidden transition-colors"
                             :class="key === 'main' ? 'bg-slate-50 border border-slate-100' : 'bg-white border border-slate-100/50'">
                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-amber-500"></div>
                            <div class="flex justify-between text-xs font-bold mb-3">
                                <span class="text-slate-600">{{ node.right_name }}</span>
                                <span class="text-amber-600">{{ node.right_points }} Student(s)</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-lg font-black text-slate-800 leading-none mb-1">Rs {{ node.right_value }}</span>
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Unmatched Value</span>
                            </div>
                        </div>

                        <div class="mt-auto border p-4 rounded-xl shadow-inner transition-colors"
                             :class="key === 'main' ? 'bg-slate-900 border-slate-800' : 'bg-slate-800 border-slate-700'">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-sm">🎯</span>
                                <span class="text-xs font-black text-teal-400 uppercase tracking-widest">Strategy</span>
                            </div>
                            <p class="text-xs font-bold text-slate-300 leading-relaxed">{{ getActionableAdvice(node) }}</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>