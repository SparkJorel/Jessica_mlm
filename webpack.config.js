var Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Add 1 entry for each "page" of your app
     * (including one that's included on every page - e.g. "app")
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry('app', './assets/js/app.js')
    .addEntry('genealogy', './assets/js/genealogy.js')
    .addEntry('datatable', './assets/js/datatable.js')
    .addEntry('validate', './assets/js/form_validate.js')
    .addEntry('fieldset', './assets/js/form_fieldset.js')
    .addEntry('autocomplete', './assets/js/user_autocomplete.js')
    .addEntry('add_product', './assets/js/add_product.js')
    .addEntry('activate_user', './assets/js/activate_user.js')
    .addEntry('paid_bonus_ap', './assets/js/paid_bonus_ap.js')
    .addEntry('bonus_sponsoring_paid', './assets/js/bonus_sponsoring_paid.js')
    .addEntry('update_price', './assets/js/update_price.js')
    .addEntry('add_bg', './assets/js/add_bg.js')
    .addEntry('collection', './assets/js/collection.js')
    .addEntry('toggle_checkbox', './assets/js/toggle_checkbox.js')
    .addEntry('toggle_boolean_button', './assets/js/toggle_boolean_button.js')
    .addEntry('check_username', './assets/js/check_username.js')
    .addEntry('cart-subscription', './assets/js/cart-subscription.js')
    .addEntry('cart', './assets/js/cart.js')
    .addEntry('login', './assets/js/login.js')
    .addStyleEntry('page_product', './assets/css/product.css')
    //.addEntry('page1', './assets/js/page1.js')
    //.addEntry('page2', './assets/js/page2.js')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // enables @babel/preset-env polyfills
    .configureBabel(() => {}, {
        useBuiltIns: 'usage',
        corejs: 3
    })

    // enables Sass/SCSS support
    //.enableSassLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    .autoProvidejQuery()

    // uncomment if you use API Platform Admin (composer req api-admin)
    //.enableReactPreset()
    //.addEntry('admin', './assets/js/admin.js')
;

module.exports = Encore.getWebpackConfig();
