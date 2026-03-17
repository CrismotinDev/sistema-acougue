<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";

const loginInput = ref(""); // Renomeado para ficar claro que aceita ambos
const password = ref("");
const loading = ref(false);
const errorMsg = ref("");

const handleLogin = () => {
  errorMsg.value = "";
  loading.value = true;

  router.post(
    "/login",
    {
      // Enviamos como 'login', que é o que o LoginController espera no $request
      login: loginInput.value,
      password: password.value,
    },
    {
      onFinish: () => {
        loading.value = false;
      },
      onError: (errors) => {
        // Captura o erro vindo da validação do Laravel
        errorMsg.value = errors.login || "Usuário ou senha incorretos.";
      },
    }
  );
};
</script>

<template>
  <v-container class="fill-height login-bg" fluid>
    <v-row align="center" justify="center" no-gutters class="fill-height">
      <v-col cols="12" sm="8" md="4" lg="3">
        <v-card rounded="xl" class="pa-4 card-glass mb-4">
          <v-card-item class="text-center">
            <div class="d-flex justify-center mb-2">
              <v-icon icon="mdi-knife" size="48" color="#8D021F"></v-icon>
            </div>
            <v-card-title class="text-h4 font-weight-black text-uppercase brand-text">
              Meat<span class="text-grey-darken-3">Flow</span>
            </v-card-title>
            <v-card-subtitle class="mt-2 font-weight-medium">
              Gestão Profissional de Carnes
            </v-card-subtitle>
          </v-card-item>

          <v-card-text class="mt-4">
            <v-alert v-if="errorMsg" type="error" variant="tonal" class="mb-4" closable>
              {{ errorMsg }}
            </v-alert>

            <v-text-field
              label="E-mail ou Usuário"
              prepend-inner-icon="mdi-account-outline"
              variant="outlined"
              v-model="loginInput"
              class="mb-2 custom-input"
              color="#8D021F"
              autocomplete="username"
              persistent-placeholder
            />

            <v-text-field
              label="Senha"
              prepend-inner-icon="mdi-lock-outline"
              persistent-placeholder
              type="password"
              variant="outlined"
              v-model="password"
              class="custom-input"
              color="#8D021F"
              @keydown.enter.prevent="handleLogin"
              autocomplete="current-password"
            />

            <v-btn
              color="#8D021F"
              block
              size="x-large"
              class="mt-8 font-weight-bold login-btn"
              elevation="8"
              @click="handleLogin"
            >
              Acessar Sistema
            </v-btn>
          </v-card-text>
        </v-card>

        <div class="text-center text-grey-lighten-1 text-caption mb-2">
          &copy; 2024 MeatFlow - Qualidade em cada corte
        </div>
      </v-col>
    </v-row>
  </v-container>
</template>

<style scoped>
/* Garante que o container ocupe toda a altura sem scroll desnecessário */
.v-container.fill-height {
  height: 100vh !important;
  display: flex !important;
  align-items: stretch !important;
}

.login-bg {
  background: radial-gradient(circle, #4a0404 0%, #1a0a0a 100%);
}

.brand-text {
  letter-spacing: 2px !important;
  color: #8d021f;
}

.card-glass {
  background: rgba(255, 255, 255, 0.95) !important;
}
</style>
