/**
 * Document Templates API
 * 
 * Endpoints para gerenciar templates de documentos
 */

import { get, post, put, del } from './client';

export const documentTemplatesApi = {
    /**
     * Listar tipos de templates disponíveis
     */
    getTypes: () => get('/document-templates/types'),

    /**
     * Listar templates
     * @param {Object} params - Filtros (type, per_page, etc)
     */
    getAll: (params = {}) => get('/document-templates', { params }),

    /**
     * Buscar template por ID
     * @param {Number} id
     */
    getById: (id) => get(`/document-templates/${id}`),

    /**
     * Criar novo template
     * @param {Object} data - Dados do template
     */
    create: (data) => post('/document-templates', data),

    /**
     * Atualizar template
     * @param {Number} id
     * @param {Object} data - Dados atualizados
     */
    update: (id, data) => put(`/document-templates/${id}`, data),

    /**
     * Deletar template
     * @param {Number} id
     */
    delete: (id) => del(`/document-templates/${id}`),

    /**
     * Definir template como padrão
     * @param {Number} id
     */
    setDefault: (id) => post(`/document-templates/${id}/set-default`),

    /**
     * Duplicar template
     * @param {Number} id
     * @param {Object} overrides - Dados para sobrescrever (ex: name)
     */
    duplicate: (id, overrides = {}) => {
        return post('/document-templates', {
            duplicate_from: id,
            ...overrides
        });
    }
};

export default documentTemplatesApi;
