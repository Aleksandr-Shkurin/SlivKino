@extends('admin.layout')@section('title',$cinema->exists?'Изменить кинотеатр':'Добавить кинотеатр')@section('section-title','Кинотеатры')
@section('content')<div class="page-head"><div><h1>{{ $cinema->exists?'Изменить кинотеатр':'Новый кинотеатр' }}</h1><p>Контактная информация и описание.</p></div><a class="btn btn-light" href="{{ route('admin.cinemas.index') }}">← К списку</a></div>
<form class="panel" method="POST" action="{{ $cinema->exists?route('admin.cinemas.update',$cinema):route('admin.cinemas.store') }}">@csrf @if($cinema->exists)@method('PUT')@endif<div class="form-grid">
<div class="field"><label>Название *</label><input name="name" value="{{ old('name',$cinema->name) }}" required></div><div class="field"><label>Название на английском</label><input name="name_en" value="{{ old('name_en',$cinema->name_en) }}"></div>
<div class="field full"><label>Адрес *</label><input name="address" value="{{ old('address',$cinema->address) }}" required></div>
<div class="field"><label>Телефон</label><input name="phone" value="{{ old('phone',$cinema->phone) }}"></div><div class="field"><label>Электронная почта</label><input type="email" name="email" value="{{ old('email',$cinema->email) }}"></div>
<div class="field full"><label>Часы работы</label><input name="working_hours" value="{{ old('working_hours',$cinema->working_hours) }}"></div>
<div class="field full"><label>Описание</label><textarea name="description" style="min-height:140px">{{ old('description',$cinema->description) }}</textarea></div>
<div class="field full"><label>Особенности (через запятую или с новой строки)</label><textarea name="features_text">{{ old('features_text',implode(', ',$cinema->features ?? [])) }}</textarea><span class="help">Например: IMAX, VIP зал, 3D, Кафе, Парковка.</span></div>
</div><div class="form-actions"><button class="btn">Сохранить кинотеатр</button><a class="btn btn-light" href="{{ route('admin.cinemas.index') }}">Отмена</a></div></form>@endsection
