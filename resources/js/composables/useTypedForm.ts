import { useForm as inertiaUseForm } from '@inertiajs/vue3'

export function useTypedForm<T extends Record<string, any>>(defaults: T) {
  const form = inertiaUseForm<T>(defaults)

  return form as typeof form & {
    errors: Record<string, string | undefined>
    setError: (field: string, message: string) => void
  }
}