<script setup>
import ChatLayout from '@/Layouts/ChatLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    room: Object,
    members: Array,
    nonMembers: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');

let searchTimeout = null;
watch(search, (newSearch) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('admin.rooms.participants.index', props.room.id), {
            search: newSearch
        }, {
            preserveState: true,
            replace: true,
            preserveScroll: true
        });
    }, 300);
});

const addForm = useForm({
    user_id: null
});

const removeForm = useForm({});

const addParticipant = (userId) => {
    addForm.user_id = userId;
    addForm.post(route('admin.rooms.participants.store', props.room.id), {
        preserveScroll: true,
    });
};

const removeParticipant = (userId) => {
    removeForm.delete(route('admin.rooms.participants.destroy', { room: props.room.id, user: userId }), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="`Manage Participants - ${room.name}`" />

    <ChatLayout>
        <div class="h-full overflow-y-auto bg-gray-50">
            <div class="max-w-5xl mx-auto py-12 px-6 lg:px-8">

                <div class="mb-10">
                    <Link
                        :href="route('chat.show', room.id)"
                        class="text-sm font-medium text-indigo-600 hover:text-indigo-500 mb-2 inline-flex items-center transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Chat
                    </Link>
                    <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight flex items-center gap-3 mt-2">
                        Manage Participants
                        <span class="px-3 py-1 bg-gray-200 text-gray-800 text-sm rounded-full font-bold align-middle">
                            # {{ room.name }}
                        </span>
                    </h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Add or remove members from this chat room.
                    </p>
                </div>

                <div class="mb-12">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Add New Participants</h3>

                    <div class="mb-4">
                        <input
                            v-model="search"
                            type="text"
                            class="block w-full sm:w-1/2 px-4 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm shadow-sm transition-colors"
                            placeholder="Search by name or email to add..."
                        />
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <table class="min-w-full text-left border-collapse">
                            <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Action</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                            <tr v-for="user in nonMembers.data" :key="user.id" class="hover:bg-gray-50/50 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-4">
                                        <img v-if="user.avatar_url" :src="user.avatar_url" class="w-8 h-8 flex-shrink-0 rounded-full object-cover shadow-sm" alt="avatar" />
                                        <div v-else class="w-8 h-8 flex-shrink-0 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600 uppercase shadow-sm">
                                            {{ user.name.charAt(0) }}
                                        </div>
                                        <div>
                                            <div class="font-medium text-sm text-gray-900">{{ user.name }}</div>
                                            <div class="text-xs text-gray-500">{{ user.email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <button
                                        @click="addParticipant(user.id)"
                                        :disabled="addForm.processing && addForm.user_id === user.id"
                                        class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-[10px] text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 disabled:opacity-50 transition ease-in-out duration-150 shadow-sm"
                                    >
                                        <span v-if="addForm.processing && addForm.user_id === user.id">Adding...</span>
                                        <span v-else>Add</span>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="nonMembers.data.length === 0">
                                <td colspan="2" class="px-6 py-8 text-center text-gray-500 font-medium text-sm">
                                    <span v-if="search">No users found matching "{{ search }}".</span>
                                    <span v-else>All registered users are already in this room!</span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="nonMembers.links && nonMembers.links.length > 3" class="mt-4 flex justify-start">
                        <div class="flex flex-wrap shadow-sm rounded-md">
                            <template v-for="(link, key) in nonMembers.links" :key="key">
                                <div
                                    v-if="link.url === null"
                                    class="mr-1 mb-1 px-3 py-1.5 text-xs text-gray-400 border border-gray-200 bg-white rounded-md cursor-not-allowed"
                                    v-html="link.label"
                                />
                                <Link
                                    v-else
                                    :href="link.url"
                                    class="mr-1 mb-1 px-3 py-1.5 text-xs border border-gray-200 rounded-md hover:bg-gray-50 focus:border-indigo-500 focus:text-indigo-500 transition-colors"
                                    :class="{ 'bg-indigo-50 text-indigo-600 border-indigo-200 font-semibold': link.active, 'bg-white text-gray-600': !link.active }"
                                    v-html="link.label"
                                />
                            </template>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        Current Members
                        <span class="ml-2 bg-gray-100 text-gray-600 py-0.5 px-2.5 rounded-full text-xs font-semibold">{{ members.length }}</span>
                    </h3>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <table class="min-w-full text-left border-collapse">
                            <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Action</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                            <tr v-for="member in members" :key="member.id" class="hover:bg-gray-50/50 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-4">
                                        <img v-if="member.avatar_url" :src="member.avatar_url" class="w-8 h-8 flex-shrink-0 rounded-full object-cover shadow-sm" alt="avatar" />
                                        <div v-else class="w-8 h-8 flex-shrink-0 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600 uppercase shadow-sm">
                                            {{ member.name.charAt(0) }}
                                        </div>
                                        <div>
                                            <div class="font-medium text-sm text-gray-900">
                                                {{ member.name }}
                                                <span v-if="member.id === $page.props.auth.user.id" class="ml-2 text-[10px] text-gray-400 font-normal uppercase">(You)</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <button
                                        @click="removeParticipant(member.id)"
                                        :disabled="removeForm.processing"
                                        class="inline-flex items-center px-3 py-1.5 bg-red-600 border border-transparent rounded-md font-semibold text-[10px] text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 disabled:opacity-50 transition ease-in-out duration-150 shadow-sm"
                                    >
                                        Remove
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="members.length === 0">
                                <td colspan="2" class="px-6 py-8 text-center text-gray-500 font-medium text-sm">
                                    This room has no members yet.
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="h-12"></div>
            </div>
        </div>
    </ChatLayout>
</template>
