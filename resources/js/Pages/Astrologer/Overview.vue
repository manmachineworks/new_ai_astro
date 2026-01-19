<template>
  <Layout>
    <div class="grid gap-4 md:grid-cols-3 mb-6">
      <StatCard title="Calls" :value="stats.calls" description="Total call logs" />
      <StatCard title="Chats" :value="stats.chats" description="Firebase chat sessions" />
      <StatCard title="Earnings" :value="`₹${stats.earnings}`" description="Net earnings captured" />
    </div>
    <div class="grid gap-4 md:grid-cols-2">
      <div>
        <h3 class="text-lg font-semibold text-slate-900 mb-3">Recent Calls</h3>
        <CallLogTable :calls="recentCalls" />
      </div>
      <div>
        <h3 class="text-lg font-semibold text-slate-900 mb-3">Recent Chats</h3>
        <ChatList :sessions="recentChats" />
      </div>
    </div>
  </Layout>
</template>

<script setup>
import Layout from '@/Layouts/AstrologerLayout.vue';
import StatCard from '@/Components/Astrologer/StatCard.vue';
import CallLogTable from '@/Components/Astrologer/CallLogTable.vue';
import ChatList from '@/Components/Astrologer/ChatList.vue';

defineProps({
  stats: { type: Object, required: true },
  recentCalls: { type: Array, default: () => [] },
  recentChats: { type: Array, default: () => [] },
});
</script>
