import { usePage } from "@inertiajs/vue3";
import { reactive, ref } from "vue";

// Cache para URLs temporárias
const photoUrlCache = reactive(new Map());

export function usePhotoUrl() {
    const loading = ref(false);
    const page = usePage();

    const getPhotoUrl = async (photoKey) => {
        if (!photoKey) return null;

        // Se é uma URL completa, usar diretamente
        if (photoKey.startsWith("http")) {
            return photoKey;
        }

        // Verificar cache primeiro
        if (photoUrlCache.has(photoKey)) {
            const cachedData = photoUrlCache.get(photoKey);
            // Verificar se ainda não expirou (1 hora = 3600000ms)
            if (Date.now() - cachedData.timestamp < 3600000) {
                return cachedData.url;
            }
            // Se expirou, remover do cache
            photoUrlCache.delete(photoKey);
        }

        // Buscar nova URL temporária
        loading.value = true;
        try {
            const response = await fetch(
                `/api/files/signed-url?key=${photoKey}`,
                {
                    method: "GET",
                    headers: {
                        "X-CSRF-TOKEN": page.props.csrf_token,
                        "X-Requested-With": "XMLHttpRequest",
                    },
                }
            );

            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    // Salvar no cache
                    photoUrlCache.set(photoKey, {
                        url: data.url,
                        timestamp: Date.now(),
                    });
                    return data.url;
                }
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
