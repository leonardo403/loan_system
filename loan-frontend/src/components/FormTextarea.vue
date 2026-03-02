<template>
  <div class="flex flex-col">
    <label :for="id" class="text-sm font-semibold text-gray-700 mb-2">
      {{ label }}
    </label>
    <textarea
      :id="id"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      :placeholder="placeholder"
      :rows="rows"
      class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent resize-none"
    />
  </div>
</template>

<script setup>
import { computed } from 'vue'

defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  label: {
    type: String,
    required: true
  },
  placeholder: {
    type: String,
    default: ''
  },
  rows: {
    type: Number,
    default: 3
  }
})

defineEmits(['update:modelValue'])

const id = computed(() => `textarea-${Math.random().toString(36).substr(2, 9)}`)
</script>

<style scoped>
</style>
