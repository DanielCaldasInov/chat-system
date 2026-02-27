<script setup>
import ChatLayout from '@/Layouts/ChatLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    users: Array,
});

const form = useForm({
    name: '',
    avatar: null,
    users: [],
});

const imagePreview = ref(null);

const handleImageUpload = (e) => {
    const file = e.target.files[0];
    form.avatar = file;

    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const submit = () => {
    form.post(route('admin.rooms.store'));
};
</script>

<template>
    <Head title="Create a Room" />

    <ChatLayout>
        <div class="h-full overflow-y-auto bg-gray-50">
            <div class="max-w-4xl mx-auto py-12 px-6 lg:px-8">

                <div class="mb-8">
                    <Link
                        :href="route('chat.index')"
                        class="text-sm font-medium text-indigo-600 hover:text-indigo-500 mb-2 inline-flex items-center transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Chat
                    </Link>
                    <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                        Create a Room
                    </h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Set up a new group chat and select the initial members.
                    </p>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden p-6 sm:p-8">
                    <form @submit.prevent="submit" class="space-y-8">

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                            <div class="md:col-span-1 space-y-6">
                                <div>
                                    <InputLabel for="name" value="Room Name" />
                                    <TextInput
                                        id="name"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.name"
                                        required
                                        autofocus
                                        placeholder="e.g. Project Alpha"
                                    />
                                    <InputError class="mt-2" :message="form.errors.name" />
                                </div>

                                <div>
                                    <InputLabel value="Room Avatar (Optional)" />
                                    <div class="mt-2 flex items-center space-x-4">
                                        <div class="h-16 w-16 rounded-full bg-gray-100 flex items-center justify-center overflow-hidden border border-gray-200">
                                            <img v-if="imagePreview" :src="imagePreview" class="h-full w-full object-cover"  alt="Image"/>
                                            <svg v-else class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <label class="cursor-pointer bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            <span>Upload</span>
                                            <input type="file" class="sr-only" accept="image/*" @change="handleImageUpload" />
                                        </label>
                                    </div>
                                    <InputError class="mt-2" :message="form.errors.avatar" />
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <InputLabel value="Add Initial Members" />
                                <p class="text-xs text-gray-500 mb-3">You (Admin) will be added automatically.</p>

                                <div class="border border-gray-200 rounded-md h-64 overflow-y-auto p-2 bg-gray-50/50">
                                    <div v-for="user in users" :key="user.id">
                                        <label v-if="user.id !== $page.props.auth.user.id" class="flex items-center p-2 hover:bg-gray-100 rounded cursor-pointer transition-colors">
                                            <input
                                                type="checkbox"
                                                :value="user.id"
                                                v-model="form.users"
                                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 h-4 w-4"
                                            />
                                            <span class="ml-3 flex items-center space-x-3">
                                                <img v-if="user.avatar_url" :src="user.avatar_url" class="w-6 h-6 rounded-full object-cover" alt="Avatar"/>
                                                <span v-else class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-[10px] font-bold text-gray-600 uppercase">
                                                    {{ user.name.charAt(0) }}
                                                </span>
                                                <span class="text-sm text-gray-700 font-medium">{{ user.name }}</span>
                                                <span class="text-xs text-gray-400">({{ user.email }})</span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                                <InputError class="mt-2" :message="form.errors.users" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end pt-6 border-t border-gray-100 space-x-4">
                            <Link :href="route('chat.index')" class="text-sm text-gray-600 hover:text-gray-900 font-medium">
                                Cancel
                            </Link>
                            <PrimaryButton
                                class="px-6 py-2.5 bg-gray-900 hover:bg-gray-800"
                                :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing"
                            >
                                Create Room
                            </PrimaryButton>
                        </div>
                    </form>
                </div>

                <div class="h-12"></div>
            </div>
        </div>
    </ChatLayout>
</template>
