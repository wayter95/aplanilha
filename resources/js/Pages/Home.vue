<template>
  <AppLayout :title="title" :description="description">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Bem-vindo ao Dashboard</h5>
            <p class="text-muted">Olá, {{ $page.props.auth.user?.name || 'Usuário' }}!</p>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'

defineProps({
  title: String,
  description: String,
})
</script>
  