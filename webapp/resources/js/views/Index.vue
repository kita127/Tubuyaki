<template>
    <div>
        <p>Vueによるメインページ</p>
    </div>
    <div>
        <div>
            <RouterLink v-bind:to="{ name: 'Sano' }">
                Sano
            </RouterLink>
        </div>
        <div>
            <RouterLink v-bind:to="{ name: 'Sana' }">
                Sana
            </RouterLink>
        </div>
    </div>
    <div>
        <button type="button" v-on:click="logout"> ログアウト </button>
    </div>
</template>

<script lang="ts" setup>
import axios from "axios";

const logout = (): void => {
    axios.post("logout");
    window.location.href = "/login";
};
</script>