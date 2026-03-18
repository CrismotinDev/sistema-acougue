<template>
  <AuthenticatedLayout>
    <v-container fluid class="pa-0">
      <v-sheet class="pa-3 border-b bg-grey-lighten-5">
        <v-row no-gutters align="center">
          <v-col>
            <div
              class="text-overline text-grey-darken-1 lh-1"
              style="font-size: 0.6rem !important"
            >
              Gestão de Estoque
            </div>
            <h2 class="text-subtitle-1 font-weight-black text-red-darken-4 mt-n1">
              PRODUTOS
            </h2>
          </v-col>
          <v-col class="text-right">
            <v-btn
              color="#8D021F"
              theme="dark"
              prepend-icon="mdi-plus"
              height="30"
              class="text-caption font-weight-bold rounded-lg shadow-none"
              @click="abrirDialogNovo"
            >
              Novo Produto
            </v-btn>
          </v-col>
        </v-row>
      </v-sheet>

      <v-data-table
        :headers="headers"
        :items="produtos"
        :search="search"
        density="compact"
        class="text-caption"
        hover
        flat
      >
        <template v-slot:top>
          <v-toolbar color="white" flat height="48" class="px-4 border-b">
            <v-text-field
              v-model="search"
              prepend-inner-icon="mdi-magnify"
              label="Filtrar produtos..."
              variant="outlined"
              color="#8D021F"
              density="compact"
              hide-details
              rounded="md"
              style="max-width: 280px"
            ></v-text-field>
          </v-toolbar>
        </template>

        <template v-slot:item.nome="{ item }">
          <span class="text-uppercase font-weight-black text-grey-darken-4">
            {{ item.nome }}
          </span>
        </template>

        <template v-slot:item.acoes="{ item }">
          <div class="text-right">
            <v-btn
              icon="mdi-pencil-outline"
              variant="text"
              size="x-small"
              color="blue-darken-2"
              class="mr-1"
              @click="editarProduto(item)"
            ></v-btn>
            <v-btn
              icon="mdi-trash-can-outline"
              variant="text"
              size="x-small"
              color="red-darken-2"
              @click="prepararExclusao(item)"
            ></v-btn>
          </div>
        </template>

        <template v-slot:no-data>
          <div class="pa-10 text-grey text-center">Nenhum produto cadastrado.</div>
        </template>
      </v-data-table>

      <v-dialog v-model="dialogForm" max-width="400" persistent>
        <v-card class="rounded-lg">
          <v-toolbar color="#8D021F" theme="dark" density="compact">
            <v-toolbar-title class="text-caption font-weight-bold">
              {{ form.id ? "EDITAR PRODUTO" : "NOVO PRODUTO" }}
            </v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn
              icon="mdi-close"
              size="small"
              variant="text"
              @click="fecharDialog"
            ></v-btn>
          </v-toolbar>

          <v-card-text class="pa-4">
            <v-form @submit.prevent="salvar">
              <v-text-field
                v-model="form.nome"
                label="Nome do Produto"
                variant="outlined"
                color="#8D021F"
                density="compact"
                :error-messages="form.errors.nome"
                placeholder="Ex: Picanha Argentina"
                persistent-placeholder
                autofocus
              ></v-text-field>

              <v-btn
                block
                color="#8D021F"
                height="40"
                class="mt-2 text-caption font-weight-bold"
                type="submit"
                :loading="form.processing"
              >
                {{ form.id ? "SALVAR ALTERAÇÕES" : "CADASTRAR" }}
              </v-btn>
            </v-form>
          </v-card-text>
        </v-card>
      </v-dialog>

      <DialogConfirm
        v-model="showDialogDelete"
        title="Confirmar Exclusão"
        :message="`Deseja realmente excluir o produto ${produtoParaExcluir?.nome}?`"
        @confirm="confirmarExclusao"
      />
    </v-container>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DialogConfirm from "@/Components/DialogConfirm.vue";
import { useForm, router } from "@inertiajs/vue3"; // Importamos o router e o useForm
import { ref } from "vue";

const props = defineProps({ produtos: Array });

const search = ref("");
const dialogForm = ref(false);
const showDialogDelete = ref(false);
const produtoParaExcluir = ref(null);

const headers = [
  { title: "ID", key: "id", width: "80px", align: "start" },
  { title: "NOME", key: "nome", align: "start" },
  { title: "AÇÕES", key: "acoes", align: "end", sortable: false },
];

const form = useForm({
  id: null,
  nome: "",
});

const abrirDialogNovo = () => {
  form.reset();
  form.clearErrors();
  dialogForm.value = true;
};

const editarProduto = (item) => {
  form.clearErrors();
  form.id = item.id;
  form.nome = item.nome;
  dialogForm.value = true;
};

const fecharDialog = () => {
  dialogForm.value = false;
  form.reset();
};

const salvar = () => {
  // Definindo a estratégia com base na existência do ID
  const isEditing = !!form.id;
  const url = isEditing ? `/produtos/${form.id}` : "/produtos";
  const method = isEditing ? "put" : "post";

  // BOA PRÁTICA: Usando o router manualmente e sincronizando com o estado do form
  router[method](url, form.data(), {
    onStart: () => {
      form.processing = true;
    },
    onFinish: () => {
      form.processing = false;
    },
    onSuccess: () => fecharDialog(),
    onError: (errors) => {
      // Importante: Passa os erros de validação do Laravel de volta para o objeto form
      form.setError(errors);
    },
    preserveScroll: true,
  });
};

const prepararExclusao = (item) => {
  produtoParaExcluir.value = item;
  showDialogDelete.value = true;
};

const confirmarExclusao = () => {
  if (!produtoParaExcluir.value) return;

  router.delete(`/produtos/${produtoParaExcluir.value.id}`, {
    onStart: () => {
      form.processing = true;
    },
    onFinish: () => {
      form.processing = false;
      showDialogDelete.value = false;
      produtoParaExcluir.value = null;
    },
    preserveScroll: true,
  });
};
</script>
