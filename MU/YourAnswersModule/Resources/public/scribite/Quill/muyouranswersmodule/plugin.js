var muyouranswersmodule = function(quill, options) {
    setTimeout(function() {
        var button;

        button = jQuery('button[value=muyouranswersmodule]');

        button
            .css('background', 'url(' + Zikula.Config.baseURL + Zikula.Config.baseURI + '/web/modules/muyouranswers/images/admin.png) no-repeat center center transparent')
            .css('background-size', '16px 16px')
            .attr('title', 'Your answers')
        ;

        button.click(function() {
            MUYourAnswersModuleFinderOpenPopup(quill, 'quill');
        });
    }, 1000);
};
