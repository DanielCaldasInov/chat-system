<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
});
</script>

<template>
    <Head title="Welcome" />

    <div class="relative flex justify-center items-center min-h-screen bg-gray-50 selection:bg-indigo-500 selection:text-white">

        <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-[30%] -right-[10%] w-[70%] h-[70%] rounded-full bg-gradient-to-br from-indigo-50 to-blue-100 blur-3xl opacity-60"></div>
            <div class="absolute -bottom-[20%] -left-[10%] w-[50%] h-[50%] rounded-full bg-gradient-to-tr from-gray-200 to-indigo-50 blur-3xl opacity-60"></div>
        </div>

        <div class="relative z-10 max-w-2xl mx-auto p-6 lg:p-8 text-center">

            <div class="flex justify-center mb-8">
                <div class="flex items-center gap-3">
                    <div class="bg-indigo-600 p-3 rounded-2xl shadow-lg shadow-indigo-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                        </svg>
                    </div>
                    <span class="text-5xl font-extrabold text-gray-900 tracking-tight">InovChat</span>
                </div>
            </div>

            <h1 class="text-3xl sm:text-5xl font-extrabold text-gray-900 mb-6 tracking-tight">
                Corporate communication <br class="hidden sm:block" />
                <span class="text-indigo-600">simplified.</span>
            </h1>
            <p class="text-gray-600 mb-10 max-w-lg mx-auto text-lg leading-relaxed">
                Connect with your team, manage project rooms, and share ideas in real-time in a secure and organized environment.
            </p>

            <div v-if="canLogin" class="flex justify-center">
                <Link
                    v-if="$page.props.auth.user"
                    :href="route('chat.index')"
                    class="inline-flex items-center px-8 py-3.5 bg-indigo-600 border border-transparent rounded-lg font-bold text-base text-white uppercase tracking-wider hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 transition ease-in-out duration-150 shadow-lg shadow-indigo-200/50 hover:shadow-xl hover:-translate-y-0.5"
                >
                    Go to Chat
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </Link>

                <template v-else>
                    <Link
                        :href="route('login')"
                        class="inline-flex items-center px-12 py-4 bg-gray-900 border border-transparent rounded-lg font-bold text-base text-white uppercase tracking-wider hover:bg-gray-800 focus:bg-gray-800 active:bg-gray-900 transition ease-in-out duration-150 shadow-lg hover:shadow-xl hover:-translate-y-0.5"
                    >
                        Log in
                    </Link>
                </template>
            </div>

        </div>
    </div>
</template>
