<template>
  <div class="receipt-page">
    <div class="receipt-actions no-print">
      <button class="action-btn secondary" @click="voltar">Voltar</button>
      <button class="action-btn primary" @click="imprimirNovamente">Imprimir</button>
    </div>

    <div class="receipt">
      <div class="center strong">ACOUGUE</div>
      <div class="center">PEDIDO #{{ pedido.id }}</div>
      <div class="center">
        {{ formatarData(pedido.criado_em_data) }} {{ pedido.criado_em_hora }}
      </div>

      <div class="divider"></div>

      <div><strong>CLIENTE:</strong> {{ pedido.cliente_nome }}</div>
      <div><strong>FONE:</strong> {{ pedido.cliente_telefone || "-" }}</div>
      <div>
        <strong>TIPO DO PEDIDO:</strong>
        {{ pedido.tipo_entrega === "entrega" ? "ENTREGA" : "RETIRADA" }}
      </div>

      <div class="divider"></div>

      <div class="strong">ITENS</div>
      <div v-for="item in pedido.itens" :key="item.id" class="item">
        <div class="item-line">
          <span>{{ item.quantidade }}kg</span>
          <span>{{ item.produto_nome }}</span>
        </div>
        <div v-if="item.observacoes" class="item-obs">
          OBS: {{ item.observacoes }}
        </div>
      </div>

      <div class="divider"></div>

      <div class="strong">
        {{ pedido.tipo_entrega === "entrega" ? "ENTREGA" : "RETIRADA" }}
      </div>
      <div>{{ formatarData(pedido.data_entrega) }} as {{ pedido.hora_entrega }}</div>
      <div v-if="pedido.tipo_entrega === 'entrega'">
        <strong>ENDERECO:</strong> {{ pedido.endereco_entrega || "-" }}
      </div>

      <div class="divider"></div>

      <div class="center small">Obrigado pela preferencia</div>
    </div>
  </div>
</template>

<script setup>
import { router } from "@inertiajs/vue3";
import { nextTick, onMounted } from "vue";

const props = defineProps({
  pedido: {
    type: Object,
    required: true,
  },
});

const formatarData = (data) => {
  if (!data) return "";

  const [ano, mes, dia] = data.split("-");
  return `${dia}/${mes}/${ano}`;
};

const imprimirNovamente = () => {
  window.print();
};

const voltar = () => {
  router.get("/dashboard");
};

onMounted(async () => {
  await nextTick();
  window.print();
});
</script>

<style scoped>
.receipt-page {
  min-height: 100vh;
  background: #f3f4f6;
  padding: 16px;
}

.receipt-actions {
  display: flex;
  justify-content: center;
  gap: 12px;
  margin-bottom: 16px;
}

.action-btn {
  border: 0;
  border-radius: 8px;
  padding: 10px 16px;
  font-weight: 700;
  cursor: pointer;
}

.action-btn.primary {
  background: #8d021f;
  color: white;
}

.action-btn.secondary {
  background: white;
  color: #8d021f;
  border: 1px solid #8d021f;
}

.receipt {
  width: 80mm;
  margin: 0 auto;
  background: white;
  color: #000;
  padding: 12px 10px;
  font-family: "Courier New", monospace;
  font-size: 12px;
  line-height: 1.35;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
}

.center {
  text-align: center;
}

.strong {
  font-weight: 700;
}

.small {
  font-size: 11px;
}

.divider {
  border-top: 1px dashed #000;
  margin: 10px 0;
}

.item {
  margin-bottom: 8px;
}

.item-line {
  display: flex;
  gap: 8px;
}

.item-obs {
  padding-left: 8px;
}

@media print {
  @page {
    size: 80mm auto;
    margin: 4mm;
  }

  .no-print {
    display: none !important;
  }

  .receipt-page {
    background: white;
    padding: 0;
  }

  .receipt {
    width: 72mm;
    box-shadow: none;
    margin: 0;
    padding: 0;
  }
}
</style>
