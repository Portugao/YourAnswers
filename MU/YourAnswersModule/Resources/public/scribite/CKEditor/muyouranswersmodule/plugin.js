CKEDITOR.plugins.add('muyouranswersmodule', {
    requires: 'popup',
    init: function (editor) {
        editor.addCommand('insertMUYourAnswersModule', {
            exec: function (editor) {
                MUYourAnswersModuleFinderOpenPopup(editor, 'ckeditor');
            }
        });
        editor.ui.addButton('muyouranswersmodule', {
            label: 'Your answers',
            command: 'insertMUYourAnswersModule',
            icon: this.path.replace('scribite/CKEditor/muyouranswersmodule', 'images') + 'admin.png'
        });
    }
});
