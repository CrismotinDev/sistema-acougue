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

    <div class="dashboard-shell">
      <section class="hero-panel">
        <div class="hero-copy-block">
          <div class="eyebrow">Painel Operacional</div>
          <div class="hero-heading-row">
            <div class="hero-mark">
              <v-icon icon="mdi-view-dashboard-outline" size="26" color="#fffaf5" />
            </div>
            <div>
              <h1 class="hero-title">Pedidos sob controle</h1>
              <p class="hero-copy">
                Visualize prioridades, encontre clientes rapido e acompanhe o fluxo do
                dia.
              </p>
            </div>
          </div>
        </div>

        <div class="hero-metrics">
          <div class="hero-metric-card">
            <div class="hero-metric-top">
              <span class="hero-metric-label">Entregas hoje</span>
              <div class="hero-metric-icon">
                <v-icon icon="mdi-bike-fast" size="18" color="#fffaf5" />
              </div>
            </div>
            <strong class="hero-metric-value">{{ highlights.entregas_hoje }}</strong>
          </div>
          <div class="hero-metric-card">
            <div class="hero-metric-top">
              <span class="hero-metric-label">Retiradas hoje</span>
              <div class="hero-metric-icon">
                <v-icon icon="mdi-storefront-outline" size="18" color="#fffaf5" />
              </div>
            </div>
            <strong class="hero-metric-value">{{ highlights.retiradas_hoje }}</strong>
          </div>
          <div class="hero-metric-card">
            <div class="hero-metric-top">
              <span class="hero-metric-label">Pedidos hoje</span>
              <div class="hero-metric-icon">
                <v-icon
                  icon="mdi-clipboard-text-clock-outline"
                  size="18"
                  color="#fffaf5"
                />
              </div>
            </div>
            <strong class="hero-metric-value">{{ highlights.pedidos_hoje }}</strong>
          </div>
          <div class="hero-metric-card">
            <div class="hero-metric-top">
              <span class="hero-metric-label">Atrasados</span>
              <div class="hero-metric-icon">
                <v-icon icon="mdi-alert-circle-outline" size="18" color="#fffaf5" />
              </div>
            </div>
            <strong class="hero-metric-value">{{ highlights.atrasados }}</strong>
          </div>
        </div>
      </section>

      <v-row class="mb-6">
        <v-col cols="12">
          <v-card elevation="0" class="rounded-xl border-soft h-100">
            <div class="panel-header panel-header-border">
              <div>
                <div class="panel-eyebrow">Consulta Rapida</div>
                <div class="panel-title">Visualizacao dos pedidos</div>
              </div>
              <v-chip size="small" variant="outlined" color="grey-darken-1">
                {{ pedidosFiltrados.length }} de {{ pedidos.length }}
              </v-chip>
            </div>

            <div class="filters-wrap">
              <v-text-field
                v-model="busca"
                variant="solo-filled"
                flat
                hide-details
                rounded="lg"
                density="comfortable"
                bg-color="#F6EFEA"
                color="#8D021F"
                prepend-inner-icon="mdi-magnify"
                placeholder="Buscar por cliente, telefone, ID ou item"
                class="search-field"
              />

              <div class="filter-chip-group">
                <v-chip
                  v-for="filtro in filtros"
                  :key="filtro.value"
                  size="small"
                  class="filter-chip"
                  :color="filtroAtivo === filtro.value ? '#8D021F' : undefined"
                  :variant="filtroAtivo === filtro.value ? 'flat' : 'outlined'"
                  @click="filtroAtivo = filtro.value"
                >
                  {{ filtro.label }}
                </v-chip>
              </div>
            </div>

            <div v-if="!mobile" class="table-wrap">
              <v-data-table
                :headers="headers"
                :items="pedidosFiltrados"
                density="comfortable"
                hover
                flat
                items-per-page="-1"
              >
                <template #bottom />

                <template #item.id="{ item }">
                  <div class="d-flex align-center py-3 ga-2">
                    <v-menu location="bottom end">
                      <template #activator="{ props }">
                        <v-btn
                          icon="mdi-dots-vertical"
                          size="small"
                          variant="text"
                          color="grey-darken-2"
                          class="table-action-trigger"
                          v-bind="props"
                        />
                      </template>

                      <v-list density="compact" min-width="170" class="rounded-lg">
                        <v-list-item
                          prepend-icon="mdi-pencil-outline"
                          title="Editar pedido"
                          @click="editarPedido(item)"
                        />
                        <v-list-item
                          prepend-icon="mdi-printer-outline"
                          title="Imprimir pedido"
                          @click="imprimirPedido(item)"
                        />
                        <v-list-item
                          v-if="item.status === 'pendente'"
                          prepend-icon="mdi-check-circle-outline"
                          title="Finalizar pedido"
                          base-color="green-darken-2"
                          @click="prepararFinalizacao(item)"
                        />
                        <v-list-item
                          prepend-icon="mdi-trash-can-outline"
                          title="Excluir pedido"
                          base-color="red-darken-2"
                          @click="prepararExclusao(item)"
                        />
                      </v-list>
                    </v-menu>

                    <div>
                      <div class="font-weight-black text-grey-darken-4">
                        #{{ item.id }}
                      </div>
                      <div class="text-caption text-grey-darken-1">
                        Criado {{ formatarDataHoraCurta(item.created_at) }}
                      </div>
                    </div>
                  </div>
                </template>

                <template #item.cliente_nome="{ item }">
                  <div class="d-flex flex-column py-3">
                    <span class="font-weight-bold text-grey-darken-4">
                      {{ item.cliente_nome }}
                    </span>
                    <span class="text-caption text-grey-darken-1">
                      {{ item.cliente_telefone || "Sem telefone" }}
                    </span>
                  </div>
                </template>

                <template #item.entrega="{ item }">
                  <div class="d-flex flex-column py-3">
                    <div class="d-flex align-center ga-2 flex-wrap">
                      <span class="text-grey-darken-4">
                        {{ formatarData(item.data_entrega) }} as {{ item.hora_entrega }}
                      </span>
                      <v-chip
                        size="x-small"
                        :color="item.is_atrasado ? 'red-darken-2' : '#8D021F'"
                        variant="tonal"
                      >
                        {{ item.periodo_label }}
                      </v-chip>
                    </div>
                    <span class="text-caption text-grey-darken-1">
                      {{ labelTipoEntrega(item.tipo_entrega) }}
                      <span v-if="item.endereco_entrega"
                        >- {{ item.endereco_entrega }}</span
                      >
                    </span>
                  </div>
                </template>

                <template #item.itens_resumo="{ item }">
                  <div class="d-flex flex-column py-3">
                    <span class="font-weight-medium text-grey-darken-4">
                      {{ item.resumo_itens || "Sem itens" }}
                    </span>
                    <span class="text-caption text-grey-darken-1">
                      {{ item.total_itens }} item(ns) no pedido
                    </span>
                  </div>
                </template>

                <template #item.status="{ item }">
                  <v-chip
                    :color="statusColor(item)"
                    variant="tonal"
                    size="small"
                  >
                    {{ statusLabel(item) }}
                  </v-chip>
                </template>

                <template #no-data>
                  <div class="pa-10 text-center">
                    <v-icon size="40" color="grey-lighten-1">mdi-cart-off</v-icon>
                    <div class="text-subtitle-2 text-grey-darken-2 mt-3">
                      Nenhum pedido encontrado.
                    </div>
                    <div class="text-body-2 text-grey mt-1">
                      Ajuste a busca ou o filtro para localizar outro pedido.
                    </div>
                  </div>
                </template>
              </v-data-table>
            </div>

            <div v-else class="mobile-orders">
              <div v-if="pedidosFiltrados.length" class="mobile-order-list">
                <article
                  v-for="pedido in pedidosFiltrados"
                  :key="pedido.id"
                  class="mobile-order-card"
                >
                  <div class="d-flex align-start justify-space-between ga-3">
                    <div>
                      <div class="text-overline">Pedido #{{ pedido.id }}</div>
                      <div class="text-subtitle-1 font-weight-bold text-grey-darken-4">
                        {{ pedido.cliente_nome }}
                      </div>
                      <div class="text-caption text-grey-darken-1">
                        {{ pedido.cliente_telefone || "Sem telefone" }}
                      </div>
                    </div>

                    <v-chip
                      size="small"
                      :color="statusColor(pedido)"
                      variant="tonal"
                    >
                      {{ statusLabel(pedido) }}
                    </v-chip>
                  </div>

                  <div class="mobile-order-meta">
                    <span
                      >{{ formatarData(pedido.data_entrega) }} as
                      {{ pedido.hora_entrega }}</span
                    >
                    <span>{{ labelTipoEntrega(pedido.tipo_entrega) }}</span>
                    <span>{{ pedido.total_itens }} item(ns)</span>
                  </div>

                  <div class="text-body-2 text-grey-darken-3 mt-3">
                    {{ pedido.resumo_itens || "Sem itens cadastrados." }}
                  </div>

                  <div class="mobile-order-actions">
                    <v-btn
                      size="small"
                      variant="tonal"
                      color="#8D021F"
                      @click="editarPedido(pedido)"
                    >
                      Editar
                    </v-btn>
                    <v-btn
                      size="small"
                      variant="outlined"
                      color="#8D021F"
                      @click="imprimirPedido(pedido)"
                    >
                      Imprimir
                    </v-btn>
                    <v-btn
                      size="small"
                      variant="text"
                      color="red-darken-2"
                      @click="prepararExclusao(pedido)"
                    >
                      Excluir
                    </v-btn>
                  </div>
                </article>
              </div>

              <div v-else class="empty-panel">
                <v-icon size="40" color="grey-lighten-1">mdi-magnify-close</v-icon>
                <div class="text-subtitle-2 text-grey-darken-2 mt-3">
                  Nenhum pedido encontrado.
                </div>
                <div class="text-body-2 text-grey mt-1">
                  Tente mudar o filtro ou pesquisar outro cliente.
                </div>
              </div>
            </div>
          </v-card>
        </v-col>
      </v-row>
    </div>

    <DialogConfirm
      v-model="showDialogDelete"
      title="Confirmar Exclusao"
      :message="`Deseja realmente excluir o pedido #${pedidoParaExcluir?.id}?`"
      @confirm="confirmarExclusao"
    />

    <DialogConfirm
      v-model="showDialogFinish"
      title="Finalizar Pedido"
      :message="`Deseja finalizar o pedido #${pedidoParaFinalizar?.id}?`"
      @confirm="confirmarFinalizacao"
    />
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DialogConfirm from "@/Components/DialogConfirm.vue";
import { router } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { useDisplay } from "vuetify";

