<?php

if (is_plugin_active('collection')) {
    require_once __DIR__ . '/recent-subjects.php';

    register_widget(RecentSubjectsWidget::class);
}
