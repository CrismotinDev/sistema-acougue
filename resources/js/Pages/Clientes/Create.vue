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
              Cadastro de Clientes
            </div>
            <h2 class="text-h6 font-weight-black text-red-darken-4">
              {{ isEditing ? "EDITAR CLIENTE" : "NOVO CLIENTE" }}
            </h2>
            <p class="text-body-2 text-grey-darken-1 mt-1">
              {{
                isEditing
                  ? "Atualize os dados principais do cliente no sistema."
                  : "Preencha os dados principais para cadastrar um novo cliente no sistema."
              }}
            </p>
          </v-col>

          <v-col cols="12" md="4" class="text-md-right mt-3 mt-md-0">
            <v-btn
              variant="outlined"
              color="#8D021F"
              prepend-icon="mdi-arrow-left"
              class="rounded-lg"
              @click="voltar"
            >
              Voltar para lista
            </v-btn>
          </v-col>
        </v-row>
      </v-sheet>

      <v-card flat rounded="lg" class="mt-3">
        <v-card-text class="pa-4 pa-md-5">
          <v-form @submit.prevent="salvarCliente">
            <v-row dense class="form-grid">
              <v-col cols="12" md="8">
                <v-text-field
                  v-model="form.nome"
                  label="Nome do Cliente"
                  placeholder="Ex: Joao da Silva"
                  variant="outlined"
                  density="comfortable"
                  color="#8D021F"
                  :error-messages="form.errors.nome"
                  autofocus
                />
              </v-col>

              <v-col cols="12" md="4">
                <v-text-field
                  v-model="telefoneInput"
                  label="Telefone"
                  placeholder="Ex: (11) 99999-9999"
                  variant="outlined"
                  density="comfortable"
                  color="#8D021F"
                  maxlength="15"
                  :error-messages="form.errors.telefone"
                />
              </v-col>

              <v-col cols="12" md="2">
                <v-text-field
                  v-model="cepInput"
                  label="CEP"
                  placeholder="Ex: 01001-000"
                  variant="outlined"
                  density="comfortable"
                  color="#8D021F"
                  maxlength="9"
                  prepend-inner-icon="mdi-map-marker-radius-outline"
                  :loading="buscandoCep"
                  :error-messages="cepErros"
                  @blur="buscarCep"
                />
              </v-col>

              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.endereco"
                  label="Endereco"
                  placeholder="Rua, avenida ou logradouro"
                  variant="outlined"
                  density="comfortable"
                  color="#8D021F"
                  :error-messages="form.errors.endereco"
                />
              </v-col>

              <v-col cols="12" md="2">
                <v-text-field
                  v-model="form.numero"
                  label="Numero"
                  placeholder="Ex: 123"
                  variant="outlined"
                  density="comfortable"
                  color="#8D021F"
                  :error-messages="form.errors.numero"
                />
              </v-col>

              <v-col cols="12" md="2">
                <v-text-field
                  v-model="form.complemento"
                  label="Complemento"
                  placeholder="Casa, apto, bloco, referencia"
                  variant="outlined"
                  density="comfortable"
                  color="#8D021F"
                  :error-messages="form.errors.complemento"
                />
              </v-col>

              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.bairro"
                  label="Bairro"
                  placeholder="Ex: Centro"
                  variant="outlined"
                  density="comfortable"
                  color="#8D021F"
                  :error-messages="form.errors.bairro"
                />
              </v-col>



              <v-col cols="12" md="4">
                <v-text-field
                  v-model="form.cidade"
                  label="Cidade"
                  placeholder="Ex: Sao Paulo"
                  variant="outlined"
                  density="comfortable"
                  color="#8D021F"
                  :error-messages="form.errors.cidade"
                />
              </v-col>
                <v-col cols="12" md="2">
                <v-text-field
                  v-model="form.uf"
                  label="UF"
                  placeholder="SP"
                  variant="outlined"
                  density="comfortable"
                  color="#8D021F"
                  maxlength="2"
                  :error-messages="form.errors.uf"
                />
              </v-col>
            </v-row>

            <div class="d-flex flex-wrap ga-3 mt-2">
              <v-btn
                type="submit"
                color="#8D021F"
                theme="dark"
                height="42"
                class="rounded-lg px-6"
                :loading="form.processing"
              >
                {{ isEditing ? "Salvar Alteracoes" : "Salvar Cliente" }}
              </v-btn>

              <v-btn
                variant="text"
                color="grey-darken-1"
                height="42"
                class="rounded-lg"
                @click="voltar"
              >
                Cancelar
              </v-btn>
            </div>
          </v-form>
        </v-card-text>
      </v-card>
    </v-container>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { router, useForm } from "@inertiajs/vue3";
