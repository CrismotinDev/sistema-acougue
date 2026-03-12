<template>
  <v-container class="fill-height login-bg" fluid>
    <v-row align="end" justify="center" no-gutters class="pb-10">
      <v-col cols="12" sm="8" md="4" lg="3" class="mt-16 pt-16">
        <v-card elevation="14" rounded="xl" class="pa-4">
          <v-card-title class="text-center text-h5 font-weight-bold pt-4">
            Pedidos Açougue
          </v-card-title>

          <v-card-subtitle class="text-center mb-6"> Acesso ao sistema </v-card-subtitle>

          <v-card-text>
            <v-text-field
              label="Usuário"
              prepend-inner-icon="mdi-account"
              variant="outlined"
              v-model="email"
              class="mb-2"
            />

            <v-text-field
              label="Senha"
              prepend-inner-icon="mdi-lock"
              type="password"
              variant="outlined"
              v-model="password"
            />

            <v-btn
              color="primary"
              block
              size="x-large"
              class="mt-6 font-weight-bold"
              elevation="4"
              :loading="loading"
              @click="handleLogin"
            >
              Entrar
            </v-btn>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

const router = useRouter();
const email = ref("");
const password = ref("");
const loading = ref(false);
const errorMsg = ref("");

const handleLogin = async () => {
  errorMsg.value = "";
  loading.value = true;

  try {
    await axios.get("/sanctum/csrf-cookie");

    await axios.post("/login", {
      login: email.value,
      password: password.value,
    });

    router.push("/dashboard");
  } catch (err) {
    errorMsg.value = err.response?.data?.message || "Erro ao autenticar.";
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.login-bg {
  min-height: 100vh;
  width: 100vw;
  background: linear-gradient(135deg, #3f51b5 0%, #1a237e 100%);
  margin: 0;
  padding: 0;
}
</style>
