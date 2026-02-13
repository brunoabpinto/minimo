const fs = require("node:fs");
const path = require("node:path");
const autoprefixer = require("autoprefixer");
const { defineConfig } = require("vite");

const hotFile = path.resolve("public/hot");

function cleanHotFile() {
  if (fs.existsSync(hotFile)) {
    fs.unlinkSync(hotFile);
  }
}

function shouldFullReload(file) {
  const normalizedPath = file.split(path.sep).join("/");

  return (
    /(^|\/)views\/.*\.(blade\.php|blade\.md|md)$/.test(normalizedPath) ||
    /(^|\/)app\/.*\.php$/.test(normalizedPath) ||
    /(^|\/)packages\/minimo-core\/src\/.*\.php$/.test(normalizedPath)
  );
}

function minimoHotPlugin() {
  return {
    name: "minimo-hot",
    buildStart() {
      cleanHotFile();
    },
    configureServer(server) {
      const host = "127.0.0.1";
      const port = server.config.server.port || 5173;
      fs.writeFileSync(hotFile, `http://${host}:${port}`);
      server.watcher.add([
        path.resolve("views/**/*.blade.php"),
        path.resolve("views/**/*.blade.md"),
        path.resolve("views/**/*.md"),
        path.resolve("app/**/*.php"),
        path.resolve("packages/minimo-core/src/**/*.php"),
      ]);

      const reload = (event, file) => {
        if (!["change", "add", "unlink"].includes(event)) {
          return;
        }

        if (!shouldFullReload(file)) {
          return;
        }

        server.ws.send({ type: "full-reload" });
      };

      server.watcher.on("all", reload);

      server.httpServer?.once("close", () => {
        server.watcher.off("all", reload);
        cleanHotFile();
      });
    },
    closeBundle() {
      cleanHotFile();
    },
  };
}

module.exports = defineConfig({
  plugins: [minimoHotPlugin()],
  server: {
    host: "127.0.0.1",
    port: 5173,
    strictPort: true,
    origin: "http://127.0.0.1:5173",
    watch: {
      usePolling: true,
      interval: 150,
    },
    hmr: {
      host: "127.0.0.1",
      protocol: "ws",
      port: 5173,
      clientPort: 5173,
    },
  },
  css: {
    postcss: {
      plugins: [autoprefixer()],
    },
  },
  build: {
    manifest: true,
    outDir: "public/build",
    emptyOutDir: true,
    rollupOptions: {
      input: {
        app: path.resolve("resources/js/app.js"),
      },
    },
  },
});
