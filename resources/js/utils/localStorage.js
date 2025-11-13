/**
 * Utilitários centralizados para localStorage
 * Uso: import { getItem, setItem, removeItem, getJSON, setJSON } from '@/utils/localStorage'
 */

export const getItem = (key) => {
  try {
    return localStorage.getItem(key)
  } catch (error) {
    console.error(`[localStorage] Erro ao ler ${key}:`, error)
    return null
  }
}

export const setItem = (key, value) => {
  try {
    localStorage.setItem(key, value)
    return true
  } catch (error) {
    console.error(`[localStorage] Erro ao salvar ${key}:`, error)
    return false
  }
}

export const removeItem = (key) => {
  try {
    localStorage.removeItem(key)
    return true
  } catch (error) {
    console.error(`[localStorage] Erro ao remover ${key}:`, error)
    return false
  }
}

export const getJSON = (key, defaultValue = null) => {
  try {
    const value = localStorage.getItem(key)
    return value ? JSON.parse(value) : defaultValue
  } catch (error) {
    console.error(`[localStorage] Erro ao ler JSON ${key}:`, error)
    return defaultValue
  }
}

export const setJSON = (key, value) => {
  try {
    localStorage.setItem(key, JSON.stringify(value))
    return true
  } catch (error) {
    console.error(`[localStorage] Erro ao salvar JSON ${key}:`, error)
    return false
  }
}

export const clear = () => {
  try {
    localStorage.clear()
    return true
  } catch (error) {
    console.error('[localStorage] Erro ao limpar:', error)
    return false
  }
}

export const has = (key) => {
  return localStorage.getItem(key) !== null
}

export default {
  getItem,
  setItem,
  removeItem,
  getJSON,
  setJSON,
  clear,
  has
}
