<template>
    <div>Main Menu</div>
</template>

<script lang="ts" setup>
import axios, { AxiosInstance } from "axios";
import { ref } from "vue";
import { onMounted } from 'vue'

const me = async (http: AxiosInstance): Promise<void> => {
    const a = await http.get("http://localhost/api/users/me");
    console.log(a);
};

const timeline = () => {
    //    axios.get();
};

onMounted(() => {
    const http = axios.create({
        baseURL: 'http://localhost',
        withCredentials: true,
    });

    http.get('/api/users/me').then((res) => {
        console.log(res);
    });

    console.log('ページが読み込まれました！')

})

</script>

<script setup>
</script>
