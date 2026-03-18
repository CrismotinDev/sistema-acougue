<template>
  <AuthenticatedLayout>
    <v-container fluid class="pa-4">
      <v-card border elevation="2" class="rounded-lg">
        <v-toolbar color="white" flat class="px-4 border-b" height="50">
          <v-toolbar-title class="text-subtitle-1 font-weight-black text-red-darken-4">
            CADASTRO DE PRODUTOS
          </v-toolbar-title>
        </v-toolbar>

        <v-form @submit.prevent="salvarProduto" class="pa-6">
          <v-row dense align="center">
            <v-col cols="12" md="8">
              <v-text-field
                v-model="form.nome"
                label="Nome do Produto (ex: Picanha)"
                variant="outlined"
                color="#8D021F"
                density="compact"
                hide-details
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="4">
              <v-btn
                block
                color="#8D021F"
                height="40"
                theme="dark"
                :loading="form.processing"
                type="submit"
              >
                Cadastrar Produto
              </v-btn>
            </v-col>
          </v-row>
        </v-form>

        <v-divider></v-divider>

        <v-table density="compact" class="pa-4">
          <thead>
            <tr>
              <th class="text-left font-weight-bold">ID</th>
              <th class="text-left font-weight-bold">Nome</th>
              <th class="text-right">Ações</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="produto in produtos" :key="produto.id">
              <td>{{ produto.id }}</td>
              <td>{{ produto.nome }}</td>
              <td class="text-right">
                <v-btn
                  icon="mdi-trash-can-outline"
                  variant="text"
                  color="red"
                  size="small"
                  @click="deletarProduto(produto.id)"
                ></v-btn>
              </td>
            </tr>
          </tbody>
        </v-table>
      </v-card>
    </v-container>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm } from "@inertiajs/vue3";

defineProps({
  produtos: Array,
});

const form = useForm({
  nome: "",
});

const salvarProduto = () => {
  form.post(route("produtos.store"), {
    onSuccess: () => form.reset(),
  });
};

const deletarProduto = (id) => {
  if (confirm("Excluir este produto?")) {
    router.delete(route("produtos.destroy", id));
  }
};
</script>
