<template>
  <Layout>
    <div class="space-y-4">
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow">
        <h3 class="text-lg font-semibold text-slate-900">Chat Session</h3>
        <p class="text-sm text-slate-500">User: {{ session.user_public_id }}</p>
        <p class="text-sm text-slate-500">Status: {{ session.status }}</p>
        <p class="text-sm text-slate-500">Started at: {{ session.started_at }}</p>
      </div>
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow">
        <h4 class="text-sm uppercase tracking-wide text-slate-500">Conversation</h4>
        <p class="text-sm text-slate-400 mt-3">Realtime chat is powered by Firebase.</p>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import Layout from '@/Layouts/AstrologerLayout.vue';

defineProps({
  session: { type: Object, required: true },
});
</script>
