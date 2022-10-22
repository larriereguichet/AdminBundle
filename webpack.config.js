let Encore = require('@symfony/webpack-encore');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('./public/')
    .setManifestKeyPrefix('')
    .setPublicPath('/bundles/lagadmin')

    .addEntry('admin', './assets/js/admin.js')
    
    .addStyleEntry('fa', './node_modules/@fortawesome/fontawesome-free/css/all.css')
    .addStyleEntry('bootstrap', './node_modules/bootstrap/dist/css/bootstrap.css')
    
    .enableSingleRuntimeChunk()
    .enableSassLoader()
    .enableVersioning(false)
    .enableSourceMaps(!Encore.isProduction())
    .cleanupOutputBeforeBuild()

    .configureBabel(() => {}, {
        useBuiltIns: 'usage',
        corejs: 3
    })
    
    // Copy the files required by tinymce
    .copyFiles({
        from: 'vendor/tinymce/tinymce/icons',
        to: 'icons/[path][name].[ext]'
    })
    .copyFiles({
        from: 'vendor/tinymce/tinymce/skins',
        to: 'skins/[path][name].[ext]'
    })
    .copyFiles({
        from: 'vendor/tinymce/tinymce/themes',
        to: 'themes/[path][name].[ext]'
    })
    .copyFiles({
        from: 'vendor/tinymce/tinymce/plugins/emoticons/js/',
        to: 'plugins/emoticons/js/[path][name].[ext]',
        pattern: /\.(js)$/
    })
    .copyFiles({
        from: './assets/favicon',
        to: '[path][name].[ext]'
    })
;

module.exports = Encore.getWebpackConfig();
