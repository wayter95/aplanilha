/**
 * API Client - Axios configurado com interceptors
 * 
 * Centraliza configuração HTTP:
 * - Headers padrão
 * - Interceptors de request/response
 * - Tratamento de erros global
 * - Loading states automáticos
 */

import axios from 'axios';
import { router } from '@inertiajs/vue3';

// Criar instância axios customizada
const apiClient = axios.create({
    baseURL: '/api',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
    },
    timeout: 30000 // 30 segundos
});

// Contador de requisições ativas (para loading global)
let activeRequests = 0;

/**
 * Interceptor de REQUEST
 * Adiciona CSRF token, auth headers, etc
 */
apiClient.interceptors.request.use(
    (config) => {
        activeRequests++;
        
        // Emitir evento de loading (se necessário)
        if (activeRequests === 1) {
            window.dispatchEvent(new CustomEvent('api:loading:start'));
        }

        // Adicionar CSRF token (Laravel)
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (token) {
            config.headers['X-CSRF-TOKEN'] = token;
        }

        return config;
    },
    (error) => {
        activeRequests--;
        return Promise.reject(error);
    }
);

/**
 * Interceptor de RESPONSE
 * Trata erros globalmente
 */
apiClient.interceptors.response.use(
    (response) => {
        activeRequests--;
        
        // Parar loading quando não houver mais requisições
        if (activeRequests === 0) {
            window.dispatchEvent(new CustomEvent('api:loading:end'));
        }

        return response;
    },
    (error) => {
        activeRequests--;

        // Parar loading
        if (activeRequests === 0) {
            window.dispatchEvent(new CustomEvent('api:loading:end'));
        }

        // Tratamento de erros específicos
        if (error.response) {
            const { status, data } = error.response;

            switch (status) {
                case 401:
                    // Não autorizado - redirecionar para login
                    window.dispatchEvent(new CustomEvent('api:error:unauthorized', { 
                        detail: { message: data.message || 'Sessão expirada' }
                    }));
                    router.visit('/login');
                    break;

                case 403:
                    // Sem permissão
                    window.dispatchEvent(new CustomEvent('api:error:forbidden', { 
                        detail: { message: data.message || 'Sem permissão' }
                    }));
                    break;

                case 404:
                    // Não encontrado
                    window.dispatchEvent(new CustomEvent('api:error:notfound', { 
                        detail: { message: data.message || 'Recurso não encontrado' }
                    }));
                    break;

                case 422:
                    // Validação falhou
                    window.dispatchEvent(new CustomEvent('api:error:validation', { 
                        detail: { 
                            message: data.message || 'Dados inválidos',
                            errors: data.errors || {}
                        }
                    }));
                    break;

                case 500:
                    // Erro servidor
                    window.dispatchEvent(new CustomEvent('api:error:server', { 
                        detail: { message: data.message || 'Erro no servidor' }
                    }));
                    break;

                default:
                    // Outro erro
                    window.dispatchEvent(new CustomEvent('api:error:generic', { 
                        detail: { 
                            status,
                            message: data.message || 'Erro desconhecido' 
                        }
                    }));
            }
        } else if (error.request) {
            // Requisição foi feita mas sem resposta (timeout, network error)
            window.dispatchEvent(new CustomEvent('api:error:network', { 
                detail: { message: 'Erro de conexão. Verifique sua internet.' }
            }));
        }

        return Promise.reject(error);
    }
);

/**
 * Helper: GET request
 */
export const get = (url, config = {}) => apiClient.get(url, config);

/**
 * Helper: POST request
 */
export const post = (url, data = {}, config = {}) => apiClient.post(url, data, config);

/**
 * Helper: PUT request
 */
export const put = (url, data = {}, config = {}) => apiClient.put(url, data, config);

/**
 * Helper: PATCH request
 */
export const patch = (url, data = {}, config = {}) => apiClient.patch(url, data, config);

/**
 * Helper: DELETE request
 */
export const del = (url, config = {}) => apiClient.delete(url, config);

/**
 * Export default client
 */
export default apiClient;