const props = defineProps({
  stats: {
    type: Array,
    default: () => [],
  },
  highlights: {
    type: Object,
    default: () => ({
      entregas_hoje: 0,
      retiradas_hoje: 0,
      pedidos_hoje: 0,
      atrasados: 0,
    }),
  },
  pedidos: {
    type: Array,
    default: () => [],
  },
});

const { mobile } = useDisplay();

const showDialogDelete = ref(false);
const showDialogFinish = ref(false);
const pedidoParaExcluir = ref(null);
const pedidoParaFinalizar = ref(null);
const busca = ref("");
const filtroAtivo = ref("todos");

const headers = [
  { title: "PEDIDO", key: "id", align: "start", sortable: false },
  { title: "CLIENTE", key: "cliente_nome", align: "start" },
  { title: "ENTREGA", key: "entrega", align: "start", sortable: false },
  { title: "ITENS", key: "itens_resumo", align: "start", sortable: false },
  { title: "STATUS", key: "status", align: "start", sortable: false },
];

const filtros = [
  { label: "Todos", value: "todos" },
  { label: "Hoje", value: "hoje" },
  { label: "Atrasados", value: "atrasados" },
  { label: "Entrega", value: "entrega" },
  { label: "Retirada", value: "retirada" },
];

const hoje = new Date().toISOString().slice(0, 10);

