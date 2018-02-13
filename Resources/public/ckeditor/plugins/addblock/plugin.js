(function () {
    var items = CKEDITOR.addblockIds;

    if (CKEDITOR.addblockIds instanceof Array) {
        CKEDITOR.plugins.add('addblock', {
            requires: ['richcombo', 'dialog'],
            icons: 'plus',
            init: function (editor) {
                editor.ui.addRichCombo('AddBlock', {
                    multiSelect: false,
                    label: 'Add Block',
                    command: 'addblock',
                    toolbar: 'insert',
                    init: function () {
                        for (var i = 0; i < items.length; i++) {
                            this.add(i, items[i].name, 'Insert ' + items[i].name + ' block');
                        }
                    },
                    onClick: function (idx) {
                        var item = items[idx];
                        var params = JSON.stringify(item.fields);

                        console.log(item, params);

                        var value = '<pre block="' + item.name + '" contenteditable = "false" >{% with ' + params + ' %}\{\{ block("' + item.name + '") \}\}{% endwith %}</pre>';

                        editor.insertHtml(value);
                    },
                });
            }
        });
    }
})();
