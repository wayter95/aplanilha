<template>
  <AppLayout :title="title" :description="description" :user="user">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <div class="card-title">Dashboard</div>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-3 col-sm-6">
                <div class="card bg-primary text-white">
                  <div class="card-body">
                    <div class="d-flex align-items-center">
                      <div class="me-3">
                        <i class="bx bx-user text-2xl"></i>
                      </div>
                      <div>
                        <h4 class="mb-0">{{ stats.totalUsers }}</h4>
                        <p class="mb-0">Total de Usuários</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-3 col-sm-6">
                <div class="card bg-success text-white">
                  <div class="card-body">
                    <div class="d-flex align-items-center">
                      <div class="me-3">
                        <i class="bx bx-checkbox-checked text-2xl"></i>
                      </div>
                      <div>
                        <h4 class="mb-0">{{ stats.activeUsers }}</h4>
                        <p class="mb-0">Usuários Ativos</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-3 col-sm-6">
                <div class="card bg-info text-white">
                  <div class="card-body">
                    <div class="d-flex align-items-center">
                      <div class="me-3">
                        <i class="bx bx-shield text-2xl"></i>
                      </div>
                      <div>
                        <h4 class="mb-0">{{ stats.admins }}</h4>
                        <p class="mb-0">Administradores</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-3 col-sm-6">
                <div class="card bg-warning text-white">
                  <div class="card-body">
                    <div class="d-flex align-items-center">
                      <div class="me-3">
                        <i class="bx bx-calendar text-2xl"></i>
                      </div>
                      <div>
                        <h4 class="mb-0">{{ stats.todayLogins }}</h4>
                        <p class="mb-0">Logins Hoje</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row mt-4">
      <div class="col-md-8">
        <div class="card">
          <div class="card-header">
            <div class="card-title">Atividade Recente</div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Usuário</th>
                    <th>Ação</th>
                    <th>Data</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="activity in recentActivities" :key="activity.id">
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="h-[2rem] w-[2rem] rounded-full bg-primary flex items-center justify-center text-white font-semibold text-sm me-2">
                          {{ activity.user?.name?.charAt(0)?.toUpperCase() || 'U' }}
                        </div>
                        <span>{{ activity.user?.name }}</span>
                      </div>
                    </td>
                    <td>{{ activity.action }}</td>
                    <td>{{ formatDate(activity.created_at) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card">
          <div class="card-header">
            <div class="card-title">Informações do Tenant</div>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label">Nome do Cliente</label>
              <p class="form-control-plaintext text-defaulttextcolor">{{ client?.name }}</p>
            </div>
            <div class="mb-3">
              <label class="form-label">Subdomínio</label>
              <p class="form-control-plaintext text-defaulttextcolor">{{ client?.subdomain }}</p>
            </div>
            <div class="mb-3">
              <label class="form-label">Plano</label>
              <p class="form-control-plaintext">
                <span class="badge bg-primary">{{ client?.plan }}</span>
              </p>
            </div>
            <div class="mb-3">
              <label class="form-label">Status</label>
              <p class="form-control-plaintext">
                <span :class="client?.active ? 'badge bg-success' : 'badge bg-danger'">
                  {{ client?.active ? 'Ativo' : 'Inativo' }}
                </span>
              </p>
            </div>
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
  