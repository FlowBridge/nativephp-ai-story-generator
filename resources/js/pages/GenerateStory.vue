<template>
        <div class="p-4">
            <div class="flex items-center justify-between mb-6">
              <h1 class="text-2xl font-bold">Select Story Elements</h1>
              <button
                @click="showAddForm = !showAddForm"
                class="px-4 py-2 text-white bg-green-500 rounded-lg"
              >
                {{ showAddForm ? 'Cancel' : 'Add New' }}
              </button>
            </div>

            <div v-if="showAddForm" class="p-4 mb-6 border rounded-lg">
              <h2 class="mb-4 text-xl font-semibold">Add New Story Element</h2>
              <div class="space-y-4">
                <div>
                  <label class="block mb-1">Name</label>
                  <input v-model="newAnimal.name" class="w-full p-2 border rounded">
                </div>
                <div>
                  <label class="block mb-1">Description</label>
                  <textarea v-model="newAnimal.description" class="w-full p-2 border rounded"></textarea>
                </div>
                <div>
                  <label class="block mb-1">Type</label>
                  <select v-model="newAnimal.type_id" class="w-full p-2 border rounded">
                    <option value="">Select Type</option>
                    <option v-for="type in types" :key="type.id" :value="type.id">
                      {{ type.name }} ({{ type.category }})
                    </option>
                  </select>
                </div>
                <div v-if="!newAnimal.type_id">
                  <label class="block mb-1">New Type</label>
                  <input v-model="newAnimal.new_type" class="w-full p-2 border rounded" placeholder="Type name">
                  <input v-model="newAnimal.new_category" class="w-full p-2 mt-2 border rounded" placeholder="Category">
                </div>
                <button
                  @click="addAnimal"
                  class="w-full py-2 text-white bg-blue-500 rounded-lg"
                >
                  Save Animal
                </button>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div
                v-for="animal in animals"
                :key="animal.id"
                @click="selectAnimal(animal)"
                class="flex flex-col items-center p-4 border rounded-lg"
                :class="{ 'bg-gray-100': selectedAnimals.includes(animal.id) }"
              >
                <img :src="animal.image" class="object-cover w-24 h-24 mb-2">
                <span>{{ animal.name }}</span>
              </div>
            </div>

            <div v-if="selectedAnimals.length > 0" class="mt-8">
              <h2 class="mb-4 text-xl font-semibold">Selected Animals</h2>
              <div class="flex flex-wrap gap-2">
                <div
                  v-for="id in selectedAnimals"
                  :key="id"
                  class="flex items-center p-2 border rounded-lg"
                >
                  <img
                    :src="animals.find(a => a.id === id).image"
                    class="object-cover w-12 h-12 mr-2"
                  >
                  <span>{{ animals.find(a => a.id === id).name }}</span>
                </div>
              </div>
            </div>

            <button
              v-if="selectedAnimals.length >= 3"
              class="w-full py-3 mt-6 text-white bg-blue-500 rounded-lg"
            >
              Generate
            </button>
        </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import MobileAppLayout from '@/layouts/MobileAppLayout.vue';

const animals = ref([]);
const selectedAnimals = ref([]);
const showAddForm = ref(false);
const newAnimal = ref({
  name: '',
  description: '',
  type_id: null,
  new_type: '',
  new_category: ''
});
const types = ref([]);

onMounted(async () => {
  await fetchAnimals();
  await fetchTypes();
});

const fetchAnimals = async () => {
  const response = await axios.get('/api/story-elements');
  animals.value = response.data;
};

const fetchTypes = async () => {
  const response = await axios.get('/api/story-element-types');
  types.value = response.data;
};

const selectAnimal = (animal) => {
  const index = selectedAnimals.value.indexOf(animal.id);
  if (index === -1) {
    selectedAnimals.value.push(animal.id);
  } else {
    selectedAnimals.value.splice(index, 1);
  }
};

const addAnimal = async () => {
  try {
    const response = await axios.post('/api/story-elements', newAnimal.value);
    animals.value.push(response.data);
    newAnimal.value = {
      name: '',
      description: '',
      type_id: null,
      new_type: '',
      new_category: ''
    };
    showAddForm.value = false;
    await fetchTypes(); // Refresh types in case a new one was added
  } catch (error) {
    console.error('Error adding animal:', error);
  }
};
</script>
