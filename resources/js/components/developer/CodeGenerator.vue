<template>
  <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6 mt-6">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">AI Code Generator</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label for="crud-model" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Model Name</label>
        <input v-model="crudModel" type="text" id="crud-model" class="mt-1 block w-full p-2 border rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
      </div>
      <div class="flex items-end">
        <button @click="generateCRUD" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-md">Generate CRUD</button>
      </div>
    </div>
    <div v-if="crudOutput" class="mt-4 p-4 bg-gray-100 dark:bg-gray-900 rounded-md text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">
      {{ crudOutput }}
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      crudModel: '',
      crudOutput: '',
    };
  },
  methods: {
    async generateCRUD() {
      try {
        const response = await axios.post('/developer/code-generator/crud', { model: this.crudModel });
        this.crudOutput = response.data.message;
      } catch (error) {
        this.crudOutput = error.response.data.message || 'An error occurred.';
      }
    },
  },
};
</script>
