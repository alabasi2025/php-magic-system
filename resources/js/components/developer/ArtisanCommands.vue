<template>
  <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6 mt-6">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Artisan Commands</h3>
    <div class="flex space-x-2">
      <input v-model="command" type="text" placeholder="Enter command" class="flex-grow p-2 border rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
      <button @click="executeCommand" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md">Execute</button>
    </div>
    <div v-if="output" class="mt-4 p-4 bg-gray-100 dark:bg-gray-900 rounded-md text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">
      {{ output }}
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      command: '',
      output: '',
    };
  },
  methods: {
    async executeCommand() {
      try {
        const response = await axios.post('/developer/artisan', { command: this.command });
        this.output = response.data.output;
      } catch (error) {
        this.output = error.response.data.message || 'An error occurred.';
      }
    },
  },
};
</script>
