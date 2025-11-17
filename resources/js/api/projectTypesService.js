import BaseService from './baseService'

/**
 * ProjectTypesService - Serviço para gerenciar tipos de projetos
 * 
 * Herda métodos CRUD do BaseService e adiciona operações específicas.
 * 
 * @example
 * import projectTypesService from '@/api/projectTypesService'
 * 
 * // Listar todos
 * const types = await projectTypesService.list({ search: 'web' })
 * 
 * // Buscar por ID
 * const type = await projectTypesService.get(1)
 * 
 * // Criar novo
 * const newType = await projectTypesService.create({
 *   title: 'Web Development',
 *   color: '#3b82f6',
 *   status: 'a'
 * })
 * 
 * // Atualizar
 * await projectTypesService.update(1, { title: 'New Title' })
 * 
 * // Deletar
 * await projectTypesService.delete(1)
 * 
 * // Ativar/Bloquear
 * await projectTypesService.activate(1)
 * await projectTypesService.block(1)
 */
class ProjectTypesService extends BaseService {
    constructor() {
        super('/project-types')
    }

    /**
     * Ativa um tipo de projeto (status = 'a')
     * 
     * @param {string|number} id - ID do tipo de projeto
     * @returns {Promise<Object>} Resposta da API
     */
    async activate(id) {
        return this.request('PATCH', `/${id}/activate`)
    }

    /**
     * Bloqueia um tipo de projeto (status = 'b')
     * 
     * @param {string|number} id - ID do tipo de projeto
     * @returns {Promise<Object>} Resposta da API
     */
    async block(id) {
        return this.request('PATCH', `/${id}/block`)
    }

    /**
     * Alterna o status de um tipo de projeto
     * Se ativo → bloqueia, se bloqueado → ativa
     * 
     * @param {string|number} id - ID do tipo de projeto
     * @param {string} currentStatus - Status atual ('a' ou 'b')
     * @returns {Promise<Object>} Resposta da API
     */
    async toggleStatus(id, currentStatus) {
        return currentStatus === 'a' 
            ? this.block(id) 
            : this.activate(id)
    }

    /**
     * Busca tipos de projetos ativos
     * 
     * @returns {Promise<Object>} Lista de tipos ativos
     */
    async listActive() {
        return this.list({ status: 'Ativo' })
    }

    /**
     * Busca tipos de projetos bloqueados
     * 
     * @returns {Promise<Object>} Lista de tipos bloqueados
     */
    async listBlocked() {
        return this.list({ status: 'Bloqueado' })
    }
}

// Exportar instância única (singleton)
export default new ProjectTypesService()
