<template>
  <AuthenticatedLayout>
    <v-container fluid class="pa-2 pa-md-4 scrollable-content">
      <v-card border elevation="2" class="rounded-lg overflow-hidden">
        <v-toolbar color="white" flat class="px-4 border-b" height="40">
          <span
            class="text-subtitle-2 font-weight-black text-red-darken-4 text-uppercase"
          >
            {{ isEditing ? "Editar Pedido" : "Novo Pedido" }}
          </span>
        </v-toolbar>

        <v-form class="pa-3 pa-md-6" @submit.prevent="salvarPedido">
          <div class="compact-section-title">Identificacao do Cliente</div>
          <v-row dense class="mb-2">
            <v-col cols="12" md="4" class="mb-1">
              <v-autocomplete
                v-model="clienteSelecionadoId"
                label="Selecionar da base"
                :items="clientesCadastrados"
                item-title="nome"
                item-value="id"
                variant="outlined"
                color="#8D021F"
                density="compact"
                hide-details
                @update:model-value="preencherClienteSelecionado"
              />
            </v-col>

            <v-col cols="12" md="5" class="mb-1">
              <v-text-field
                v-model="nomeCliente"
                label="Nome do Cliente"
                variant="outlined"
                color="#8D021F"
                density="compact"
                :error-messages="form.errors.cliente_nome"
              />
            </v-col>

            <v-col cols="12" md="3">
              <v-text-field
                v-model="telefoneCliente"
                label="WhatsApp"
                variant="outlined"
                color="#8D021F"
                density="compact"
                :error-messages="form.errors.cliente_telefone"
              />
            </v-col>
          </v-row>

          <v-row dense class="mb-2">
            <v-col cols="12" md="3" class="mb-1">
              <v-select
                v-model="tipoEntrega"
                label="Tipo"
                :items="tiposEntrega"
                item-title="label"
                item-value="value"
                variant="outlined"
                color="#8D021F"
                density="compact"
                :error-messages="form.errors.tipo_entrega"
              />
            </v-col>

            <v-col cols="12" md="9">
              <v-text-field
                v-model="enderecoEntrega"
                label="Endereco"
                variant="outlined"
                color="#8D021F"
                density="compact"
                :disabled="tipoEntrega === 'retirada'"
                :error-messages="form.errors.endereco_entrega"
              />
            </v-col>
          </v-row>

          <v-divider class="my-4" />

          <div class="compact-section-title">Itens do Pedido</div>
          <v-card
            color="red-lighten-5"
            variant="flat"
            class="pa-3 rounded-lg border-red mb-4"
          >
            <v-row dense align="center">
              <v-col cols="12" md="5">
                <v-autocomplete
                  v-model="novoItem.produto"
                  label="Produto"
                  :items="produtosCadastrados"
                  item-title="nome"
                  item-value="id"
                  variant="outlined"
                  bg-color="white"
                  density="compact"
                  hide-details
                  clearable
                />
              </v-col>

              <v-col cols="4" md="2">
                <v-text-field
                  v-model="novoItem.quantidade"
                  label="Kg"
                  type="number"
                  variant="outlined"
                  bg-color="white"
                  density="compact"
                  hide-details
                  step="0.01"
                />
              </v-col>

              <v-col cols="8" md="4">
                <v-text-field
                  v-model="novoItem.obs"
                  label="Observacoes"
                  variant="outlined"
                  bg-color="white"
                  density="compact"
                  hide-details
                />
              </v-col>

              <v-col cols="12" md="1">
                <v-btn
                  color="#8D021F"
                  block
                  height="40"
                  theme="dark"
                  :disabled="!novoItem.produto || !novoItem.quantidade"
                  @click="adicionarItem"
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
                    <td class="text-caption">{{ item.produtoNome }}</td>
                    <td class="text-center" style="max-width: 110px">
                      <v-text-field
                        v-model="item.quantidade"
                        type="number"
                        density="compact"
                        variant="outlined"
                        hide-details
                        bg-color="white"
                        step="0.01"
                        class="item-edit-field"
                      />
                    </td>
                    <td style="min-width: 220px">
                      <v-text-field
                        v-model="item.obs"
                        density="compact"
                        variant="outlined"
                        hide-details
                        bg-color="white"
                        placeholder="Observacoes"
                        class="item-edit-field"
                      />
                    </td>
                    <td class="text-right">
                      <v-btn
                        icon="mdi-trash-can-outline"
                        variant="text"
                        color="red"
                        size="small"
                        @click="removerItem(index)"
                      />
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
                v-model="dataPedido"
                label="Data"
                type="date"
                variant="outlined"
                density="compact"
                :error-messages="form.errors.data_entrega"
              />
            </v-col>

            <v-col cols="6" md="3">
              <v-text-field
                v-model="horaPedido"
                label="Hora"
                type="time"
                variant="outlined"
                density="compact"
                :error-messages="form.errors.hora_entrega"
              />
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
                :loading="form.processing"
                :disabled="itensPedido.length === 0"
                @click="imprimirPedido"
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
                :disabled="itensPedido.length === 0"
                @click="showDialogLimpar = true"
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
      message="Deseja realmente remover todos os itens da lista? Esta acao nao pode ser desfeita."
      @confirm="confirmarLimpar"
    />
  </AuthenticatedLayout>
</template>

<script setup>
import DialogConfirm from "@/Components/DialogConfirm.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm } from "@inertiajs/vue3";
import { computed, nextTick, onMounted, ref, watch } from "vue";

