import { reactive, ref } from "vue";
import { get } from '@/api/client';

const photoUrlCache = reactive(new Map());

export function usePhotoUrl() {
    const loading = ref(false);

    const getPhotoUrl = async (photoKey) => {
        if (!photoKey) return null;

        // Se é uma URL completa, usar diretamente
        if (photoKey.startsWith("http")) {
            return photoKey;
        }

        // Verificar cache primeiro
        if (photoUrlCache.has(photoKey)) {
            const cachedData = photoUrlCache.get(photoKey);
            if (Date.now() - cachedData.timestamp < 3600000) {
                return cachedData.url;
            }
            photoUrlCache.delete(photoKey);
        }

        loading.value = true;
        try {
            // Usar api/client em vez de fetch direto
            const { data } = await get('/files/signed-url', {
                params: { key: photoKey }
            });

            if (data.success) {
                photoUrlCache.set(photoKey, {
                    url: data.url,
                    timestamp: Date.now(),
                });
                return data.url;
            }
        } catch (error) {
            console.error("Erro ao buscar URL temporária:", error);
        } finally {
            loading.value = false;
        }

        return null;
    };

    const clearCache = () => {
        photoUrlCache.clear();
    };

    const removeFromCache = (photoKey) => {
        photoUrlCache.delete(photoKey);
    };

    return {
        getPhotoUrl,
        clearCache,
        removeFromCache,
        loading,
    };
}
