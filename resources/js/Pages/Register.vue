<template>
    <AppLayout>
        <div class="container">
            <h3>Cadastro</h3>
            <span
                v-if="$page.props.success"
                style="
                    padding: 10px;
                    margin: 10px;
                    display: flex;
                    border-radius: 10px;
                    background-color: #86f99a;
                    color: #168126;
                "
                >{{ $page.props.success }}</span
            >
            <span
                v-if="$page.props.errors.error"
                style="
                    padding: 10px;
                    margin: 10px;
                    display: flex;
                    border-radius: 10px;
                    background-color: #ff919f;
                    color: #81162b;
                "
                >{{ $page.props.errors.error }}</span
            >
            <form method="post" action="/register/store">
                <input
                    type="hidden"
                    name="_token"
                    :value="this.$page.props.csrf_token"
                />
                <div class="form">
                    <label for="nome">Nome: </label>
                    <input
                        type="text"
                        name="firstname"
                        placeholder="digite seu Nome aqui"
                        :value="
                            $page.props.old_input &&
                            $page.props.old_input.firstname
                                ? $page.props.old_input.firstname
                                : ''
                        "
                    />
                    <div>
                        <span v-if="$page.props.errors.firstname">{{
                            $page.props.errors.firstname
                        }}</span>
                    </div>
                    <label for="nome">Sobrenome: </label>
                    <input
                        type="text"
                        name="lastname"
                        placeholder="digite seu Nome aqui"
                        :value="
                            $page.props.old_input &&
                            $page.props.old_input.lastname
                                ? $page.props.old_input.lastname
                                : ''
                        "
                    />
                    <div>
                        <span v-if="$page.props.errors.lastname">{{
                            $page.props.errors.lastname
                        }}</span>
                    </div>
                    <label for="nome">E-mail: </label>
                    <input
                        type="text"
                        name="email"
                        placeholder="digite seu e-mail aqui"
                        :value="
                            $page.props.old_input && $page.props.old_input.email
                                ? $page.props.old_input.email
                                : ''
                        "
                    />
                    <div>
                        <span v-if="$page.props.errors.email">{{
                            $page.props.errors.email
                        }}</span>
                    </div>
                    <label for="nome">Senha: </label>
                    <input
                        type="password"
                        name="password"
                        placeholder="digite sua senha aqui"
                        :value="
                            $page.props.old_input &&
                            $page.props.old_input.password
                                ? $page.props.old_input.password
                                : ''
                        "
                    />
                    <div>
                        <span v-if="$page.props.errors.password">{{
                            $page.props.errors.password
                        }}</span>
                    </div>
                    <div class="form-row">
                        <input type="submit" name="enviar" value="Enviar" />
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from "../Layouts/App.vue";

export default {
    components: {
        AppLayout,
    },
    props: ["errors", "message", "success", "old_input"],
    setup() {
        return {};
    },
};
</script>

<style>
.container {
    padding-left: 30px;
    padding-top: 0px;
}
.form {
    width: 800px;
    display: grid;
    grid-template-columns: 1fr 7fr 4fr;
    flex-direction: column;
    gap: 20px;
}
.form label {
    text-align: right;
}
</style>
