@extends('layouts.vendor.app')

@section('title', translate('addon_update'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title"><i class="tio-edit"></i> {{translate('messages.addon_update')}}</h1>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2" >
                <form action="{{route('vendor.addon.update',[$addon['id']])}}" method="post" enctype="multipart/form-data" class="row">
                    @csrf
                    @php($language=\App\Models\BusinessSetting::where('key','language')->first())
                    @php($language = $language->value ?? null)
                    @php($default_lang = str_replace('_', '-', app()->getLocale()))

                    @if($language)
                        <div class="col-12">
                            <ul class="nav nav-tabs mb-4">
                                <li class="nav-item">
                                    <a class="nav-link lang_link active" href="#" id="default-link">{{ translate('Default')}}</a>
                                </li>
                                @foreach(json_decode($language) as $lang)
                                    <li class="nav-item">
                                        <a class="nav-link lang_link" href="#" id="{{$lang}}-link">{{\App\CentralLogics\Helpers::get_language_name($lang).'('.strtoupper($lang).')'}}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if ($language)
                    <div class="form-group lang_form col-md-6" id="default-form">
                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.name')}}</label>
                        <input type="text" name="name[]" class="form-control" placeholder="{{translate('messages.new_addon')}}" value="{{ $addon?->getRawOriginal('name')}}" required maxlength="191" />
                    </div>
                    <input type="hidden" name="lang[]" value="default">
                        @foreach(json_decode($language) as $lang)
                            <?php
                                if(count($addon['translations'])){
                                    $translate = [];
                                    foreach($addon['translations'] as $t)
                                    {
                                        if($t->locale == $lang && $t->key=="name"){
                                            $translate[$lang]['name'] = $t->value;
                                        }
                                    }
                                }
                            ?>
                            <div class="col-md-6 form-group d-none lang_form" id="{{$lang}}-form">
                                <label class="input-label" for="exampleFormControlInput1">{{translate('messages.name')}} ({{strtoupper($lang)}})</label>
                                <input type="text" name="name[]" class="form-control" placeholder="{{translate('messages.new_addon')}}" maxlength="191" value="{{$lang==$default_lang?$addon?->getRawOriginal('name'):($translate[$lang]['name']??'')}}" {{$lang == $default_lang? 'required':''}} oninvalid="document.getElementById('en-link').click()">
                            </div>
                            <input type="hidden" name="lang[]" value="{{$lang}}">
                        @endforeach
                    @else
                        <div class="form-group lang_form col-md-6" id="default-form">
                            <label class="input-label" for="exampleFormControlInput1">{{translate('messages.name')}}</label>
                            <input type="text" name="name[]" class="form-control" placeholder="{{translate('messages.new_addon')}}" value="{{ $addon['name'] }}" required maxlength="191">
                        </div>
                        <input type="hidden" name="lang[]" value="default">
                    @endif
                        <div class="form-group col-md-6">
                            <label class="input-label" for="exampleFormControlInput1">Addon Image</label>
                            <input type="file" min="0" max="999999999999.99" step="0.01" name="image"  class="form-control" placeholder="200" >
                        </div>
                        <div class="form-group col-md-6">
                            <label class="input-label" for="exampleFormControlInput1">{{translate('messages.price')}}</label>
                            <input type="number" min="0" max="999999999999.99" step="0.01" name="price" value="{{$addon['price']}}" class="form-control" placeholder="200" required>
                        </div>


                    <div class="col-12">
                        <div class="btn--container justify-content-end">
                            <button type="reset" class="btn btn--reset">{{translate('messages.reset')}}</button>
                            <button type="submit" class="btn btn--primary">{{translate('messages.update')}}</button>
                        </div>
                    </div>
                </form>
            </div>
            <!-- End Table -->
        </div>
    </div>

@endsection

@push('script_2')
<script>
    $(".lang_link").click(function(e){
        e.preventDefault();
        $(".lang_link").removeClass('active');
        $(".lang_form").addClass('d-none');
        $(this).addClass('active');

        let form_id = this.id;
        let lang = form_id.substring(0, form_id.length - 5);
        console.log(lang);
        $("#"+lang+"-form").removeClass('d-none');
        if(lang == '{{$default_lang}}')
        {
            $(".from_part_2").removeClass('d-none');
        }
        else
        {
            $(".from_part_2").addClass('d-none');
        }
    });
</script>
@endpush
