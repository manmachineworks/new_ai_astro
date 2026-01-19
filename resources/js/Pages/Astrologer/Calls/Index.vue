<template>
  <Layout>
    <div class="space-y-4">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-slate-900">Call History</h3>
        <select v-model="status" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
          <option value="">All Status</option>
          <option value="initiated">Initiated</option>
          <option value="connected">Connected</option>
          <option value="ended">Ended</option>
          <option value="missed">Missed</option>
        </select>
      </div>
      <CallLogTable :calls="calls.data ?? []" />
      <div class="flex justify-end">
        <button disabled class="px-4 py-2 text-xs uppercase text-slate-500">Pagination Placeholder</button>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import Layout from '@/Layouts/AstrologerLayout.vue';
import CallLogTable from '@/Components/Astrologer/CallLogTable.vue';
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/inertia-vue3';

defineProps({
  calls: { type: Object, default: () => ({}) },
});

const status = ref('');
watch(status, value => {
  // TODO: Should trigger Inertia visit. Placeholder for future.
});
</script>
