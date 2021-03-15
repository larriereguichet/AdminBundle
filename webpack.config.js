let Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('./src/Resources/public')
    // public path used by the web server to access the output path
    .setManifestKeyPrefix('assets')
    .setPublicPath('/bundles/lagadmin')

    // TODO remove bootstrap js
    .addEntry('js/jquery', './node_modules/jquery-easing/jquery.easing.1.3.js')
    .addEntry('js/jquery-easing', './node_modules/jquery/dist/jquery.js')
    .addEntry('js/sb-admin', './node_modules/startbootstrap-sb-admin-2/js/sb-admin-2.js')
    .addEntry('js/bootstrap', './node_modules/bootstrap/dist/js/bootstrap.bundle.js')
    .addEntry('assets/admin', './assets/js/admin.js')
    .addStyleEntry('css/sb-admin', './node_modules/startbootstrap-sb-admin-2/css/sb-admin-2.css')
    .addStyleEntry('css/fa', './node_modules/@fortawesome/fontawesome-free/css/all.css')
    .disableSingleRuntimeChunk()

    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())

    .configureBabel(() => {}, {
        useBuiltIns: 'usage',
        corejs: 3
    })
    .enableSassLoader()
    .autoProvidejQuery()
    // Copy the files required by tinymce
    .copyFiles({
        from: 'node_modules/tinymce/icons',
        to: 'assets/icons/[path][name].[ext]'
    })
    .copyFiles({
        from: 'node_modules/tinymce/skins',
        to: 'assets/skins/[path][name].[ext]'
    })
    .copyFiles({
        from: 'node_modules/tinymce/themes',
        to: 'assets/themes/[path][name].[ext]'
    })
    .copyFiles({
        from: 'node_modules/tinymce/plugins/emoticons/js/',
        to: 'assets/plugins/emoticons/js/[path][name].[ext]',
        pattern: /\.(js)$/
    })
    .copyFiles({
        from: 'node_modules/tinymce-i18n/langs',
        to: 'assets/langs/[path][name].[ext]'
    })
;

module.exports = Encore.getWebpackConfig();
