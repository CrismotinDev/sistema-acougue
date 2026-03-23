<template>
  <AuthenticatedLayout>
    <v-container fluid class="pa-0">
      <v-sheet class="pa-4 border-b bg-grey-lighten-5">
        <v-row class="align-center" no-gutters>
          <v-col cols="12" md="8">
            <div
              class="text-overline text-grey-darken-1"
              style="font-size: 0.7rem !important"
            >
              Relacionamento com Clientes
            </div>
            <h2 class="text-h6 font-weight-black text-red-darken-4">CLIENTES</h2>
            <p class="text-body-2 text-grey-darken-1 mt-1">
              Gerencie os clientes cadastrados e mantenha os dados de contato sempre
              organizados.
            </p>
          </v-col>

          <v-col cols="12" md="4" class="text-md-right mt-3 mt-md-0">
            <v-btn
              color="#8D021F"
              theme="dark"
              prepend-icon="mdi-account-plus-outline"
              class="rounded-lg text-caption font-weight-bold"
              @click="irParaCriacao"
            >
              Novo Cliente
            </v-btn>
          </v-col>
        </v-row>
      </v-sheet>

      <v-card flat rounded="lg" class="mt-4">
        <v-toolbar color="white" flat class="px-4 border-b">
          <v-text-field
            v-model="search"
            prepend-inner-icon="mdi-magnify"
            label="Buscar por nome, telefone ou endereco"
            variant="outlined"
            density="compact"
            color="#8D021F"
            hide-details
            rounded="md"
            class="search-field"
          />

          <v-spacer />

          <v-chip color="#8D021F" variant="tonal" class="font-weight-medium">
            {{ clientes.length }} cliente(s)
          </v-chip>
        </v-toolbar>

        <v-data-table
          :headers="headers"
          :items="clientes"
          :search="search"
          density="comfortable"
          hover
          flat
          class="text-body-2"
        >
          <template #item.id="{ item }">
            <div class="d-flex align-center py-2">
              <span class="font-weight-bold text-grey-darken-4">
                {{ item.id }}
              </span>

              <v-menu location="bottom end">
                <template #activator="{ props }">
                  <v-btn
                    icon="mdi-dots-vertical"
                    variant="text"
                    size="small"
                    color="grey-darken-1"
                    class="id-action-btn ml-1"
                    v-bind="props"
                  />
                </template>

                <v-list density="compact" min-width="160">
                  <v-list-item
                    prepend-icon="mdi-pencil-outline"
                    title="Editar"
                    @click="editarCliente(item)"
                  />
                  <v-list-item
                    prepend-icon="mdi-trash-can-outline"
                    title="Excluir"
                    base-color="red-darken-2"
                    @click="prepararExclusao(item)"
                  />
                </v-list>
              </v-menu>
            </div>
          </template>
          <template #item.nome="{ item }">
            <div class="d-flex flex-column py-2">
              <span class="font-weight-bold text-grey-darken-4">
                {{ item.nome }}
              </span>
            </div>
          </template>

          <template #item.telefone="{ item }">
            <span class="text-grey-darken-3">{{ item.telefone }}</span>
          </template>

          <template #item.endereco="{ item }">
            <span class="text-grey-darken-3">{{ item.endereco }}</span>
          </template>

          <template #no-data>
            <div class="pa-10 text-center">
              <v-icon size="40" color="grey-lighten-1"> mdi-account-off-outline </v-icon>
              <div class="text-subtitle-2 text-grey-darken-2 mt-3">
                Nenhum cliente cadastrado.
              </div>
              <div class="text-body-2 text-grey mt-1">
                Cadastre o primeiro cliente para comecar a organizar os atendimentos.
              </div>
            </div>
          </template>
        </v-data-table>
      </v-card>

      <DialogConfirm
        v-model="showDialogDelete"
        title="Confirmar Exclusao"
        :message="`Deseja realmente excluir o cliente ${clienteParaExcluir?.nome}?`"
        @confirm="confirmarExclusao"
      />
    </v-container>
  </AuthenticatedLayout>
</template>

<script setup>
import DialogConfirm from "@/Components/DialogConfirm.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { router } from "@inertiajs/vue3";
import { ref } from "vue";

defineProps({
  clientes: {
    type: Array,
    default: () => [],
  },
});

const search = ref("");
const showDialogDelete = ref(false);
const clienteParaExcluir = ref(null);

const headers = [
  { title: "ID", key: "id", align: "start" },
  { title: "CLIENTE", key: "nome", align: "start" },
  { title: "TELEFONE", key: "telefone", align: "start" },
  { title: "ENDERECO", key: "endereco", align: "start" },
];

const irParaCriacao = () => {
  router.get("/clientes/criar");
};

const editarCliente = (cliente) => {
  router.get(`/clientes/${cliente.id}/editar`);
};

const prepararExclusao = (cliente) => {
  clienteParaExcluir.value = cliente;
  showDialogDelete.value = true;
};

const confirmarExclusao = () => {
  if (!clienteParaExcluir.value) return;

  router.delete(`/clientes/${clienteParaExcluir.value.id}`, {
    onFinish: () => {
      showDialogDelete.value = false;
      clienteParaExcluir.value = null;
    },
    preserveScroll: true,
  });
};
</script>

<style scoped>
.search-field {
  max-width: 360px;
}

.id-action-btn {
  min-width: 20px !important;
  width: 20px !important;
}
</style>
