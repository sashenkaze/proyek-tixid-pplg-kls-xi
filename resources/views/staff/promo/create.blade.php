@extends('templates.app')

@section('content')
<div class="w-75 d-block mx-auto my-5">
    <form action="{{ route('staff.promos.store') }}" method="POST">
        @csrf

        <div class="row mb-3">
            <div class="col-6">
                <label for="promo_code" class="form-label">Kode Promo</label>
                <input type="text" name="promo_code" id="promo_code"
                       class="form-control @error('promo_code') is-invalid @enderror"
                       value="{{ old('promo_code') }}">
                @error('promo_code')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="col-6">
                <label for="type" class="form-label">Tipe</label>
                <select name="type" id="type" class="form-select @error('type') is-invalid @enderror">
                    <option value="">Pilih Tipe</option>
                    <option value="percent" {{ old('type') == 'percent' ? 'selected' : '' }}>Persen</option>
                    <option value="rupiah" {{ old('type') == 'rupiah' ? 'selected' : '' }}>Rupiah</option>
                </select>
                @error('type')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <div class="mb-3">
            <label for="discount" class="form-label">Diskon</label>
            <input type="number" name="discount" id="discount"
                   class="form-control @error('discount') is-invalid @enderror"
                   value="{{ old('discount') }}"
                   min="1">
            @error('discount')
                <small class="text-danger">{{ $message }}</small>
            @enderror
            <small class="form-text text-muted">Jika Persen: maksimal 100, Jika Rupiah: minimal 1000</small>
        </div>

        <button type="submit" class="btn btn-primary btn-lg">Kirim</button>
    </form>
</div>
@endsection
