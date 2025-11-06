/**
 * Helpers para criação e gerenciamento de Tabs
 * 
 * Centraliza lógica comum para criar tabs de forma consistente
 */

import type { Tab } from '@/stores/useTabsStore'

export interface TabConfig {
  componentName: string
  context: string
  mode: 'create' | 'edit'
  id?: string
  tempKey?: string
  title?: string
  props?: Record<string, any>
  metadata?: Record<string, any>
}

/**
 * Gera o path de uma tab baseado no contexto e modo
 * 
 * @param config - Configuração da tab
 * @returns Path completo da tab
 */
export function generateTabPath(config: TabConfig): string {
  const { context, mode, id, tempKey } = config
  
  if (mode === 'create') {
    if (!tempKey) {
      throw new Error('tempKey é obrigatório para modo create')
    }
    return `/${context}/new/${tempKey}`
  }
  
  if (mode === 'edit') {
    if (!id) {
      throw new Error('id é obrigatório para modo edit')
    }
    return `/${context}/${id}/edit`
  }
  
  throw new Error(`Modo inválido: ${mode}`)
}

/**
 * Gera o título padrão de uma tab
 * 
 * @param config - Configuração da tab
 * @param customTitle - Título customizado (opcional)
 * @returns Título da tab
 */
export function generateTabTitle(
  config: TabConfig,
  customTitle?: string
): string {
  if (customTitle) {
    return customTitle
  }
  
  if (config.mode === 'create') {
    return `Novo ${config.context.replace('-', ' ')}`
  }
  
  if (config.mode === 'edit') {
    return 'Carregando…'
  }
  
  return 'Tab'
}

/**
 * Cria uma configuração de Tab de forma validada e consistente
 * 
 * @param config - Configuração da tab
 * @returns Objeto Tab válido
 * 
 * @example
 * const tab = createTabConfig({
 *   componentName: 'UsersForm',
 *   context: 'users',
 *   mode: 'create',
 *   tempKey: 'temp-123'
 * })
 */
export function createTabConfig(config: TabConfig): Tab {
  // Validações
  if (!config.componentName) {
    throw new Error('componentName é obrigatório')
  }
  
  if (!config.context) {
    throw new Error('context é obrigatório')
  }
  
  if (config.mode === 'create' && !config.tempKey) {
    throw new Error('tempKey é obrigatório para modo create')
  }
  
  if (config.mode === 'edit' && !config.id) {
    throw new Error('id é obrigatório para modo edit')
  }
  
  // Gera key baseado no modo
  const key = config.mode === 'create' ? config.tempKey! : config.id!
  
  // Gera path
  const path = generateTabPath(config)
  
  // Gera título
  const title = generateTabTitle(config, config.title)
  
  // Monta props padrão
  const props = {
    mode: config.mode,
    ...(config.mode === 'create' ? { tempKey: config.tempKey } : { id: config.id }),
    ...(config.props || {}),
  }
  
  return {
    key,
    title,
    mode: config.mode,
    componentName: config.componentName,
    path,
    props,
    context: config.context,
    ...(config.metadata && { metadata: config.metadata }),
  }
}

/**
 * Valida se uma Tab está bem formada
 * 
 * @param tab - Tab para validar
 * @returns true se válido
 */
export function validateTab(tab: Tab): boolean {
  if (!tab.key || !tab.title || !tab.componentName || !tab.path) {
    return false
  }
  
  if (tab.mode && !['create', 'edit'].includes(tab.mode)) {
    return false
  }
  
  return true
}

/**
 * Gera um tempKey único para tabs de criação
 * 
 * @returns String única (timestamp + random)
 */
export function generateTempKey(): string {
  return `temp-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`
}

