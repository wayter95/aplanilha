import BaseService from './baseService'

/**
 * ContactsService - Serviço para gerenciar contatos
 * 
 * @example
 * import contactsService from '@/api/contactsService'
 * 
 * // Buscar para Select2
 * const contacts = await contactsService.searchForSelect2({ search: 'João', type: 'customer' })
 * 
 * // Buscar clientes
 * const customers = await contactsService.getCustomers()
 * 
 * // Buscar locais
 * const locations = await contactsService.getLocations()
 */
class ContactsService extends BaseService {
    constructor() {
        super('/contacts')
    }

    /**
     * Busca contatos para uso em Select2
     * Retorna formato otimizado para autocomplete
     * 
     * @param {Object} params - Parâmetros de busca
     * @param {string} params.search - Termo de busca
     * @param {string} params.type - Tipo de contato (customer, supplier, location)
     * @param {number} params.limit - Limite de resultados
     * @returns {Promise<Object>} Lista de contatos
     */
    async searchForSelect2(params = {}) {
        return this.request('GET', '/search-select2', params)
    }

    /**
     * Busca todos os clientes (tipo customer)
     * 
     * @returns {Promise<Object>} Lista de clientes
     */
    async getCustomers() {
        return this.request('GET', '/customers')
    }

    /**
     * Busca todos os fornecedores (tipo supplier)
     * 
     * @returns {Promise<Object>} Lista de fornecedores
     */
    async getSuppliers() {
        return this.request('GET', '/suppliers')
    }

    /**
     * Busca todos os locais (tipo location)
     * 
     * @returns {Promise<Object>} Lista de locais
     */
    async getLocations() {
        return this.request('GET', '/locations')
    }

    /**
     * Busca contatos por tipo
     * 
     * @param {string} type - Tipo de contato (customer, supplier, location)
     * @returns {Promise<Object>} Lista de contatos do tipo especificado
     */
    async listByType(type) {
        return this.list({ type })
    }
}

// Exportar instância única (singleton)
export default new ContactsService()
