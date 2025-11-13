/**
 * Composable: useDocumentTypes
 * 
 * Gerencia estado e ações de Document Types
 */

import { ref } from 'vue';
import { documentTypesApi } from '@/api';

export function useDocumentTypes() {
    const loading = ref(false);
    const error = ref(null);
    const types = ref([]);

    /**
     * Listar todos os tipos
     */
    const fetchAll = async (params = {}) => {
        loading.value = true;
        error.value = null;

        try {
            const { data } = await documentTypesApi.getAll(params);
            types.value = data;
            return data;
        } catch (e) {
            error.value = e;
            throw e;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Buscar tipo por ID
     */
    const fetchById = async (id) => {
        loading.value = true;
        error.value = null;

        try {
            const { data } = await documentTypesApi.getById(id);
            return data;
        } catch (e) {
            error.value = e;
            throw e;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Criar tipo
     */
    const create = async (typeData) => {
        loading.value = true;
        error.value = null;

        try {
            const { data } = await documentTypesApi.create(typeData);
            await fetchAll(); // Atualizar lista
            return data;
        } catch (e) {
            error.value = e;
            throw e;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Atualizar tipo
     */
    const update = async (id, typeData) => {
        loading.value = true;
        error.value = null;

        try {
            const { data } = await documentTypesApi.update(id, typeData);
            await fetchAll(); // Atualizar lista
            return data;
        } catch (e) {
            error.value = e;
            throw e;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Deletar tipo
     */
    const remove = async (id) => {
        loading.value = true;
        error.value = null;

        try {
            await documentTypesApi.delete(id);
            await fetchAll(); // Atualizar lista
            return true;
        } catch (e) {
            error.value = e;
            throw e;
        } finally {
            loading.value = false;
        }
    };

    return {
        loading,
        error,
        types,
        fetchAll,
        fetchById,
        create,
        update,
        remove
    };
}
