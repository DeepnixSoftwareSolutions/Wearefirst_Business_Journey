<script setup>
import { ref, onMounted } from 'vue';
import * as d3 from 'd3';
import axios from 'axios';
import { router, usePage, Link } from '@inertiajs/vue3';
import { useConfirm } from '@/Composables/useConfirm';

const { confirmAction } = useConfirm();
// Get the currently logged-in user to show role-based actions
const authUser = usePage().props.auth.user;

const treeContainer = ref(null);
const loading = ref(true);
const error = ref(null);

const slideOverNode = ref(null);

// Search Feature State
const searchQuery = ref('');
const d3RootData = ref(null);
const d3Zoom = ref(null);
const d3SvgGroup = ref(null);

const loadTreeData = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/network-tree');
        loading.value = false;
        setTimeout(() => renderTree(response.data), 50);
    } catch (err) {
        console.error(err);
        error.value = "Failed to load network tree.";
        loading.value = false;
    }
};

onMounted(() => {
    loadTreeData();
});

// --- Network Commands ---

const promoteUser = async (userId) => {
    const confirmed = await confirmAction({
        title: 'Promote Student',
        message: 'Promote this student to an Agent? They will receive login credentials via email.',
        confirmText: 'Promote',
        confirmButtonTheme: 'primary'
    });
    if(confirmed) {
        router.post(`/manager/promote/${userId}`, {}, {
            preserveScroll: true,
            onSuccess: () => { closeSlideOver(); loadTreeData(); }
        });
    }
};

const unlockLegs = async (userId) => {
    const confirmed = await confirmAction({
        title: 'Unlock Middle Legs',
        message: 'Permanently unlock G2 and G3 lines for this agent?',
        confirmText: 'Unlock G2/G3',
        confirmButtonTheme: 'success'
    });
    if(confirmed) {
        router.post(`/admin/agents/${userId}/unlock-legs`, {}, {
            preserveScroll: true,
            onSuccess: () => { closeSlideOver(); loadTreeData(); }
        });
    }
};

const toggleHold = async (userId) => {
    const confirmed = await confirmAction({
        title: 'Toggle Hold',
        message: 'Are you sure you want to toggle the hold status for this agent?',
        confirmText: 'Confirm',
        confirmButtonTheme: 'success'
    });
    if(confirmed) {
        router.post(`/admin/agents/${userId}/toggle-hold`, {}, {
            preserveScroll: true,
            onSuccess: () => { closeSlideOver(); loadTreeData(); }
        });
    }
};

const terminateUser = async (userId) => {
    const confirmed = await confirmAction({
        title: 'Terminate Account',
        message: 'WARNING: This will anonymize the user and freeze their nodes permanently. This action is irreversible. Proceed?',
        confirmText: 'Terminate',
        confirmButtonTheme: 'danger'
    });
    if(confirmed) {
        router.delete(`/admin/users/${userId}/safe-delete`, {
            preserveScroll: true,
            onSuccess: () => { closeSlideOver(); loadTreeData(); }
        });
    }
};

// Helper function to determine if an empty node is a locked middle leg
const checkIsLocked = (uniqueName, isUnlocked) => {
    if (!uniqueName) return false;
    if (isUnlocked) return false;
    const clean = uniqueName.toUpperCase().replace(/\s+/g, '');
    return clean.includes('G2') || clean.includes('G3');
};

