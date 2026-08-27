{{-- resources/views/components/subscribe-form.blade.php --}}
@props(['wrapperClass' => 'sidetitle mt-5'])

<div class="{{ $wrapperClass }}">
    <h2>Підписатися</h2>
</div>
@include('admin.layouts.alerts')
<form action="{{ route('subscribe') }}" method="POST" class="mb-4">
    @csrf
    <div class="input-group">
        <input type="email" name="email" class="form-control" placeholder="Ваш Email">
        <button class="btn btn-subscribe" type="submit">
            <i class="bi bi-send"></i>
        </button>
    </div>
</form>
