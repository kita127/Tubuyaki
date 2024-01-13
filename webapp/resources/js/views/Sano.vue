<template>
    <router-link to="/color/sena">遷移</router-link>
    {{ message }}
</template>
<script lang="ts">
import { defineComponent, ref } from "vue";

export default defineComponent({
    setup() {
        const message = ref<string>("Yellow");
        return {
            message
        };
    }
});
</script>
  