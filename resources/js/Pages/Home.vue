<template>
    <AppLayout>
        <div class="login-container" v-if="!$page.props.auth">
            <div class="campos left">
                <h2>Login</h2>
                <span
                    v-if="$page.props.success == 'true'"
                    style="
                        padding: 10px;
                        margin: 10px;
                        display: flex;
                        border-radius: 10px;
                        background-color: #86f99a;
                        color: #168126;
                    "
                    >{{ $page.props.message }}</span
                >
                <span
                    v-if="$page.props.success == 'false'"
                    style="
                        padding: 10px;
                        margin: 10px;
                        display: flex;
                        border-radius: 10px;
                        background-color: #ff919f;
                        color: #81162b;
                    "
                    >{{ $page.props.message }}</span
                >
            </div>
            <form action="auth" method="post">
                <input
                    type="hidden"
                    name="_token"
                    :value="this.$page.props.csrf_token"
                />
                <div class="campos">
                    <div>
                        <input
                            type="text"
                            placeholder="Your email"
                            name="email"
                        />
                    </div>
                    <div>
                        <input
                            type="password"
                            placeholder="Your password"
                            name="password"
                        />
                    </div>
                    <div>
                        <button type="submit">Login</button>
                    </div>
                </div>
                <div class="campos">
                    <a
                        :href="authUrl"
                        class="googleButton"
                        role="link"
                        tabindex="8"
                        ><div>Logar com o Google</div></a
                    >
                </div>
                <div class="campos">
                    <div>
                        <a href="/register"> Cadastrar usuário</a>
                    </div>
                </div>
            </form>
        </div>
        <div v-if="$page.props.auth">
            <h4>Seja bem vindo {{ $page.props.auth.firstname }}!</h4>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from "../Layouts/App.vue";

export default {
    components: {
        AppLayout,
    },
    props: ["authUrl"],
    setup() {
        return {};
    },
};
</script>
<style scoped>
.campos {
    width: 660px;
    display: flex;
    gap: 1.6rem;
    padding: 5px;
    justify-content: center;
    align-items: center;
}

.center {
    justify-content: center;
    align-items: center;
}

.left {
    justify-content: left;
    align-items: center;
}

.googleButton {
    background-size: 10% 70%;
    background-position-x: 3%;
    background-position-y: 50%;
    background-repeat: no-repeat;
    background-color: #e7e7e7;
    color: #898989;

    display: flex;
    justify-content: center;
    align-items: center;

    box-shadow: 0 3.2rem 6.4rem rgba(0, 0, 0, 0.06);

    font-size: 16px;
    height: 45px;
    width: 97%;
    overflow: hidden;
    max-width: 380px;
    border: 1px solid var(--c-gray);
    -moz-border-radius: 0.5rem;
    -webkit-border-radius: 0.5rem;
    border-radius: 0.5rem;
    margin-top: 10px;

    text-decoration: none;
}

.googleButton {
    background-image: url(https://stgacpfldistprdb2cadr.blob.core.windows.net/blobcpflenergiab2c/front-end-prd/img/logos/google.svg);
}
</style>
