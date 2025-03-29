<template>
    <MobileAppLayout title="My Stories">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                My Stories
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <!-- Stories Grid -->
                        <div v-if="stories.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div v-for="story in stories" :key="story.id"
                                class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                                <div class="p-6">
                                    <!-- Story Content Preview -->
                                    <div class="mb-4">
                                        <p class="text-gray-600 line-clamp-3">{{ story.content }}</p>
                                    </div>

                                    <!-- Audio Player -->
                                    <div class="mt-4">
                                        <audio
                                            :src="'/storage/' + story.audio_url"
                                            controls
                                            class="w-full"
                                        >
                                            Your browser does not support the audio element.
                                        </audio>
                                    </div>

                                    <!-- Story Elements Tags -->
                                    <div class="mt-4 flex flex-wrap gap-2">
                                        <span v-for="elementId in story.story_elements" :key="elementId"
                                            class="px-2 py-1 bg-blue-100 text-blue-800 text-sm rounded-full">
                                            {{ getElementName(elementId) }}
                                        </span>
                                    </div>

                                    <!-- Creation Date -->
                                    <div class="mt-4 text-sm text-gray-500">
                                        Created {{ formatDate(story.created_at) }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div v-else class="text-center py-12">
                            <div class="text-gray-400 mb-4">
                                <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No stories yet</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating your first story!</p>
                            <div class="mt-6">
                                <Link :href="route('story-elements')"
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Create a Story
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MobileAppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import axios from 'axios'
import { format } from 'date-fns'
import MobileAppLayout from '@/layouts/MobileAppLayout.vue';

const stories = ref([])
const elements = ref({}) // Cache for story elements

onMounted(async () => {
    try {
        const [storiesResponse, elementsResponse] = await Promise.all([
            axios.get('/api/stories'),
            axios.get('/api/story-elements')
        ])
        stories.value = storiesResponse.data
        elements.value = elementsResponse.data.reduce((acc, element) => {
            acc[element.id] = element.name
            return acc
        }, {})
    } catch (error) {
        console.error('Error fetching stories:', error)
    }
})

const getElementName = (elementId) => {
    return elements.value[elementId] || `Element ${elementId}`
}

const formatDate = (date) => {
    return format(new Date(date), 'MMM d, yyyy')
}
</script>
