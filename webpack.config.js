let Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('./public/')
    .setManifestKeyPrefix('')
    // public path used by the web server to access the output path
    .setPublicPath('/bundles/lagadmin')

    // TODO remove bootstrap js
    // .addEntry('jquery', './node_modules/jquery-easing/jquery.easing.1.3.js')
    // .addEntry('jquery-easing', './node_modules/jquery/dist/jquery.js')
    // .addEntry('sb-admin', './node_modules/startbootstrap-sb-admin-2/js/sb-admin-2.js')
    // .addEntry('bootstrap', './node_modules/bootstrap/dist/js/bootstrap.bundle.js')
    .addEntry('admin', './assets/js/admin.js')
    
    // .addStyleEntry('sb-admin-css', './node_modules/startbootstrap-sb-admin-2/css/sb-admin-2.css')
    .addStyleEntry('fa', './node_modules/@fortawesome/fontawesome-free/css/all.css')
    .addStyleEntry('bootstrap', './node_modules/bootstrap/dist/css/bootstrap.css')
    
    .enableSingleRuntimeChunk()
    .enableVersioning(false)
    .cleanupOutputBeforeBuild()
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
        to: 'icons/[path][name].[ext]'
    })
    .copyFiles({
        from: 'node_modules/tinymce/skins',
        to: 'skins/[path][name].[ext]'
    })
    .copyFiles({
        from: 'node_modules/tinymce/themes',
        to: 'themes/[path][name].[ext]'
    })
    .copyFiles({
        from: 'node_modules/tinymce/plugins/emoticons/js/',
        to: 'plugins/emoticons/js/[path][name].[ext]',
        pattern: /\.(js)$/
    })
    .copyFiles({
        from: 'node_modules/tinymce-i18n/langs',
        to: '/langs/[path][name].[ext]'
    })
    .copyFiles({
        from: './assets/favicon',
        to: '[path][name].[ext]'
    })
;

module.exports = Encore.getWebpackConfig();
