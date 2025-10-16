@extends('templates.app')

@section('content')
    <div class="w75 d-block mx-auto mt-3 p-4">
        @if (Session::get('error'))
            <div class="alert alert-danger">{{ Session::get('error') }}</div>
        @endif

        <h5 class="text-center mb-3">Ubah Data Promo</h5>

        <form method="POST" action="{{ route('staff.promos.update', $promo->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="promo_code" class="form-label">Kode Promo</label>
                <input type="text" name="promo_code" id="promo_code"
                       class="form-control @error('promo_code') is-invalid @enderror"
                       value="{{ old('promo_code', $promo->promo_code) }}">
                @error('promo_code')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3">
                <label for="type" class="form-label">Tipe</label>
                <select name="type" id="type" class="form-select @error('type') is-invalid @enderror">
                    <option value="">Pilih Tipe</option>
                    <option value="percent" {{ old('type', $promo->type) == 'percent' ? 'selected' : '' }}>Persen</option>
                    <option value="rupiah" {{ old('type', $promo->type) == 'rupiah' ? 'selected' : '' }}>Rupiah</option>
                </select>
                @error('type')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3">
                <label for="discount" class="form-label">Diskon</label>
                <input type="number" name="discount" id="discount"
                       class="form-control @error('discount') is-invalid @enderror"
                       value="{{ old('discount', $promo->discount) }}"
                       @if(old('type', $promo->type) == 'percent')
                           min="1" max="100"
                       @else
                           min="1000"
                       @endif>
                @error('discount')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
                <small class="form-text text-muted">Jika Persen: maksimal 100, Jika Rupiah: minimal 1000</small>
            </div>

            <div class="mb-3">
                <label for="actived" class="form-label">Aktif</label>
                <select name="actived" id="actived" class="form-select @error('actived') is-invalid @enderror">
                    <option value="">Pilih Status</option>
                    <option value="1" {{ old('actived', $promo->actived) == '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ old('actived', $promo->actived) == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
                @error('actived')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Update Data</button>
        </form>
    </div>
@endsection
