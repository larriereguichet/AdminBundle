let Encore = require('@symfony/webpack-encore');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('./public/')
    .setManifestKeyPrefix('')
    .setPublicPath('/bundles/lagadmin')

    .addEntry('admin', './assets/js/admin.js')

    .enableSassLoader()
    .enableVersioning(false)
    .enableSourceMaps(!Encore.isProduction())
    .disableSingleRuntimeChunk()

    .cleanupOutputBeforeBuild()

    .configureBabel(() => {}, {
        useBuiltIns: 'usage',
        corejs: 3
    })

    .copyFiles({
        from: './assets/favicon',
        to: '[path][name].[ext]'
    })
;

module.exports = Encore.getWebpackConfig();
