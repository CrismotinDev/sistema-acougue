<template>
  <v-app theme="light">
    <v-navigation-drawer expand-on-hover rail permanent color="#3b33a4" theme="dark">
      <v-list>
        <v-list-item class="py-4" title="Açougue Manager" subtitle="Atendente: Suporte">
          <template v-slot:prepend>
            <v-icon
              icon="mdi-account-circle-outline"
              size="x-large"
              color="white"
            ></v-icon>
          </template>
        </v-list-item>
      </v-list>

      <v-divider class="opacity-20"></v-divider>

      <v-list density="compact" nav>
        <v-list-item
          v-for="item in menuItems"
          :key="item.title"
          :prepend-icon="item.icon"
          :title="item.title"
          :value="item.value"
          :active="activeItem === item.value"
          @click="navegar(item)"
          class="rounded-lg mb-1 nav-item"
        />
      </v-list>

      <template v-slot:append>
        <v-divider class="opacity-20"></v-divider>
        <div class="pa-4 text-caption d-flex align-center">
          <v-icon icon="mdi-account-tie" size="small" class="mr-2"></v-icon>
          Atendente: Carlos
        </div>
      </template>
    </v-navigation-drawer>

    <v-app-bar elevation="0" border="b" color="white" class="px-4">
      <v-app-bar-title class="font-weight-bold text-indigo-darken-4">
        Dashboard - Pedidos
      </v-app-bar-title>
      <v-spacer></v-spacer>
      <v-btn
        color="#3b33a4"
        variant="elevated"
        prepend-icon="mdi-plus"
        class="text-none px-6 font-weight-bold"
        rounded="lg"
      >
        Novo Pedido
      </v-btn>
    </v-app-bar>

    <v-main class="bg-grey-lighten-4">
      <v-container fluid class="pa-8">
        <v-alert
          type="error"
          variant="tonal"
          border="start"
          class="mb-8 bg-white border-opacity-100 rounded-lg shadow-sm"
          icon="mdi-alert-circle-outline"
        >
          <template v-slot:title>
            <span class="text-subtitle-1 font-weight-bold">3 Pedidos Atrasados!</span>
          </template>
          <div class="text-caption">João Silva, Maria Santos, Carlos Oliveira</div>
        </v-alert>

        <v-row class="mb-6">
          <v-col v-for="card in stats" :key="card.title" cols="12" sm="6" md="3">
            <v-card
              elevation="0"
              class="rounded-lg pa-2 border shadow-sm"
              :style="`border-left: 6px solid ${card.hexColor} !important`"
            >
              <v-card-item>
                <template v-slot:title>
                  <span class="text-caption text-grey-darken-1 font-weight-medium">{{
                    card.title
                  }}</span>
                </template>
                <template v-slot:append>
                  <v-icon :icon="card.icon" :color="card.color" size="small"></v-icon>
                </template>
              </v-card-item>
              <v-card-text class="text-h4 font-weight-bold pt-0 text-indigo-darken-4">
                {{ card.value }}
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>

        <v-row class="mb-6">
          <v-col cols="12" md="7">
            <v-card border elevation="0" class="rounded-lg pa-4 h-100">
              <div class="text-subtitle-2 font-weight-bold mb-4 text-indigo-darken-4">
                Pedidos por Horário
              </div>
              <v-sheet
                height="200"
                color="grey-lighten-3"
                class="rounded d-flex align-center justify-center"
              >
                <v-icon icon="mdi-chart-bar" size="x-large" color="grey"></v-icon>
              </v-sheet>
            </v-card>
          </v-col>
          <v-col cols="12" md="5">
            <v-card border elevation="0" class="rounded-lg pa-4 h-100">
              <div class="text-subtitle-2 font-weight-bold mb-4 text-indigo-darken-4">
                Distribuição por Status
              </div>
              <v-sheet
                height="200"
                color="grey-lighten-3"
                class="rounded d-flex align-center justify-center"
              >
                <v-icon icon="mdi-chart-pie" size="x-large" color="grey"></v-icon>
              </v-sheet>
            </v-card>
          </v-col>
        </v-row>

        <v-card elevation="0" border class="rounded-lg mb-6 pa-4 shadow-sm">
          <v-row density="compact" align="center">
            <v-col cols="12" md="3">
              <v-text-field
                label="Buscar Cliente"
                variant="outlined"
                density="compact"
                hide-details
                prepend-inner-icon="mdi-magnify"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="2">
              <v-select
                label="Data"
                :items="['Hoje']"
                variant="outlined"
                density="compact"
                hide-details
              ></v-select>
            </v-col>
            <v-col cols="12" md="2">
              <v-select
                label="Status"
                :items="['Todos', 'Aguardando', 'Em Preparo', 'Pronto']"
                variant="outlined"
                density="compact"
                hide-details
              ></v-select>
            </v-col>
            <v-col cols="12" md="3">
              <v-select
                label="Horário"
                :items="['Manhã', 'Tarde']"
                variant="outlined"
                density="compact"
                hide-details
              ></v-select>
            </v-col>
            <v-col cols="12" md="2">
              <v-btn color="#3b33a4" block height="40" class="text-none font-weight-bold"
                >Filtrar</v-btn
              >
            </v-col>
          </v-row>
        </v-card>

        <v-card elevation="0" border class="rounded-lg shadow-sm">
          <v-card-title class="d-flex align-center pe-2 py-3 bg-white">
            <v-icon
              icon="mdi-format-list-bulleted"
              class="mr-2"
              color="indigo-darken-4"
            ></v-icon>
            <span class="text-subtitle-1 font-weight-bold text-indigo-darken-4"
              >Listagem de Pedidos</span
            >
            <v-spacer></v-spacer>
            <v-text-field
              v-model="search"
              prepend-inner-icon="mdi-magnify"
              label="Buscar na tabela..."
              variant="solo-filled"
              flat
              density="compact"
              hide-details
              single-line
              class="max-width-300"
            ></v-text-field>
          </v-card-title>

          <v-divider></v-divider>

          <v-data-table
            v-model:search="search"
            :headers="headers"
            :items="listaPedidos"
            :items-per-page="5"
            hover
            class="rounded-lg"
            no-data-text="Nenhum pedido encontrado"
            items-per-page-text="Linhas por página"
          >
            <template v-slot:item.status="{ item }">
              <v-chip
                :color="item.color"
                size="x-small"
                variant="flat"
                class="font-weight-bold text-uppercase"
              >
                {{ item.status }}
              </v-chip>
            </template>

            <template v-slot:item.actions="{ item }">
              <div class="d-flex justify-center">
                <v-btn
                  icon="mdi-eye-outline"
                  variant="text"
                  size="small"
                  color="indigo"
                  title="Ver"
                ></v-btn>
                <v-btn
                  icon="mdi-pencil-outline"
                  variant="text"
                  size="small"
                  color="grey"
                  title="Editar"
                ></v-btn>
                <v-btn
                  icon="mdi-printer-outline"
                  variant="text"
                  size="small"
                  color="grey"
                  title="Imprimir"
                ></v-btn>
              </div>
            </template>
          </v-data-table>
        </v-card>
      </v-container>
    </v-main>
  </v-app>
