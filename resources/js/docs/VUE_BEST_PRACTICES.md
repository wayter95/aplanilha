# Boas Práticas Vue.js - Organização do `<script setup>`

## ❌ Código RUIM (Desorganizado)

```vue
<script setup>
import Header from '@/Components/Header.vue'
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'

const user = ref(null)

router.on('navigate', () => {
  console.log('navigated')
})

const loadUser = () => {
  // ...
}

onMounted(() => {
  loadUser()
})

const something = ref(null)

router.on('start', () => {
  console.log('started')
})

const anotherThing = computed(() => {
  return something.value * 2
})
</script>
```

**Problemas:**
- ❌ Código espalhado sem ordem lógica
- ❌ Imports misturados com lógica
- ❌ Difícil de ler e manter
- ❌ Variáveis sem agrupamento semântico

---

## ✅ Código BOM (Organizado)

```vue
<script setup>
// 1. IMPORTS (sempre no topo)
import Header from '@/Components/Header.vue'
import { ref, computed, onMounted } from 'vue'
import { useRouterEvents } from '@/composables/useRouterEvents'
import { getElement } from '@/utils/dom'

// 2. PROPS & EMITS
const props = defineProps({
  user: Object,
  title: String
})

const emit = defineEmits(['update', 'close'])

// 3. COMPOSABLES (reutilizáveis)
const { onStart, onNavigate } = useRouterEvents()

// 4. STATE (refs, reactive)
const something = ref(null)
const loading = ref(false)

// 5. COMPUTED (valores derivados)
const anotherThing = computed(() => {
  return something.value * 2
})

// 6. METHODS (funções)
const loadUser = () => {
  // ...
}

const handleClick = () => {
  // ...
}

// 7. LIFECYCLE HOOKS
onMounted(() => {
  loadUser()
})

// 8. WATCHERS & ROUTER EVENTS (efeitos colaterais)
onStart(() => {
  console.log('started')
})

onNavigate(() => {
  console.log('navigated')
})
</script>
```

---

## 📋 Ordem Recomendada

### 1️⃣ **Imports** (sempre primeiro)
```javascript
import ComponentA from '@/Components/ComponentA.vue'
import ComponentB from '@/Components/ComponentB.vue'
import { ref, computed, watch, onMounted } from 'vue'
import { useMyComposable } from '@/composables/useMyComposable'
import { helper } from '@/utils/helpers'
```

**Ordem de imports:**
1. Componentes Vue
2. Vue core (ref, computed, etc)
3. Composables
4. Utils/Helpers
5. Assets (imagens, CSS)

---

### 2️⃣ **Props & Emits** (interface do componente)
```javascript
const props = defineProps({
  user: Object,
  title: String,
  isActive: { type: Boolean, default: false }
})

const emit = defineEmits(['update', 'close', 'submit'])
```

**Por quê aqui?**
- Define a interface pública do componente
- Fácil de ver o que o componente recebe/emite

---

### 3️⃣ **Composables** (lógica reutilizável)
```javascript
const { user, login, logout } = useAuth()
const { onStart, onNavigate } = useRouterEvents()
const { getElement, setAttr } = useDom()
```

**Por quê aqui?**
- Mostra dependências externas
- Separa lógica compartilhada da lógica local

---

### 4️⃣ **State** (ref, reactive)
```javascript
const count = ref(0)
const loading = ref(false)
const form = reactive({
  name: '',
  email: ''
})
```

**Por quê aqui?**
- Define o estado local do componente
- Fácil identificar o que é estado mutável

---

### 5️⃣ **Computed** (valores derivados)
```javascript
const doubleCount = computed(() => count.value * 2)
const isValid = computed(() => form.name && form.email)
const displayName = computed(() => {
  return user.value?.name || 'Guest'
})
```

**Por quê aqui?**
- Valores que dependem de outros estados
- Fica claro que são derivados, não estado base

---

### 6️⃣ **Methods** (funções)
```javascript
const increment = () => {
  count.value++
}

const submitForm = async () => {
  loading.value = true
  try {
    await api.submit(form)
    emit('submit', form)
  } finally {
    loading.value = false
  }
}
```

**Por quê aqui?**
- Lógica de negócio do componente
- Event handlers

---

