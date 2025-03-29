<script setup lang="ts">
import DockNavigation from '@/layouts/mobile/DockNavigation.vue';
</script>

<template>
    <div class="flex flex-col min-h-screen bg-background">
        <main class="flex-1 overflow-y-auto">
            <slot></slot>
        </main>

        <DockNavigation class="fixed bottom-0 left-0 right-0 z-50" />
    </div>
</template>
