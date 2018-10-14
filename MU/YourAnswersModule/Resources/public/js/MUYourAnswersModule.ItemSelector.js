'use strict';

var mUYourAnswersModule = {};

mUYourAnswersModule.itemSelector = {};
mUYourAnswersModule.itemSelector.items = {};
mUYourAnswersModule.itemSelector.baseId = 0;
mUYourAnswersModule.itemSelector.selectedId = 0;

mUYourAnswersModule.itemSelector.onLoad = function (baseId, selectedId) {
    mUYourAnswersModule.itemSelector.baseId = baseId;
    mUYourAnswersModule.itemSelector.selectedId = selectedId;

    // required as a changed object type requires a new instance of the item selector plugin
    jQuery('#mUYourAnswersModuleObjectType').change(mUYourAnswersModule.itemSelector.onParamChanged);

    jQuery('#' + baseId + '_catidMain').change(mUYourAnswersModule.itemSelector.onParamChanged);
    jQuery('#' + baseId + '_catidsMain').change(mUYourAnswersModule.itemSelector.onParamChanged);
    jQuery('#' + baseId + 'Id').change(mUYourAnswersModule.itemSelector.onItemChanged);
    jQuery('#' + baseId + 'Sort').change(mUYourAnswersModule.itemSelector.onParamChanged);
    jQuery('#' + baseId + 'SortDir').change(mUYourAnswersModule.itemSelector.onParamChanged);
    jQuery('#mUYourAnswersModuleSearchGo').click(mUYourAnswersModule.itemSelector.onParamChanged);
    jQuery('#mUYourAnswersModuleSearchGo').keypress(mUYourAnswersModule.itemSelector.onParamChanged);

    mUYourAnswersModule.itemSelector.getItemList();
};

mUYourAnswersModule.itemSelector.onParamChanged = function () {
    jQuery('#ajaxIndicator').removeClass('hidden');

    mUYourAnswersModule.itemSelector.getItemList();
};

mUYourAnswersModule.itemSelector.getItemList = function () {
    var baseId;
    var params;

    baseId = mUYourAnswersModule.itemSelector.baseId;
    params = {
        ot: baseId,
        sort: jQuery('#' + baseId + 'Sort').val(),
        sortdir: jQuery('#' + baseId + 'SortDir').val(),
        q: jQuery('#' + baseId + 'SearchTerm').val()
    }
    if (jQuery('#' + baseId + '_catidMain').length > 0) {
        params[catidMain] = jQuery('#' + baseId + '_catidMain').val();
    } else if (jQuery('#' + baseId + '_catidsMain').length > 0) {
        params[catidsMain] = jQuery('#' + baseId + '_catidsMain').val();
    }

    jQuery.getJSON(Routing.generate('muyouranswersmodule_ajax_getitemlistfinder'), params, function (data) {
        var baseId;

        baseId = mUYourAnswersModule.itemSelector.baseId;
        mUYourAnswersModule.itemSelector.items[baseId] = data;
        jQuery('#ajaxIndicator').addClass('hidden');
        mUYourAnswersModule.itemSelector.updateItemDropdownEntries();
        mUYourAnswersModule.itemSelector.updatePreview();
    });
};

mUYourAnswersModule.itemSelector.updateItemDropdownEntries = function () {
    var baseId, itemSelector, items, i, item;

    baseId = mUYourAnswersModule.itemSelector.baseId;
    itemSelector = jQuery('#' + baseId + 'Id');
    itemSelector.length = 0;

    items = mUYourAnswersModule.itemSelector.items[baseId];
    for (i = 0; i < items.length; ++i) {
        item = items[i];
        itemSelector.get(0).options[i] = new Option(item.title, item.id, false);
    }

    if (mUYourAnswersModule.itemSelector.selectedId > 0) {
        jQuery('#' + baseId + 'Id').val(mUYourAnswersModule.itemSelector.selectedId);
    }
};

mUYourAnswersModule.itemSelector.updatePreview = function () {
    var baseId, items, selectedElement, i;

    baseId = mUYourAnswersModule.itemSelector.baseId;
    items = mUYourAnswersModule.itemSelector.items[baseId];

    jQuery('#' + baseId + 'PreviewContainer').addClass('hidden');

    if (items.length === 0) {
        return;
    }

    selectedElement = items[0];
    if (mUYourAnswersModule.itemSelector.selectedId > 0) {
        for (var i = 0; i < items.length; ++i) {
            if (items[i].id == mUYourAnswersModule.itemSelector.selectedId) {
                selectedElement = items[i];
                break;
            }
        }
    }

    if (null !== selectedElement) {
        jQuery('#' + baseId + 'PreviewContainer')
            .html(window.atob(selectedElement.previewInfo))
            .removeClass('hidden');
    }
};

mUYourAnswersModule.itemSelector.onItemChanged = function () {
    var baseId, itemSelector, preview;

    baseId = mUYourAnswersModule.itemSelector.baseId;
    itemSelector = jQuery('#' + baseId + 'Id').get(0);
    preview = window.atob(mUYourAnswersModule.itemSelector.items[baseId][itemSelector.selectedIndex].previewInfo);

    jQuery('#' + baseId + 'PreviewContainer').html(preview);
    mUYourAnswersModule.itemSelector.selectedId = jQuery('#' + baseId + 'Id').val();
};

jQuery(document).ready(function () {
    var infoElem;

    infoElem = jQuery('#itemSelectorInfo');
    if (infoElem.length == 0) {
        return;
    }

    mUYourAnswersModule.itemSelector.onLoad(infoElem.data('base-id'), infoElem.data('selected-id'));
});
