<template>
  <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <table class="min-w-full text-left text-sm text-slate-600">
      <thead class="bg-slate-50 text-xs uppercase text-slate-500">
        <tr>
          <th class="px-4 py-3">Call</th>
          <th class="px-4 py-3">Status</th>
          <th class="px-4 py-3">Duration</th>
          <th class="px-4 py-3">Amount</th>
          <th class="px-4 py-3">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="call in calls" :key="call.id" class="border-t border-slate-100">
          <td class="px-4 py-3">
            <MaskedUserBadge :masked="call.user_public_id" :display="call.meta?.user_display ?? 'Client'" />
          </td>
          <td class="px-4 py-3">{{ call.status }}</td>
          <td class="px-4 py-3">{{ duration(call.duration_seconds) }}</td>
          <td class="px-4 py-3">₹{{ call.amount_charged.toFixed(2) }}</td>
          <td class="px-4 py-3">
            <Link :href="`/astrologer/calls/${call.id}`" class="text-indigo-600">View</Link>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/inertia-vue3';
import MaskedUserBadge from './MaskedUserBadge.vue';

defineProps({
  calls: { type: Array, required: true },
});

const duration = seconds => {
  if (!seconds) return '0m';
  const mins = Math.ceil(seconds / 60);
  return `${mins}m`;
};
</script>
