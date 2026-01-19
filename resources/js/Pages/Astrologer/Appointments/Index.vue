<template>
  <Layout>
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow">
      <h3 class="text-lg font-semibold text-slate-900 mb-4">Your Appointments</h3>
      <table class="w-full text-left text-sm text-slate-600">
        <thead class="text-xs uppercase text-slate-500">
          <tr>
            <th class="px-3 py-2">User</th>
            <th class="px-3 py-2">Status</th>
            <th class="px-3 py-2 text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="appointment in appointments.data ?? []" :key="appointment.id" class="border-t border-slate-100">
            <td class="px-3 py-2">{{ appointment.user.name }}</td>
            <td class="px-3 py-2 capitalize">{{ appointment.status }}</td>
            <td class="px-3 py-2 text-right">
              <inertia-link v-if="appointment.status === 'pending'" href="#" class="text-indigo-600 text-xs">Manage</inertia-link>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </Layout>
</template>

<script setup>
import Layout from '@/Layouts/AstrologerLayout.vue';

defineProps({
  appointments: { type: Object, default: () => ({}) },
});
</script>