import axios from "axios";
import { computed, ref, watch } from "vue";

const props = defineProps({
  cliente: {
    type: Object,
    default: null,
  },
});

const form = useForm({
  nome: "",
  telefone: "",
  cep: "",
  endereco: "",
  numero: "",
  complemento: "",
  bairro: "",
  cidade: "",
  uf: "",
});

const telefoneInput = ref("");
const cepInput = ref("");
const buscandoCep = ref(false);
const cepMensagemErro = ref("");

const isEditing = computed(() => Boolean(props.cliente?.id));

const somenteDigitos = (valor) => (valor ?? "").replace(/\D/g, "");

const formatarTelefone = (valor) => {
  const digitos = somenteDigitos(valor).slice(0, 11);

  if (digitos.length <= 2) return digitos ? `(${digitos}` : "";
  if (digitos.length <= 6) return `(${digitos.slice(0, 2)}) ${digitos.slice(2)}`;
  if (digitos.length <= 10) {
    return `(${digitos.slice(0, 2)}) ${digitos.slice(2, 6)}-${digitos.slice(6)}`;
  }

  return `(${digitos.slice(0, 2)}) ${digitos.slice(2, 7)}-${digitos.slice(7)}`;
};

const formatarCep = (valor) => {
  const digitos = somenteDigitos(valor).slice(0, 8);
  if (digitos.length <= 5) return digitos;
  return `${digitos.slice(0, 5)}-${digitos.slice(5)}`;
};

const cepErros = computed(() => {
  const erros = [];

  if (form.errors.cep) erros.push(form.errors.cep);
  if (cepMensagemErro.value) erros.push(cepMensagemErro.value);

  return erros;
});

watch(telefoneInput, (valor) => {
  const formatado = formatarTelefone(valor);
  if (telefoneInput.value !== formatado) {
    telefoneInput.value = formatado;
    return;
  }

  form.telefone = formatado;
});

watch(cepInput, (valor) => {
  const formatado = formatarCep(valor);
  if (cepInput.value !== formatado) {
    cepInput.value = formatado;
    return;
  }

  form.cep = formatado;
  cepMensagemErro.value = "";
});

watch(
  () => form.uf,
  (valor) => {
    form.uf = (valor ?? "").toUpperCase().slice(0, 2);
  }
);

watch(
  () => props.cliente,
  (cliente) => {
    form.nome = cliente?.nome ?? "";
    form.telefone = cliente?.telefone ?? "";
    form.cep = cliente?.cep ?? "";
    form.endereco = cliente?.endereco ?? "";
    form.numero = cliente?.numero ?? "";
    form.complemento = cliente?.complemento ?? "";
    form.bairro = cliente?.bairro ?? "";
    form.cidade = cliente?.cidade ?? "";
    form.uf = cliente?.uf ?? "";

    telefoneInput.value = formatarTelefone(cliente?.telefone ?? "");
    cepInput.value = formatarCep(cliente?.cep ?? "");
    cepMensagemErro.value = "";
    form.clearErrors();
  },
  { immediate: true }
);

const buscarCep = async () => {
  const cep = somenteDigitos(cepInput.value);

  if (!cep) {
    form.cep = "";
    cepMensagemErro.value = "";
    return;
  }

  if (cep.length < 8) {
    cepMensagemErro.value = "Informe um CEP completo.";
    return;
  }

  buscandoCep.value = true;
  cepMensagemErro.value = "";

  try {
    const { data } = await axios.get(`https://viacep.com.br/ws/${cep}/json/`);

    if (data?.erro) {
      cepMensagemErro.value = "CEP nao encontrado.";
      return;
    }

    form.cep = formatarCep(cep);
    form.endereco = data.logradouro ?? "";
    form.bairro = data.bairro ?? "";
    form.cidade = data.localidade ?? "";
    form.uf = data.uf ?? "";
  } catch (error) {
    cepMensagemErro.value = "Nao foi possivel consultar o CEP agora.";
  } finally {
    buscandoCep.value = false;
  }
};

const salvarCliente = () => {
  form.telefone = telefoneInput.value;
  form.cep = cepInput.value;

  if (isEditing.value) {
    form.put(`/clientes/${props.cliente.id}`);
    return;
  }

  form.post("/clientes");
};

const voltar = () => {
  router.get("/clientes");
};
</script>

<style scoped>
.form-grid {
  row-gap: 2px;
}

.helper-copy {
  margin-top: 0;
  line-height: 1.25;
}

:deep(.v-input) {
  margin-bottom: 2px;
}

:deep(.v-field__input) {
  min-height: 44px;
}
</style>
