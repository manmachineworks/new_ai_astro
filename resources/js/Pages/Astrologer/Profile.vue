<template>
  <Layout>
    <form @submit.prevent="form.post('/astrologer/profile', { preserveState: true })" class="space-y-4 max-w-xl">
      <div>
        <label class="text-sm font-semibold text-slate-600">Display Name</label>
        <input v-model="form.display_name" class="w-full rounded-xl border border-slate-200 px-3 py-2" />
      </div>
      <div>
        <label class="text-sm font-semibold text-slate-600">Bio</label>
        <textarea v-model="form.bio" rows="3" class="w-full rounded-xl border border-slate-200 px-3 py-2"></textarea>
      </div>
      <div>
        <label class="text-sm font-semibold text-slate-600">Languages (comma separated)</label>
        <input v-model="form.languages" class="w-full rounded-xl border border-slate-200 px-3 py-2" />
      </div>
      <div>
        <label class="text-sm font-semibold text-slate-600">Specializations</label>
        <input v-model="form.specializations" class="w-full rounded-xl border border-slate-200 px-3 py-2" />
      </div>
      <button type="submit" class="rounded-full bg-indigo-600 px-4 py-2 text-white">Save Profile</button>
    </form>
  </Layout>
</template>

<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import Layout from '@/Layouts/AstrologerLayout.vue';

defineProps({
  astrologer: { type: Object, required: true },
});

const form = useForm({
  display_name: astrologer.display_name ?? '',
  bio: astrologer.bio ?? '',
  languages: (astrologer.languages ?? []).join(', '),
  specializations: (astrologer.specializations ?? []).join(', '),
  profile_photo_url: astrologer.profile_photo_url ?? '',
});
</script>
