(function() {
    if (CKEDITOR.addblockIds instanceof Array) {
        CKEDITOR.plugins.add('addblock', {
            icons: 'plus',
            init: function (editor) {
                editor.ui.addRichCombo('AddBlock', {
                    multiSelect: false,
                    label: 'Add Block',
                    command: 'addblock',
                    toolbar: 'insert',
                    init: function () {
                        var items = CKEDITOR.addblockIds;
                        for (var i = 0; i < items.length; i++) {
                            this.add(
                                '<pre block="' + items[i] + '"contenteditable = "false" >\{\{ block("' + items[i] + '") \}\}</pre>',
                                items[i],
                                items[i]
                            );
                        }
                    },
                    onClick: function (value) {
                        editor.insertHtml(value);
                    },
                });
            }
        });
    }
})();