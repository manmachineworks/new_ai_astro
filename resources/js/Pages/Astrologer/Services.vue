<template>
  <Layout>
    <form @submit.prevent="form.put('/astrologer/services', { preserveScroll: true })" class="space-y-4">
      <label class="flex items-center gap-3">
        <input type="checkbox" v-model="form.call_enabled" class="form-checkbox" />
        <span>Call enabled</span>
      </label>
      <label class="flex items-center gap-3">
        <input type="checkbox" v-model="form.chat_enabled" class="form-checkbox" />
        <span>Chat enabled</span>
      </label>
      <label class="flex items-center gap-3">
        <input type="checkbox" v-model="form.sms_enabled" class="form-checkbox" />
        <span>SMS enabled</span>
      </label>
      <label class="block">
        <span class="text-sm font-semibold text-slate-600 mb-1">Presence</span>
        <select v-model="form.online_status" class="form-select w-full rounded-xl border border-slate-200 px-3 py-2">
          <option value="online">Online</option>
          <option value="offline">Offline</option>
        </select>
      </label>
      <button type="submit" class="px-4 py-2 rounded-full bg-indigo-600 text-white">Save</button>
    </form>
  </Layout>
</template>

<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import Layout from '@/Layouts/AstrologerLayout.vue';

defineProps({
  service: { type: Object, default: () => ({}) },
});

const form = useForm({
  call_enabled: service.call_enabled ?? false,
  chat_enabled: service.chat_enabled ?? false,
  sms_enabled: service.sms_enabled ?? false,
  online_status: service.online_status ?? 'offline',
});
</script>
