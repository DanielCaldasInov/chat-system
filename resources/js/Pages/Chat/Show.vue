<script setup>
import { ref, onMounted, onUnmounted, watch, computed } from 'vue';
import { Head, usePage, useForm } from '@inertiajs/vue3';
import ChatLayout from '@/Layouts/ChatLayout.vue';

const props = defineProps({
    currentRoom: Object,
    messages: Array,
});

const user = usePage().props.auth.user;

const form = useForm({
    body: '',
});

const messagesContainer = ref(null);

const localMessages = ref([...props.messages]);

watch(() => props.messages, (newMessages) => {
    localMessages.value = [...newMessages];
    setTimeout(() => scrollToBottom(), 50);
});

const scrollToBottom = () => {
    if (messagesContainer.value) {
        messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
    }
};

onMounted(() => {
    scrollToBottom();

    window.Echo.private(`chat.room.${props.currentRoom.id}`)
        .listen('MessageSent', (e) => {
            localMessages.value.push(e.message);

            setTimeout(() => scrollToBottom(), 50);
        });
});

onUnmounted(() => {
    window.Echo.leave(`chat.room.${props.currentRoom.id}`);
});

const submitMessage = () => {
    if (!form.body.trim()) return;

    form.post(route('messages.store', props.currentRoom.id), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('body');
            setTimeout(() => scrollToBottom(), 50);
        },
    });
};

const formatTime = (dateString) => {
    return new Date(dateString).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
};

const roomName = computed(() => {
    if (props.currentRoom.type === 'group') return props.currentRoom.name;
    const otherUser = props.currentRoom.users.find(u => u.id !== user.id);
    return otherUser ? otherUser.name : 'Unknown User';
});
</script>

<template>
    <Head :title="roomName" />
    <ChatLayout>
        <div class="h-14 flex items-center px-6 border-b border-gray-200 bg-white font-semibold shadow-sm z-10 flex-shrink-0">
            <span v-if="currentRoom.type === 'group'" class="text-gray-400 mr-2 text-xl leading-none">#</span>
            <span class="text-gray-800">{{ roomName }}</span>
        </div>

        <div class="flex-1 overflow-y-auto p-6 space-y-6 bg-white" ref="messagesContainer">
            <div
                v-for="message in localMessages"
                :key="message.id"
                class="flex items-start mb-4"
                :class="{ 'flex-row-reverse': message.user_id === user.id }"
            >
                <div
                    class="flex-shrink-0"
                    :class="message.user_id === user.id ? 'ml-3' : 'mr-3'"
                >
                    <img
                        v-if="message.user.avatar_url"
                        :src="message.user.avatar_url"
                        class="h-10 w-10 rounded-full object-cover shadow-sm"
                        :alt="message.user.name"
                    />
                    <div
                        v-else
                        class="h-10 w-10 rounded-full bg-gray-800 flex items-center justify-center text-white font-bold text-sm uppercase shadow-sm"
                    >
                        {{ message.user.name.charAt(0) }}
                    </div>
                </div>

                <div
                    class="max-w-[70%] px-4 py-2 rounded-lg shadow-sm"
                    :class="[
            message.user_id === user.id
                ? 'bg-blue-600 text-white rounded-tr-none'
                : 'bg-white text-gray-800 rounded-tl-none border border-gray-100'
        ]"
                >
                    <div
                        v-if="message.user_id !== user.id"
                        class="text-xs font-bold mb-1 opacity-75"
                    >
                        {{ message.user.name }}
                    </div>

                    <p class="text-sm leading-relaxed break-words">
                        {{ message.body }}
                    </p>

                    <div
                        class="text-[10px] mt-1 text-right opacity-70"
                    >
                        {{ formatTime(message.created_at) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="p-4 bg-white border-t border-gray-200 flex-shrink-0">
            <form @submit.prevent="submitMessage" class="flex relative max-w-4xl mx-auto">
                <input
                    v-model="form.body"
                    type="text"
                    placeholder="Type your message here..."
                    class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-3.5 pr-20 shadow-sm"
                    autocomplete="off"
                />
                <button
                    type="submit"
                    class="absolute right-2 top-1.5 bottom-1.5 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium text-sm transition-colors disabled:opacity-50"
                    :disabled="form.processing || !form.body.trim()"
                >
                    Send
                </button>
            </form>
        </div>
    </ChatLayout>
</template>
