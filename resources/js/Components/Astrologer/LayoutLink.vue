<template>
  <Link :href="href" class="flex items-center px-3 py-2 rounded-md text-sm font-medium text-slate-700 hover:bg-slate-50">
    <span>{{ label }}</span>
  </Link>
</template>

<script setup>
import { Link } from '@inertiajs/inertia-vue3';

defineProps({
  href: { type: String, required: true },
  label: { type: String, required: true },
});
</script>
