<x-core::form.on-off.checkbox
    name="collection_subject_schema_enabled"
    :label="trans('plugins/collection::base.settings.enable_collection_subject_schema')"
    :checked="setting('collection_subject_schema_enabled', true)"
    :description="trans('plugins/collection::base.settings.enable_collection_subject_schema_description')"
    data-bb-toggle="collapse"
    data-bb-target=".collection_subject_schema_type"
    class="mb-0"
    :wrapper="false"
/>

<x-core::form.fieldset
    class="collection_subject_schema_type mt-3"
    data-bb-value="1"
    @style(['display: none' => !setting('collection_subject_schema_enabled', true)])
>
    <x-core::form.select
        name="collection_subject_schema_type"
        :label="trans('plugins/collection::base.settings.schema_type')"
        :options="[
            'NewsArticle' => 'NewsArticle',
            'News' => 'News',
            'Article' => 'Article',
            // 'CollectionSubjecting' => 'CollectionSubjecting',
        ]"
        :value="setting('collection_subject_schema_type', 'NewsArticle')"
    />
</x-core::form.fieldset>
