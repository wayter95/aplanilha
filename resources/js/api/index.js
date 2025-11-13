/**
 * API Module - Exports centralizados
 * 
 * Import único: import { documentTemplatesApi, documentTypesApi, usersApi } from '@/api'
 */

export { default as apiClient, get, post, put, patch, del } from './client';
export { default as documentTemplatesApi } from './documentTemplates';
export { default as documentTypesApi } from './documentTypes';
export { default as usersApi } from './users';
