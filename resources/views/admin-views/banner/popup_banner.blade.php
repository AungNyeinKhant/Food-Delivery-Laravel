@extends('layouts.admin.app')

@section('title',translate('messages.popup_banner'))


@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title"><i class="tio-edit"></i>{{translate('messages.popup_banner')}}</h1>
            </div>
            <div class="col-1">
                <label class="toggle-switch toggle-switch-sm" for="stocksCheckbox">
                    <input onclick="status_change_alert('{{route('admin.banner.popup_status_update',[ 'value' => $banner_status?->value?0:1] ) }}', '{{translate('messages.you_want_to_change_popup_banner_status')}}', event);" type="checkbox"  class="toggle-switch-input" id="stocksCheckbox" {{$banner_status?->getRawOriginal('value') == '1'?'checked':''}}>
                    <span class="toggle-switch-label">
                        <span class="toggle-switch-indicator"></span>
                    </span>
                </label>
            </div>
        </div>
    </div>

        <div class="col-lg-12 mb-3 mb-lg-12">
            <div class="card h-100">
                <div class="card-body">
                    <form action="{{route('admin.banner.popup_banner_update')}}" enctype="multipart/form-data" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    
                                    <ul class="nav nav-tabs mb-4">
                                        <li class="nav-item">
                                            <a class="nav-link lang_link active" href="#"
                                                id="def$banner_linkault-link">{{translate('messages.default')}}</a>
                                        </li>
                                        <li class="nav-item">
                                            <?php
                                                // if($banner_link){
                                                //     var_dump($banner_link);
                                                // }else{
                                                //     echo "It's not here";
                                                // };
                                                
                                            ?>
                                        </li>
                                        
                                    </ul>
                                    <div class="lang_form" id="default-form">
                                        <div class="form-group">
                                            <label class="input-label"
                                                for="default_title">{{translate('messages.Banner_link')}}
                                            </label>
                                            <input type="text" name="popup_banner_link" id="default_title" class="form-control"
                                                placeholder="{{translate('messages.enter_banner_link')}}" value="{{ $banner_link?->getRawOriginal('value') ?? null }}"
                                                oninvalid="document.getElementById('en-link').click()">
                                        </div>
                                        
                                    </div>
                                    

                                </div>

                            </div>

                        </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12 mb-3 mb-lg-12">
            <div class="card h-100">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12 d-flex justify-content-between">
                                <span class="d-flex g-1">
                                    <img src="{{asset('public/assets/admin/img/other-banner.png')}}" class="h-85"
                                        alt="">
                                </span>
                                <div>
                                    <div class="blinkings">
                                        <div>
                                            <i class="tio-info-outined"></i>
                                        </div>
                                        <div class="business-notes">
                                            <h6><img src="{{asset('/public/assets/admin/img/notes.png')}}" alt="">
                                                {{translate('Note')}}</h6>
                                            <div>
                                                {{translate('messages.this_banner_is_only_for_web.')}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <h3 class="form-label d-block mb-5 text-center">
                                    {{translate('Upload_Banner')}}
                                </h3>
                                <label class="__upload-img aspect-5-1 m-auto d-block">
                                    <div class="img">
                                        <img src="{{asset('storage/app/public/banner')}}/{{$banner_image?->value ?? null}}"
                                            onerror='this.src="{{asset('/public/assets/admin/img/upload-placeholder.png')}}"' alt="">
                                    </div>
                                    <input type="file" name="popup_banner_image" hidden>
                                </label>

                                <div class="text-center mt-5">
                                    <h3 class="form-label d-block mt-2">
                                        {{translate('Min_Size_for_Better_Resolution_5:1')}}
                                    </h3>
                                    <p>{{translate('image_format_:_jpg_,_png_,_jpeg_|_maximum_size:_2_MB')}}</p>

                                </div>
                            </div>
                        </div>

                    </div>
            </div>

        </div>
        <div class="btn--container justify-content-end mt-3">
            <button type="submit" class="btn btn--primary mb-2">{{translate('messages.Save')}}</button>
        </div>
        </form>

    </div>

</div>


@endsection

@push('script_2')
<script>
    function status_change_alert(url, message, e) {
        e.preventDefault();
        Swal.fire({
            title: '{{ translate('Are_you_sure?') }}',
            text: message,
            type: 'warning',
            showCancelButton: true,
            cancelButtonColor: 'default',
            confirmButtonColor: '#FC6A57',
            cancelButtonText: '{{ translate('no') }}',
            confirmButtonText: '{{ translate('yes') }}',
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                location.href=url;
            }
        })
    }
    $(document).ready(function() {
        "use strict"
        $(".__upload-img, .upload-img-4, .upload-img-2, .upload-img-5, .upload-img-1, .upload-img").each(function(){
            var targetedImage = $(this).find('.img');
            var targetedImageSrc = $(this).find('.img img');
            function proPicURL(input) {
                if (input.files && input.files[0]) {
                    var uploadedFile = new FileReader();
                    uploadedFile.onload = function (e) {
                        targetedImageSrc.attr('src', e.target.result);
                        targetedImage.addClass('image-loaded');
                        targetedImage.hide();
                        targetedImage.fadeIn(650);
                    }
                    uploadedFile.readAsDataURL(input.files[0]);
                }
            }
            $(this).find('input').on('change', function () {
                proPicURL(this);
            })
        })
    });



    
</script>
@endpush
