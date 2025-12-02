<template>
  <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">System Overview</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-blue-100 dark:bg-blue-900 p-4 rounded-lg text-center">
        <p class="text-sm font-medium text-blue-800 dark:text-blue-200">PHP Version</p>
        <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ phpVersion }}</p>
      </div>
      <div class="bg-green-100 dark:bg-green-900 p-4 rounded-lg text-center">
        <p class="text-sm font-medium text-green-800 dark:text-green-200">Laravel Version</p>
        <p class="text-2xl font-bold text-green-900 dark:text-green-100">{{ laravelVersion }}</p>
      </div>
      <div class="bg-yellow-100 dark:bg-yellow-900 p-4 rounded-lg text-center">
        <p class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Database</p>
        <p class="text-2xl font-bold text-yellow-900 dark:text-yellow-100">{{ database }}</p>
      </div>
      <div class="bg-red-100 dark:bg-red-900 p-4 rounded-lg text-center">
        <p class="text-sm font-medium text-red-800 dark:text-red-200">Environment</p>
        <p class="text-2xl font-bold text-red-900 dark:text-red-100">{{ environment }}</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    phpVersion: String,
    laravelVersion: String,
    database: String,
    environment: String,
  },
};
</script>
