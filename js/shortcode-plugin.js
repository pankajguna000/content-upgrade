(function() {
    tinymce.create('tinymce.plugins.downloadBtn', {
        init: function(ed, urls) {
            // Register commands
            ed.addCommand('mcebuttons', function() {
                ed.windowManager.open({
                    title: 'Content Upgrade',
                    file: urls + '/dl-popup.php', // file that contains HTML for our modal window
                    width:600+ parseInt(ed.getLang('button.delta_width', 0)), // size of our window
                    height: 150 + parseInt(ed.getLang('button.delta_height', 0)), // size of our window
                    inline: 1
                }, {
                    plugin_url: urls
                });
            });

            // Register buttons
            ed.addButton('dlv_button', {title: 'Content Upgrade Button', cmd: 'mcebuttons', image: urls + '/download.png'});
        },
        getInfo: function() {
            return {
                longname: 'Dlv Button',
                author: 'InkThemes',
                authorurl: 'http://www.inkthemes.com',
                infourl: 'http://www.inkthemes.com',
                version: tinymce.majorVersion + "." + tinymce.minorVersion
            };
        }
    });

    // Register plugin
    // first parameter is the button ID and must match ID elsewhere
    // second parameter must match the first parameter of the tinymce.create() function above
    tinymce.PluginManager.add('dlv_button', tinymce.plugins.downloadBtn);

})();