// --- The Search & Pan Function ---
const performSearch = () => {
    if (!searchQuery.value || !d3RootData.value) return;
    
    const cleanTerm = searchQuery.value.toLowerCase().replace(/\s+/g, '');
    
    const targetNode = d3RootData.value.descendants().find(d => {
        const cleanName = d.data.name ? d.data.name.toLowerCase().replace(/\s+/g, '') : '';
        if (cleanName.includes(cleanTerm)) return true;

        if (d.data.attributes && d.data.attributes.unique_name) {
            const noSpacesUnique = d.data.attributes.unique_name.toLowerCase().replace(/\s+/g, '');
            const noDirectionUnique = noSpacesUnique.replace(/l(?=g)/, '').replace(/r(?=g)/, '');
            return noSpacesUnique.includes(cleanTerm) || noDirectionUnique.includes(cleanTerm);
        }
        return false;
    });

    if (targetNode) {
        const width = treeContainer.value.clientWidth;
        const height = treeContainer.value.clientHeight;
        const scale = 1.2; 

        const x = -targetNode.x * scale + width / 2;
        const y = -targetNode.y * scale + height / 2;

        d3SvgGroup.value.transition().duration(1000).call(
            d3Zoom.value.transform, 
            d3.zoomIdentity.translate(x, y).scale(scale)
        );

        d3.selectAll('g.node')
          .filter(d => d.data.id === targetNode.data.id)
          .select('rect')
          .transition().duration(200).style('stroke', '#0ea5e9').style('stroke-width', '4px')
          .transition().duration(2000).style('stroke', d => {
              if (d.data.attributes.is_pending) return "#fcd34d";
              if (d.data.attributes.is_empty) {
                  return checkIsLocked(d.data.attributes.unique_name, d.data.attributes.middle_legs_unlocked) ? "#94a3b8" : "#cbd5e1";
              }
              if (d.data.attributes.user_role === 'Student') return "#bfdbfe";
              return d.data.attributes.is_held ? "#ef4444" : "#e2e8f0";
          }).style('stroke-width', '2px');

    } else {
        confirmAction({
            title: 'Search Result',
            message: 'Node not found on the tree. Please verify the ID or Name and try again.',
            confirmText: 'Okay',
            cancelText: 'Close',
            confirmButtonTheme: 'primary'
        });
    }
};

