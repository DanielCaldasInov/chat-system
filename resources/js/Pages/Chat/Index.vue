<script setup>
import { Head } from '@inertiajs/vue3';
import ChatLayout from '@/Layouts/ChatLayout.vue';
</script>

<template>
    <Head title="Chat" />
    <ChatLayout>
        <div class="flex-1 flex items-center justify-center bg-gray-50/50 h-full">
            <div class="text-center max-w-sm px-4">
                <div class="text-5xl mb-6 select-none grayscale opacity-20">💬</div>
                <h2 class="text-xl font-semibold text-gray-800 mb-2">Welcome to InovChat</h2>
                <p class="text-sm text-gray-500">
                    Select a room or a ping from the left menu to start chatting.
                </p>
            </div>
        </div>
    </ChatLayout>
</template>
