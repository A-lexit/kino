@php
    $field = $field ?? 'title';
    $label = $label ?? 'Назва';
@endphp

<form role="form"
      method="POST"
      action="{{ $action }}">

    @csrf

    @isset($httpMethod)
        @method($httpMethod)
    @endisset

    <div class="card-body">
        <div class="form-group">

            <label for="{{ $field }}">
                {{ $label }}
            </label>

            <input type="text"
                   name="{{ $field }}"
                   id="{{ $field }}"
                   value="{{ old($field, $value ?? '') }}"
                   class="form-control @error($field) is-invalid @enderror"
                   placeholder="{{ $placeholder ?? $label }}">

            @error($field)
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror

        </div>
    </div>

    @include('admin.layouts.inc.form-submit-button')

</form>
