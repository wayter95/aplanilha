<template>
  <AppLayout>
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Hello World</h5>
            <p>Bem-vindo ao sistema, {{ $page.props.auth.user?.name || 'Usuário' }}!</p>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
</script>
  