</template>

<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";

const activeItem = ref("dashboard");
const search = ref("");

// Definição das colunas da Tabela
const headers = [
  { title: "HORÁRIO", align: "start", key: "horario", sortable: true },
  { title: "CLIENTE", align: "start", key: "cliente", sortable: true },
  { title: "STATUS", align: "center", key: "status", sortable: false },
  { title: "AÇÕES", align: "center", key: "actions", sortable: false },
];

const menuItems = [
  {
    title: "Dashboard",
    icon: "mdi-view-dashboard",
    value: "dashboard",
    route: "/dashboard",
  },
  { title: "Pedidos", icon: "mdi-cart-outline", value: "orders", route: "/pedidos" },
  {
    title: "Produtos",
    icon: "mdi-package-variant-closed",
    value: "products",
    route: "/produtos",
  },
  {
    title: "Clientes",
    icon: "mdi-account-group-outline",
    value: "clients",
    route: "/clientes",
  },
  {
    title: "Configurações",
    icon: "mdi-cog-outline",
    value: "settings",
    route: "/configuracoes",
  },
];

const listaPedidos = [
  { horario: "08:30", cliente: "João da Silva", status: "Aguardando", color: "orange" },
  { horario: "09:45", cliente: "Maria Santos", status: "Em Preparo", color: "blue" },
  { horario: "10:20", cliente: "Carlos Oliveira", status: "Pronto", color: "green" },
  { horario: "11:00", cliente: "Ana Paula", status: "Aguardando", color: "orange" },
  { horario: "13:30", cliente: "Ricardo Lima", status: "Pronto", color: "green" },
];

const navegar = (item) => {
  activeItem.value = item.value;
};

const stats = [
  {
    title: "Total de Pedidos",
    value: 4,
    icon: "mdi-trending-up",
    color: "indigo",
    hexColor: "#3b33a4",
  },
  {
    title: "Aguardando",
    value: 2,
    icon: "mdi-clock-outline",
    color: "orange",
    hexColor: "#fb8c00",
  },
  {
    title: "Em Preparo",
    value: 1,
    icon: "mdi-progress-clock",
    color: "blue",
    hexColor: "#2196f3",
  },
  {
    title: "Prontos",
    value: 1,
    icon: "mdi-check-circle-outline",
    color: "green",
    hexColor: "#4caf50",
  },
];
</script>

<style scoped>
.nav-item :deep(.v-list-item--active) {
  background-color: #5c51ff !important;
  color: white !important;
}

.max-width-300 {
  max-width: 300px;
}

/* Deixa o cabeçalho da data-table profissional */
:deep(.v-data-table-header__content) {
  font-weight: bold !important;
  color: #3b33a4 !important;
  font-size: 0.75rem !important;
  letter-spacing: 0.5px;
}

/* Estilo para a coluna de horário */
:deep(td:first-child) {
  font-weight: bold !important;
  color: #3b33a4 !important;
}
</style>
