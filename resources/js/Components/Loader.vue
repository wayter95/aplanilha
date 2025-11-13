<template>
  <div v-if="isLoading" id="loader">
    <img :src="loaderSvg" alt="Loading...">
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import loaderSvg from '../../assets/images/media/loader.svg'

const isLoading = ref(true)

onMounted(() => {
  // Esconde o loader após o carregamento inicial
  setTimeout(() => {
    isLoading.value = false
  }, 500)
})
</script>

<style scoped>
#loader {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, 0.9);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

#loader img {
  width: 60px;
  height: 60px;
}
</style>
