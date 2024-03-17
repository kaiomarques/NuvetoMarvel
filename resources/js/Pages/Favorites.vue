<template>
    <AppLayout>
        <div class="q-pa-md">
            <h3>Favoritos</h3>
            <div class="q-gutter-y-md" style="max-width: 600px"></div>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from "../Layouts/App.vue";

export default {
    model: {
        prop: "current",
    },
    components: {
        AppLayout,
    },
    props: ["comics", "page"],
    setup() {
        return {};
    },
};
</script>
