// vite.config.js
import { defineConfig } from "file:///mnt/f/Projects/Pishkhanak/pishkhanak.com/node_modules/vite/dist/node/index.js";
import laravel from "file:///mnt/f/Projects/Pishkhanak/pishkhanak.com/node_modules/laravel-vite-plugin/dist/index.js";
import fg from "file:///mnt/f/Projects/Pishkhanak/pishkhanak.com/node_modules/fast-glob/out/index.js";
var exclude_files = [
  "resources/css/filament/access/theme.css",
  "resources/js/bootstrap.js",
  "resources/js/echo.js"
];
var entries_js = fg.sync(["resources/js/**/*.js"]).reduce((acc, file) => {
  if (!exclude_files.includes(file)) {
    const name = file.replace(/^resources\/js\//, "").replace(/\.js$/, "");
    acc[name + "_js"] = file;
  }
  return acc;
}, {});
var entries_css = fg.sync(["resources/css/**/*.css"]).reduce((acc, file) => {
  if (!exclude_files.includes(file)) {
    const name = file.replace(/^resources\/css\//, "").replace(/\.css$/, "");
    acc[name + "_css"] = file;
  }
  return acc;
}, {});
var custom_entries = {
  "app_js": "resources/js/app.js",
  "services_js": "resources/js/services.js"
};
var entries = { ...entries_js, ...entries_css, ...custom_entries };
console.log(entries);
var vite_config_default = defineConfig({
  plugins: [
    laravel({
      input: entries,
      refresh: true
    })
  ],
  resolve: {
    alias: {
      "@": "/resources/js"
    }
  }
});
export {
  vite_config_default as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCIvbW50L2YvUHJvamVjdHMvUGlzaGtoYW5hay9waXNoa2hhbmFrLmNvbVwiO2NvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9maWxlbmFtZSA9IFwiL21udC9mL1Byb2plY3RzL1Bpc2hraGFuYWsvcGlzaGtoYW5hay5jb20vdml0ZS5jb25maWcuanNcIjtjb25zdCBfX3ZpdGVfaW5qZWN0ZWRfb3JpZ2luYWxfaW1wb3J0X21ldGFfdXJsID0gXCJmaWxlOi8vL21udC9mL1Byb2plY3RzL1Bpc2hraGFuYWsvcGlzaGtoYW5hay5jb20vdml0ZS5jb25maWcuanNcIjtpbXBvcnQgeyBkZWZpbmVDb25maWcgfSBmcm9tICd2aXRlJztcbmltcG9ydCBsYXJhdmVsIGZyb20gJ2xhcmF2ZWwtdml0ZS1wbHVnaW4nO1xuaW1wb3J0IGZnIGZyb20gJ2Zhc3QtZ2xvYic7XG5cbmNvbnN0IGV4Y2x1ZGVfZmlsZXMgPSBbXG4gICAgJ3Jlc291cmNlcy9jc3MvZmlsYW1lbnQvYWNjZXNzL3RoZW1lLmNzcycsXG4gICAgJ3Jlc291cmNlcy9qcy9ib290c3RyYXAuanMnLFxuICAgICdyZXNvdXJjZXMvanMvZWNoby5qcycsXG5dO1xuXG5jb25zdCBlbnRyaWVzX2pzID0gZmcuc3luYyhbJ3Jlc291cmNlcy9qcy8qKi8qLmpzJ10pLnJlZHVjZSgoYWNjLCBmaWxlKSA9PiB7XG4gICAgaWYoIWV4Y2x1ZGVfZmlsZXMuaW5jbHVkZXMoZmlsZSkpe1xuICAgICAgICBjb25zdCBuYW1lID0gZmlsZS5yZXBsYWNlKC9ecmVzb3VyY2VzXFwvanNcXC8vLCAnJykucmVwbGFjZSgvXFwuanMkLywgJycpO1xuICAgICAgICBhY2NbbmFtZSArICdfanMnXSA9IGZpbGU7XG4gICAgfVxuICAgIHJldHVybiBhY2M7XG59LCB7fSk7XG5cbmNvbnN0IGVudHJpZXNfY3NzID0gZmcuc3luYyhbJ3Jlc291cmNlcy9jc3MvKiovKi5jc3MnXSkucmVkdWNlKChhY2MsIGZpbGUpID0+IHtcbiAgICBpZighZXhjbHVkZV9maWxlcy5pbmNsdWRlcyhmaWxlKSl7XG4gICAgICAgIGNvbnN0IG5hbWUgPSBmaWxlLnJlcGxhY2UoL15yZXNvdXJjZXNcXC9jc3NcXC8vLCAnJykucmVwbGFjZSgvXFwuY3NzJC8sICcnKTtcbiAgICAgICAgYWNjW25hbWUgKyAnX2NzcyddID0gZmlsZTtcbiAgICB9XG4gICAgcmV0dXJuIGFjYztcbn0sIHt9KTtcblxuXG5jb25zdCBjdXN0b21fZW50cmllcyA9IHtcbiAgICAnYXBwX2pzJyA6ICdyZXNvdXJjZXMvanMvYXBwLmpzJyxcbiAgICAnc2VydmljZXNfanMnIDogJ3Jlc291cmNlcy9qcy9zZXJ2aWNlcy5qcycsXG59O1xuXG5jb25zdCBlbnRyaWVzID0geyAuLi5lbnRyaWVzX2pzLCAuLi5lbnRyaWVzX2NzcywgLi4uY3VzdG9tX2VudHJpZXMgfTtcblxuXG5jb25zb2xlLmxvZyhlbnRyaWVzKTtcbmV4cG9ydCBkZWZhdWx0IGRlZmluZUNvbmZpZyh7XG4gICAgcGx1Z2luczogW1xuICAgICAgICBsYXJhdmVsKHtcbiAgICAgICAgICAgIGlucHV0OiBlbnRyaWVzLFxuICAgICAgICAgICAgcmVmcmVzaDogdHJ1ZSxcbiAgICAgICAgfSksXG4gICAgXSxcbiAgICByZXNvbHZlOiB7XG4gICAgICAgIGFsaWFzOiB7XG4gICAgICAgICAgICAnQCc6ICcvcmVzb3VyY2VzL2pzJyxcbiAgICAgICAgfSxcbiAgICB9LFxufSk7Il0sCiAgIm1hcHBpbmdzIjogIjtBQUE2UyxTQUFTLG9CQUFvQjtBQUMxVSxPQUFPLGFBQWE7QUFDcEIsT0FBTyxRQUFRO0FBRWYsSUFBTSxnQkFBZ0I7QUFBQSxFQUNsQjtBQUFBLEVBQ0E7QUFBQSxFQUNBO0FBQ0o7QUFFQSxJQUFNLGFBQWEsR0FBRyxLQUFLLENBQUMsc0JBQXNCLENBQUMsRUFBRSxPQUFPLENBQUMsS0FBSyxTQUFTO0FBQ3ZFLE1BQUcsQ0FBQyxjQUFjLFNBQVMsSUFBSSxHQUFFO0FBQzdCLFVBQU0sT0FBTyxLQUFLLFFBQVEsb0JBQW9CLEVBQUUsRUFBRSxRQUFRLFNBQVMsRUFBRTtBQUNyRSxRQUFJLE9BQU8sS0FBSyxJQUFJO0FBQUEsRUFDeEI7QUFDQSxTQUFPO0FBQ1gsR0FBRyxDQUFDLENBQUM7QUFFTCxJQUFNLGNBQWMsR0FBRyxLQUFLLENBQUMsd0JBQXdCLENBQUMsRUFBRSxPQUFPLENBQUMsS0FBSyxTQUFTO0FBQzFFLE1BQUcsQ0FBQyxjQUFjLFNBQVMsSUFBSSxHQUFFO0FBQzdCLFVBQU0sT0FBTyxLQUFLLFFBQVEscUJBQXFCLEVBQUUsRUFBRSxRQUFRLFVBQVUsRUFBRTtBQUN2RSxRQUFJLE9BQU8sTUFBTSxJQUFJO0FBQUEsRUFDekI7QUFDQSxTQUFPO0FBQ1gsR0FBRyxDQUFDLENBQUM7QUFHTCxJQUFNLGlCQUFpQjtBQUFBLEVBQ25CLFVBQVc7QUFBQSxFQUNYLGVBQWdCO0FBQ3BCO0FBRUEsSUFBTSxVQUFVLEVBQUUsR0FBRyxZQUFZLEdBQUcsYUFBYSxHQUFHLGVBQWU7QUFHbkUsUUFBUSxJQUFJLE9BQU87QUFDbkIsSUFBTyxzQkFBUSxhQUFhO0FBQUEsRUFDeEIsU0FBUztBQUFBLElBQ0wsUUFBUTtBQUFBLE1BQ0osT0FBTztBQUFBLE1BQ1AsU0FBUztBQUFBLElBQ2IsQ0FBQztBQUFBLEVBQ0w7QUFBQSxFQUNBLFNBQVM7QUFBQSxJQUNMLE9BQU87QUFBQSxNQUNILEtBQUs7QUFBQSxJQUNUO0FBQUEsRUFDSjtBQUNKLENBQUM7IiwKICAibmFtZXMiOiBbXQp9Cg==
