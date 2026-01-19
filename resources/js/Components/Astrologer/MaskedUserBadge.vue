<template>
  <div class="flex flex-col text-sm">
    <strong class="text-slate-900">{{ masked }}</strong>
    <span class="text-xs text-slate-500">{{ display }}</span>
  </div>
</template>

<script setup>
defineProps({
  masked: { type: String, required: true },
  display: { type: String, default: 'Member' },
});
</script>
