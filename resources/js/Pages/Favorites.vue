<template>
    <AppLayout>
        <div class="q-pa-md">
            <h3>Favoritos</h3>
            <h4>Quadrinhos</h4>
            <div class="q-gutter-y-md" style="max-width: 600px">
                <div class="comic-container" v-if="favoriteComics.length != 0">
                    <div class="comic" v-for="comic in favoriteComics">
                        <img class="imagem" :src="comic.image" />
                        <span>{{ comic.title }}</span>
                    </div>
                </div>
                <div class="comic-container" v-if="favoriteComics.length == 0">
                    <h6>Nenhum quadrinho favorito no momento</h6>
                </div>
            </div>
            <h4>Personagens</h4>
            <div class="q-gutter-y-md" style="max-width: 600px">
                <div
                    class="character-container"
                    v-if="favoriteCharacters.length != 0"
                >
                    <div
                        class="character"
                        v-for="character in favoriteCharacters"
                    >
                        <img class="imagem" :src="character.image" />
                        <span>{{ character.name }}</span>
                    </div>
                </div>
                <div
                    class="comic-container"
                    v-if="favoriteCharacters.length == 0"
                >
                    <h6>Nenhum personagem favorito no momento</h6>
                </div>
            </div>
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
    props: ["favoriteComics", "favoriteCharacters"],
    setup() {
        return {};
    },
};
</script>

<style>
.comic-container,
.character-container {
    display: flex;
    gap: 20px;
}

.comic .imagem,
.character .imagem {
    display: flex;
    width: 100px;
}
</style>
