(function () {
    var items = CKEDITOR.addblockIds;

    CKEDITOR.config.contentsCss = CKEDITOR.plugins.getPath('addblock') + 'css/style.css';

    if (items instanceof Array) {
        CKEDITOR.plugins.add('addblock',
            {
                init: function (editor) {
                    CKEDITOR.dialog.add('blockChoice', this.path + 'dialogs/addblock.js');

                    editor.ui.addButton('AddBlock', {
                        label: 'Add Block',
                        command: 'AddBlock',
                        icon: CKEDITOR.plugins.getPath('addblock') + 'icons/puzzle.png'
                    });

                    editor.addCommand('AddBlock', {
                        exec: function (editor) {
                            editor.openDialog('blockChoice'); // voir addblock.js
                        },
                    });

                    // gestion de l'edition
                    editor.on('doubleclick', function (event) {
                        var element = event.data.element;

                        if (element.is('div')) {
                            event.data.dialog = 'blockChoice'; // Marche pas
                        }
                    });
                },
            });
    }
})();