// --- D3 Render Logic ---
const renderTree = (data) => {
    const container = treeContainer.value;
    const width = container.clientWidth;
    const height = container.clientHeight;

    d3.select(container).selectAll("*").remove();

    const svg = d3.select(container)
        .append("svg")
        .attr("width", width)
        .attr("height", height)
        .style("cursor", "grab");

    const defs = svg.append("defs");
    const filter = defs.append("filter").attr("id", "drop-shadow").attr("x", "-20%").attr("y", "-20%").attr("width", "140%").attr("height", "140%");
    filter.append("feDropShadow").attr("dx", "0").attr("dy", "4").attr("stdDeviation", "6").attr("flood-color", "#0f172a").attr("flood-opacity", "0.1");

    const g = svg.append("g");

    const zoom = d3.zoom()
        .scaleExtent([0.2, 2.5])
        .on("zoom", (event) => g.attr("transform", event.transform));

    svg.call(zoom);
    svg.call(zoom.transform, d3.zoomIdentity.translate(width / 2, 80).scale(0.85));

    d3Zoom.value = zoom;
    d3SvgGroup.value = svg;

    const tree = d3.tree().nodeSize([220, 140]);
    const root = d3.hierarchy(data);
    root.x0 = 0;
    root.y0 = 0;

    d3RootData.value = root;

    let i = 0; 
    update(root);

    function update(source) {
        const treeData = tree(root);
        const nodes = treeData.descendants();
        const links = treeData.descendants().slice(1);

        const link = g.selectAll('path.link')
            .data(links, d => d.id || (d.id = ++i));

        const linkEnter = link.enter().insert('path', "g")
            .attr("class", "link")
            .style("fill", "none")
            .style("stroke", "#cbd5e1")
            .style("stroke-width", "2px")
            .attr('d', d => {
                const o = {x: source.x0, y: source.y0};
                return diagonal(o, o);
            });

        link.merge(linkEnter).transition().duration(500)
            .attr('d', d => diagonal(d, d.parent));

        link.exit().transition().duration(500)
            .attr('d', d => {
                const o = {x: source.x, y: source.y};
                return diagonal(o, o);
            }).remove();

        const node = g.selectAll('g.node')
            .data(nodes, d => d.id || (d.id = ++i));

        const nodeEnter = node.enter().append('g')
            .attr('class', 'node')
            .attr("transform", d => `translate(${source.x0},${source.y0})`)
            .on('click', handleNodeClick)
            .on('dblclick', handleNodeDoubleClick);

        nodeEnter.append('rect')
            .attr("width", 180)
            .attr("height", 60)
            .attr("x", -90)
            .attr("y", -30)
            .attr("rx", 30)
            .style("fill", d => {
                if (d.data.attributes.is_pending) return "#fef3c7";
                if (d.data.attributes.is_empty) {
                    return checkIsLocked(d.data.attributes.unique_name, d.data.attributes.middle_legs_unlocked) ? "#f1f5f9" : "#f8fafc";
                }
                if (d.data.attributes.user_role === 'Student') return "#eff6ff";
                return d.data.attributes.is_held ? "#fef2f2" : "#ffffff";
            })
            .style("stroke", d => {
                if (d.data.attributes.is_pending) return "#fcd34d";
                if (d.data.attributes.is_empty) {
                    return checkIsLocked(d.data.attributes.unique_name, d.data.attributes.middle_legs_unlocked) ? "#94a3b8" : "#cbd5e1";
                }
                if (d.data.attributes.user_role === 'Student') return "#bfdbfe";
                return d.data.attributes.is_held ? "#ef4444" : "#e2e8f0";
            })
            .style("stroke-width", "2px")
            .style("stroke-dasharray", d => d.data.attributes.is_empty && !d.data.attributes.is_pending ? "6,6" : "0")
            .style("filter", d => d.data.attributes.is_empty && !d.data.attributes.is_pending ? "" : "url(#drop-shadow)")
            .style("cursor", d => {
                if (d.data.attributes.is_pending) return "not-allowed";
                if (d.data.attributes.is_empty && checkIsLocked(d.data.attributes.unique_name, d.data.attributes.middle_legs_unlocked)) return "not-allowed";
                return "pointer";
            })
            .style("transition", "all 0.2s ease");

        nodeEnter.append('circle')
            .attr('r', 22)
            .attr('cx', -60)
            .attr('cy', 0)
            .style("fill", d => {
                if (d.data.attributes.is_pending) return "#fbbf24";
                if (d.data.attributes.is_empty) return "transparent";
                if (d.data.attributes.user_role === 'Student') return "#dbeafe";
                return d.data.attributes.is_held ? "#fecaca" : "#0f172a";
            })
            .style("stroke", d => d.data.attributes.is_empty && !d.data.attributes.is_pending ? "#cbd5e1" : "none")
            .style("stroke-dasharray", d => d.data.attributes.is_empty && !d.data.attributes.is_pending ? "4,4" : "0");

        nodeEnter.append('text')
            .attr("x", -60)
            .attr("y", d => d.data.attributes.is_empty && !d.data.attributes.is_pending ? "8px" : "5px")
            .attr("text-anchor", "middle")
            .style("font-size", d => d.data.attributes.is_empty && !d.data.attributes.is_pending ? "24px" : "12px")
            .style("font-weight", "900")
            .style("fill", d => {
                if (d.data.attributes.is_pending) return "#78350f";
                if (d.data.attributes.is_empty) return "#94a3b8";
                return d.data.attributes.is_held ? "#b91c1c" : "#ffffff";
            })
            .text(d => {
                // If pending, we still return the hourglass emoji
                if (d.data.attributes.is_pending) return "⏳";
                if (d.data.attributes.is_empty) {
                    return checkIsLocked(d.data.attributes.unique_name, d.data.attributes.middle_legs_unlocked) ? "🔒" : "+";
                }
                return d.data.attributes.initials;
            });

        nodeEnter.append('text')
            .attr("x", -30)
            .attr("y", -4)
            .attr("text-anchor", "start")
            .style("font-size", "12px")
            .style("font-weight", "bold")
            .style("fill", d => d.data.attributes.user_role === 'Student' ? "#1e40af" : "#1e293b")
            .text(d => {
                // Dynamic pending status instead of hardcoded 'Pending Approval'
                if (d.data.attributes.is_pending) return d.data.attributes.pending_status; 
                if (d.data.attributes.is_empty) {
                    return checkIsLocked(d.data.attributes.unique_name, d.data.attributes.middle_legs_unlocked) ? "Locked Line" : "Available Slot";
                }
                return d.data.name.split(' - ')[0];
            });

        nodeEnter.append('text')
            .attr("x", -30)
            .attr("y", 12)
            .attr("text-anchor", "start")
            .style("font-size", "10px")
            .style("font-weight", "600")
            .style("fill", "#64748b")
            .text(d => {
                // Dynamic pending status instead of hardcoded 'Pending Approval'
                if (d.data.attributes.is_pending) return d.data.attributes.pending_status; 
                if (d.data.attributes.is_empty) return d.data.attributes.unique_name;
                return `L: ${d.data.attributes.left_points} | R: ${d.data.attributes.right_points}`;
            });

        nodeEnter.append('text')
            .attr("x", 80)
            .attr("y", -38)
            .attr("text-anchor", "end")
            .style("font-size", "9px")
            .style("font-weight", "800")
            .style("fill", "#94a3b8")
            .text(d => d.data.attributes.unique_name);

        const nodeUpdate = nodeEnter.merge(node);
        nodeUpdate.transition().duration(500)
            .attr("transform", d => `translate(${d.x},${d.y})`);

        const nodeExit = node.exit().transition().duration(500)
            .attr("transform", d => `translate(${source.x},${source.y})`)
            .remove();

        nodeExit.select('rect').attr('width', 1e-6).attr('height', 1e-6);
        nodeExit.selectAll('text').style('fill-opacity', 1e-6);

        nodes.forEach(d => { d.x0 = d.x; d.y0 = d.y; });

        function handleNodeClick(event, d) {
            if (d.data.attributes.is_pending) {
                // Determine the exact message based on the pending status
                const isAwaitingPayment = d.data.attributes.pending_status === 'Pending Payment';
                
                confirmAction({
                    title: 'Slot Reserved',
                    message: isAwaitingPayment 
                        ? 'This slot is currently reserved for an incoming student. The registering agent has not yet submitted the payment details.'
                        : 'This slot is currently reserved for an incoming student. The payment is awaiting verification by an Accountant.',
                    confirmText: 'Understood',
                    cancelText: 'Close', // We can still provide a cancel text, or your modal might handle single buttons
                    confirmButtonTheme: 'primary'
                });
                return;
            }
            // Open slide-over for BOTH empty and occupied nodes now!
            slideOverNode.value = d.data;
        }

        function handleNodeDoubleClick(event, d) {
            if (d.data.attributes.is_empty) return;
            if (d.children) {
                d._children = d.children;
                d.children = null;
            } else if (d._children) {
                d.children = d._children;
                d._children = null;
            }
            update(d);
        }
    }

    function diagonal(s, d) {
        return `M ${s.x} ${s.y} C ${s.x} ${(s.y + d.y) / 2}, ${d.x} ${(s.y + d.y) / 2}, ${d.x} ${d.y}`;
    }
};

