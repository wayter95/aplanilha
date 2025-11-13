/**
 * Utilitários centralizados para manipulação DOM
 * Uso: import { getHtml, setAttr, removeAttr, addClass, removeClass } from '@/utils/dom'
 */

export const getHtml = () => {
  return document.querySelector('html')
}

export const getElement = (selector) => {
  return document.querySelector(selector)
}

export const getElements = (selector) => {
  return document.querySelectorAll(selector)
}

export const setAttr = (element, attr, value) => {
  if (!element) return false
  element.setAttribute(attr, value)
  return true
}

export const getAttr = (element, attr) => {
  if (!element) return null
  return element.getAttribute(attr)
}

export const removeAttr = (element, attr) => {
  if (!element) return false
  element.removeAttribute(attr)
  return true
}

export const hasAttr = (element, attr) => {
  if (!element) return false
  return element.hasAttribute(attr)
}

export const addClass = (element, ...classes) => {
  if (!element) return false
  element.classList.add(...classes)
  return true
}

export const removeClass = (element, ...classes) => {
  if (!element) return false
  element.classList.remove(...classes)
  return true
}

export const toggleClass = (element, className) => {
  if (!element) return false
  element.classList.toggle(className)
  return true
}

export const hasClass = (element, className) => {
  if (!element) return false
  return element.classList.contains(className)
}

export const setStyle = (element, property, value) => {
  if (!element) return false
  element.style.setProperty(property, value)
  return true
}

export const getStyle = (element, property) => {
  if (!element) return null
  return element.style.getPropertyValue(property)
}

export const removeStyle = (element, property) => {
  if (!element) return false
  element.style.removeProperty(property)
  return true
}

export default {
  getHtml,
  getElement,
  getElements,
  setAttr,
  getAttr,
  removeAttr,
  hasAttr,
  addClass,
  removeClass,
  toggleClass,
  hasClass,
  setStyle,
  getStyle,
  removeStyle
}
