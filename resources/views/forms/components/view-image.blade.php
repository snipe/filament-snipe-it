<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field">
    {{ ($getRecord()) ? '<img src="'.url('uploads').'/'. $getRecord()->getTable().'/'.$getRecord()->image.'">' : '' }}
</x-dynamic-component>




