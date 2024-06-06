<template>
    <div>Login</div>
    <div>
        <input type="email" name="email" v-model="email" required autofocus>
    </div>
    <div>
        <p>password</p>
        <input type="text" name="password" v-model="password">
    </div>
    <button v-on:click="login">login</button>
</template>

<script lang="ts" setup>
import axios, { AxiosInstance } from "axios";
import { ref } from "vue";
import { useRouter } from "vue-router";
import { onBeforeMount } from 'vue'

let http: AxiosInstance;
const router = useRouter();

const email = ref<string>('');
const password = ref<string>('');

const login = async () => {
    await http.post('/api/login', { email: email.value, password: password.value });
};

onBeforeMount(async () => {
    http = axios.create({
        baseURL: 'http://localhost',
        withCredentials: true,
    });
    await http.get('/sanctum/csrf-cookie');
})


</script>