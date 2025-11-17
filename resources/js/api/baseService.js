import apiClient from './client'

/**
 * BaseService - Classe base para serviços de API
 * 
 * Fornece métodos CRUD padrão que podem ser herdados por serviços específicos.
 * Reduz duplicação de código e padroniza operações comuns.
 * 
 * @example
 * class ProjectTypesService extends BaseService {
 *   constructor() {
 *     super('/project-types')
 *   }
 * }
 */
class BaseService {
    /**
     * @param {string} endpoint - Endpoint base da API (ex: '/users', '/project-types')
     */
    constructor(endpoint) {
        this.endpoint = endpoint
    }

    /**
     * Lista todos os recursos (com paginação e filtros opcionais)
     * 
     * @param {Object} params - Query params (page, search, filters, etc)
     * @returns {Promise<Object>} Resposta da API
     */
    async list(params = {}) {
        const response = await apiClient.get(this.endpoint, { params })
        return response.data
    }

    /**
     * Busca um recurso específico por ID
     * 
     * @param {string|number} id - ID do recurso
     * @returns {Promise<Object>} Dados do recurso
     */
    async get(id) {
        const response = await apiClient.get(`${this.endpoint}/${id}`)
        return response.data
    }

    /**
     * Cria um novo recurso
     * 
     * @param {Object} data - Dados do recurso
     * @returns {Promise<Object>} Resposta da API
     */
    async create(data) {
        const response = await apiClient.post(this.endpoint, data)
        return response.data
    }

    /**
     * Atualiza um recurso existente
     * 
     * @param {string|number} id - ID do recurso
     * @param {Object} data - Dados atualizados
     * @returns {Promise<Object>} Resposta da API
     */
    async update(id, data) {
        const response = await apiClient.put(`${this.endpoint}/${id}`, data)
        return response.data
    }

    /**
     * Deleta um recurso
     * 
     * @param {string|number} id - ID do recurso
     * @returns {Promise<Object>} Resposta da API
     */
    async delete(id) {
        const response = await apiClient.delete(`${this.endpoint}/${id}`)
        return response.data
    }

    /**
     * Faz uma requisição customizada
     * 
     * @param {string} method - Método HTTP (get, post, put, patch, delete)
     * @param {string} path - Caminho adicional após o endpoint base
     * @param {Object} data - Dados da requisição
     * @param {Object} config - Configuração adicional do axios
     * @returns {Promise<Object>} Resposta da API
     */
    async request(method, path = '', data = null, config = {}) {
        const url = path ? `${this.endpoint}${path}` : this.endpoint
        const response = await apiClient.request({
            method,
            url,
            data,
            ...config
        })
        return response.data
    }
}

export default BaseService