const props = defineProps({
  clientesCadastrados: {
    type: Array,
    default: () => [],
  },
  produtosCadastrados: {
    type: Array,
    default: () => [],
  },
  pedido: {
    type: Object,
    default: null,
  },
  imprimir: {
    type: Boolean,
    default: false,
  },
});

const montarEnderecoCliente = (cliente) =>
  cliente?.endereco_completo || cliente?.endereco || "";

const dataHoje = new Date().toISOString().slice(0, 10);
const horaAgora = new Date().toLocaleTimeString("pt-BR", {
  hour: "2-digit",
  minute: "2-digit",
});

const dataPedido = ref(dataHoje);
const horaPedido = ref(horaAgora);
const itensPedido = ref([]);
const showDialogLimpar = ref(false);
const clienteSelecionadoId = ref(null);
const nomeCliente = ref("");
const telefoneCliente = ref("");
const tipoEntrega = ref("retirada");
const enderecoEntrega = ref("");

const form = useForm({
  cliente_id: null,
  cliente_nome: "",
  cliente_telefone: "",
  tipo_entrega: "retirada",
  endereco_entrega: "",
  data_entrega: dataHoje,
  hora_entrega: horaAgora,
  itens: [],
});

const isEditing = computed(() => Boolean(props.pedido?.id));

const novoItem = ref({
  produto: null,
  quantidade: "",
  obs: "",
});
const impressaoDisparada = ref(false);
const tiposEntrega = [
  { label: "Retirada", value: "retirada" },
  { label: "Entrega", value: "entrega" },
];

const preencherPedido = (pedido) => {
  clienteSelecionadoId.value = pedido?.cliente_id ?? null;
  nomeCliente.value = pedido?.cliente_nome ?? "";
  telefoneCliente.value = pedido?.cliente_telefone ?? "";
  tipoEntrega.value = pedido?.tipo_entrega ?? "retirada";
  enderecoEntrega.value = pedido?.endereco_entrega ?? "";
  dataPedido.value = pedido?.data_entrega ?? dataHoje;
  horaPedido.value = pedido?.hora_entrega ?? horaAgora;
  itensPedido.value = (pedido?.itens ?? []).map((item) => ({
    id: item.id ?? null,
    produto: item.produto ?? item.produto_id ?? null,
    produtoNome: item.produtoNome ?? item.produto_nome ?? "",
    quantidade: item.quantidade?.toString() ?? "",
    obs: item.obs ?? item.observacoes ?? "",
  }));
  novoItem.value = {
    produto: null,
    quantidade: "",
    obs: "",
  };
  form.clearErrors();
};

const preencherClienteSelecionado = (clienteId) => {
  const cliente = props.clientesCadastrados.find(({ id }) => id === clienteId);

  if (!cliente) {
    if (clienteId === null) {
      telefoneCliente.value = "";
    }
    return;
  }

  nomeCliente.value = cliente.nome;
  telefoneCliente.value = cliente.telefone;
  if (tipoEntrega.value === "entrega") {
    enderecoEntrega.value = montarEnderecoCliente(cliente);
  }
};

const adicionarItem = () => {
  if (!novoItem.value.produto || !novoItem.value.quantidade) {
    return;
  }

  const produtoSelecionado = props.produtosCadastrados.find(
    ({ id }) => id === novoItem.value.produto
  );

  itensPedido.value.push({
    ...novoItem.value,
    produtoNome: produtoSelecionado?.nome ?? "",
  });

  novoItem.value.produto = null;
  novoItem.value.quantidade = "";
  novoItem.value.obs = "";
};

const removerItem = (index) => {
  itensPedido.value.splice(index, 1);
};

const confirmarLimpar = () => {
  itensPedido.value = [];
  showDialogLimpar.value = false;
};

watch(
  () => props.pedido,
  (pedido) => {
    preencherPedido(pedido);
  },
  { immediate: true }
);

onMounted(() => {
  preencherPedido(props.pedido);
});

watch(
  () => props.imprimir,
  async (deveImprimir) => {
    if (!deveImprimir || impressaoDisparada.value === true) {
      return;
    }

    impressaoDisparada.value = true;
    await nextTick();
    window.print();
  },
  { immediate: true }
);

const salvarPedido = () => {
  form.cliente_id = clienteSelecionadoId.value;
  form.cliente_nome = nomeCliente.value;
  form.cliente_telefone = telefoneCliente.value;
  form.tipo_entrega = tipoEntrega.value;
  form.endereco_entrega =
    tipoEntrega.value === "entrega" ? enderecoEntrega.value : null;
  form.data_entrega = dataPedido.value;
  form.hora_entrega = horaPedido.value;
  form.itens = itensPedido.value.map((item) => ({
    produto_id: item.produto,
    produto_nome: item.produtoNome,
    quantidade: item.quantidade,
    observacoes: item.obs,
  }));

  if (isEditing.value) {
    form.put(`/pedidos/${props.pedido.id}`, {
      preserveScroll: true,
    });
    return;
  }

  form.post("/pedidos", {
    preserveScroll: true,
  });
};

const imprimirPedido = () => {
  salvarPedido();
};

watch(tipoEntrega, (tipo) => {
  if (tipo === "retirada") {
    enderecoEntrega.value = "";
  } else if (clienteSelecionadoId.value) {
    const cliente = props.clientesCadastrados.find(
      ({ id }) => id === clienteSelecionadoId.value
    );
    enderecoEntrega.value = montarEnderecoCliente(cliente) || enderecoEntrega.value;
  }
});
</script>

<style scoped>
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

.item-edit-field {
  min-width: 90px;
}
</style>
