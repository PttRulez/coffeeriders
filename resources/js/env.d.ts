/// <reference types="vite/client" />

interface ImportMetaEnv {
  readonly VITE_APP_NAME: string
  // добавляй сюда свои переменные VITE_*
}

interface ImportMeta {
  readonly env: ImportMetaEnv
}