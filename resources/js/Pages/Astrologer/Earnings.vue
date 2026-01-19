<template>
  <Layout>
    <div class="space-y-3">
      <div v-for="earning in earnings.data ?? []" :key="earning.id" class="rounded-2xl border border-slate-200 bg-white p-4 shadow">
        <div class="flex items-center justify-between">
          <p class="font-semibold text-slate-900 capitalize">{{ earning.source }}</p>
          <p class="text-sm text-slate-500">{{ earning.status }}</p>
        </div>
        <p class="text-2xl font-semibold text-slate-800">₹{{ earning.net_amount }}</p>
        <p class="text-xs text-slate-500">Recorded on {{ earning.created_at }}</p>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import Layout from '@/Layouts/AstrologerLayout.vue';

defineProps({
  earnings: { type: Object, default: () => ({}) },
});
</script>
