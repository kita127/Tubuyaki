import Tweet from "../../js/components/Tweet.vue";
import { describe, expect, test } from "vitest";
import { shallowMount } from "@vue/test-utils";

describe("Tweet", () => {
    test("Component Test", async () => {
        const expected = "test message";
        const wrapper = shallowMount(Tweet, {
            propsData: { text: expected },
        })
        expect(wrapper.text()).toContain(expected)
    })
})