const normalizarTexto = (valor) =>
  (valor ?? "")
    .toString()
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .toLowerCase();

const pedidosFiltrados = computed(() => {
  const termo = normalizarTexto(busca.value);

  return props.pedidos.filter((pedido) => {
    const combinaFiltro =
      filtroAtivo.value === "todos" ||
      (filtroAtivo.value === "hoje" && pedido.data_entrega === hoje) ||
      (filtroAtivo.value === "atrasados" && pedido.is_atrasado) ||
      (filtroAtivo.value === "entrega" && pedido.tipo_entrega === "entrega") ||
      (filtroAtivo.value === "retirada" && pedido.tipo_entrega === "retirada");

    if (!combinaFiltro) {
      return false;
    }

    if (!termo) {
      return true;
    }

    const textoPesquisa = normalizarTexto(
      [
        pedido.id,
        pedido.cliente_nome,
        pedido.cliente_telefone,
        pedido.tipo_entrega,
        pedido.status,
        pedido.resumo_itens,
        pedido.endereco_entrega,
        ...(pedido.itens ?? []).map((item) => item.produto_nome),
      ].join(" ")
    );

    return textoPesquisa.includes(termo);
  });
});

const formatarData = (data) => {
  if (!data) return "";

  return new Intl.DateTimeFormat("pt-BR", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
  }).format(new Date(`${data}T00:00:00`));
};

