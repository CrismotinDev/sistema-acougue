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

      <v-card flat rounded="lg" class="mt-4">
        <v-card-text class="pa-6">
          <v-form @submit.prevent="salvarCliente">
            <v-row>
              <v-col cols="12" md="6">
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

              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.telefone"
                  label="Telefone"
                  placeholder="Ex: (11) 99999-9999"
                  variant="outlined"
                  density="comfortable"
                  color="#8D021F"
                  :error-messages="form.errors.telefone"
                />
              </v-col>

              <v-col cols="12">
                <v-textarea
                  v-model="form.endereco"
                  label="Endereco"
                  placeholder="Rua, numero, bairro e referencia"
                  variant="outlined"
                  density="comfortable"
                  color="#8D021F"
                  rows="3"
                  auto-grow
                  :error-messages="form.errors.endereco"
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
import { computed, watch } from "vue";

const props = defineProps({
  cliente: {
    type: Object,
    default: null,
  },
});

const form = useForm({
  nome: "",
  telefone: "",
  endereco: "",
});

const isEditing = computed(() => Boolean(props.cliente?.id));

watch(
  () => props.cliente,
  (cliente) => {
    form.nome = cliente?.nome ?? "";
    form.telefone = cliente?.telefone ?? "";
    form.endereco = cliente?.endereco ?? "";
    form.clearErrors();
  },
  { immediate: true }
);

const salvarCliente = () => {
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