### 7️⃣ **Lifecycle Hooks** (ciclo de vida)
```javascript
onBeforeMount(() => {
  console.log('before mount')
})

onMounted(() => {
  loadData()
  initializeThirdPartyLib()
})

onUnmounted(() => {
  cleanup()
})
```

**Por quê aqui?**
- Efeitos que rodam em momentos específicos
- Inicialização e limpeza

---

### 8️⃣ **Watchers & Eventos** (efeitos colaterais)
```javascript
watch(() => props.user, (newUser) => {
  console.log('user changed', newUser)
})

watchEffect(() => {
  console.log('count is', count.value)
})

onNavigate(() => {
  closeModal()
})
```

**Por quê aqui?**
- Efeitos colaterais (side effects)
- Reações a mudanças
- Por último porque dependem de tudo acima

---

## 🎯 Exemplo Completo Bem Organizado

```vue
<script setup>
// 1. IMPORTS
import { ref, computed, onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { useAuth } from '@/composables/useAuth'
import { useRouterEvents } from '@/composables/useRouterEvents'
import { getElement, setAttr } from '@/utils/dom'
import Header from '@/Components/Header.vue'
import Sidebar from '@/Components/Sidebar.vue'

// 2. PROPS & EMITS
const props = defineProps({
  title: String,
  user: Object
})

const emit = defineEmits(['update', 'close'])

// 3. COMPOSABLES
const page = usePage()
const { login, logout } = useAuth()
const { onNavigate } = useRouterEvents()

// 4. STATE
const isMenuOpen = ref(false)
const loading = ref(false)

// 5. COMPUTED
const displayTitle = computed(() => {
  return props.title || 'Default Title'
})

const isLoggedIn = computed(() => {
  return !!props.user
})

// 6. METHODS
const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value
}

const handleLogout = async () => {
  loading.value = true
  try {
    await logout()
    emit('close')
  } finally {
    loading.value = false
  }
}

// 7. LIFECYCLE
onMounted(() => {
  console.log('Component mounted')
  const element = getElement('#menu')
  setAttr(element, 'data-initialized', 'true')
})

// 8. WATCHERS & EVENTS
onNavigate(() => {
  isMenuOpen.value = false
})
</script>
```

---

## 🚫 Evite Isso

### ❌ Código solto demais
```javascript
// Tudo misturado, difícil de ler
const user = ref(null)
router.on('navigate', () => {})
const something = computed(() => {})
const another = ref(0)
onMounted(() => {})
```

### ❌ Muita lógica inline
```javascript
// Lógica complexa direto no template
<button @click="loading ? null : count > 10 ? reset() : increment()">
  {{ loading ? 'Loading...' : count > 10 ? 'Reset' : 'Increment' }}
</button>

// ✅ MELHOR: Extrair para computed e methods
const buttonLabel = computed(() => {
  if (loading.value) return 'Loading...'
  return count.value > 10 ? 'Reset' : 'Increment'
})

const handleClick = () => {
  if (loading.value) return
  count.value > 10 ? reset() : increment()
}
```

### ❌ Props não tipadas
```javascript
// Sem tipos, sem defaults
defineProps(['user', 'title', 'items'])

// ✅ MELHOR: Com tipos e defaults
defineProps({
  user: { type: Object, required: true },
  title: { type: String, default: '' },
  items: { type: Array, default: () => [] }
})
```

---

## 📚 Resumo das Boas Práticas

| Prática | ✅ Fazer | ❌ Evitar |
|---------|---------|-----------|
| **Ordem** | Seguir ordem lógica (imports → props → state → methods) | Código espalhado aleatoriamente |
| **Agrupamento** | Agrupar código relacionado | Misturar conceitos diferentes |
| **Composables** | Extrair lógica reutilizável | Repetir código em vários componentes |
| **Utils** | Usar helpers para DOM, localStorage | `document.querySelector` direto |
| **Comentários** | Seções claras (// STATE, // METHODS) | Código sem estrutura |
| **Template** | Lógica simples | Lógica complexa inline |

---

## 🎓 Referências

- [Vue.js Style Guide](https://vuejs.org/style-guide/)
- [Composition API Best Practices](https://vuejs.org/guide/reusability/composables.html)
- [Script Setup Order](https://github.com/vuejs/rfcs/discussions/436)

**Última atualização:** 07/11/2025
