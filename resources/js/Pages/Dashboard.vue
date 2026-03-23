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

    <v-row class="mb-6">
      <v-col v-for="card in stats" :key="card.title" cols="12" sm="6" md="3">
        <v-card
          elevation="0"
          class="rounded-lg pa-4 border shadow-sm"
          :style="`border-left: 6px solid ${card.hexColor} !important`"
        >
          <div class="text-caption text-grey-darken-1 text-uppercase">
            {{ card.title }}
          </div>
          <div class="text-h5 font-weight-black mt-2 text-grey-darken-4">
            {{ card.value }}
          </div>
        </v-card>
      </v-col>
    </v-row>

    <v-card elevation="0" class="rounded-lg border shadow-sm">
      <v-toolbar color="white" flat class="px-4 border-b">
        <v-toolbar-title class="text-subtitle-1 font-weight-bold text-grey-darken-4">
          Pedidos Recentes
        </v-toolbar-title>
      </v-toolbar>

      <v-data-table :headers="headers" :items="pedidos" density="comfortable" hover flat>
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
                  prepend-icon="mdi-printer-outline"
                  title="Imprimir"
                  @click="imprimirPedido(item)"
                />
                <v-list-item
                  prepend-icon="mdi-pencil-outline"
                  title="Editar"
                  @click="editarPedido(item)"
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

        <template #item.cliente_nome="{ item }">
          <div class="d-flex flex-column py-2">
            <span class="font-weight-bold text-grey-darken-4">
              {{ item.cliente_nome }}
            </span>
            <span class="text-caption text-grey-darken-1">
              {{ item.cliente_telefone || "Sem telefone" }}
            </span>
          </div>
        </template>

        <template #item.entrega="{ item }">
          <div class="d-flex flex-column py-2">
            <span class="text-grey-darken-4">{{ formatarData(item.data_entrega) }}</span>
            <span class="text-caption text-grey-darken-1">{{ item.hora_entrega }}</span>
          </div>
        </template>

        <template #item.status="{ item }">
          <v-chip color="#8D021F" variant="tonal" size="small">
            {{ item.status }}
          </v-chip>
        </template>

        <template #item.total_itens="{ item }">
          <span class="font-weight-medium">{{ item.total_itens }}</span>
        </template>

        <template #no-data>
          <div class="pa-10 text-center">
            <v-icon size="40" color="grey-lighten-1">mdi-cart-off</v-icon>
            <div class="text-subtitle-2 text-grey-darken-2 mt-3">
              Nenhum pedido salvo ainda.
            </div>
            <div class="text-body-2 text-grey mt-1">
              Crie o primeiro pedido para acompanhar o movimento no dashboard.
            </div>
          </div>
        </template>
      </v-data-table>
    </v-card>

    <DialogConfirm
      v-model="showDialogDelete"
      title="Confirmar Exclusao"
      :message="`Deseja realmente excluir o pedido #${pedidoParaExcluir?.id}?`"
      @confirm="confirmarExclusao"
    />
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DialogConfirm from "@/Components/DialogConfirm.vue";
import { router } from "@inertiajs/vue3";
import { ref } from "vue";

defineProps({
  stats: {
    type: Array,
    default: () => [],
  },
  pedidos: {
    type: Array,
    default: () => [],
  },
});

const showDialogDelete = ref(false);
const pedidoParaExcluir = ref(null);

const headers = [
  { title: "ID", key: "id", align: "start" },
  { title: "CLIENTE", key: "cliente_nome", align: "start" },
  { title: "ENTREGA", key: "entrega", align: "start", sortable: false },

  { title: "ITENS", key: "total_itens", align: "center" },
  { title: "STATUS", key: "status", align: "start" },
];

const formatarData = (data) => {
  if (!data) return "";

  const [ano, mes, dia] = data.split("-");
  return `${dia}/${mes}/${ano}`;
};

const editarPedido = (pedido) => {
  router.get(`/pedidos/${pedido.id}/editar`);
};

const imprimirPedido = (pedido) => {
  router.get(`/pedidos/${pedido.id}/imprimir`);
};

const prepararExclusao = (pedido) => {
  pedidoParaExcluir.value = pedido;
  showDialogDelete.value = true;
};

const confirmarExclusao = () => {
  if (!pedidoParaExcluir.value) return;

  router.delete(`/pedidos/${pedidoParaExcluir.value.id}`, {
    onFinish: () => {
      showDialogDelete.value = false;
      pedidoParaExcluir.value = null;
    },
    preserveScroll: true,
  });
};
</script>

<style scoped>
.id-action-btn {
  min-width: 20px !important;
  width: 20px !important;
}
</style>
