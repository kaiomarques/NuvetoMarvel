<template>
    <q-layout view="hHh lpR fFf">
        <q-header elevated class="bg-primary text-white" height-hint="98">
            <q-toolbar>
                <q-btn dense flat round icon="menu" @click="toggleLeftDrawer" />

                <q-toolbar-title>
                    <q-avatar>
                        <img
                            src="https://cdn.quasar.dev/logo-v2/svg/logo-mono-white.svg"
                        />
                    </q-avatar>
                    Title
                </q-toolbar-title>
            </q-toolbar>

            <q-tabs align="left">
                <q-route-tab to="/page1" label="Page One" />
                <q-route-tab to="/page2" label="Page Two" />
                <q-route-tab to="/page3" label="Page Three" />
            </q-tabs>
        </q-header>

        <q-drawer show-if-above v-model="leftDrawerOpen" side="left" bordered>
            <!-- drawer content -->
        </q-drawer>

        <q-page-container>
            <!------------------------------ -->
            <!------------------------------ -->
            <!------------------------------ -->
            <!------------------------------ -->

            <div class="q-pa-md row items-start q-gutter-md">
                <q-card
                    class="my-card"
                    flat
                    bordered
                    v-for="comic in comics"
                    :key="comic.id"
                >
                    <q-img :src="comic.image" />

                    <q-card-section>
                        <div class="text-overline text-orange-9">
                            {{ comic.creators }}
                        </div>
                        <div class="text-h5 q-mt-sm q-mb-xs">
                            {{ comic.title }}
                        </div>
                        <div class="text-caption text-grey">
                            Lorem ipsum dolor sit amet, consectetur adipiscing
                            elit, sed do eiusmod tempor incididunt ut labore et
                            dolore magna aliqua.
                        </div>
                    </q-card-section>

                    <q-card-actions>
                        <q-btn flat color="primary" label="Share" />
                        <q-btn flat color="secondary" label="Book" />

                        <q-space />

                        <q-btn
                            color="grey"
                            round
                            flat
                            dense
                            :icon="
                                expanded
                                    ? 'keyboard_arrow_up'
                                    : 'keyboard_arrow_down'
                            "
                            @click="expanded = !expanded"
                        />
                    </q-card-actions>

                    <q-slide-transition>
                        <div v-show="expanded">
                            <q-separator />
                            <q-card-section class="text-subtitle2">
                                {{ lorem }}
                            </q-card-section>
                        </div>
                    </q-slide-transition>
                </q-card>
            </div>

            <!------------------------------ -->
            <!------------------------------ -->
            <!------------------------------ -->
            <!------------------------------ -->
            <div class="q-pa-lg">
                <div class="q-gutter-md">
                    <q-pagination
                        v-model="current"
                        :max="5"
                        direction-links
                        @click="changePage"
                    />
                </div>
            </div>
            <!------------------------------ -->
            <!------------------------------ -->
            <!------------------------------ -->
            <!------------------------------ -->
        </q-page-container>
    </q-layout>
</template>

<script>
import { ref } from "vue";

export default {
    model: {
        prop: "current",
    },
    props: ["comics", "page"],
    setup(props) {
        const leftDrawerOpen = ref(false);
        const current = ref(parseInt(props.page)); // Definindo a variável current com o valor de props.page

        //console.log(props.page);

        const toggleLeftDrawer = () => {
            leftDrawerOpen.value = !leftDrawerOpen.value;
        };

        const changePage = () => {
            window.location.href = "comics?page=" + current.value;
        };

        return {
            current,
            leftDrawerOpen,
            toggleLeftDrawer,
            changePage,
            expanded: ref(false),
            lorem: "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.",
        };
    },
};
</script>

<style lang="sass" scoped>
.my-card
  width: 100%
  max-width: 350px
</style>
