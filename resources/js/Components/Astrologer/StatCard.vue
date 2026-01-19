<template>
  <div class="p-4 bg-white rounded-2xl shadow-sm border border-slate-200">
    <p class="text-xs uppercase text-slate-500">{{ title }}</p>
    <p class="text-2xl font-semibold text-slate-900">{{ value }}</p>
    <p v-if="description" class="text-sm text-slate-500 mt-1">{{ description }}</p>
  </div>
</template>

<script setup>
defineProps({
  title: { type: String, required: true },
  value: { type: [String, Number], required: true },
  description: { type: String, default: '' },
});
</script>
