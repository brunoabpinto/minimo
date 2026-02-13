import { defineConfig } from "vite";
import minimo from "minimo-vite-plugin";

export default defineConfig({
  plugins: [minimo()],
  server: {
    host: "127.0.0.1",
    port: 5173,
    strictPort: true,
  },
  build: {
    manifest: true,
    outDir: "public/build",
    emptyOutDir: true,
    rollupOptions: {
      input: {
        app: "resources/js/app.js",
      },
    },
  },
});
