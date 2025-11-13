/**
 * Composable para gerenciar eventos do Inertia Router
 * Uso: import { useRouterEvents } from '@/composables/useRouterEvents'
 * 
 * const { onStart, onFinish, onNavigate, onSuccess, onError } = useRouterEvents()
 * 
 * onStart(() => console.log('Navigation started'))
 * onFinish(() => console.log('Navigation finished'))
 */

import { router } from '@inertiajs/vue3'
import { onMounted, onUnmounted } from 'vue'

export function useRouterEvents() {
  const callbacks = {
    start: [],
    finish: [],
    navigate: [],
    success: [],
    error: [],
    before: [],
    progress: []
  }

  const cleanup = []

  const onStart = (callback) => {
    callbacks.start.push(callback)
    const unregister = router.on('start', callback)
    cleanup.push(unregister)
    return unregister
  }

  const onFinish = (callback) => {
    callbacks.finish.push(callback)
    const unregister = router.on('finish', callback)
    cleanup.push(unregister)
    return unregister
  }

  const onNavigate = (callback) => {
    callbacks.navigate.push(callback)
    const unregister = router.on('navigate', callback)
    cleanup.push(unregister)
    return unregister
  }

  const onSuccess = (callback) => {
    callbacks.success.push(callback)
    const unregister = router.on('success', callback)
    cleanup.push(unregister)
    return unregister
  }

  const onError = (callback) => {
    callbacks.error.push(callback)
    const unregister = router.on('error', callback)
    cleanup.push(unregister)
    return unregister
  }

  const onBefore = (callback) => {
    callbacks.before.push(callback)
    const unregister = router.on('before', callback)
    cleanup.push(unregister)
    return unregister
  }

  const onProgress = (callback) => {
    callbacks.progress.push(callback)
    const unregister = router.on('progress', callback)
    cleanup.push(unregister)
    return unregister
  }

  const cleanupAll = () => {
    cleanup.forEach(fn => fn && fn())
    cleanup.length = 0
  }

  onUnmounted(() => {
    cleanupAll()
  })

  return {
    onStart,
    onFinish,
    onNavigate,
    onSuccess,
    onError,
    onBefore,
    onProgress,
    cleanupAll
  }
}
