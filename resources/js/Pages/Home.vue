<template>
  <AppLayout :title="title" :description="description" :user="user">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Bem-vindo ao sistema</h5>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  title: String,
  description: String,
  user: Object,
  stats: Object,
  recentActivities: Array,
  client: Object
})

const formatDate = (date) => {
  return new Date(date).toLocaleString('pt-BR')
}
</script>
  