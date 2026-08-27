@props([
    'action',
    'httpMethod' => null,
    'value' => '',
    'field' => 'title',
    'label' => 'Назва',
    'placeholder' => null,
])

<form role="form"
      method="POST"
      action="{{ $action }}">

    @csrf

    @if($httpMethod)
        @method($httpMethod)
    @endif

    <div class="card-body">
        <div class="form-group">

            <label for="{{ $field }}">
                {{ $label }}
            </label>

            <input type="text"
                   name="{{ $field }}"
                   id="{{ $field }}"
                   value="{{ old($field, $value) }}"
                   class="form-control @error($field) is-invalid @enderror"
                   placeholder="{{ $placeholder ?? $label }}">

            @error($field)
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror

        </div>
    </div>

    <x-admin.forms.form-submit-button />

</form>
