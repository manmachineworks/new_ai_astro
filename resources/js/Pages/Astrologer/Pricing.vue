<template>
  <Layout>
    <form @submit.prevent="form.put('/astrologer/pricing', { preserveScroll: true })" class="space-y-4 max-w-xl">
      <div>
        <label class="block text-sm font-semibold text-slate-600">Call Rate (per minute)</label>
        <input v-model="form.call_per_minute" type="number" step="0.5" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" />
      </div>
      <div>
        <label class="block text-sm font-semibold text-slate-600">Chat Price (per session)</label>
        <input v-model="form.chat_price" type="number" step="1" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" />
      </div>
      <div>
        <label class="block text-sm font-semibold text-slate-600">AI Chat Price (per response)</label>
        <input v-model="form.ai_chat_price" type="number" step="0.5" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" />
      </div>
      <button type="submit" class="px-4 py-2 rounded-full bg-indigo-600 text-white">Update Pricing</button>
    </form>
  </Layout>
</template>

<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import Layout from '@/Layouts/AstrologerLayout.vue';

defineProps({
  pricing: { type: Object, default: () => ({}) },
});

const form = useForm({
  call_per_minute: pricing.call_per_minute ?? 0,
  chat_price: pricing.chat_price ?? 0,
  ai_chat_price: pricing.ai_chat_price ?? 0,
});
</script>
