<template>
  <v-app theme="light">
    <v-navigation-drawer expand-on-hover rail permanent color="#4A0404" theme="dark">
      <v-list>
        <v-list-item
          class="py-4"
          title="MeatFlow"
          :subtitle="`Atendente: ${$page.props.auth.user.name}`"
        >
          <template v-slot:prepend>
            <v-icon icon="mdi-knife" size="x-large" color="#ffff"></v-icon>
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

        <v-list-item
          prepend-icon="mdi-logout"
          title="Sair do Sistema"
          class="rounded-lg mt-4 logout-item"
          @click="handleLogout"
        />
      </v-list>
    </v-navigation-drawer>

    <v-app-bar elevation="0" border="b" color="white" class="px-4">
      <v-app-bar-title class="font-weight-bold text-red-darken-4">
        Meat<span class="text-grey-darken-3">Flow</span> - Dashboard
      </v-app-bar-title>
      <v-spacer></v-spacer>
      <v-btn
        color="#8D021F"
        variant="elevated"
        prepend-icon="mdi-plus"
        class="text-none px-6 font-weight-bold"
        rounded="lg"
        @click="router.visit('/pedidos/novo')"
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
          color="#8D021F"
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
                  <v-icon :icon="card.icon" :color="card.hexColor" size="small"></v-icon>
                </template>
              </v-card-item>
              <v-card-text
                class="text-h4 font-weight-bold pt-0"
                :style="`color: ${card.hexColor}`"
              >
                {{ card.value }}
              </v-card-text>
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
                label="Status"
                :items="['Todos', 'Aguardando', 'Em Preparo', 'Pronto']"
                variant="outlined"
                density="compact"
                hide-details
              ></v-select>
            </v-col>
            <v-col cols="12" md="2">
              <v-btn color="#8D021F" block height="40" class="text-none font-weight-bold"
                >Filtrar</v-btn
              >
            </v-col>
          </v-row>
        </v-card>

        <v-card elevation="0" border class="rounded-lg shadow-sm">
          <v-card-title class="d-flex align-center pe-2 py-3 bg-white">
            <v-icon icon="mdi-format-list-bulleted" class="mr-2" color="#8D021F"></v-icon>
            <span class="text-subtitle-1 font-weight-bold text-grey-darken-4"
              >Listagem de Pedidos</span
            >
            <v-spacer></v-spacer>
            <v-text-field
              v-model="search"
              prepend-inner-icon="mdi-magnify"
              label="Buscar..."
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
            hover
            class="rounded-lg"
          >
            <template v-slot:item.status="{ item }">
              <v-chip
                :color="item.color"
                size="x-small"
                variant="flat"
                class="font-weight-bold text-uppercase"
                >{{ item.status }}</v-chip
              >
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
  { title: "Produtos", icon: "mdi-food-steak", value: "products", route: "/produtos" },
  {
    title: "Clientes",
    icon: "mdi-account-group-outline",
    value: "clients",
    route: "/clientes",
  },
];

const listaPedidos = [
  { horario: "08:30", cliente: "João da Silva", status: "Aguardando", color: "orange" },
  { horario: "09:45", cliente: "Maria Santos", status: "Em Preparo", color: "blue" },
  { horario: "10:20", cliente: "Carlos Oliveira", status: "Pronto", color: "green" },
];

const stats = [
  { title: "Total de Pedidos", value: 4, icon: "mdi-trending-up", hexColor: "#8D021F" },
  { title: "Aguardando", value: 2, icon: "mdi-clock-outline", hexColor: "#fb8c00" },
  { title: "Em Preparo", value: 1, icon: "mdi-progress-clock", hexColor: "#2196f3" },
  { title: "Prontos", value: 1, icon: "mdi-check-circle-outline", hexColor: "#4caf50" },
];

const navegar = (item) => {
  activeItem.value = item.value;
  if (item.route) router.visit(item.route);
};

const handleLogout = () => {
  router.post("/logout");
};
</script>

<style scoped>
.nav-item :deep(.v-list-item--active) {
  background-color: #8d021f !important;
  color: white !important;
}

.max-width-300 {
  max-width: 300px;
}

:deep(.v-data-table-header__content) {
  font-weight: bold !important;
  color: #8d021f !important;
  font-size: 0.75rem !important;
  letter-spacing: 0.5px;
}

.logout-item {
  color: #ffffff !important;
}

:deep(td:first-child) {
  font-weight: bold !important;
  color: #8d021f !important;
}
</style>
