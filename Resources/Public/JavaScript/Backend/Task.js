define(['jquery', 'TYPO3/CMS/Form/Backend/FormEditor/ViewModel'], function ( $ ) {
    'use strict';

    var Task = {};

    Task.addInterval = function () {
        $(".t3js-create-new-button")[0].click();
    }

    Task.addInterval();

    return Task;
});
