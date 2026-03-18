<template>
  <AuthenticatedLayout>
    <template #header-action>
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
    </template>

    <v-alert
      type="error"
      variant="tonal"
      border="start"
      class="mb-8 bg-white rounded-lg shadow-sm"
      color="#8D021F"
    >
      <template v-slot:title>3 Pedidos Atrasados!</template>
      <div class="text-caption">João Silva, Maria Santos, Carlos Oliveira</div>
    </v-alert>

    <v-row class="mb-6">
      <v-col v-for="card in stats" :key="card.title" cols="12" sm="6" md="3">
        <v-card
          elevation="0"
          class="rounded-lg pa-2 border shadow-sm"
          :style="`border-left: 6px solid ${card.hexColor} !important`"
        >
        </v-card>
      </v-col>
    </v-row>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { router } from "@inertiajs/vue3";
// ... suas variáveis stats, listaPedidos, etc ...
</script>
