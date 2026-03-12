<template>
  <v-container class="fill-height login-bg" fluid>
    <v-row justify="center">
      <v-col cols="12" sm="8" md="4">
        <v-card elevation="12" rounded="xl" class="pa-4">
          <v-card-title class="text-center">Criar Usuário</v-card-title>
          <v-card-text>
            <v-text-field label="Nome Completo" variant="outlined" />
            <v-text-field label="E-mail" variant="outlined" />
            <v-text-field label="Senha" type="password" variant="outlined" />
            <v-btn block color="success" size="large">Cadastrar</v-btn>
            <v-btn block variant="text" class="mt-2" @click="$router.push('/')">Voltar ao Login</v-btn>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>
