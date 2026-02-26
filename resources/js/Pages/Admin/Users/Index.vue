<script setup>
import ChatLayout from '@/Layouts/ChatLayout.vue';
import Modal from '@/Components/Modal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    users: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');
const role = ref(props.filters.role || 'all');

let searchTimeout = null;
watch([search, role], ([newSearch, newRole]) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('admin.users.index'), {
            search: newSearch,
            role: newRole === 'all' ? null : newRole
        }, {
            preserveState: true,
            replace: true,
            preserveScroll: true
        });
    }, 300);
});

const confirmingUserDeletion = ref(false);
const userToDelete = ref(null);
const deleteForm = useForm({});

const confirmUserDeletion = (user) => {
    userToDelete.value = user;
    confirmingUserDeletion.value = true;
};

const deleteUser = () => {
    deleteForm.delete(route('admin.users.destroy', userToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    setTimeout(() => { userToDelete.value = null; }, 200);
    deleteForm.reset();
};
</script>

<template>
    <Head title="Manage Users" />

    <ChatLayout>
        <div class="h-full overflow-y-auto bg-gray-50">
            <div class="max-w-5xl mx-auto py-12 px-6 lg:px-8">

                <div class="flex flex-col items-center mb-10 gap-4 py-2">
                    <h2 class="text-4xl font-extrabold text-gray-900 tracking-tight">
                        Users
                    </h2>
                    <Link
                        :href="route('admin.users.create')"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm whitespace-nowrap"
                    >
                        + New User
                    </Link>
                </div>

                <div class="flex flex-row gap-4 mb-6">

                    <div class="flex-1 w-full">
                        <input
                            v-model="search"
                            type="text"
                            class="block w-full px-4 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm shadow-sm transition-colors"
                            placeholder="Search by name or email..."
                        />
                    </div>

                    <div class="sm:w-56 shrink-0">
                        <select
                            v-model="role"
                            class="block w-full pl-4 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md shadow-sm transition-colors cursor-pointer"
                        >
                            <option value="all">All Roles</option>
                            <option value="admin">Admin</option>
                            <option value="member">Members</option>
                        </select>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <table class="min-w-full text-left border-collapse">
                        <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                        <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50/50 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-4">
                                    <img v-if="user.avatar_url" :src="user.avatar_url" class="w-10 h-10 flex-shrink-0 rounded-full object-cover shadow-sm"  alt="avatar"/>
                                    <div v-else class="w-10 h-10 flex-shrink-0 rounded-full bg-gray-200 flex items-center justify-center text-sm font-bold text-gray-600 uppercase shadow-sm">
                                        {{ user.name.charAt(0) }}
                                    </div>
                                    <span class="px-2 font-medium text-gray-900">{{ user.name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ user.email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="user.is_admin ? 'bg-purple-100 text-purple-700 border-purple-200' : 'bg-blue-100 text-blue-700 border-blue-200'" class="px-4 py-1.5 border rounded-full text-xs font-bold uppercase tracking-wider">
                                        {{ user.is_admin ? 'Admin' : 'Member' }}
                                    </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                                <Link
                                    :href="route('admin.users.edit', user.id)"
                                    class="inline-flex items-center px-3 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 transition ease-in-out duration-150 shadow-sm"
                                >
                                    Edit
                                </Link>

                                <button
                                    v-if="user.id !== $page.props.auth.user.id"
                                    @click="confirmUserDeletion(user)"
                                    class="inline-flex items-center px-3 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 transition ease-in-out duration-150 shadow-sm"
                                >
                                    Delete
                                </button>
                            </td>
                        </tr>
                        <tr v-if="users.data.length === 0">
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500 font-medium text-sm">
                                No users found matching your filters.
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="users.links && users.links.length > 3" class="mt-6 flex justify-center">
                    <div class="flex flex-wrap shadow-sm rounded-md">
                        <template v-for="(link, key) in users.links" :key="key">
                            <div
                                v-if="link.url === null"
                                class="mr-1 mb-1 px-4 py-2 text-sm text-gray-400 border border-gray-200 bg-white rounded-md cursor-not-allowed"
                                v-html="link.label"
                            />
                            <Link
                                v-else
                                :href="link.url"
                                class="mr-1 mb-1 px-4 py-2 text-sm border border-gray-200 rounded-md hover:bg-gray-50 focus:border-indigo-500 focus:text-indigo-500 transition-colors"
                                :class="{ 'bg-indigo-50 text-indigo-600 border-indigo-200 font-semibold': link.active, 'bg-white text-gray-600': !link.active }"
                                v-html="link.label"
                            />
                        </template>
                    </div>
                </div>

                <div class="h-12"></div>
            </div>
        </div>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Are you sure you want to delete <span class="font-bold">{{ userToDelete?.name }}</span>?
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    Once this user is deleted, all of their resources and data will be permanently deleted. This action cannot be undone.
                </p>
                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal"> Cancel </SecondaryButton>
                    <DangerButton
                        class="ml-3"
                        :class="{ 'opacity-25': deleteForm.processing }"
                        :disabled="deleteForm.processing"
                        @click="deleteUser"
                    >
                        Delete User
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </ChatLayout>
</template>
