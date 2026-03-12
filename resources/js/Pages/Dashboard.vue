<template>
  <v-app>
    <v-navigation-drawer expand-on-hover rail permanent color="grey-darken-4">
      <v-list>
        <v-list-item prepend-avatar="https://cdn-icons-png.flaticon.com/512/924/924514.png" title="Admin Açougue" subtitle="suporte@acougue.com"></v-list-item>
      </v-list>
      <v-divider></v-divider>
      <v-list density="compact" nav>
        <v-list-item prepend-icon="mdi-view-dashboard" title="Resumo" value="dashboard"></v-list-item>
        <v-list-item prepend-icon="mdi-food-steak" title="Produtos/Cortes" value="products"></v-list-item>
        <v-list-item prepend-icon="mdi-cart" title="Vendas" value="sales"></v-list-item>
      </v-list>
    </v-navigation-drawer>

    <v-app-bar elevation="1">
      <v-app-bar-title>Painel de Controle - Sistema Açougue</v-app-bar-title>
      <v-spacer></v-spacer>
      <v-btn icon="mdi-logout" @click="logout"></v-btn>
    </v-app-bar>

    <v-main class="bg-grey-lighten-3">
      <v-container>
        <v-row>
          <v-col cols="12" md="4">
            <v-card subtitle="Vendas hoje" title="R$ 1.250,00" color="success" variant="elevated"></v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-main>
  </v-app>
</template>
