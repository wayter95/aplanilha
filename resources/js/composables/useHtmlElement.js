/**
 * Composable: useHtmlElement
 * 
 * Acesso centralizado ao elemento <html>
 * Usado para: theme switching, classes globais, etc
 */

import { ref, onMounted } from 'vue';

export function useHtmlElement() {
    const html = ref(null);

    onMounted(() => {
        html.value = document.querySelector('html');
    });

    /**
     * Adicionar classe ao <html>
     */
    const addClass = (className) => {
        if (html.value) {
            html.value.classList.add(className);
        }
    };

    /**
     * Remover classe do <html>
     */
    const removeClass = (className) => {
        if (html.value) {
            html.value.classList.remove(className);
        }
    };

    /**
     * Toggle classe no <html>
     */
    const toggleClass = (className) => {
        if (html.value) {
            html.value.classList.toggle(className);
        }
    };

    /**
     * Verificar se <html> tem classe
     */
    const hasClass = (className) => {
        return html.value ? html.value.classList.contains(className) : false;
    };

    /**
     * Obter atributo do <html>
     */
    const getAttribute = (attrName) => {
        return html.value ? html.value.getAttribute(attrName) : null;
    };

    /**
     * Definir atributo no <html>
     */
    const setAttribute = (attrName, value) => {
        if (html.value) {
            html.value.setAttribute(attrName, value);
        }
    };

    /**
     * Remover atributo do <html>
     */
    const removeAttribute = (attrName) => {
        if (html.value) {
            html.value.removeAttribute(attrName);
        }
    };

    return {
        html,
        addClass,
        removeClass,
        toggleClass,
        hasClass,
        getAttribute,
        setAttribute,
        removeAttribute
    };
}
