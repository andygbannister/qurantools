module.exports = {
  paths: {
    public: "app/assets",
    watched: ["app/library/styles", "app/library/js/js_for_brunch"]
  },
  files: {
    stylesheets: {
      joinTo: "qt_styles.css",
      order: {
        before: [
          "app/library/styles/less/tipped.less",
          "app/library/styles/css/datatables.min.css"
        ] // put these styles in first, so that we can override them with our own styles if needs be
      }
    },

    javascripts: {
      joinTo: "qt_javascript.js",
      order: {
        before: ["app/library/js/js_for_brunch/jquery-3.2.1.min.js"] // put jQuery first
      }
    }
  },
  plugins: {
    // from https://github.com/brunch/less-brunch
    less: {
      // less command-line options
      dumpLineNumbers: "comments"
    },
    // from https://github.com/dlepaux/fingerprint-brunch
    fingerprint: {
      manifest: "./app/assets/assets.json",
      targets: "*",
      alwaysRun: true,
      autoReplaceAndHash: false,
      autoClearOldFiles: true,
      srcBasePath: "app/assets/",
      destBasePath: "app/assets/"
    }
  },
  watcher: {
    awaitWriteFinish: true,
    usePolling: true
  },

  npm: {
    enabled: false
  },

  modules: {
    wrapper: false,
    definition: false
  }
};
