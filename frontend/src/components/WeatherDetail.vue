<template>
  <div class="flex flex-col">
    <span class="text-sm font-medium text-gray-500">{{ label }}</span>
    <span class="text-lg font-semibold text-gray-900">{{ value }}</span>
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{
  label: string;
  value: string;
}>();
</script>
