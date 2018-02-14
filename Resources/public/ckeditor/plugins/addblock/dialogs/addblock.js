(function () {
    var items = CKEDITOR.addblockIds;

    CKEDITOR.dialog.add('blockChoice', function (editor) {
        return {
            title: 'Add block',
            minHeight: 300,
            contents: [{
                id: 'config',
                elements: initElements()
            }],
            onOk: function () {
                processForm(this, editor);
            }
        };
    });

    function processForm(form, editor) {
        var item = items[form.getValueOf('config', 'idx')]; // Config YML du block

        if (item) {
            // Collect parameters depuis la config yml
            var parameters = {};
            for (var i = 0; i < item.fields.length; i++) {
                var property = item.fields[i].property;

                parameters[property] = form.getValueOf('config', property);
            }

            // Créer et insert une div contenant notre block
            var div = editor.document.createElement('div');
            var html = (item.fields.length === 0) ?
                '\{\{ block("' + item.name + '") \}\}' : // block normal
                '\{% with ' + JSON.stringify(parameters) + ' %\}\{\{ block("' + item.name + '") \}\}\{% endwith %\}'; // block avec params

            div.setHtml(html);
            div.setAttribute('contenteditable', 'false');
            div.setAttribute('class', 'krg-cms-block-area');
            editor.insertElement(div);
        }
    }

    /**
     * Collecte les champs des blocks, créer 1 champ par propriété (hidden) puis affiche le nécessaire au onChange
     */
    function initElements() {
        var elements = [];

        // Input select listing blocks
        elements.push({
            id: 'idx',
            type: 'select',
            label: 'Block',
            items: getBlockChoices(),
            onChange: function () {
                onBlockChange(this);
            },
        });

        // Create one field for each properties (no duplicates)
        var propretiesAdded = [];
        for (var i = 0; i < items.length; i++) {
            for (var t = 0; t < items[i].fields.length; t++) {
                var property = items[i].fields[t].property;
                var type = items[i].fields[t].type;

                if (false === propretiesAdded.includes(property)) {
                    elements.push({
                        id: property,
                        type: type,
                        label: property,
                        style: 'display: none' // On init, all fields hidden
                    });

                    propretiesAdded.push(property);
                }
            }
        }

        return elements;
    }

    function getBlockChoices() {
        var choices = [];

        for (var i = 0; i < items.length; i++) {
            choices.push([items[i].name, i]);
        }

        return choices;
    }

    function onBlockChange(input) {
        var dialog = input.getDialog();
        var item = items[parseInt(input.getValue())];

        hideFields(dialog); // On change, hide all fields
        showItemFields(dialog, item.fields); // then show only needed fields by the block
    }

    function hideFields(dialog) {
        for (var i = 0; i < items.length; i++) {
            for (var t = 0; t < items[i].fields.length; t++) {
                var el = dialog.getContentElement('config', items[i].fields[t].property);

                if (el) {
                    el.getElement().hide();
                }
            }
        }
    }

    function showItemFields(dialog, itemFields) {
        if (itemFields.length) {
            for (var i = 0; i < itemFields.length; i++) {
                var el = dialog.getContentElement('config', itemFields[i].property);

                if (el) {
                    el.getElement().show();
                }
            }
        }
    }
})();
