const Encore = require('@symfony/webpack-encore');

// Configuration manuelle de l'environnement
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // Répertoire où les assets compilés seront stockés
    .setOutputPath('public/build/')
    
    // Chemin public utilisé par le serveur web pour accéder au répertoire de sortie
    .setPublicPath('/build')
    
    // Uniquement nécessaire pour les déploiements CDN ou les sous-répertoires
    // .setManifestKeyPrefix('build/')

    /*
     * CONFIGURATION DES ENTRÉES
     */
    .addEntry('app', './assets/app.js')
    .addEntry('pos', './assets/js/sales/pos.js')
    .addEntry('dashboard', './assets/js/dashboard.js')
    
    // Découpe en plusieurs fichiers pour optimiser le chargement
    .splitEntryChunks()

    // Active un seul fichier runtime.js
    .enableSingleRuntimeChunk()

    /*
     * FONCTIONNALITÉS
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    
    // Active le hashing des noms de fichiers (app.abc123.js)
    .enableVersioning(Encore.isProduction())

    // Configure Babel
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = '3.23';
    })

    // Active Sass/SCSS
    .enableSassLoader()

    // Active PostCSS
    .enablePostCssLoader((options) => {
        options.postcssOptions = {
            plugins: [
                require('autoprefixer'),
            ],
        };
    })

    // Active l'importation d'images
    .configureImageRule({
        filename: 'images/[name].[hash:8][ext]'
    })

    // Active l'importation de fonts
    .configureFontRule({
        filename: 'fonts/[name].[hash:8][ext]'
    })

    // jQuery global
    .autoProvidejQuery()

    // Configuration des loaders
    .addLoader({
        test: /\.json$/,
        loader: 'json-loader',
        type: 'javascript/auto'
    })

    // Optimisations pour la production
    .configureTerserPlugin((options) => {
        options.terserOptions = {
            compress: {
                drop_console: Encore.isProduction(),
            },
        };
    })

    // Active l'analyse des bundles (optionnel)
    // .addPlugin(new (require('webpack-bundle-analyzer')).BundleAnalyzerPlugin())
;

module.exports = Encore.getWebpackConfig();
