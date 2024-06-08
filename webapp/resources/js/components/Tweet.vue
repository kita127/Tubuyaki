<template>
    <div class="tweet">{{ text }}</div>
</template>

<script setup lang="ts">

defineProps<{
    text: string
}>();

</script>

<style scoped>
.tweet {
    border: 1px solid black;
}
</style>
