<template>
  <v-app theme="light">
    <v-navigation-drawer expand-on-hover rail permanent color="#4A0404" theme="dark">
      <v-list>
        <v-list-item
          class="py-4"
          title="MeatFlow"
          :subtitle="`Atendente: ${$page.props.auth.user?.name || 'Suporte'}`"
        >
          <template v-slot:prepend>
            <v-icon icon="mdi-knife" size="x-large" color="#fff"></v-icon>
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
          :active="$page.component.includes(item.componentName)"
          @click="router.visit(item.route)"
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
        Meat<span class="text-grey-darken-3">Flow</span>
      </v-app-bar-title>
      <v-spacer></v-spacer>
      <slot name="header-action" />
    </v-app-bar>

    <v-main class="bg-grey-lighten-4 main-container">
      <div class="scrollable-content">
        <slot />
      </div>
    </v-main>
  </v-app>
</template>

<script setup>
import { router } from "@inertiajs/vue3";

const menuItems = [
  {
    title: "Dashboard",
    icon: "mdi-view-dashboard",
    componentName: "Dashboard",
    route: "/dashboard",
  },
  {
    title: "Pedidos",
    icon: "mdi-cart-outline",
    componentName: "Pedidos",
    route: "/pedidos/novo",
  },
  {
    title: "Produtos",
    icon: "mdi-food-steak",
    componentName: "Produtos",
    route: "/produtos",
  },
  {
    title: "Clientes",
    icon: "mdi-account-group-outline",
    componentName: "Clientes",
    route: "/clientes",
  },
];

const handleLogout = () => router.post("/logout");
</script>

<style scoped>
.main-container {
  height: 100vh;
  overflow: hidden;
}
.scrollable-content {
  height: calc(100vh - 64px);
  overflow-y: auto;
  padding: 24px;
}
.nav-item :deep(.v-list-item--active) {
  background-color: #8d021f !important;
  color: white !important;
}
.logout-item {
  color: #ffffff !important;
  opacity: 0.7;
}
.logout-item:hover {
  opacity: 1;
}
</style>
