<template>
  <Layout>
    <div class="grid gap-6 md:grid-cols-3">
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow">
        <h3 class="font-semibold text-slate-800">Call Details</h3>
        <p class="text-sm text-slate-500 mt-2">User ID: {{ call.user_public_id }}</p>
        <p class="text-sm text-slate-500">Status: {{ call.status }}</p>
        <p class="text-sm text-slate-500">Duration: {{ duration }}</p>
        <p class="text-sm text-slate-500">Charged: ₹{{ call.amount_charged }}</p>
      </div>
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow">
        <h3 class="font-semibold text-slate-800">Metadata</h3>
        <pre class="mt-2 text-xs text-slate-500">{{ call.meta }}</pre>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { computed } from 'vue';
import Layout from '@/Layouts/AstrologerLayout.vue';

defineProps({
  call: { type: Object, required: true },
});

const duration = computed(() => {
  if (!call.duration_seconds) return '0m';
  const minutes = Math.ceil(call.duration_seconds / 60);
  return `${minutes}m`;
});
</script>
