<template>
  <AuthenticatedLayout>
    <v-container fluid class="pa-2 pa-md-4 scrollable-content">
      <v-card border elevation="2" class="rounded-lg overflow-hidden">
        <v-toolbar color="white" flat class="px-4 border-b" height="40">
          <span
            class="text-subtitle-2 font-weight-black text-red-darken-4 text-uppercase"
          >
            Novo Pedido
          </span>
        </v-toolbar>

        <v-form class="pa-3 pa-md-6">
          <div class="compact-section-title">Identificação do Cliente</div>
          <v-row dense class="mb-2">
            <v-col cols="12" md="4" class="mb-1">
              <v-autocomplete
                label="Selecionar da base"
                :items="['João Silva', 'Maria Oliveira']"
                variant="outlined"
                color="#8D021F"
                density="compact"
                hide-details
              ></v-autocomplete>
            </v-col>
            <v-col cols="12" md="5" class="mb-1">
              <v-text-field
                label="Nome do Novo Cliente"
                variant="outlined"
                color="#8D021F"
                density="compact"
                hide-details
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="3">
              <v-text-field
                label="WhatsApp"
                variant="outlined"
                color="#8D021F"
                density="compact"
                hide-details
              ></v-text-field>
            </v-col>
          </v-row>

          <v-divider class="my-4"></v-divider>

          <div class="compact-section-title">Itens do Pedido</div>
          <v-card
            color="red-lighten-5"
            variant="flat"
            class="pa-3 rounded-lg border-red mb-4"
          >
            <v-row dense align="center">
              <v-col cols="12" md="5">
                <v-select
                  label="Produto"
                  :items="['Picanha', 'Alcatra', 'Costela']"
                  v-model="novoItem.produto"
                  variant="outlined"
                  bg-color="white"
                  density="compact"
                  hide-details
                ></v-select>
              </v-col>
              <v-col cols="4" md="2">
                <v-text-field
                  label="Kg"
                  v-model="novoItem.quantidade"
                  type="number"
                  variant="outlined"
                  bg-color="white"
                  density="compact"
                  hide-details
                ></v-text-field>
              </v-col>
              <v-col cols="8" md="4">
                <v-text-field
                  label="Observações"
                  v-model="novoItem.obs"
                  variant="outlined"
                  bg-color="white"
                  density="compact"
                  hide-details
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="1">
                <v-btn
                  color="#8D021F"
                  block
                  height="40"
                  theme="dark"
                  @click="adicionarItem"
                  :disabled="!novoItem.produto || !novoItem.quantidade"
                >
                  <v-icon>mdi-plus</v-icon>
                </v-btn>
              </v-col>
            </v-row>
          </v-card>

          <v-expand-transition>
            <div v-if="itensPedido.length > 0" class="mb-6">
              <v-table density="compact" class="border rounded-lg">
                <thead>
                  <tr class="bg-grey-lighten-4">
                    <th class="text-left text-caption font-weight-bold">Produto</th>
                    <th class="text-center text-caption font-weight-bold">Qtd</th>
                    <th class="text-left text-caption font-weight-bold">Obs</th>
                    <th class="text-right"></th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in itensPedido" :key="index">
                    <td class="text-caption">{{ item.produto }}</td>
                    <td class="text-center text-caption">{{ item.quantidade }}kg</td>
                    <td class="text-caption text-grey">{{ item.obs || "-" }}</td>
                    <td class="text-right">
                      <v-btn
                        icon="mdi-trash-can-outline"
                        variant="text"
                        color="red"
                        size="small"
                        @click="removerItem(index)"
                      ></v-btn>
                    </td>
                  </tr>
                </tbody>
              </v-table>
            </div>
          </v-expand-transition>

          <div class="compact-section-title">Agendamento</div>
          <v-row dense class="mb-6">
            <v-col cols="6" md="3" class="pr-1">
              <v-text-field
                label="Data"
                type="date"
                variant="outlined"
                density="compact"
                hide-details
                v-model="dataPedido"
              ></v-text-field>
            </v-col>
            <v-col cols="6" md="3">
              <v-text-field
                label="Hora"
                type="time"
                variant="outlined"
                density="compact"
                hide-details
                v-model="horaPedido"
              ></v-text-field>
            </v-col>
          </v-row>

          <v-row dense class="mt-2" justify="end">
            <v-col cols="12" md="2">
              <v-btn
                block
                color="#8D021F"
                height="36"
                class="text-caption font-weight-bold"
                rounded="md"
              >
                <v-icon start size="small">mdi-printer</v-icon>
                Imprimir
              </v-btn>
            </v-col>

            <v-col cols="6" md="2">
              <v-btn
                block
                variant="outlined"
                color="orange-darken-4"
                height="36"
                class="text-caption font-weight-bold"
                rounded="md"
                @click="showDialogLimpar = true"
                :disabled="itensPedido.length === 0"
              >
                Limpar
              </v-btn>
            </v-col>
          </v-row>
        </v-form>
      </v-card>
    </v-container>

    <DialogConfirm
      v-model="showDialogLimpar"
      title="Limpar Pedido"
      message="Deseja realmente remover todos os itens da lista? Esta ação não pode ser desfeita."
      @confirm="confirmarLimpar"
    />
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DialogConfirm from "@/Components/DialogConfirm.vue";
import { router } from "@inertiajs/vue3";
import { ref } from "vue";

// Datas iniciais
const dataHoje = new Date().toISOString().substr(0, 10);
const horaAgora = new Date().toLocaleTimeString("pt-BR", {
  hour: "2-digit",
  minute: "2-digit",
});

// Estados Reativos
const dataPedido = ref(dataHoje);
const horaPedido = ref(horaAgora);
const itensPedido = ref([]);
const showDialogLimpar = ref(false);

const novoItem = ref({
  produto: null,
  quantidade: "",
  obs: "",
});

// Funções de Negócio
const adicionarItem = () => {
  if (novoItem.value.produto && novoItem.value.quantidade) {
    itensPedido.value.push({ ...novoItem.value });
    novoItem.value.produto = null;
    novoItem.value.quantidade = "";
    novoItem.value.obs = "";
  }
};

const removerItem = (index) => {
  itensPedido.value.splice(index, 1);
};

const confirmarLimpar = () => {
  itensPedido.value = [];
  showDialogLimpar.value = false;
};
</script>

<style scoped>
/* Estilos mantidos conforme sua versão anterior */
:deep(.v-label) {
  font-size: 0.75rem !important;
  font-weight: 700 !important;
}
:deep(.v-field__input) {
  font-size: 0.85rem !important;
  min-height: 32px !important;
}
.compact-section-title {
  font-size: 0.7rem;
  font-weight: 900;
  text-transform: uppercase;
  color: #8d021f;
  margin-bottom: 4px;
}
.border-red {
  border: 1px solid rgba(141, 2, 31, 0.15) !important;
}
.scrollable-content {
  max-height: calc(100vh - 48px);
  overflow-y: auto;
}
</style>
