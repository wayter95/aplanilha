import BaseService from './baseService'

/**
 * ProjectsService - Serviço para gerenciar projetos
 * 
 * Herda métodos CRUD do BaseService e adiciona operações específicas.
 * 
 * @example
 * import projectsService from '@/api/projectsService'
 * 
 * // Listar todos
 * const projects = await projectsService.list({ search: 'projeto' })
 * 
 * // Buscar por ID
 * const project = await projectsService.get(1)
 * 
 * // Criar novo
 * const newProject = await projectsService.create({
 *   name: 'Novo Projeto',
 *   project_types_id: '123e4567-e89b-12d3-a456-426614174000',
 *   status: 'active'
 * })
 * 
 * // Atualizar
 * await projectsService.update(1, { name: 'Nome Atualizado' })
 * 
 * // Deletar
 * await projectsService.delete(1)
 * 
 * // Ativar/Cancelar/Completar
 * await projectsService.activate(1)
 * await projectsService.cancel(1)
 * await projectsService.complete(1)
 */
class ProjectsService extends BaseService {
    constructor() {
        super('/projects')
    }

    /**
     * Ativa um projeto (status = 'active')
     * 
     * @param {string|number} id - ID do projeto
     * @returns {Promise<Object>} Resposta da API
     */
    async activate(id) {
        return this.request('PATCH', `/${id}/activate`)
    }

    /**
     * Cancela um projeto (status = 'cancelled')
     * 
     * @param {string|number} id - ID do projeto
     * @returns {Promise<Object>} Resposta da API
     */
    async cancel(id) {
        return this.request('PATCH', `/${id}/cancel`)
    }

    /**
     * Completa um projeto (status = 'completed')
     * 
     * @param {string|number} id - ID do projeto
     * @returns {Promise<Object>} Resposta da API
     */
    async complete(id) {
        return this.request('PATCH', `/${id}/complete`)
    }

    /**
     * Busca projetos ativos
     * 
     * @returns {Promise<Object>} Lista de projetos ativos
     */
    async listActive() {
        return this.list({ status: 'active' })
    }

    /**
     * Busca projetos pendentes
     * 
     * @returns {Promise<Object>} Lista de projetos pendentes
     */
    async listPending() {
        return this.list({ status: 'pending' })
    }

    /**
     * Busca projetos cancelados
     * 
     * @returns {Promise<Object>} Lista de projetos cancelados
     */
    async listCancelled() {
        return this.list({ status: 'cancelled' })
    }

    /**
     * Busca projetos completos
     * 
     * @returns {Promise<Object>} Lista de projetos completos
     */
    async listCompleted() {
        return this.list({ status: 'completed' })
    }

    /**
     * Busca projetos por tipo
     * 
     * @param {string} projectTypeId - ID do tipo de projeto
     * @returns {Promise<Object>} Lista de projetos do tipo especificado
     */
    async listByType(projectTypeId) {
        return this.list({ project_type: projectTypeId })
    }
}

// Exportar instância única (singleton)
export default new ProjectsService()
