<script setup>
import ChatLayout from '@/Layouts/ChatLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    is_admin: false,
});

const submit = () => {
    form.post(route('admin.users.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Create User" />

    <ChatLayout>
        <div class="h-full overflow-y-auto bg-gray-50">
            <div class="max-w-5xl mx-auto py-12 px-6 lg:px-8">

                <div class="mb-8">
                    <Link
                        :href="route('admin.users.index')"
                        class="text-sm font-medium text-indigo-600 hover:text-indigo-500 mb-2 inline-flex items-center transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Users
                    </Link>
                    <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                        Create New User
                    </h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Add a new member to the organization. They will receive an email with their credentials.
                    </p>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-6 sm:p-8 max-w-3xl"> <form @submit.prevent="submit" class="space-y-6">

                        <div>
                            <InputLabel for="name" value="Full Name" />
                            <TextInput
                                id="name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.name"
                                required
                                autofocus
                                placeholder="e.g. John Doe"
                            />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <div>
                            <InputLabel for="email" value="Email Address" />
                            <TextInput
                                id="email"
                                type="email"
                                class="mt-1 block w-full"
                                v-model="form.email"
                                required
                                placeholder="john@example.com"
                            />
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <InputLabel for="password" value="Password" />
                                <TextInput
                                    id="password"
                                    type="password"
                                    class="mt-1 block w-full"
                                    v-model="form.password"
                                    required
                                    autocomplete="new-password"
                                />
                                <InputError class="mt-2" :message="form.errors.password" />
                            </div>
                            <div>
                                <InputLabel for="password_confirmation" value="Confirm Password" />
                                <TextInput
                                    id="password_confirmation"
                                    type="password"
                                    class="mt-1 block w-full"
                                    v-model="form.password_confirmation"
                                    required
                                    autocomplete="new-password"
                                />
                                <InputError class="mt-2" :message="form.errors.password_confirmation" />
                            </div>
                        </div>

                        <Link
                            v-if="$page.props.auth.user.is_admin"
                            :href="route('admin.users.index')"
                            class="flex items-center w-full p-2 mb-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-200/75 transition-colors focus:outline-none"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            Manage Users
                        </Link>

                        <div class="flex items-center justify-end pt-6 space-x-4">
                            <Link
                                :href="route('admin.users.index')"
                                class="px-6 py-2.5 text-sm text-gray-600 hover:text-gray-900 font-medium"
                            >
                                Cancel
                            </Link>

                            <PrimaryButton
                                class="px-6 py-2.5 bg-gray-900 hover:bg-gray-800 focus:bg-gray-800 active:bg-gray-900"
                                :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing"
                            >
                                Create User
                            </PrimaryButton>
                        </div>
                    </form>
                    </div>
                </div>

                <div class="h-12"></div>
            </div>
        </div>
    </ChatLayout>
</template>
