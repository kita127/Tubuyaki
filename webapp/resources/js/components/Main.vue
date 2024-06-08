<template>
    <section>
        <h1>Main Menu</h1>
    </section>
    <section>
        <div>
            <span>ユーザー : </span>
            <span>{{ me?.name }}</span>
        </div>
    </section>
    <section>
        <div v-for="tweet in tweets" v-bind:key="'tweet' + tweet.id" class="tweet">
            {{ tweet.text }}
        </div>
    </section>

    <div>
        <button type="button" v-on:click="logout">logout</button>
    </div>

    <button v-on:click="getUsers">users</button>
    <div v-for="user in users">{{ user }}</div>
</template>

<script lang="ts" setup>
import axios, { AxiosInstance } from "axios";
import { ref } from "vue";
import { onBeforeMount, onMounted, onBeforeUnmount } from 'vue'
import { useRouter } from 'vue-router'

let http: AxiosInstance;
const router = useRouter();

const logout = (): void => {
    http.post("/api/logout");
};

type User = {
    id: number;
    account_name: string;
    name: string;
    email: string;
};
const me = ref<User>();
const getMe = async () => {
    try {
        const { data } = await http.get<User>('/api/users/me');
        me.value = data;
    } catch (err) {
        router.push({ name: 'Login' });
    }
};

type Tweet = {
    id: number;
    text: string;
    tweet_type: string;
    user: {
        id: number;
        account_name: string;
        name: string;
    };
    target_id: number | null;
    created_at: string;
    updated_at: string;
};

type TimelineResponse = {
    next: number | null;
    tweets: Tweet[];
};
const tweets = ref<Tweet[]>([]);
const next = ref<number | null>(0);
const getTimeline = async (userId: number | undefined) => {
    if (!userId) {
        router.push({ name: 'Login' });
    }
    if (next.value !== null) {
        const { data } = await http.get<{ contents: TimelineResponse }>(`/api/users/${userId}/timeline?index=${next.value}&count=20`);
        tweets.value = data.contents.tweets;
        next.value = data.contents.next;
    }
};

const users = ref<string[]>([]);
const getUsers = async () => {
    users.value = [];
    const { data } = await http.get("http://localhost/api/users");
    users.value.push(data[1]);
    users.value.push(data[2]);
    users.value.push(data[3]);
};

const loadMore = async () => {
    getTimeline(me.value?.id);
}

const handleScroll = () => {
    if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight) {
        loadMore()
    }
}

// TODO: onBeforeMountとonMountedの違いを調べる
onBeforeMount(async () => {
});

onMounted(async () => {
    window.addEventListener('scroll', handleScroll)

    http = axios.create({
        baseURL: 'http://localhost',
        withCredentials: true,
    });
    await http.get('/sanctum/csrf-cookie');
    await getMe();

    loadMore()
})

onBeforeUnmount(() => {
    window.removeEventListener('scroll', handleScroll)
})

</script>

<style scoped>
.tweet {
    border: 1px solid black;
}
</style>
