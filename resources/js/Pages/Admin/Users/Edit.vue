<script setup>
import ChatLayout from '@/Layouts/ChatLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Modal from '@/Components/Modal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    user: Object,
});

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: '',
    password_confirmation: '',
    is_admin: props.user.is_admin === 1 || props.user.is_admin === true,
});

const submit = () => {
    form.patch(route('admin.users.update', props.user.id), {
        preserveScroll: true,
        onSuccess: () => form.reset('password', 'password_confirmation'),
    });
};

const confirmingUserDeletion = ref(false);
const deleteForm = useForm({});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
};

const deleteUser = () => {
    deleteForm.delete(route('admin.users.destroy', props.user.id), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    deleteForm.reset();
};
</script>

<template>
    <Head title="Edit User" />

    <ChatLayout>
        <div class="h-full overflow-y-auto bg-gray-50">
            <div class="max-w-5xl mx-auto py-12 px-6 lg:px-8">

                <div class="mb-8 flex items-center justify-between">
                    <div>
                        <Link
                            :href="route('admin.users.index')"
                            class="text-sm font-medium text-indigo-600 hover:text-indigo-500 mb-2 inline-flex items-center transition-colors"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to Users
                        </Link>
                        <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight flex items-center gap-3">
                            Edit User
                            <span v-if="props.user.is_admin" class="px-3 py-1 bg-purple-100 text-purple-700 text-xs rounded-full uppercase tracking-wider font-bold align-middle">
                                Admin
                            </span>
                        </h2>
                        <p class="mt-2 text-sm text-gray-600">
                            Update account details and permissions for <span class="font-semibold">{{ props.user.name }}</span>.
                        </p>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-6 sm:p-8 max-w-3xl">
                        <form @submit.prevent="submit" class="space-y-6">

                            <div>
                                <InputLabel for="name" value="Full Name" />
                                <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div>
                                <InputLabel for="email" value="Email Address" />
                                <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required />
                                <InputError class="mt-2" :message="form.errors.email" />
                            </div>

                            <div class="pt-4 mt-2 border-t border-gray-100">
                                <h3 class="text-sm font-medium text-gray-900 mb-1">Change Password</h3>
                                <p class="text-xs text-gray-500 mb-4">Leave these fields blank if you do not want to change the current password.</p>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <InputLabel for="password" value="New Password" />
                                        <TextInput id="password" type="password" class="mt-1 block w-full placeholder-gray-300" v-model="form.password" autocomplete="new-password" placeholder="Leave blank to keep current" />
                                        <InputError class="mt-2" :message="form.errors.password" />
                                    </div>
                                    <div>
                                        <InputLabel for="password_confirmation" value="Confirm New Password" />
                                        <TextInput id="password_confirmation" type="password" class="mt-1 block w-full placeholder-gray-300" v-model="form.password_confirmation" autocomplete="new-password" placeholder="Leave blank to keep current" />
                                        <InputError class="mt-2" :message="form.errors.password_confirmation" />
                                    </div>
                                </div>
                            </div>

                            <div class="pt-4 border-t border-gray-100">
                                <label class="flex items-start cursor-pointer">
                                    <span class="flex items-center h-5">
                                        <input type="checkbox" v-model="form.is_admin" class="rounded border-gray-300 text-gray-900 shadow-sm focus:ring-indigo-500 h-4 w-4" :disabled="props.user.id === $page.props.auth.user.id" />
                                    </span>
                                                                <span class="ml-3 text-sm flex flex-col">
                                        <span class="font-medium text-gray-700" :class="{ 'opacity-50': props.user.id === $page.props.auth.user.id }">
                                            Administrator Role
                                        </span>
                                        <span class="text-gray-500 text-xs mt-1" v-if="props.user.id === $page.props.auth.user.id">
                                            You cannot remove your own admin privileges.
                                        </span>
                                        <span class="text-gray-500 mt-1" v-else>
                                            Grant full access to manage users and settings.
                                        </span>
                                    </span>
                                </label>
                            </div>

                            <div class="flex items-center justify-between pt-6 border-t border-gray-100 mt-6">
                                <div>
                                    <button
                                        v-if="props.user.id !== $page.props.auth.user.id"
                                        type="button"
                                        @click="confirmUserDeletion"
                                        class="text-sm text-red-600 hover:text-red-900 font-medium transition-colors"
                                    >
                                        Delete User
                                    </button>
                                </div>

                                <div class="flex items-center space-x-4">
                                    <Link :href="route('admin.users.index')" class="px-2 py-2 text-sm text-gray-600 hover:text-gray-900 font-medium transition-colors">
                                        Cancel
                                    </Link>

                                    <PrimaryButton class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                        Save Changes
                                    </PrimaryButton>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="h-12"></div>
            </div>
        </div>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Are you sure you want to delete <span class="font-bold">{{ props.user.name }}</span>?
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
