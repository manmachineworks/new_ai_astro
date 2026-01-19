<template>
  <div class="space-y-3">
    <div v-for="session in sessions" :key="session.id" class="rounded-2xl border border-slate-200 bg-white p-4 shadow">
      <div class="flex items-center justify-between">
        <div>
          <p class="font-semibold text-slate-900">{{ session.user_public_id }}</p>
          <p class="text-xs text-slate-500">Status: {{ session.status }}</p>
        </div>
        <Link :href="`/astrologer/chats/${session.id}`" class="text-indigo-600 text-sm">Open</Link>
      </div>
      <div class="text-xs text-slate-500 mt-2">
        Started {{ formatDate(session.started_at) }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/inertia-vue3';

defineProps({
  sessions: { type: Array, required: true },
});

const formatDate = raw => raw ? new Date(raw).toLocaleString() : '-';
</script>
