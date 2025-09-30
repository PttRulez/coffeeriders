<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import FormInput from '@/components/form-elements/FormInput.vue'

const model = defineModel<string>({ default: '' })

type Props = {
  class?: string
  errorMessage?: string
  fieldName: string
  label?: string
  placeholder?: string
}
const props = withDefaults(defineProps<Props>(), {
  placeholder: 'Телефон',
})

const view = ref('')

const digitsOnly = (s: string) => (s.match(/\d/g) || []).join('')
const countDigits = (s: string) => (s.match(/\d/g) || []).length

function normalizeToE164(raw: string): string {
  let d = digitsOnly(raw)
  if (d.startsWith('8')) d = '7' + d.slice(1)
  if (!d.startsWith('7')) d = '7' + d
  d = d.slice(0, 11)
  return `+${d}`
}

// форматируем, закрывая скобку только при >= 3 цифрах кода
function formatFromDigits(d: string) {
  // d: "7" + до 10 цифр
  const len = d.length
  const get = (i: number) => d[i] ?? ''
  let out = '+7'
  if (len >= 2) {
    out += ' (' + get(1)
    if (len >= 3) out += get(2)
    if (len >= 4) out += get(3) + ') '
  }
  if (len >= 5) out += get(4)
  if (len >= 6) out += get(5)
  if (len >= 7) out += get(6)
  if (len >= 8) out += '-' + get(7)
  if (len >= 9) out += get(8)
  if (len >= 10) out += '-' + get(9)
  if (len >= 11) out += get(10)
  return out
}

function formatRuPhone(raw: string) {
  let d = digitsOnly(raw)
  if (d.startsWith('8')) d = '7' + d.slice(1)
  if (!d.startsWith('7')) d = '7' + d
  d = d.slice(0, 11)
  return formatFromDigits(d)
}

// позиция каретки ПОСЛЕ n-й цифры (n=1 — ведущая «7»)
function caretPosAfterNthDigit(viewStr: string, n: number) {
  if (n <= 1) {
    // после "+7 " или после "(" если его ещё нет
    const idx = viewStr.indexOf('(')
    return idx >= 0 ? idx + 1 : Math.min(viewStr.length, 3)
  }
  let seen = 0
  for (let i = 0; i < viewStr.length; i++) {
    if (/\d/.test(viewStr[i])) {
      seen++
      if (seen === n) return i + 1
    }
  }
  return viewStr.length
}

// ——— sync model ↔ view ———
onMounted(() => {
  if (model.value) {
    const d = digitsOnly(model.value)
    view.value = formatFromDigits(d)
  }
})

watch(model, (val) => {
  const d = digitsOnly(val || '')
  view.value = d ? formatFromDigits(d) : ''
})

// ——— handlers ———
function onFocus(e: FocusEvent) {
  const el = e.target as HTMLInputElement
  if (!view.value?.startsWith('+7')) {
    view.value = '+7'
    requestAnimationFrame(() => el.setSelectionRange(view.value.length, view.value.length))
  }
}

function onClick(e: MouseEvent) {
  const el = e.target as HTMLInputElement
  const s = el.selectionStart ?? 0
  const t = el.selectionEnd ?? s
  if (s < 2 || t < 2) {
    const pos = caretPosAfterNthDigit(view.value, 1)
    requestAnimationFrame(() => el.setSelectionRange(pos, pos))
  }
}

function onKeydown(e: KeyboardEvent) {
  const el = e.target as HTMLInputElement
  const s = el.selectionStart ?? 0
  // защита префикса
  if ((e.key === 'Backspace' && s <= 2) || (e.key === 'Delete' && s < 2)) {
    e.preventDefault()
    return
  }
  // если печатают цифру слева от +7 — переносим каретку внутрь до ввода
  if (/^[0-9]$/.test(e.key) && s <= 2) {
    const pos = caretPosAfterNthDigit(view.value, 1)
    el.setSelectionRange(pos, pos)
  }
}

function onInput(e: Event) {
  const el = e.target as HTMLInputElement
  // считаем, сколько цифр слева от каретки ДО форматирования
  const nativePos = el.selectionStart ?? 0
  let leftDigits = countDigits(el.value.slice(0, nativePos))
  if (leftDigits < 1) leftDigits = 1 // минимум — ведущая 7

  // форматируем + нормализуем наружу
  const formatted = formatRuPhone(el.value)
  view.value = formatted
  model.value = normalizeToE164(formatted)

  // вычисляем позицию каретки ПОСЛЕ той же по счёту цифры
  const totalDigits = countDigits(formatted)
  leftDigits = Math.min(leftDigits, totalDigits)

  let pos = caretPosAfterNthDigit(formatted, leftDigits)

  // спец-правило: только что ввели 3-ю цифру кода (n=4 с учётом «7»)
  // → переносим каретку на символ после ") " (т.е. сразу в следующую группу)
  if (leftDigits === 4) {
    const closeIdx = formatted.indexOf(') ')
    if (closeIdx !== -1) pos = closeIdx + 2
  }

  requestAnimationFrame(() => el.setSelectionRange(pos, pos))
}

function onPaste(e: ClipboardEvent) {
  e.preventDefault()
  const text = e.clipboardData?.getData('text') ?? ''
  view.value = formatRuPhone(text)
  model.value = normalizeToE164(view.value)
}
</script>

<template>
  <FormInput
    v-model="view"
    :field-name="props.fieldName"
    :label="props.label"
    :error-message="props.errorMessage"
    :placeholder="props.placeholder"
    :class="props.class"
    type="tel"
    inputmode="tel"
    autocomplete="tel"
    @focus="onFocus"
    @click="onClick"
    @keydown="onKeydown"
    @input="onInput"
    @paste="onPaste"
  />
</template>