const formatarDataHoraCurta = (dataHora) => {
  if (!dataHora) return "";

  return new Intl.DateTimeFormat("pt-BR", {
    day: "2-digit",
    month: "2-digit",
    hour: "2-digit",
    minute: "2-digit",
  }).format(new Date(dataHora));
};

const capitalize = (texto) => {
  if (!texto) return "";
  return texto.charAt(0).toUpperCase() + texto.slice(1);
};

const statusLabel = (pedido) => {
  if (pedido.is_atrasado) return "Atrasado";
  if (pedido.status === "concluido") return "Concluido";
  return capitalize(pedido.status);
};

const statusColor = (pedido) => {
  if (pedido.is_atrasado) return "red-darken-2";
  if (pedido.status === "concluido") return "green-darken-2";
  return "grey-darken-1";
};

const labelTipoEntrega = (tipo) => (tipo === "entrega" ? "Entrega" : "Retirada");

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

const prepararFinalizacao = (pedido) => {
  pedidoParaFinalizar.value = pedido;
  showDialogFinish.value = true;
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

const confirmarFinalizacao = () => {
  if (!pedidoParaFinalizar.value) return;

  router.patch(`/pedidos/${pedidoParaFinalizar.value.id}/finalizar`, {}, {
    onFinish: () => {
      showDialogFinish.value = false;
      pedidoParaFinalizar.value = null;
    },
    preserveScroll: true,
  });
};
</script>

<style scoped>
.dashboard-shell {
  display: flex;
  flex-direction: column;
  gap: 18px;
}

.hero-panel {
  display: grid;
  grid-template-columns: minmax(0, 1.4fr) minmax(0, 1fr);
  gap: 16px;
  padding: 20px 22px;
  border-radius: 24px;
  background: radial-gradient(
      circle at top left,
      rgba(255, 214, 153, 0.35),
      transparent 34%
    ),
    linear-gradient(135deg, #4a0404 0%, #8d021f 52%, #c2410c 100%);
  color: #fffaf5;
  box-shadow: 0 18px 42px rgba(74, 4, 4, 0.16);
}

.hero-copy-block {
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.hero-heading-row {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  margin-top: 8px;
}

.hero-mark {
  display: grid;
  place-items: center;
  width: 48px;
  height: 48px;
  flex-shrink: 0;
  border-radius: 16px;
  background: rgba(255, 248, 240, 0.14);
  border: 1px solid rgba(255, 248, 240, 0.18);
}

.eyebrow {
  font-size: 0.7rem;
  font-weight: 800;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  opacity: 0.8;
}

.hero-title {
  margin: 0;
  font-size: clamp(1.5rem, 2vw, 2.2rem);
  line-height: 1.02;
  font-weight: 900;
}

.hero-copy {
  max-width: 42ch;
  color: rgba(255, 250, 245, 0.85);
  margin: 8px 0 0;
  font-size: 0.95rem;
}

.hero-metrics {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 10px;
  align-self: stretch;
}

.hero-metric-card {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  min-height: 88px;
  padding: 14px;
  border-radius: 18px;
  background: rgba(255, 248, 240, 0.12);
  border: 1px solid rgba(255, 248, 240, 0.16);
  backdrop-filter: blur(12px);
}

.hero-metric-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
}

.hero-metric-label {
  font-size: 0.78rem;
  color: rgba(255, 250, 245, 0.78);
}

.hero-metric-icon {
  display: grid;
  place-items: center;
  width: 30px;
  height: 30px;
  border-radius: 10px;
  background: rgba(255, 250, 245, 0.1);
}

.hero-metric-value {
  font-size: 1.7rem;
  font-weight: 900;
  line-height: 1;
  margin-top: 10px;
}

.stats-row {
  margin-top: -2px;
}

.stat-card {
  height: 100%;
  padding: 16px 18px;
  border: 1px solid rgba(141, 2, 31, 0.08);
  background: linear-gradient(180deg, #ffffff 0%, #fff8f5 100%);
  box-shadow: 0 10px 24px rgba(15, 23, 42, 0.045);
  position: relative;
  overflow: hidden;
  min-height: 108px;
}

.stat-card::before {
  content: "";
  position: absolute;
  inset: 0 auto 0 0;
  width: 6px;
  background: var(--card-accent);
}

.stat-content {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.stat-title {
  font-size: 0.74rem;
  line-height: 1.2;
}

.stat-value {
  font-size: 1.7rem;
  font-weight: 900;
  line-height: 1;
}

.stat-description {
  font-size: 0.8rem;
  line-height: 1.3;
}

.stat-icon-wrap {
  display: grid;
  place-items: center;
  width: 40px;
  height: 40px;
  border-radius: 14px;
  background: rgba(255, 244, 237, 1);
  flex-shrink: 0;
}

.border-soft {
  border: 1px solid rgba(15, 23, 42, 0.08);
  background: #ffffff;
  box-shadow: 0 16px 40px rgba(15, 23, 42, 0.05);
}

.panel-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  padding: 20px 22px;
}

.panel-header-border {
  border-bottom: 1px solid rgba(15, 23, 42, 0.08);
}

.panel-eyebrow {
  font-size: 0.72rem;
  font-weight: 900;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: #8d021f;
}

.panel-title {
  font-size: 1.1rem;
  font-weight: 800;
  color: #1f2937;
}

.queue-list {
  padding: 0 12px 16px;
}

.queue-item {
  width: 100%;
  display: grid;
  grid-template-columns: 84px minmax(0, 1fr);
  gap: 16px;
  align-items: center;
  text-align: left;
  padding: 14px;
  border: 0;
  background: transparent;
  border-radius: 18px;
  transition: background-color 0.18s ease, transform 0.18s ease;
}

.queue-item:hover {
  background: #f8f4f1;
  transform: translateY(-1px);
}

.queue-time {
  display: grid;
  place-items: center;
  min-height: 64px;
  border-radius: 18px;
  background: #fdf0ea;
  color: #8d021f;
  font-size: 1.15rem;
  font-weight: 900;
}

.queue-time-late {
  background: #fee2e2;
  color: #b91c1c;
}

.queue-content {
  min-width: 0;
}

.queue-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  font-size: 0.82rem;
  color: #6b7280;
}

.filters-wrap {
  display: flex;
  flex-direction: column;
  gap: 14px;
  padding: 18px 22px 0;
}

.search-field :deep(.v-field) {
  box-shadow: none !important;
}

.filter-chip-group {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.filter-chip {
  font-weight: 700;
}

.table-wrap {
  padding: 6px 8px 12px;
}

.mobile-orders {
  padding: 10px 16px 18px;
}

.mobile-order-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.mobile-order-card {
  padding: 18px;
  border-radius: 22px;
  background: linear-gradient(180deg, #ffffff 0%, #fff8f5 100%);
  border: 1px solid rgba(141, 2, 31, 0.08);
}

.mobile-order-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-top: 14px;
  font-size: 0.8rem;
  color: #6b7280;
}

.mobile-order-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 16px;
}

.empty-panel {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 34px 24px 40px;
  color: #374151;
}

.table-action-trigger {
  min-width: 32px !important;
  width: 32px !important;
}

@media (max-width: 1279px) {
  .hero-panel {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 767px) {
  .dashboard-shell {
    gap: 16px;
  }

  .hero-panel {
    padding: 18px 16px;
    border-radius: 20px;
  }

  .hero-metrics {
    grid-template-columns: 1fr 1fr;
  }

  .hero-heading-row {
    gap: 12px;
  }

  .hero-mark {
    width: 42px;
    height: 42px;
    border-radius: 14px;
  }

  .panel-header,
  .filters-wrap {
    padding-left: 16px;
    padding-right: 16px;
  }

  .queue-item {
    grid-template-columns: 72px minmax(0, 1fr);
    gap: 12px;
    padding: 12px;
  }

  .stat-card {
    min-height: 96px;
  }

  .stat-description {
    font-size: 0.76rem;
  }
}
</style>
