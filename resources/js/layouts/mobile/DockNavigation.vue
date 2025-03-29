<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

const navigation = [
    {
        name: 'Generate Story',
        href: route('story-elements'),
        icon: `<svg class="size-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g fill="currentColor" stroke-linejoin="miter" stroke-linecap="butt"><polyline points="3 14 9 14 9 17 15 17 15 14 21 14" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="2"></polyline><rect x="3" y="3" width="18" height="18" rx="2" ry="2" fill="none" stroke="currentColor" stroke-linecap="square" stroke-miterlimit="10" stroke-width="2"></rect></g></svg>`
    },
    {
        name: 'My Stories',
        href: route('story-elements.stories'),
        icon: `<svg class="size-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g fill="currentColor" stroke-linejoin="miter" stroke-linecap="butt"><circle cx="12" cy="12" r="3" fill="none" stroke="currentColor" stroke-linecap="square" stroke-miterlimit="10" stroke-width="2"></circle><path d="m22,13.25v-2.5l-2.318-.966c-.167-.581-.395-1.135-.682-1.654l.954-2.318-1.768-1.768-2.318.954c-.518-.287-1.073-.515-1.654-.682l-.966-2.318h-2.5l-.966,2.318c-.581.167-1.135.395-1.654.682l-2.318-.954-1.768,1.768.954,2.318c-.287.518-.515,1.073-.682,1.654l-2.318.966v2.5l2.318.966c.167.581.395,1.135.682,1.654l-.954,2.318,1.768,1.768,2.318-.954c.518.287,1.073.515,1.654.682l.966,2.318h2.5l.966-2.318c.581-.167,1.135-.395,1.654-.682l2.318.954,1.768-1.768-.954-2.318c.287-.518.515-1.073.682-1.654l2.318-.966Z" fill="none" stroke="currentColor" stroke-linecap="square" stroke-miterlimit="10" stroke-width="2"></path></g></svg>`
    }
];
</script>

<template>
    <nav class="border-t bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
        <div class="flex items-center justify-around h-16 px-4">
            <Link
                v-for="item in navigation"
                :key="item.name"
                :href="item.href"
                :class="[
                    route().current(item.href)
                        ? 'text-primary'
                        : 'text-muted-foreground hover:text-foreground',
                    'flex flex-col items-center gap-1'
                ]"
            >
                <div v-html="item.icon" class="flex items-center justify-center"></div>
                <span class="text-xs font-medium">{{ item.name }}</span>
            </Link>
        </div>
    </nav>
</template>
