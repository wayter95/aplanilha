import { ref } from "vue";

// Estado global dos toasts
const toasts = ref([]);
let toastId = 0;

// Configurações padrão
const defaultConfig = {
    duration: 5000,
    position: "top-right",
    closable: true,
    pauseOnHover: true,
};

// Tipos de toast disponíveis
const toastTypes = {
    success: {
        class: "alert alert-solid-success",
        icon: "success",
    },
    error: {
        class: "alert alert-solid-danger",
        icon: "danger",
    },
    warning: {
        class: "alert alert-solid-warning",
        icon: "warning",
    },
    info: {
        class: "alert alert-solid-info",
        icon: "primary",
    },
};

export function useToast() {
    // Adicionar novo toast
    const addToast = (message, type = "info", options = {}) => {
        const config = { ...defaultConfig, ...options };
        const toastType = toastTypes[type] || toastTypes.info;

        const toast = {
            id: ++toastId,
            message,
            type,
            config,
            class: toastType.class,
            icon: toastType.icon,
            visible: true,
            paused: false,
        };

        toasts.value.push(toast);

        // Auto remover após duração
        if (config.duration > 0) {
            setTimeout(() => {
                removeToast(toast.id);
            }, config.duration);
        }

        return toast.id;
    };

    // Remover toast específico
    const removeToast = (id) => {
        const index = toasts.value.findIndex((toast) => toast.id === id);
        if (index > -1) {
            toasts.value[index].visible = false;
            // Remove do array após animação
            setTimeout(() => {
                toasts.value.splice(index, 1);
            }, 300);
        }
    };

    // Limpar todos os toasts
    const clearAll = () => {
        toasts.value.forEach((toast) => {
            toast.visible = false;
        });
        setTimeout(() => {
            toasts.value.splice(0);
        }, 300);
    };

    // Pausar toast
    const pauseToast = (id) => {
        const toast = toasts.value.find((t) => t.id === id);
        if (toast && toast.config.pauseOnHover) {
            toast.paused = true;
        }
    };

    // Retomar toast
    const resumeToast = (id) => {
        const toast = toasts.value.find((t) => t.id === id);
        if (toast && toast.config.pauseOnHover) {
            toast.paused = false;
        }
    };

    // Métodos de conveniência
    const success = (message, options = {}) =>
        addToast(message, "success", options);
    const error = (message, options = {}) =>
        addToast(message, "error", options);
    const warning = (message, options = {}) =>
        addToast(message, "warning", options);
    const info = (message, options = {}) => addToast(message, "info", options);

    return {
        toasts,
        addToast,
        removeToast,
        clearAll,
        pauseToast,
        resumeToast,
        success,
        error,
        warning,
        info,
    };
}
