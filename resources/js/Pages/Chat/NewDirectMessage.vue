<script setup>
import ChatLayout from '@/Layouts/ChatLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';

const props = defineProps({ users: Array, filters: Object });
const search = ref(props.filters.search || '');

const startDM = (userId) => {
    router.post(route('chat.direct.start', userId));
};

watch(search, debounce((value) => {
    router.get(route('chat.direct.new'), { search: value }, { preserveState: true, replace: true });
}, 300));
</script>

<template>
    <Head title="New Message" />
    <ChatLayout>
        <div class="flex-1 flex flex-col bg-gray-50 h-full">
            <div class="p-8 max-w-3xl mx-auto w-full">
                <h1 class="text-2xl font-bold text-gray-900 mb-6">Start a new conversation</h1>

                <div class="mb-6">
                    <input v-model="search" type="text" placeholder="Search users by name or email..."
                           class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <button v-for="u in users" :key="u.id" @click="startDM(u.id)"
                            class="flex items-center p-4 bg-white rounded-2xl shadow-sm border border-gray-100 hover:border-indigo-300 hover:shadow-md transition-all group text-left w-full">
                    <span class="w-12 h-12 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-lg mr-4 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                        {{ u.name.charAt(0) }}
                    </span>
                        <span class="flex-1 truncate block">
                        <span class="block font-bold text-gray-900 truncate">{{ u.name }}</span>
                        <span class="block text-sm text-gray-500 truncate">{{ u.email }}</span>
                    </span>

                    </button>
                </div>

                <div v-if="users.length === 0" class="text-center py-12 bg-white rounded-2xl border border-dashed border-gray-300">
                    <p class="text-gray-500">No users found matching your search.</p>
                </div>
            </div>
        </div>
    </ChatLayout>
</template>
