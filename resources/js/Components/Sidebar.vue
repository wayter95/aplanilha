<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import logoFull from '../../assets/images/brand-logos/logo-full.png'
import logoIcon from '../../assets/images/brand-logos/logo-icon.png'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    isCollapsed: { type: Boolean, default: true },
    isHovered: { type: Boolean, default: false },
    menuItems: { type: Array, default: () => [] }
})

const emit = defineEmits(['link-click'])
const emitLinkClick = () => emit('link-click')

const openSubmenus = ref([])

const toggleSubmenu = (index) => {
    if (props.isCollapsed && !props.isHovered) return
    const label = props.menuItems[index]?.label
    if (!label) return

    if (openSubmenus.value.includes(label)) {
        openSubmenus.value = []
    } else {
        openSubmenus.value = [label]
    }

    localStorage.setItem('sidebar-open-submenus', JSON.stringify(openSubmenus.value))
}

const isSubmenuOpen = (index) => {
    const label = props.menuItems[index]?.label
    return openSubmenus.value.includes(label)
}

const showLabels = computed(() => !props.isCollapsed || props.isHovered)

const currentLogo = computed(() => {
    if (props.isCollapsed && !props.isHovered) return logoIcon
    return logoFull
})

onMounted(() => {
    const saved = localStorage.getItem('sidebar-open-submenus')
    if (saved) {
        try {
            const restored = JSON.parse(saved)
            if (Array.isArray(restored)) {
                setTimeout(() => {
                    openSubmenus.value = restored
                }, 200)
            }
        } catch {
            openSubmenus.value = []
        }
    }
})

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
        <!-- LOGO -->
        <div 
            class="sidebar-logo-area flex items-center justify-center"
            :class="{
                'logo-collapsed': isCollapsed && !isHovered,
                'logo-expanded': !isCollapsed || isHovered
            }"
        >
            <a href="/" class="header-logo flex items-center justify-center w-full h-full">
                <img 
                    :src="currentLogo"
                    alt="Logo Aplanilha"
                    class="sidebar-logo-img"
                />
            </a>
        </div>

        <!-- MENU -->
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
/* Estrutura principal */
.app-sidebar {
    position: fixed;
    top: 0;
    left: 0;
    height: 100%;
    background: #0f172a;
    transition: width 0.35s ease-in-out, box-shadow 0.3s ease;
    overflow-y: auto;
    color: #fff;
    z-index: 999;
    box-shadow: 2px 0 12px rgba(0, 0, 0, 0.25);
}

/* === LOGO AREA === */
.sidebar-logo-area {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 64px; /* altura da barra superior */
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    transition: all 0.3s ease;
}

/* Centralização da imagem */
.sidebar-logo-img {
    max-width: 140px;
    height: auto;
    object-fit: contain;
    transition: width 0.3s ease;
}

/* colapsada */
.logo-collapsed .sidebar-logo-img {
    max-width: 48px;
}

/* Sidebar colapsada e expandida */
.app-sidebar.sidebar-collapsed {
    width: 72px;
}
.app-sidebar.sidebar-hovered,
.app-sidebar.sidebar-open {
    width: 250px;
}

/* MENU */
.main-sidebar {
    padding-top: 0.75rem;
    transition: all 0.3s ease;
}

/* ITENS */
.side-menu__item {
    display: flex;
    align-items: center;
    padding: 0.85rem 1rem;
    color: #d1d5db;
    text-decoration: none;
    border: none;
    width: 100%;
    background: transparent;
    transition: all 0.25s ease;
    border-left: 3px solid transparent;
}

.side-menu__item:hover {
    background: rgba(255, 255, 255, 0.08);
    color: #fff;
    border-left-color: #8b5cf6;
    transform: translateX(2px);
}

/* ÍCONES */
.side-menu__icon {
    font-size: 1.4rem;
    margin-right: 1rem;
    transition: transform 0.25s ease;
}

.side-menu__item:hover .side-menu__icon {
    transform: scale(1.15);
}

/* Colapsada */
.sidebar-collapsed .side-menu__item .side-menu__icon {
    margin-right: 0;
    width: 100%;
    text-align: center;
    font-size: 1.5rem;
}

.sidebar-collapsed .side-menu__label {
    display: none;
}

/* SUBMENU */
.submenu {   
    padding-left: 1.5rem;
    overflow: hidden;
    border-left: 2px solid rgba(139, 92, 246, 0.25);
}

.submenu-item {
    padding: 0.7rem 1rem;
    font-size: 0.95rem;
}

.submenu-item:hover {
    background: rgba(255, 255, 255, 0.08);
}

/* Arrow */
.side-menu__arrow {
    margin-left: auto;
    font-size: 1rem;
    transition: transform 0.3s ease;
}
.is-open .side-menu__arrow {
    transform: rotate(180deg);
}

/* Animações suaves */
.submenu-slide-enter-active,
.submenu-slide-leave-active {
    transition: all 0.35s cubic-bezier(0.25, 0.8, 0.25, 1);
}
.submenu-slide-enter-from,
.submenu-slide-leave-to {
    max-height: 0;
    opacity: 0;
    transform: translateY(-8px);
}
.submenu-slide-enter-to,
.submenu-slide-leave-from {
    max-height: 400px;
    opacity: 1;
    transform: translateY(0);
}
</style>
