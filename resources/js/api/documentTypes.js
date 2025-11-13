/**
 * Document Types API
 * 
 * Endpoints para gerenciar tipos de documentos
 */

import { get, post, put, del } from './client';

export const documentTypesApi = {
    /**
     * Listar todos os tipos
     * @param {Object} params - Filtros opcionais
     */
    getAll: (params = {}) => get('/document-types', { params }),

    /**
     * Buscar tipo por ID
     * @param {Number} id
     */
    getById: (id) => get(`/document-types/${id}`),

    /**
     * Criar novo tipo
     * @param {Object} data - Dados do tipo
     */
    create: (data) => post('/document-types', data),

    /**
     * Atualizar tipo
     * @param {Number} id
     * @param {Object} data - Dados atualizados
     */
    update: (id, data) => put(`/document-types/${id}`, data),

    /**
     * Deletar tipo
     * @param {Number} id
     */
    delete: (id) => del(`/document-types/${id}`)
};

export default documentTypesApi;
