<template>
    {{ message }}
</template>
<script lang="ts">
import { defineComponent, ref } from "vue";

export default defineComponent({
    setup() {
        const message = ref<string>("Purple");
        return {
            message
        };
    }
});
</script>
