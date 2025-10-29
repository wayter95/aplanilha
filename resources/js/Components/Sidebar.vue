<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    isCollapsed: { type: Boolean, default: true },
    isHovered: { type: Boolean, default: false },
    menuItems: { type: Array, default: () => [] }
})

const emit = defineEmits(['link-click'])
const emitLinkClick = () => emit('link-click')

// Submenus abertos (armazenados por label)
const openSubmenus = ref([])

/**
 * Alterna o estado do submenu (com comportamento de acordeão)
 */
const toggleSubmenu = (index) => {
    if (props.isCollapsed && !props.isHovered) return

    const label = props.menuItems[index]?.label
    if (!label) return

    if (openSubmenus.value.includes(label)) {
        // Fecha se já estiver aberto
        openSubmenus.value = []
    } else {
        // Fecha todos e abre apenas o atual
        openSubmenus.value = [label]
    }

    // Atualiza localStorage
    localStorage.setItem('sidebar-open-submenus', JSON.stringify(openSubmenus.value))
}

/**
 * Verifica se submenu está aberto
 */
const isSubmenuOpen = (index) => {
    const label = props.menuItems[index]?.label
    return openSubmenus.value.includes(label)
}

/**
 * Exibir labels apenas quando sidebar estiver expandida
 */
const showLabels = computed(() => !props.isCollapsed || props.isHovered)

/**
 * Restaurar submenus abertos (com atraso para permitir animação)
 */
onMounted(() => {
    const saved = localStorage.getItem('sidebar-open-submenus')
    if (saved) {
        try {
            const restored = JSON.parse(saved)
            if (Array.isArray(restored)) {
                // Pequeno atraso para suavizar abertura
                setTimeout(() => {
                    openSubmenus.value = restored
                }, 200)
            }
        } catch {
            openSubmenus.value = []
        }
    }
})

// Atualizar localStorage quando mudar
watch(openSubmenus, (val) => {
    localStorage.setItem('sidebar-open-submenus', JSON.stringify(val))
}, { deep: true })
</script>

<template>
    <aside 
        :class="[
            'app-sidebar',
            { 
                'sidebar-collapsed': isCollapsed && !isHovered,
                'sidebar-hovered': isCollapsed && isHovered,
                'sidebar-open': !isCollapsed
            }
        ]"
    >
        <div class="main-sidebar-header">
            <a href="/" class="header-logo">
                <h3 class="text-xl font-bold text-white">Aplanilha</h3>
            </a>
        </div>

        <div class="main-sidebar" id="sidebar-scroll">
            <ul class="main-menu">
                <li 
                    v-for="(item, index) in menuItems" 
                    :key="index"
                    class="slide"
                >
                    <!-- Item simples -->
                    <template v-if="!item.children">
                        <Link 
                            :href="item.route"
                            class="side-menu__item"
                            @click="emitLinkClick"
                        >
                            <i :class="[item.icon, 'side-menu__icon']"></i>
                            <span v-if="showLabels" class="side-menu__label">{{ item.label }}</span>
                        </Link>
                    </template>

                    <!-- Item com submenu -->
                    <template v-else>
                        <button 
                            class="side-menu__item submenu-toggle"
                            @click="toggleSubmenu(index)"
                        >
                            <i :class="[item.icon, 'side-menu__icon']"></i>
                            <span v-if="showLabels" class="side-menu__label">{{ item.label }}</span>
                            <i 
                                v-if="showLabels"
                                class="bx side-menu__arrow"
                                :class="isSubmenuOpen(index) ? 'bx-chevron-up' : 'bx-chevron-down'"
                            ></i>
                        </button>

                        <transition name="submenu-slide">
                            <ul 
                                v-show="isSubmenuOpen(index) && showLabels"
                                class="submenu"
                            >
                                <li 
                                    v-for="(child, cIndex) in item.children"
                                    :key="cIndex"
                                >
                                    <Link 
                                        :href="child.route"
                                        class="side-menu__item submenu-item"
                                        @click="emitLinkClick"
                                    >
                                        <i :class="[child.icon, 'side-menu__icon']"></i>
                                        <span class="side-menu__label">{{ child.label }}</span>
                                    </Link>
                                </li>
                            </ul>
                        </transition>
                    </template>
                </li>
            </ul>
        </div>
    </aside>
</template>

<style scoped>
.app-sidebar {
    position: fixed;
    top: 0;
    left: 0;
    height: 100%;
    background: #1f2937;
    transition: width 0.3s ease;
    overflow-y: auto;
    color: #fff;
    z-index: 999;
}

.app-sidebar.sidebar-collapsed {
    width: 72px;
}

.app-sidebar.sidebar-hovered,
.app-sidebar.sidebar-open {
    width: 250px;
}

.side-menu__item {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    color: #d1d5db;
    text-decoration: none;
    border: none;
    width: 100%;
    background: transparent;
    transition: background 0.2s ease, color 0.2s ease;
}

.side-menu__item:hover {
    background: rgba(255, 255, 255, 0.08);
    color: #fff;
}

.side-menu__icon {
    font-size: 1.5rem;
    margin-right: 1rem;
}

.sidebar-collapsed .side-menu__label {
    display: none;
}

.submenu {
    background: transparent;
    padding-left: 1.5rem;
    overflow: hidden;
}

.submenu-item:hover {
    background: rgba(255, 255, 255, 0.08);
}

.side-menu__arrow {
    margin-left: auto;
}

/* 🔥 Animação suave para abertura e fechamento dos submenus */
.submenu-slide-enter-active,
.submenu-slide-leave-active {
    transition: all 0.3s ease;
}
.submenu-slide-enter-from,
.submenu-slide-leave-to {
    max-height: 0;
    opacity: 0;
    transform: translateY(-5px);
}
.submenu-slide-enter-to,
.submenu-slide-leave-from {
    max-height: 500px;
    opacity: 1;
    transform: translateY(0);
}
</style>
