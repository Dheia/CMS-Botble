$(() => {
    'use strict'

    BDashboard.loadWidget(
        $('#widget_subjects_recent').find('.widget-content'), 
        $('#widget_subjects_recent').data('url')
    );
})
