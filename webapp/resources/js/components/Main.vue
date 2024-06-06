<template>
    <div>Main Menu</div>
    <div>
        <button type="button" v-on:click="logout">logout</button>
    </div>

    <button v-on:click="getUsers">users</button>
    <div v-for="user in users">{{ user }}</div>
</template>

<script lang="ts" setup>
import axios, { AxiosInstance } from "axios";
import { ref } from "vue";
import { onBeforeMount } from 'vue'
import { useRouter } from 'vue-router'

let http: AxiosInstance;
const router = useRouter();

const logout = (): void => {
    http.post("/api/logout");
};

const users = ref<string[]>([]);
const getUsers = async () => {
    users.value = [];
    const { data } = await http.get("http://localhost/api/users");
    users.value.push(data[1]);
    users.value.push(data[2]);
    users.value.push(data[3]);
};

onBeforeMount(async () => {
    http = axios.create({
        baseURL: 'http://localhost',
        withCredentials: true,
    });
    await http.get('/sanctum/csrf-cookie');
    try {
        await http.get('/api/users/me');
    } catch (err) {
        router.push({ name: 'Login' });
    }
});

</script>

<script setup>
</script>
