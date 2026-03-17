<template>
  <v-app theme="light">
    <v-navigation-drawer expand-on-hover rail permanent color="#3b33a4" theme="dark">
      <v-list>
        <v-list-item
          class="py-4"
          title="Açougue Manager"
          subtitle="Atendente: Suporte"
          prepend-icon="mdi-account-circle-outline"
        />
      </v-list>
      <v-divider class="opacity-20"></v-divider>
      <v-list density="compact" nav>
        <v-list-item
          prepend-icon="mdi-view-dashboard"
          title="Dashboard"
          @click="router.visit('/dashboard')"
        />
        <v-list-item prepend-icon="mdi-cart" title="Pedidos" active />
      </v-list>
    </v-navigation-drawer>

    <v-app-bar elevation="0" border="b" color="white" class="px-4">
      <v-app-bar-title class="font-weight-bold text-indigo-darken-4 text-h5"
        >Gestão de Pedidos</v-app-bar-title
      >
    </v-app-bar>

    <v-main class="bg-grey-lighten-4">
      <v-container fluid class="pa-10" style="max-width: 1200px">
        <v-card border elevation="4" class="rounded-xl overflow-hidden shadow-lg">
          <v-toolbar color="white" flat class="px-6 border-b">
            <v-icon color="indigo" class="mr-2">mdi-form-select</v-icon>
            <span class="text-overline font-weight-bold">Preencha os dados abaixo</span>
          </v-toolbar>

          <v-form class="pa-10">
            <div
              class="text-subtitle-1 font-weight-bold text-indigo mb-4 d-flex align-center"
            >
              <v-icon size="small" class="mr-2">mdi-account-search</v-icon> Identificação
              do Cliente
            </div>

            <v-row class="mb-6">
              <v-col cols="12" md="6">
                <v-autocomplete
                  label="Selecionar cliente da base"
                  prepend-inner-icon="mdi-database-search"
                  :items="['João Silva', 'Maria Oliveira', 'Carlos Souza']"
                  variant="outlined"
                  color="indigo"
                  placeholder="Busque por nome ou CPF..."
                  persistent-placeholder
                ></v-autocomplete>
              </v-col>
              <v-col cols="12" md="1" class="d-flex align-center justify-center">
                <v-chip size="small" variant="tonal" color="grey">OU</v-chip>
              </v-col>
              <v-col cols="12" md="5">
                <v-text-field
                  label="Cliente Não Cadastrado"
                  prepend-inner-icon="mdi-account-plus"
                  variant="outlined"
                  color="indigo"
                  placeholder="Nome completo do cliente"
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-text-field
                  label="Telefone de Contato"
                  prepend-inner-icon="mdi-whatsapp"
                  variant="outlined"
                  color="indigo"
                  placeholder="(00) 00000-0000"
                  persistent-placeholder
                ></v-text-field>
              </v-col>
            </v-row>

            <v-divider class="mb-8"></v-divider>

            <div
              class="text-subtitle-1 font-weight-bold text-indigo mb-4 d-flex align-center"
            >
              <v-icon size="small" class="mr-2">mdi-food-steak</v-icon> Detalhes do Corte
            </div>

            <v-card
              color="grey-lighten-5"
              variant="flat"
              class="pa-6 rounded-lg border mb-8"
            >
              <v-row>
                <v-col cols="12" md="5">
                  <v-select
                    label="Corte / Produto"
                    :items="[
                      'Picanha Argentina',
                      'Alcatra Grill',
                      'Costela Janela',
                      'Filé Mignon',
                    ]"
                    variant="solo"
                    flat
                    density="comfortable"
                  ></v-select>
                </v-col>
                <v-col cols="12" md="3">
                  <v-text-field
                    label="Quantidade"
                    suffix="Kg"
                    variant="solo"
                    flat
                    density="comfortable"
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="3">
                  <v-text-field
                    label="Observações"
                    placeholder="Ex: moída 2x"
                    variant="solo"
                    flat
                    density="comfortable"
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="1" class="d-flex align-center">
                  <v-btn color="indigo" icon="mdi-plus" elevation="2"></v-btn>
                </v-col>
              </v-row>
            </v-card>

            <div
              class="text-subtitle-1 font-weight-bold text-indigo mb-4 d-flex align-center"
            >
              <v-icon size="small" class="mr-2">mdi-truck-delivery</v-icon> Agendamento e
              Logística
            </div>

            <v-row class="mb-10">
              <v-col cols="12" md="6">
                <v-text-field
                  label="Data de Retirada/Entrega"
                  type="date"
                  prepend-inner-icon="mdi-calendar"
                  variant="outlined"
                  color="indigo"
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field
                  label="Horário Previsto"
                  type="time"
                  prepend-inner-icon="mdi-clock-outline"
                  variant="outlined"
                  color="indigo"
                ></v-text-field>
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12" sm="8">
                <v-btn
                  block
                  size="x-large"
                  color="#3b33a4"
                  class="text-none font-weight-black elevation-8"
                  rounded="xl"
                  height="70"
                >
                  <v-icon class="mr-2">mdi-check-circle</v-icon>
                  FINALIZAR E IMPRIMIR PEDIDO
                </v-btn>
              </v-col>
              <v-col cols="12" sm="4">
                <v-btn
                  block
                  size="x-large"
                  variant="outlined"
                  color="grey-darken-1"
                  class="text-none font-weight-bold"
                  rounded="xl"
                  height="70"
                  @click="router.visit('/dashboard')"
                >
                  CANCELAR
                </v-btn>
              </v-col>
            </v-row>
          </v-form>
        </v-card>
      </v-container>
    </v-main>
  </v-app>
</template>

<script setup>
import { router } from "@inertiajs/vue3";
</script>

<style scoped>
.v-container {
  animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Deixa os labels mais fortes */
:deep(.v-label) {
  font-weight: 600 !important;
  color: #3b33a4 !important;
}

/* Custom shadow para o card principal */
.shadow-lg {
  box-shadow: 0 15px 35px rgba(59, 51, 164, 0.1) !important;
}
</style>
