<template>
  <v-dialog
    :model-value="modelValue"
    @update:model-value="$emit('update:modelValue', $event)"
    max-width="400"
  >
    <v-card class="rounded-lg">
      <v-card-title class="text-subtitle-1 font-weight-black bg-grey-lighten-4 pa-4">
        <v-icon color="orange-darken-4" start>mdi-alert-outline</v-icon>
        {{ title }}
      </v-card-title>

      <v-card-text class="pa-6 text-body-2">
        {{ message }}
      </v-card-text>

      <v-divider></v-divider>

      <v-card-actions class="pa-4">
        <v-spacer></v-spacer>
        <v-btn
          variant="text"
          color="grey-darken-1"
          @click="$emit('update:modelValue', false)"
        >
          Cancelar
        </v-btn>
        <v-btn color="#8D021F" variant="flat" class="px-4" @click="$emit('confirm')">
          Confirmar
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup>
defineProps(["modelValue", "title", "message"]);
defineEmits(["update:modelValue", "confirm"]);
</script>
