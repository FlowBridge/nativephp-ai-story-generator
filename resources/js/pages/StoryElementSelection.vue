<template>
<MobileAppLayout>
  <div class="flex flex-col h-[calc(100vh-4rem)] p-4">
    <!-- Header - fixed height -->
    <div class="flex items-center justify-between h-12">
      <h1 class="text-2xl font-bold">Select ({{ selectedElements.length }}/3)</h1>
      <button
        @click="showModal = true"
        class="px-4 py-2 text-white transition-colors bg-blue-500 rounded-lg hover:bg-blue-600"
      >
        Add New
      </button>
    </div>

    <!-- Main grid area - dynamic height -->
    <div class="flex-1 mt-4 overflow-y-auto">
      <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
        <div
          v-for="element in elements"
          :key="element.id"
          @click="selectElement(element)"
          class="flex flex-col items-center p-2 transition-colors border rounded-lg shadow-sm cursor-pointer hover:bg-gray-50"
          :class="{
            'bg-blue-50 border-blue-200': selectedElements.includes(element.id),
            'opacity-50 cursor-not-allowed': selectedElements.length >= 3 && !selectedElements.includes(element.id)
          }"
        >
          <div class="flex items-center justify-center w-20 h-20 mb-1 overflow-hidden bg-gray-100 rounded-lg">
            <img v-if="element.image" :src="element.image" class="object-cover w-full h-full" :alt="element.name">
            <span v-else class="text-3xl">📖</span>
          </div>
          <span class="w-full text-sm font-medium text-center truncate">{{ element.name }}</span>
          <span v-if="element.type?.name" class="text-xs text-gray-500">{{ element.type.name }}</span>
        </div>
      </div>
    </div>

    <!-- Bottom panel - dynamic height -->
    <div class="mt-2">
      <!-- Selected Elements -->
      <div v-if="selectedElements.length > 0" class="mb-4">
        <div class="flex items-center justify-between mb-3">
          <h2 class="text-sm font-semibold text-gray-700">Selected Elements</h2>
          <span class="px-2 py-1 text-xs font-medium text-blue-600 rounded-full bg-blue-50">
            {{ selectedElements.length }}/3
          </span>
        </div>
        <div class="grid grid-cols-3 gap-2">
          <div
            v-for="id in selectedElements"
            :key="id"
            class="relative group"
          >
            <div class="flex flex-col items-center p-2 transition-all border border-blue-100 rounded-xl bg-blue-50">
              <div class="relative flex items-center justify-center w-12 h-12 mb-1 overflow-hidden bg-white rounded-lg shadow-sm">
                <img
                  v-if="elements.find(e => e.id === id)?.image"
                  :src="elements.find(e => e.id === id)?.image"
                  class="object-cover w-full h-full"
                  :alt="elements.find(e => e.id === id)?.name || ''"
                >
                <span v-else class="text-2xl">📖</span>
              </div>
              <span class="w-full text-xs font-medium text-center text-gray-700 truncate">
                {{ elements.find(e => e.id === id)?.name }}
              </span>
              <button
                @click="selectElement(elements.find(e => e.id === id))"
                class="absolute flex items-center justify-center w-5 h-5 text-white transition-colors bg-red-500 rounded-full opacity-0 -top-1 -right-1 group-hover:opacity-100 hover:bg-red-600"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>
          <!-- Empty slots -->
          <template v-for="i in (3 - selectedElements.length)" :key="'empty-'+i">
            <div class="flex items-center justify-center p-2 border-2 border-gray-200 border-dashed rounded-xl bg-gray-50">
              <span class="text-sm text-gray-400">Empty</span>
            </div>
          </template>
        </div>
      </div>

      <!-- Language Selection and Generate Button -->
      <div v-if="selectedElements.length >= 3" class="flex flex-col gap-3">
        <div class="p-3 bg-white rounded-lg shadow-sm">
          <div class="flex items-center gap-3">
            <!-- Language Selection -->
            <div class="flex-1">
              <label class="block mb-2 text-xs font-medium text-gray-500">Language</label>
              <div class="grid grid-cols-2 gap-2">
                <button
                  @click="selectedLanguage = 'en'"
                  class="flex items-center justify-center p-2 transition-all border rounded-lg"
                  :class="{
                    'bg-blue-50 border-blue-200 text-blue-700': selectedLanguage === 'en',
                    'border-gray-200 hover:bg-gray-50': selectedLanguage !== 'en'
                  }"
                >
                  <span class="mr-1 text-lg">🇬🇧</span>
                  <span class="text-sm font-medium">English</span>
                </button>
                <button
                  @click="selectedLanguage = 'nl'"
                  class="flex items-center justify-center p-2 transition-all border rounded-lg"
                  :class="{
                    'bg-blue-50 border-blue-200 text-blue-700': selectedLanguage === 'nl',
                    'border-gray-200 hover:bg-gray-50': selectedLanguage !== 'nl'
                  }"
                >
                  <span class="mr-1 text-lg">🇳🇱</span>
                  <span class="text-sm font-medium">Dutch</span>
                </button>
              </div>
            </div>

            <!-- Age Selection -->
            <div class="w-24">
              <label class="block mb-2 text-xs font-medium text-gray-500">Age</label>
              <div class="relative">
                <input
                  v-model="selectedAge"
                  type="number"
                  min="1"
                  max="12"
                  class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                <span class="absolute text-xs text-gray-400 -translate-y-1/2 right-2 top-1/2">yrs</span>
              </div>
            </div>
          </div>
        </div>

        <button
          @click="generateStory"
          :disabled="!selectedLanguage"
          class="w-full px-4 py-3 text-white transition-colors rounded-lg whitespace-nowrap"
          :class="{
            'bg-green-500 hover:bg-green-600': selectedLanguage,
            'bg-gray-300 cursor-not-allowed': !selectedLanguage
          }"
        >
          Generate Story
        </button>
      </div>
    </div>

    <!-- Story Result Modal -->
    <div v-if="storyResult || isGenerating" class="fixed inset-0 z-[60] flex items-center justify-center bg-black bg-opacity-90">
      <!-- Loading Screen -->
      <div v-if="isGenerating" class="flex flex-col items-center justify-center w-full h-full text-white">
        <div class="relative w-24 h-24 mb-8">
          <div class="absolute inset-0 border-4 border-blue-500 rounded-full border-t-transparent animate-spin"></div>
          <div class="absolute border-4 border-blue-300 rounded-full inset-2 border-t-transparent animate-spin-slow"></div>
          <div class="absolute border-4 border-blue-100 rounded-full inset-4 border-t-transparent animate-spin-slower"></div>
        </div>
        <h2 class="mb-4 text-2xl font-bold">Creating Your Story</h2>
        <div class="max-w-sm px-4 text-center text-gray-300">
          <p class="mb-2">Weaving together your selected elements into a magical tale...</p>
          <div class="flex items-center justify-center space-x-2 text-blue-400">
            <span class="animate-bounce delay-0">•</span>
            <span class="delay-100 animate-bounce">•</span>
            <span class="delay-200 animate-bounce">•</span>
          </div>
        </div>
      </div>

      <!-- Story Result -->
      <div v-else class="w-full max-w-2xl mx-4">
        <div
          class="transition-all duration-500 transform bg-white rounded-lg shadow-2xl"
          :class="{ 'translate-y-0 opacity-100': showStoryContent, 'translate-y-4 opacity-0': !showStoryContent }"
        >
          <div class="relative max-h-[85vh] overflow-hidden flex flex-col">
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500"></div>

            <!-- Fixed Header -->
            <div class="sticky top-0 z-10 flex items-center justify-between p-6 pb-4 bg-white">
              <h2 class="text-2xl font-bold text-transparent bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text">
                Your Magical Story
              </h2>
              <button @click="closeStory" class="p-1 text-gray-500 transition-colors hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <!-- Scrollable Content -->
            <div class="flex-1 px-6 pb-6 overflow-y-auto">
              <!-- Audio Player -->
              <div v-if="storyResult.audio_url" class="p-4 mb-6 rounded-lg bg-gray-50">
                <h3 class="mb-2 text-sm font-medium text-gray-700">Listen to Your Story</h3>
                <audio controls class="w-full">
                  <source :src="storyResult.audio_url" type="audio/mpeg">
                  Your browser does not support the audio element.
                </audio>
              </div>

              <div class="prose max-w-none">
                <p class="leading-relaxed text-gray-700 whitespace-pre-line">{{ storyResult.story }}</p>
              </div>

              <div class="pt-6 mt-6 border-t">
                <h3 class="mb-2 text-sm font-medium text-gray-700">Story Elements</h3>
                <div class="flex flex-wrap gap-2">
                  <div
                    v-for="id in selectedElements"
                    :key="id"
                    class="flex items-center p-2 rounded-lg bg-gray-50"
                  >
                    <div class="w-8 h-8 mr-2 overflow-hidden rounded">
                      <img
                        v-if="elements.find(e => e.id === id)?.image"
                        :src="elements.find(e => e.id === id)?.image"
                        class="object-cover w-full h-full"
                        :alt="elements.find(e => e.id === id)?.name || ''"
                      >
                      <span v-else class="text-xl">📖</span>
                    </div>
                    <span class="text-sm text-gray-600">{{ elements.find(e => e.id === id)?.name }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Element Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="w-full max-w-md p-6 mx-4 bg-white rounded-lg shadow-lg">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-xl font-semibold">Add New Story Element</h2>
          <button @click="showModal = false" class="p-1 text-gray-500 hover:text-gray-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <form @submit.prevent="addElement" class="space-y-3">
          <div>
            <label class="block mb-1 text-sm font-medium">Name</label>
            <input v-model="newElement.name" type="text" class="w-full p-2 text-sm border rounded" required>
          </div>
          <div>
            <label class="block mb-1 text-sm font-medium">Description</label>
            <textarea v-model="newElement.description" class="w-full p-2 text-sm border rounded" rows="3" required></textarea>
          </div>
          <div>
            <label class="block mb-1 text-sm font-medium">Image URL</label>
            <input
              type="url"
              v-model="newElement.image"
              @input="handleImageUrlInput"
              placeholder="Enter image URL (e.g., https://example.com/image.jpg)"
              class="w-full p-2 text-sm border rounded"
              required
            >
            <img v-if="imagePreview" :src="imagePreview" class="object-cover w-32 h-32 mt-2 rounded">
          </div>
          <div>
            <label class="block mb-1 text-sm font-medium">Type</label>
            <select v-model="newElement.type_id" class="w-full p-2 text-sm border rounded">
              <option value="">Select existing type</option>
              <option v-for="type in elementTypes" :key="type.id" :value="type.id">
                {{ type.name }} ({{ type.category }})
              </option>
            </select>
          </div>
          <div>
            <label class="block mb-1 text-sm font-medium">Or create new type</label>
            <div class="flex gap-2">
              <input v-model="newElement.new_type" type="text" placeholder="Type name" class="flex-1 p-2 text-sm border rounded">
              <input v-model="newElement.new_category" type="text" placeholder="Category" class="flex-1 p-2 text-sm border rounded">
            </div>
          </div>
          <div class="flex justify-end gap-2 pt-2">
            <button
              type="button"
              @click="showModal = false"
              class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200"
            >
              Cancel
            </button>
            <button type="submit" class="px-4 py-2 text-white bg-blue-500 rounded-lg hover:bg-blue-600">
              Add Story Element
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</MobileAppLayout>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue';
import axios from 'axios';
import MobileAppLayout from '@/layouts/MobileAppLayout.vue';
const elements = ref([]);
const selectedElements = ref([]);
const elementTypes = ref([]);
const showModal = ref(false);
const storyResult = ref(null);
const imagePreview = ref(null);
const newElement = ref({
  name: '',
  description: '',
  type_id: null,
  new_type: '',
  new_category: '',
  image: ''
});
const isGenerating = ref(false);
const showStoryContent = ref(false);
const selectedLanguage = ref(null);
const selectedAge = ref(5); // Default age set to 5

const handleImageUrlInput = () => {
  // Update preview when URL changes
  imagePreview.value = newElement.value.image;
};

onMounted(async () => {
  await fetchElementTypes();
  await fetchElements();
});

async function fetchElements() {
  const response = await axios.get('/api/story-elements');
  elements.value = response.data.map(el => ({
    ...el,
    type: elementTypes.value.find(t => t.id === el.type_id) || null
  }));
}

async function fetchElementTypes() {
  const response = await axios.get('/api/story-element-types');
  elementTypes.value = response.data;
}

const selectElement = (element) => {
  const index = selectedElements.value.indexOf(element.id);
  if (index === -1) {
    if (selectedElements.value.length >= 3) {
      alert('You can only select up to 3 elements');
      return;
    }
    selectedElements.value.push(element.id);
  } else {
    selectedElements.value.splice(index, 1);
  }
};

async function addElement() {
  try {
    if (!newElement.value.name || !newElement.value.description || !newElement.value.image) {
      alert('Name, Description, and Image URL are required');
      return;
    }

    // Validate URL
    try {
      new URL(newElement.value.image);
    } catch (e) {
      alert('Please enter a valid image URL');
      return;
    }

    const response = await axios.post('/api/story-elements', newElement.value);
    elements.value.push({
      ...response.data,
      type: elementTypes.value.find(t => t.id === response.data.type_id)
    });
    newElement.value = { name: '', description: '', type_id: null, new_type: '', new_category: '', image: '' };
    imagePreview.value = null;
    showModal.value = false;
    await fetchElementTypes();
  } catch (error) {
    console.error('Error adding element:', error);
    alert('Failed to add story element');
  }
}

async function generateStory() {
  try {
    if (!selectedLanguage.value) {
      alert('Please select a language first');
      return;
    }

    isGenerating.value = true;
    showStoryContent.value = false;
    storyResult.value = null;

    const response = await axios.post('/api/generate-story', {
      elements: selectedElements.value,
      language: selectedLanguage.value,
      age: selectedAge.value
    });

    storyResult.value = response.data;
    await nextTick();
    showStoryContent.value = true;
  } catch (error) {
    console.error('Error generating story:', error);
    alert('Failed to generate story');
  } finally {
    isGenerating.value = false;
  }
}

function closeStory() {
  showStoryContent.value = false;
  selectedLanguage.value = null;
  selectedAge.value = 5; // Reset age to default
  setTimeout(() => {
    storyResult.value = null;
  }, 500);
}
</script>

<style scoped>
/* Hide scrollbar but keep functionality */
.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

/* Animation delays for loading dots */
.delay-0 {
  animation-delay: 0ms;
}
.delay-100 {
  animation-delay: 100ms;
}
.delay-200 {
  animation-delay: 200ms;
}

/* Custom spin animations */
@keyframes spin-slow {
  to {
    transform: rotate(360deg);
  }
}
@keyframes spin-slower {
  to {
    transform: rotate(-360deg);
  }
}

.animate-spin-slow {
  animation: spin-slow 3s linear infinite;
}
.animate-spin-slower {
  animation: spin-slower 4s linear infinite;
}
</style>
