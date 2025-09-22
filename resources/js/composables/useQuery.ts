import { usePage } from '@inertiajs/vue3'

export function useQuery() {
  const page = usePage()
  return new URLSearchParams(page.url.split('?')[1] ?? '')
}