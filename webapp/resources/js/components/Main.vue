<template>
    <div>Main Menu</div>
    <div>
        <button type="button" v-on:click="logout">logout</button>
    </div>
    <div>
        <input type="email" name="email" v-model="email" required autofocus>
    </div>
    <div>
        <p>password</p>
        <input type="text" name="password" v-model="password">
    </div>
    <button v-on:click="login">login</button>

    <button v-on:click="getUsers">users</button>
    <div v-for="user in users">{{ user }}</div>
</template>

<script lang="ts" setup>
import axios, { AxiosInstance } from "axios";
import { ref } from "vue";
import { onMounted } from 'vue'

let http: AxiosInstance;

const email = ref<string>('');
const password = ref<string>('');
const login = async () => {
    http.get('/sanctum/csrf-cookie').then((res) => {
        // ログイン処理
        http.post('/api/login', { email: email.value, password: password.value }).then((res) => {
            console.log(res);
        });
    })
};

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

onMounted(() => {
    http = axios.create({
        baseURL: 'http://localhost',
        withCredentials: true,
    });
    console.log('ページが読み込まれました！')
})

</script>

<script setup>
</script>