const closeSlideOver = () => slideOverNode.value = null;
</script>

<template>
    <div class="w-full h-full relative overflow-hidden bg-slate-50 rounded-[1.5rem]" style="background-image: radial-gradient(#cbd5e1 1px, transparent 1px); background-size: 24px 24px;">
        
        <div class="absolute top-4 z-10 bg-white/90 backdrop-blur-md p-2 rounded-xl shadow-lg border border-slate-200 flex items-center transition-all duration-300"
             :class="slideOverNode ? 'right-4 opacity-0 pointer-events-none md:pointer-events-auto md:opacity-100 md:right-[400px]' : 'right-4 opacity-100'">
             
            <input v-model="searchQuery" @keyup.enter="performSearch" type="text" placeholder="Search Member ID (e.g. 0002 L G1) or Name" class="border-none bg-transparent focus:ring-0 text-sm font-bold text-slate-800 w-64 md:w-80 placeholder-slate-400">
            
            <button @click="performSearch" class="bg-teal-500 hover:bg-teal-600 text-white p-2 rounded-lg transition ml-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </button>
        </div>

        <div v-if="loading" class="absolute inset-0 flex flex-col items-center justify-center bg-white/50 backdrop-blur-sm z-30">
            <div class="w-12 h-12 border-4 border-slate-200 border-t-teal-500 rounded-full animate-spin mb-4"></div>
        </div>
        <div v-if="error" class="absolute inset-0 flex items-center justify-center bg-rose-50 text-rose-600 font-bold z-30">{{ error }}</div>

        <div ref="treeContainer" class="w-full h-full cursor-grab active:cursor-grabbing outline-none z-0 relative"></div>

        <transition enter-active-class="transform transition ease-in-out duration-300" enter-from-class="translate-x-full" enter-to-class="translate-x-0" leave-active-class="transform transition ease-in-out duration-300" leave-from-class="translate-x-0" leave-to-class="translate-x-full">
            <div v-if="slideOverNode" class="absolute top-0 right-0 h-full w-80 md:w-96 bg-white/95 backdrop-blur-xl border-l border-slate-200 shadow-2xl z-20 flex flex-col">
                
                <div class="p-6 border-b border-slate-100 flex justify-between items-start text-white" 
                     :class="slideOverNode.attributes.is_empty ? 'bg-slate-700' : 'bg-slate-900'">
                    <div>
                        <div class="flex items-center space-x-2 mb-2">
                            <span class="px-2 py-1 text-[10px] font-bold uppercase tracking-widest rounded-md border"
                                  :class="slideOverNode.attributes.is_empty ? 'bg-slate-600 border-slate-500 text-slate-200' : 'bg-teal-500/20 text-teal-400 border-teal-500/30'">
                                {{ slideOverNode.attributes.is_empty ? 'Network Node' : slideOverNode.attributes.node_type }}
                            </span>
                            <span v-if="slideOverNode.attributes.is_held" class="px-2 py-1 text-[10px] font-bold uppercase tracking-widest bg-rose-500/20 text-rose-400 rounded-md border border-rose-500/30">
                                ON HOLD
                            </span>
                        </div>
                        <h2 class="text-xl font-bold tracking-tight">
                            <template v-if="slideOverNode.attributes.is_pending">
                                {{ slideOverNode.attributes.pending_status }}
                            </template>
                            <template v-else>
                                {{ slideOverNode.attributes.is_empty ? 'Available Slot' : slideOverNode.name.split(' - ')[0] }}
                            </template>
                        </h2>
                        <p class="text-xs mt-1" :class="slideOverNode.attributes.is_empty ? 'text-slate-300' : 'text-slate-400'">
                            Ref ID: {{ slideOverNode.attributes.unique_name }}
                            <span v-if="!slideOverNode.attributes.is_empty">| Role: {{ slideOverNode.attributes.user_role }}</span>
                        </p>
                    </div>
                    <button @click="closeSlideOver" class="text-slate-400 hover:text-white bg-white/10 p-2 rounded-full transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6 flex-1 overflow-y-auto space-y-6">
                    
                    <template v-if="slideOverNode.attributes.is_empty">
                        
                        <div v-if="checkIsLocked(slideOverNode.attributes.unique_name, slideOverNode.attributes.middle_legs_unlocked)" class="bg-rose-50 border border-rose-200 rounded-2xl p-6 text-center shadow-sm">
                            <div class="w-12 h-12 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto mb-4 text-xl">🔒</div>
                            <h3 class="text-rose-900 font-black mb-2">Line is Locked</h3>
                            <p class="text-xs text-rose-700 leading-relaxed">
                                This placement line is currently locked. The parent Agent must have their G2 and G3 lines unlocked by an Administrator before registrations can occur here.
                            </p>
                        </div>

                        <div v-else class="space-y-6">
                            <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-6 text-center shadow-sm">
                                <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4 text-xl">✨</div>
                                <h3 class="text-emerald-900 font-black mb-2">Slot Available</h3>
                                <p class="text-xs text-emerald-700 leading-relaxed mb-4">
                                    This node is ready for a new placement.
                                </p>
                                
                                <Link :href="route('agent.register', { 
                                        manual_parent_id: slideOverNode.attributes.parent_id, 
                                        manual_position: slideOverNode.attributes.position,
                                        manual_line: slideOverNode.attributes.unique_name 
                                      })" 
                                      class="block w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-md transition">
                                    Register Student Here
                                </Link>
                            </div>
                            
                            <div class="text-[11px] text-slate-500 bg-slate-50 p-4 rounded-xl border border-slate-100">
                                <span class="font-bold block mb-1">Target Coordinates:</span>
                                Parent Node ID: {{ slideOverNode.attributes.parent_id }} <br>
                                Leg Position: {{ slideOverNode.attributes.position }}
                            </div>
                        </div>

                    </template>


                    <template v-else>
                        <div>
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Network Points</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 flex flex-col items-center justify-center">
                                    <span class="text-3xl font-black text-slate-800">{{ slideOverNode.attributes.left_points }}</span>
                                    <span class="text-xs font-semibold text-slate-500 mt-1">LEFT</span>
                                </div>
                                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 flex flex-col items-center justify-center">
                                    <span class="text-3xl font-black text-slate-800">{{ slideOverNode.attributes.right_points }}</span>
                                    <span class="text-xs font-semibold text-slate-500 mt-1">RIGHT</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Financials</h4>
                            <div class="space-y-3">
                                <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-2xl flex justify-between items-center">
                                    <span class="text-sm font-semibold text-emerald-800">Today's Projection</span>
                                    <span class="text-lg font-black text-emerald-600">Rs {{ slideOverNode.attributes.today_projected }}</span>
                                </div>
                                <div class="bg-blue-50 border border-blue-100 p-4 rounded-2xl flex justify-between items-center">
                                    <span class="text-sm font-semibold text-blue-800">Node Cumulative</span>
                                    <span class="text-lg font-black text-blue-600">Rs {{ slideOverNode.attributes.node_cumulative }}</span>
                                </div>
                                <div v-if="slideOverNode.attributes.user_total_cumulative !== null" class="bg-indigo-50 border border-indigo-100 p-4 rounded-2xl flex justify-between items-center shadow-sm">
                                    <span class="text-sm font-bold text-indigo-900">User Total</span>
                                    <span class="text-xl font-black text-indigo-700">Rs {{ slideOverNode.attributes.user_total_cumulative }}</span>
                                </div>
                            </div>
                        </div>

                        <div v-if="authUser.role === 'Admin' || authUser.role === 'Manager'" class="border-t border-slate-200 pt-6">
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Admin Commands</h4>
                            <div class="space-y-3">
                                
                                <Link v-if="authUser.role === 'Admin'" 
                                      :href="`/admin/users?search=${slideOverNode.name.split(' - ')[0]}`" 
                                      class="flex justify-center w-full py-3 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl shadow-md transition">
                                    View Full Profile
                                </Link>

                                <button v-if="authUser.role === 'Admin' && ['Agent', 'Student'].includes(slideOverNode.attributes.user_role) && !slideOverNode.attributes.middle_legs_unlocked" 
                                        @click="unlockLegs(slideOverNode.attributes.user_id)" 
                                        class="w-full py-3 bg-teal-50 hover:bg-teal-100 text-teal-700 border border-teal-200 font-bold rounded-xl transition">
                                    Unlock G2/G3 Lines
                                </button>

                                <button v-if="slideOverNode.attributes.user_role === 'Student' && slideOverNode.attributes.admission_fee_paid >= 15000" 
                                        @click="promoteUser(slideOverNode.attributes.user_id)" 
                                        class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md transition">
                                    Promote to Agent
                                </button>
                                
                                <div v-else-if="slideOverNode.attributes.user_role === 'Student'" class="text-center text-xs text-rose-500 font-bold p-2 bg-rose-50 rounded-lg">
                                    Cannot promote: Payment Incomplete
                                </div>

                                <template v-if="authUser.role === 'Admin' && slideOverNode.attributes.user_role !== 'Admin' && slideOverNode.attributes.user_role !== 'Manager'">
                                    <button @click="toggleHold(slideOverNode.attributes.user_id)" 
                                            class="w-full py-3 bg-amber-100 hover:bg-amber-200 text-amber-900 font-bold rounded-xl border border-amber-300 transition">
                                        {{ slideOverNode.attributes.is_held ? 'Remove Financial Hold' : 'Place Account on Hold' }}
                                    </button>
                                    
                                    <button @click="terminateUser(slideOverNode.attributes.user_id)" 
                                            class="w-full py-3 bg-rose-100 hover:bg-rose-200 text-rose-900 font-bold rounded-xl border border-rose-300 transition mt-2">
                                        Terminate Account
                                    </button>
                                </template>
                            </div>
                        </div>

                        <div v-if="slideOverNode.attributes.is_terminated" class="p-4 mt-6 bg-rose-50 text-rose-800 font-bold rounded-xl text-center border border-rose-200">
                            Account Terminated
                        </div>
                    </template>

                </div>
            </div>
        </transition>
    </div>
</template>

<style>
.node rect:hover {
    stroke: #0ea5e9 !important; 
    transition: stroke 0.2s;
}
</style>