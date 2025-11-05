/**
 * Sistema de Registro Dinâmico de Componentes para Tabs
 * 
 * Permite registrar componentes de formulário de forma dinâmica,
 * sem precisar editar o AppLayout para cada novo formulário.
 */

type ComponentLoader = () => Promise<any>

/**
 * Mapa de componentes registrados
 * Chave: nome do componente (componentName)
 * Valor: função que carrega o componente
 */
const componentRegistry = new Map<string, ComponentLoader>()

/**
 * Registra um componente para uso em tabs
 * 
 * @param name - Nome único do componente (ex: 'UsersForm', 'DocumentTypesForm')
 * @param loader - Função que importa o componente (ex: () => import('@/Pages/Users/Form.vue'))
 * 
 * @example
 * registerTabComponent('UsersForm', () => import('@/Pages/Users/Form.vue'))
 */
export function registerTabComponent(name: string, loader: ComponentLoader): void {
  if (componentRegistry.has(name)) {
    console.warn(`Componente "${name}" já está registrado. Sobrescrevendo...`)
  }
  componentRegistry.set(name, loader)
}

/**
 * Obtém o loader de um componente registrado
 * 
 * @param name - Nome do componente
 * @returns Função loader ou null se não encontrado
 */
export function getTabComponent(name: string): ComponentLoader | null {
  return componentRegistry.get(name) || null
}

/**
 * Verifica se um componente está registrado
 * 
 * @param name - Nome do componente
 * @returns true se está registrado
 */
export function hasTabComponent(name: string): boolean {
  return componentRegistry.has(name)
}

/**
 * Lista todos os componentes registrados
 * 
 * @returns Array com nomes dos componentes
 */
export function listTabComponents(): string[] {
  return Array.from(componentRegistry.keys())
}

/**
 * Remove um componente do registro
 * 
 * @param name - Nome do componente
 */
export function unregisterTabComponent(name: string): void {
  componentRegistry.delete(name)
}

/**
 * Limpa todos os componentes registrados
 */
export function clearTabComponents(): void {
  componentRegistry.clear()
}

/**
 * Carrega um componente de forma assíncrona
 * 
 * @param name - Nome do componente
 * @returns Promise com o módulo do componente
 * @throws Error se componente não encontrado
 */
export async function loadTabComponent(name: string): Promise<any> {
  const loader = getTabComponent(name)
  
  if (!loader) {
    throw new Error(`Componente "${name}" não está registrado. Use registerTabComponent() primeiro.`)
  }
  
  try {
    const module = await loader()
    return module.default || module
  } catch (error) {
    console.error(`Erro ao carregar componente "${name}":`, error)
    throw error
  }
}

// ============================================================================
// REGISTRO DE COMPONENTES PADRÃO
// ============================================================================

// Registra componentes existentes automaticamente
registerTabComponent('DocumentTemplatesForm', () => import('@/Pages/DocumentTemplates/Form.vue'))
registerTabComponent('DocumentTypesForm', () => import('@/Pages/DocumentTypes/Form.vue'))

// Para adicionar novos componentes, use:
// registerTabComponent('UsersForm', () => import('@/Pages/Users/Form.vue'))
// registerTabComponent('RolesForm', () => import('@/Pages/Roles/Form.vue'))
// etc...

