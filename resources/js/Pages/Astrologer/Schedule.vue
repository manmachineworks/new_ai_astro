<template>
  <Layout>
    <div class="grid md:grid-cols-2 gap-6">
      <section class="space-y-3">
        <h3 class="text-lg font-semibold text-slate-900">Weekly Schedule</h3>
        <div v-for="slot in schedules" :key="slot.id" class="rounded-2xl border border-slate-200 bg-white p-4">
          <p class="text-base font-semibold">{{ formatDay(slot.day_of_week) }}</p>
          <p class="text-sm text-slate-500">{{ slot.start_time }} - {{ slot.end_time }}</p>
        </div>
      </section>
      <section class="space-y-3">
        <h3 class="text-lg font-semibold text-slate-900">Add Time Off</h3>
        <form @submit.prevent="timeOffForm.post('/astrologer/time-off', { preserveScroll: true })" class="space-y-3 rounded-2xl border border-slate-200 bg-white p-4">
          <label class="block text-sm text-slate-600">Start</label>
          <input v-model="timeOffForm.start_datetime" type="datetime-local" class="w-full rounded-xl border border-slate-200 px-3 py-2" />
          <label class="block text-sm text-slate-600">End</label>
          <input v-model="timeOffForm.end_datetime" type="datetime-local" class="w-full rounded-xl border border-slate-200 px-3 py-2" />
          <label class="block text-sm text-slate-600">Reason</label>
          <input v-model="timeOffForm.reason" class="w-full rounded-xl border border-slate-200 px-3 py-2" />
          <button type="submit" class="w-full rounded-full bg-indigo-600 py-2 text-white">Add</button>
        </form>
      </section>
    </div>
  </Layout>
</template>

<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import Layout from '@/Layouts/AstrologerLayout.vue';

defineProps({
  schedules: { type: Array, default: () => [] },
  timeOff: { type: Array, default: () => [] },
});

const timeOffForm = useForm({
  start_datetime: '',
  end_datetime: '',
  reason: '',
});

const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

const formatDay = day => days[day] ?? 'Unknown';
</script>
