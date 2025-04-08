interface ImportMetaEnv {
  VITE_API_DOMAIN: string;
  VITE_PUSHER_HOST: string;
  VITE_PUSHER_PORT: string;
  VITE_PUSHER_APP_KEY: string;
  BASE_URL: string;
  VITE_AUTOREFRESH: string;
}

interface ImportMeta {
  env: ImportMetaEnv;
}
