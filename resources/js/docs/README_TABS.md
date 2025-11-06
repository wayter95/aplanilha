# 📚 Documentação do Sistema de Tabs

Bem-vindo à documentação completa do Sistema de Tabs!

---

## 📖 Documentos Disponíveis

### 🚀 [Quick Start](./TABS_QUICK_START.md)

**⏱️ 5 minutos**

Guia rápido para adicionar tabs em um novo módulo. Ideal para quem quer começar rapidamente.

**Quando usar:**

-   ✅ Primeira vez usando o sistema
-   ✅ Quer implementar rapidamente
-   ✅ Precisa de um exemplo básico

---

### 📘 [Guia Completo](./TABS_COMPLETE_GUIDE.md)

**⏱️ 30 minutos**

Documentação completa e detalhada com todos os passos, exemplos práticos e casos de uso avançados.

**Quando usar:**

-   ✅ Precisa entender todos os detalhes
-   ✅ Quer implementar casos avançados
-   ✅ Precisa de referência completa
-   ✅ Quer seguir boas práticas

**Conteúdo:**

-   Passo a passo completo
-   Exemplos práticos
-   API de referência
-   Casos de uso avançados
-   Troubleshooting
-   Boas práticas
-   Checklist de implementação

---

### 📚 [Guia de Uso](./TABS_USAGE_GUIDE.md)

**⏱️ 15 minutos**

Guia de referência rápida com exemplos de código e API detalhada.

**Quando usar:**

-   ✅ Precisa consultar a API
-   ✅ Quer ver exemplos de código
-   ✅ Precisa de referência rápida

**Conteúdo:**

-   Como adicionar novo formulário
-   API do composable `useTabForm`
-   Sistema de hooks
-   Helpers disponíveis
-   Exemplos completos
-   Troubleshooting

---

## 🎯 Por Onde Começar?

### Sou novo no sistema

1. Comece pelo **[Quick Start](./TABS_QUICK_START.md)** para um exemplo rápido
2. Depois leia o **[Guia Completo](./TABS_COMPLETE_GUIDE.md)** para entender tudo

### Já conheço o sistema

1. Use o **[Guia de Uso](./TABS_USAGE_GUIDE.md)** como referência
2. Consulte o **[Guia Completo](./TABS_COMPLETE_GUIDE.md)** para casos avançados

### Preciso de referência rápida

1. Use o **[Guia de Uso](./TABS_USAGE_GUIDE.md)** - API e exemplos

---

## 📋 Checklist Rápido

Para adicionar tabs em um novo módulo:

-   [ ] Registrar componente em `tabComponents.ts`
-   [ ] Criar componente `Form.vue` com `useTabForm`
-   [ ] Criar rotas no backend
-   [ ] Criar controller com métodos `create` e `edit`
-   [ ] Configurar salvamento automático com `watch`
-   [ ] Implementar função de salvar
-   [ ] Adicionar botões na listagem (opcional)

**Tempo estimado:** 10-15 minutos

---

## 🔍 Exemplos de Código

### Exemplo Básico

```typescript
// 1. Registrar
registerTabComponent("UsersForm", () => import("@/Pages/Users/Form.vue"));

// 2. Usar no componente
const { saveFormData, updateTabTitle } = useTabForm({
    componentName: "UsersForm",
    context: "users",
    mode: "create",
    getTitle: (form) => form.name || "Novo Usuário",
});
```

Veja mais exemplos em:

-   [Quick Start - Exemplos](./TABS_QUICK_START.md)
-   [Guia Completo - Exemplos Práticos](./TABS_COMPLETE_GUIDE.md#exemplos-práticos)

---

## 🛠️ Arquivos Principais

### Frontend

-   `resources/js/config/tabComponents.ts` - Registro de componentes
-   `resources/js/composables/useTabForm.ts` - Composable principal
-   `resources/js/stores/useTabsStore.ts` - Store Pinia
-   `resources/js/stores/useTabFormDataStore.ts` - Store de dados
-   `resources/js/utils/tabHelpers.ts` - Helpers utilitários

### Exemplos

-   `resources/js/Pages/DocumentTypes/Form.vue` - Exemplo de uso
-   `resources/js/Pages/DocumentTemplates/Form.vue` - Exemplo de uso

---

## ❓ FAQ

### Como adicionar um novo formulário?

→ Veja [Quick Start](./TABS_QUICK_START.md)

### Como validar antes de fechar?

→ Veja [Casos de Uso Avançados](./TABS_COMPLETE_GUIDE.md#casos-de-uso-avançados)

### Como funciona o salvamento automático?

→ Veja [Guia Completo - Salvamento](./TABS_COMPLETE_GUIDE.md#salvamento-de-dados)

### Erro "Componente não está registrado"

→ Veja [Troubleshooting](./TABS_COMPLETE_GUIDE.md#troubleshooting)

---

## 📞 Suporte

Se tiver dúvidas:

1. Consulte a documentação apropriada acima
2. Veja os exemplos em `resources/js/Pages/DocumentTypes/Form.vue`
3. Verifique o código fonte dos arquivos principais

---

## 📝 Changelog

### Versão 1.0 (2025-11-05)

-   ✅ Sistema de registro dinâmico
-   ✅ Composable reutilizável
-   ✅ Store genérica
-   ✅ Sistema de hooks
-   ✅ Documentação completa

---

**Última atualização:** 2025-11-05
