/**
 * Composable: useEvents
 * 
 * EventBus centralizado com CustomEvents
 * - Type-safe event names (constantes)
 * - Cleanup automático com onUnmounted
 * - Namespace para evitar conflitos
 */

import { onUnmounted } from 'vue';

/**
 * Event Names (Constantes)
 * Centralize TODOS os eventos customizados aqui
 */
export const APP_EVENTS = {
    // User events
    USER_PHOTO_UPDATED: 'app:user:photo-updated',
    USER_UPDATED: 'app:user:updated',
    USER_LOGGED_OUT: 'app:user:logged-out',
    
    // Theme events
    THEME_CHANGED: 'app:theme:changed',
    THEME_MODE_CHANGED: 'app:theme:mode-changed',
    
    // Overlay events
    OVERLAY_TOGGLE: 'app:overlay:toggle',
    OVERLAY_OPEN: 'app:overlay:open',
    OVERLAY_CLOSE: 'app:overlay:close',
    
    // Sidebar events
    SIDEBAR_TOGGLE: 'app:sidebar:toggle',
    SIDEBAR_OPEN: 'app:sidebar:open',
    SIDEBAR_CLOSE: 'app:sidebar:close',
    
    // API events (já emitidos por api/client.js)
    API_LOADING_START: 'api:loading:start',
    API_LOADING_END: 'api:loading:end',
    API_ERROR_UNAUTHORIZED: 'api:error:unauthorized',
    API_ERROR_FORBIDDEN: 'api:error:forbidden',
    API_ERROR_VALIDATION: 'api:error:validation',
    API_ERROR_SERVER: 'api:error:server',
    API_ERROR_NETWORK: 'api:error:network'
};

export function useEvents() {
    // Array para armazenar listeners registrados (cleanup)
    const listeners = [];

    /**
     * Emitir evento customizado
     * @param {String} eventName - Nome do evento (use APP_EVENTS.*)
     * @param {*} detail - Dados do evento
     */
    const emit = (eventName, detail = null) => {
        const event = new CustomEvent(eventName, { detail });
        window.dispatchEvent(event);
    };

    /**
     * Escutar evento
     * @param {String} eventName - Nome do evento
     * @param {Function} handler - Callback
     * @returns {Function} cleanup - Função para remover listener
     */
    const on = (eventName, handler) => {
        window.addEventListener(eventName, handler);
        
        // Guardar para cleanup
        listeners.push({ eventName, handler });
        
        // Retornar função de cleanup manual
        return () => off(eventName, handler);
    };

    /**
     * Remover listener
     * @param {String} eventName
     * @param {Function} handler
     */
    const off = (eventName, handler) => {
        window.removeEventListener(eventName, handler);
        
        // Remover do array de listeners
        const index = listeners.findIndex(
            l => l.eventName === eventName && l.handler === handler
        );
        if (index > -1) {
            listeners.splice(index, 1);
        }
    };

    /**
     * Escutar evento UMA VEZ
     * @param {String} eventName
     * @param {Function} handler
     */
    const once = (eventName, handler) => {
        const onceHandler = (event) => {
            handler(event);
            off(eventName, onceHandler);
        };
        
        on(eventName, onceHandler);
    };

    /**
     * Cleanup automático quando componente é desmontado
     */
    onUnmounted(() => {
        listeners.forEach(({ eventName, handler }) => {
            window.removeEventListener(eventName, handler);
        });
        listeners.length = 0; // Limpar array
    });

    return {
        // Constantes de eventos
        events: APP_EVENTS,
        
        // Métodos
        emit,
        on,
        off,
        once
    };
}

/**
 * Helpers específicos para eventos comuns
 */

export function useUserEvents() {
    const { events, emit, on, off } = useEvents();
    
    return {
        onPhotoUpdated: (handler) => on(events.USER_PHOTO_UPDATED, handler),
        emitPhotoUpdated: (url) => emit(events.USER_PHOTO_UPDATED, { url }),
        onUserUpdated: (handler) => on(events.USER_UPDATED, handler),
        emitUserUpdated: (user) => emit(events.USER_UPDATED, { user })
    };
}

export function useThemeEvents() {
    const { events, emit, on } = useEvents();
    
    return {
        onThemeChanged: (handler) => on(events.THEME_CHANGED, handler),
        emitThemeChanged: (theme) => emit(events.THEME_CHANGED, { theme }),
        onModeChanged: (handler) => on(events.THEME_MODE_CHANGED, handler),
        emitModeChanged: (mode) => emit(events.THEME_MODE_CHANGED, { mode })
    };
}

export function useOverlayEvents() {
    const { events, emit, on } = useEvents();
    
    return {
        onToggle: (handler) => on(events.OVERLAY_TOGGLE, handler),
        emitToggle: (isOpen) => emit(events.OVERLAY_TOGGLE, { isOpen }),
        onOpen: (handler) => on(events.OVERLAY_OPEN, handler),
        emitOpen: () => emit(events.OVERLAY_OPEN),
        onClose: (handler) => on(events.OVERLAY_CLOSE, handler),
        emitClose: () => emit(events.OVERLAY_CLOSE)
    };
}
