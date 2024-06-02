<template>
    <div>
        <button type="button" v-on:click="logout"> ログアウト </button>
    </div>
    <Main></Main>
</template>

<script lang="ts" setup>
import axios from "axios";
import Main from "../components/Main.vue";

const logout = (): void => {
    axios.post("logout");
    window.location.href = "/login";
};
</script>