<script setup lang="ts">
import { useCarritoStore } from '../stores/carrito'

const carrito = useCarritoStore()
</script>

<template>
  <router-link to="/carrito" style="text-decoration: none; position: relative; display: inline-block;">
    <span style="font-size: 22px; cursor: pointer;">🛒</span>
    <span v-if="carrito.totalItems > 0" style="position: absolute; top: -5px; right: -10px; background: #e74c3c; color: white; border-radius: 50%; padding: 2px 6px; font-size: 11px; font-weight: bold;">
      {{ carrito.totalItems }}
    </span>
  </router-link>
</template>