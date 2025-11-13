/**
 * Users API
 * 
 * Endpoints para gerenciar usuários
 */

import { get, post, put, del, patch } from './client';

export const usersApi = {
    /**
     * Listar usuários
     * @param {Object} params - Filtros (per_page, search, etc)
     */
    getAll: (params = {}) => get('/users', { params }),

    /**
     * Buscar usuário por ID
     * @param {Number} id
     */
    getById: (id) => get(`/users/${id}`),

    /**
     * Criar novo usuário
     * @param {Object} data - Dados do usuário
     */
    create: (data) => post('/users', data),

    /**
     * Atualizar usuário
     * @param {Number} id
     * @param {Object} data - Dados atualizados
     */
    update: (id, data) => put(`/users/${id}`, data),

    /**
     * Deletar usuário
     * @param {Number} id
     */
    delete: (id) => del(`/users/${id}`),

    /**
     * Atualizar foto do usuário
     * @param {Number} id
     * @param {FormData} formData - FormData com campo 'photo'
     */
    updatePhoto: (id, formData) => post(`/users/${id}/photo`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
    }),

    /**
     * Atualizar senha do usuário
     * @param {Number} id
     * @param {Object} data - { current_password, password, password_confirmation }
     */
    updatePassword: (id, data) => patch(`/users/${id}/password`, data)
};

export default usersApi;
