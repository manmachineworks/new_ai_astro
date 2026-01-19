<template>
  <div class="min-h-screen flex bg-slate-100">
    <aside class="w-64 bg-white border-r border-slate-200 shadow-sm">
      <div class="p-5">
        <h1 class="font-bold text-lg text-slate-900">Astrologer Panel</h1>
        <p class="text-xs text-slate-500 mt-1">Powered by Astro API</p>
      </div>
      <nav class="space-y-1 px-2 pb-6">
        <LayoutLink href="/astrologer/overview" label="Overview" />
        <LayoutLink href="/astrologer/services" label="Services & Presence" />
        <LayoutLink href="/astrologer/pricing" label="Pricing" />
        <LayoutLink href="/astrologer/schedule" label="Schedule" />
        <LayoutLink href="/astrologer/calls" label="Call Logs" />
        <LayoutLink href="/astrologer/chats" label="Chats" />
        <LayoutLink href="/astrologer/appointments" label="Appointments" />
        <LayoutLink href="/astrologer/earnings" label="Earnings" />
        <LayoutLink href="/astrologer/profile" label="Profile & Verification" />
      </nav>
    </aside>
    <main class="flex-1 p-6">
      <header class="mb-6 flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-semibold text-slate-900">{{ page.props.title || 'Dashboard' }}</h2>
          <p class="text-sm text-slate-600">{{ page.props.description }}</p>
        </div>
      </header>
      <slot />
    </main>
  </div>
</template>

<script setup>
import { usePage } from '@inertiajs/inertia-vue3';
import LayoutLink from '@/Components/Astrologer/LayoutLink.vue';

const page = usePage();
</script>
