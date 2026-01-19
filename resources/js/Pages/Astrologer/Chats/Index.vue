<template>
  <Layout>
    <div class="space-y-4">
      <ChatList :sessions="sessions.data ?? []" />
    </div>
  </Layout>
</template>

<script setup>
import Layout from '@/Layouts/AstrologerLayout.vue';
import ChatList from '@/Components/Astrologer/ChatList.vue';

defineProps({
  sessions: { type: Object, default: () => ({}) },
});
</script>
