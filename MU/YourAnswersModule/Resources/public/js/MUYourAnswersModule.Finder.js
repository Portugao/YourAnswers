'use strict';

var currentMUYourAnswersModuleEditor = null;
var currentMUYourAnswersModuleInput = null;

/**
 * Returns the attributes used for the popup window. 
 * @return {String}
 */
function getMUYourAnswersModulePopupAttributes() {
    var pWidth, pHeight;

    pWidth = screen.width * 0.75;
    pHeight = screen.height * 0.66;

    return 'width=' + pWidth + ',height=' + pHeight + ',location=no,menubar=no,toolbar=no,dependent=yes,minimizable=no,modal=yes,alwaysRaised=yes,resizable=yes,scrollbars=yes';
}

/**
 * Open a popup window with the finder triggered by an editor button.
 */
function MUYourAnswersModuleFinderOpenPopup(editor, editorName) {
    var popupUrl;

    // Save editor for access in selector window
    currentMUYourAnswersModuleEditor = editor;

    popupUrl = Routing.generate('muyouranswersmodule_external_finder', { objectType: 'question', editor: editorName });

    if (editorName == 'ckeditor') {
        editor.popup(popupUrl, /*width*/ '80%', /*height*/ '70%', getMUYourAnswersModulePopupAttributes());
    } else {
        window.open(popupUrl, '_blank', getMUYourAnswersModulePopupAttributes());
    }
}


var mUYourAnswersModule = {};

mUYourAnswersModule.finder = {};

mUYourAnswersModule.finder.onLoad = function (baseId, selectedId) {
    if (jQuery('#mUYourAnswersModuleSelectorForm').length < 1) {
        return;
    }
    jQuery('select').not("[id$='pasteAs']").change(mUYourAnswersModule.finder.onParamChanged);
    
    jQuery('.btn-default').click(mUYourAnswersModule.finder.handleCancel);

    var selectedItems = jQuery('#muyouranswersmoduleItemContainer a');
    selectedItems.bind('click keypress', function (event) {
        event.preventDefault();
        mUYourAnswersModule.finder.selectItem(jQuery(this).data('itemid'));
    });
};

mUYourAnswersModule.finder.onParamChanged = function () {
    jQuery('#mUYourAnswersModuleSelectorForm').submit();
};

mUYourAnswersModule.finder.handleCancel = function (event) {
    var editor;

    event.preventDefault();
    editor = jQuery("[id$='editor']").first().val();
    if ('ckeditor' === editor) {
        mUYourAnswersClosePopup();
    } else if ('quill' === editor) {
        mUYourAnswersClosePopup();
    } else if ('summernote' === editor) {
        mUYourAnswersClosePopup();
    } else if ('tinymce' === editor) {
        mUYourAnswersClosePopup();
    } else {
        alert('Close Editor: ' + editor);
    }
};


function mUYourAnswersGetPasteSnippet(mode, itemId) {
    var quoteFinder;
    var itemPath;
    var itemUrl;
    var itemTitle;
    var itemDescription;
    var pasteMode;

    quoteFinder = new RegExp('"', 'g');
    itemPath = jQuery('#path' + itemId).val().replace(quoteFinder, '');
    itemUrl = jQuery('#url' + itemId).val().replace(quoteFinder, '');
    itemTitle = jQuery('#title' + itemId).val().replace(quoteFinder, '').trim();
    itemDescription = jQuery('#desc' + itemId).val().replace(quoteFinder, '').trim();
    pasteMode = jQuery("[id$='pasteAs']").first().val();

    // item ID
    if (pasteMode === '3') {
        return '' + itemId;
    }

    // relative link to detail page
    if (pasteMode === '1') {
        return mode === 'url' ? itemPath : '<a href="' + itemPath + '" title="' + itemDescription + '">' + itemTitle + '</a>';
    }
    // absolute url to detail page
    if (pasteMode === '2') {
        return mode === 'url' ? itemUrl : '<a href="' + itemUrl + '" title="' + itemDescription + '">' + itemTitle + '</a>';
    }

    return '';
}


// User clicks on "select item" button
mUYourAnswersModule.finder.selectItem = function (itemId) {
    var editor, html;

    html = mUYourAnswersGetPasteSnippet('html', itemId);
    editor = jQuery("[id$='editor']").first().val();
    if ('ckeditor' === editor) {
        if (null !== window.opener.currentMUYourAnswersModuleEditor) {
            window.opener.currentMUYourAnswersModuleEditor.insertHtml(html);
        }
    } else if ('quill' === editor) {
        if (null !== window.opener.currentMUYourAnswersModuleEditor) {
            window.opener.currentMUYourAnswersModuleEditor.clipboard.dangerouslyPasteHTML(window.opener.currentMUYourAnswersModuleEditor.getLength(), html);
        }
    } else if ('summernote' === editor) {
        if (null !== window.opener.currentMUYourAnswersModuleEditor) {
            html = jQuery(html).get(0);
            window.opener.currentMUYourAnswersModuleEditor.invoke('insertNode', html);
        }
    } else if ('tinymce' === editor) {
        window.opener.currentMUYourAnswersModuleEditor.insertContent(html);
    } else {
        alert('Insert into Editor: ' + editor);
    }
    mUYourAnswersClosePopup();
};

function mUYourAnswersClosePopup() {
    window.opener.focus();
    window.close();
}

jQuery(document).ready(function () {
    mUYourAnswersModule.finder.onLoad();
});
