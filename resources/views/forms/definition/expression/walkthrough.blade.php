@extends('forms.definition.walkthrough')

@section('page-title', 'Suggest a new saying or expression')

@section('form')

    {{-- Phrase --}}
    <div class="row">
        <div class="col-sm-12">
            <input
                type="text"
                name="titleStr"
                class="text-input center"
                placeholder="your saying or expression"
                value="{{ Request::old('titleStr') }}"
                autocomplete="off"
                required>
        </div>
    </div>

    {{-- Sub type --}}
    <div class="row">
        <div class="col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
            {!! Form::select(
                'subType',
                $definition->getSubTypes(),
                $definition->rawSubType,
                ['class' => 'en-text-input text-center']
            ) !!}
        </div>
    </div>

    <div class="row center">
        is a sentence in <em>{{ $lang->name }}</em> that means
    </div>

    <!-- Translation -->
    <div class="row">
        <div class="col-sm-12">
            <input
                type="text"
                name="translations[eng][practical]"
                class="en-text-input center"
                placeholder="your translation"
                value="{{ Request::old('translations[eng][practical]') }}"
                autocomplete="off"
                required>
        </div>
    </div>

    <div class="row center">
        in English.
    </div>

@stop