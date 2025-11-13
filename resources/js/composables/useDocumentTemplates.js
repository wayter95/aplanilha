/**
 * Composable: useDocumentTemplates
 * 
 * Gerencia estado e ações de Document Templates
 * - Loading states automáticos
 * - Tratamento de erros
 * - Agrupamento por tipo
 * - CRUD completo
 */

import { ref, reactive, computed } from 'vue';
import { documentTemplatesApi } from '@/api';

export function useDocumentTemplates() {
    // Estado
    const loading = ref(false);
    const error = ref(null);
    const types = ref([]);
    const itemsByType = reactive({});

    /**
     * Computed: Agrupar templates por tipo
     */
    const grouped = computed(() => {
        return types.value.map(type => ({
            type,
            items: itemsByType[type] || []
        }));
    });

    /**
     * Buscar tipos disponíveis
     */
    const fetchTypes = async () => {
        loading.value = true;
        error.value = null;

        try {
            const { data } = await documentTemplatesApi.getTypes();
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
     * Buscar templates por tipo
     * @param {String} type - Tipo do template
     * @param {Object} params - Parâmetros adicionais
     */
    const fetchByType = async (type, params = {}) => {
        loading.value = true;
        error.value = null;

        try {
            const { data } = await documentTemplatesApi.getAll({ 
                type, 
                per_page: 100,
                ...params 
            });
            
            // Normalizar resposta (pode ser { data: [] } ou [])
            const items = Array.isArray(data?.data) 
                ? data.data 
                : (Array.isArray(data) ? data : []);
            
            itemsByType[type] = items;
            return items;
        } catch (e) {
            error.value = e;
            throw e;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Buscar todos os tipos e seus templates
     */
    const fetchAll = async () => {
        await fetchTypes();
        await Promise.all(types.value.map(type => fetchByType(type)));
    };

    /**
     * Buscar template por ID
     * @param {Number} id
     */
    const fetchById = async (id) => {
        loading.value = true;
        error.value = null;

        try {
            const { data } = await documentTemplatesApi.getById(id);
            return data;
        } catch (e) {
            error.value = e;
            throw e;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Criar novo template
     * @param {Object} templateData
     */
    const create = async (templateData) => {
        loading.value = true;
        error.value = null;

        try {
            const { data } = await documentTemplatesApi.create(templateData);
            
            // Atualizar lista do tipo
            if (templateData.type) {
                await fetchByType(templateData.type);
            }
            
            return data;
        } catch (e) {
            error.value = e;
            throw e;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Atualizar template
     * @param {Number} id
     * @param {Object} templateData
     */
    const update = async (id, templateData) => {
        loading.value = true;
        error.value = null;

        try {
            const { data } = await documentTemplatesApi.update(id, templateData);
            
            // Atualizar lista do tipo
            if (templateData.type) {
                await fetchByType(templateData.type);
            }
            
            return data;
        } catch (e) {
            error.value = e;
            throw e;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Deletar template
     * @param {Number} id
     * @param {String} type - Tipo para atualizar lista
     */
    const remove = async (id, type = null) => {
        loading.value = true;
        error.value = null;

        try {
            await documentTemplatesApi.delete(id);
            
            // Atualizar lista do tipo se fornecido
            if (type) {
                await fetchByType(type);
            }
            
            return true;
        } catch (e) {
            error.value = e;
            throw e;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Definir como padrão
     * @param {Number} id
     * @param {String} type - Tipo para atualizar lista
     */
    const setDefault = async (id, type) => {
        loading.value = true;
        error.value = null;

        try {
            await documentTemplatesApi.setDefault(id);
            
            // Atualizar lista do tipo
            if (type) {
                await fetchByType(type);
            }
            
            return true;
        } catch (e) {
            error.value = e;
            throw e;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Duplicar template
     * @param {Object} item - Template original
     */
    const duplicate = async (item) => {
        loading.value = true;
        error.value = null;

        try {
            const payload = { ...item };
            delete payload.id;
            payload.name = `${item.name} (Cópia)`;
            payload.is_default = false;

            const { data } = await documentTemplatesApi.create(payload);
            
            // Atualizar lista do tipo
            if (item.type) {
                await fetchByType(item.type);
            }
            
            return data;
        } catch (e) {
            error.value = e;
            throw e;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Deletar múltiplos templates
     * @param {Array} ids - IDs para deletar
     */
    const bulkDelete = async (ids) => {
        loading.value = true;
        error.value = null;

        try {
            // Deletar em paralelo
            await Promise.all(ids.map(id => documentTemplatesApi.delete(id)));
            
            // Atualizar todas as listas
            await Promise.all(types.value.map(type => fetchByType(type)));
            
            return true;
        } catch (e) {
            error.value = e;
            throw e;
        } finally {
            loading.value = false;
        }
    };

    return {
        // Estado
        loading,
        error,
        types,
        itemsByType,
        grouped,

        // Ações
        fetchTypes,
        fetchByType,
        fetchAll,
        fetchById,
        create,
        update,
        remove,
        setDefault,
        duplicate,
        bulkDelete
    };
}
