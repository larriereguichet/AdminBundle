let Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('./src/Resources/public/assets')
    // public path used by the web server to access the output path
    .setManifestKeyPrefix('assets')
    .setPublicPath('/bundles/lagadmin/assets')
    
    .addEntry('admin', './assets/js/admin.js')
    .addEntry('admin.collection', './assets/js/admin.collection.js')
    .disableSingleRuntimeChunk()
    
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    
    .configureBabel(() => {
    }, {
        useBuiltIns: 'usage',
        corejs: 3
    })
    .enableSassLoader()
    .autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();
