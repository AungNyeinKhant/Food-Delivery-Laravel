@extends('layouts.vendor.app')

@section('title',translate('messages.settings'))

@push('css_or_js')
<link href="{{asset('public/assets/admin/css/croppie.css')}}" rel="stylesheet">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h2 class="page-header-title text-capitalize">
                <div class="card-header-icon d-inline-flex mr-2 img">
                    <img src="{{asset('/public/assets/admin/img/resturant-panel/page-title/resturant.png')}}" alt="public">
                </div>
                <span>
                    {{translate('Restaurant Setup')}}
                </span>
            </h2>
        </div>
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex flex-row justify-content-between ">
                    <h4 class="text-capitalize m-0">
                        <span class="card-header-icon">
                            <i class="tio-settings-outlined"></i>
                        </span>
                        {{translate('messages.Close_Restaurant_Temporarily')}}
                        <span data-toggle="tooltip" data-placement="right" data-original-title="{{translate('If_enabled,_this_restaurant_will_be_closed_temporarily_and_hidden_from_customer_app_and_web_app._Restaurant_owners_can_re-open_this_restaurant_anytime_by_turning_off_this_button.')}}" class="input-label-secondary"><img src="{{asset('public/assets/admin/img/info-circle.svg')}}" alt="i"></span>

                    </h4>
                    <label class="switch toggle-switch-lg m-0">
                        <input type="checkbox" class="toggle-switch-input" onclick="restaurant_open_status(this)"
                            {{$restaurant->active ?'':'checked'}}>
                        <span class="toggle-switch-label">
                            <span class="toggle-switch-indicator"></span>
                        </span>
                    </label>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="tio-fastfood"></i> &nbsp; {{ translate('General_settings')}}
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-lg-4 col-sm-6">
                        <div class="form-group m-0">
                            <label class="toggle-switch toggle-switch-sm d-flex justify-content-between border rounded px-3 form-control" for="schedule_order">
                                <span class="pr-2">{{translate('messages.scheduled_Delivery')}}:
                                    <span data-toggle="tooltip" data-placement="right" data-original-title="{{translate('With_this_feature_enabled,_customers_can_choose_their_preferred_delivery_time_and_calendar_selection_from_your_restaurant.')}}" class="input-label-secondary"><img src="{{asset('public/assets/admin/img/info-circle.svg')}}" alt="i"></span>
                                </span>
                                <input type="checkbox" class="toggle-switch-input"

                                onclick="toogleStatusModal(event,'schedule_order','schedule-on.png','schedule-off.png','{{translate('Want_to_enable_the')}} <strong>{{translate('Scheduled_Delivery')}}</strong> {{translate('option')}} ?','{{translate('Want_to_disable_the')}} <strong>{{translate('Scheduled_Delivery')}}</strong> {{translate('option')}} ?',`<p>{{translate('If_enabled,_customers_can_order_food_on_a_scheduled_basis_from_your_restaurant.')}}</p>`,`<p>{{translate('If_disabled,_the_Scheduled_Order_option_will_be_hidden_from_your_restaurant.')}}</p>`)"
                                id="schedule_order" {{$restaurant->schedule_order?'checked':''}}>
                                <span class="toggle-switch-label">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                            <form action="{{route('vendor.business-settings.toggle-settings',[$restaurant->id,$restaurant->schedule_order?0:1, 'schedule_order'])}}" method="get" id="schedule_order_form">
                            </form>
                        </div>
                    </div>
                    <?php
                    $restaurant_subscription = \App\Models\RestaurantSubscription::where('restaurant_id', $restaurant->id)->first();
                    ?>
                     @if ($restaurant_subscription && $restaurant_subscription->delivery === 1)
                    <div class="col-lg-4 col-sm-6">
                        <div class="form-group m-0">
                            <label class="toggle-switch toggle-switch-sm d-flex justify-content-between border rounded px-3 form-control" for="delivery">
                                <span class="pr-2">
                                    {{translate('messages.Home_Delivery')}}:
                                    <span data-toggle="tooltip" data-placement="right" data-original-title="{{translate('If_enabled,_customers_can_order_food_for_home_delivery.')}}" class="input-label-secondary"><img src="{{asset('public/assets/admin/img/info-circle.svg')}}" alt="i"></span>
                                </span>
                                <input type="checkbox" name="delivery" class="toggle-switch-input"
                                onclick="toogleStatusModal(event,'delivery','dm-self-reg-on.png','dm-self-reg-off.png','{{translate('Want_to_enable_the')}} <strong>{{translate('Home_Delivery')}}</strong> {{translate('option')}} ?','{{translate('Want_to_disable_the')}} <strong>{{translate('Home_Delivery')}}</strong> {{translate('option')}} ?',`<p>{{translate('If_enabled,_customers_can_order_food_for_home_delivery.')}}</p>`,`<p>{{translate('If_disabled,_the_home_delivery_option_will_be_hidden_from_your_restaurant.')}}</p>`)"

                                id="delivery" {{$restaurant->delivery?'checked':''}}>
                                <span class="toggle-switch-label">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                            <form action="{{route('vendor.business-settings.toggle-settings',[$restaurant->id,$restaurant->delivery?0:1, 'delivery'])}}" method="get" id="delivery_form">
                            </form>
                        </div>
                    </div>
                     @endif

                    @php($data =0)
                    @if (($restaurant->restaurant_model == 'subscription' && isset($restaurant->restaurant_sub) && $restaurant->restaurant_sub->self_delivery == 1)  || ($restaurant->restaurant_model == 'commission' && $restaurant->self_delivery_system == 1) )
                    @php($data =1)
                    @endif

                    @if ($data)
                    <div class="col-lg-4 col-sm-6">
                        <div class="form-group m-0">
                            <label class="toggle-switch toggle-switch-sm d-flex justify-content-between border rounded px-3 form-control" for="free_delivery">
                                <span class="pr-2">
                                    {{translate('messages.free_delivery')}}:
                                    <span data-toggle="tooltip" data-placement="right" data-original-title="{{translate('If this option is on, customers will get free delivery')}}" class="input-label-secondary"><img src="{{asset('public/assets/admin/img/info-circle.svg')}}" alt="i"></span>
                                </span>
                                <input type="checkbox" name="free_delivery" class="toggle-switch-input"
                                onclick="toogleStatusModal(event,'free_delivery','free-delivery-on.png','free-delivery-off.png','{{translate('Want_to_enable_the')}} <strong>{{translate('free_delivery')}}</strong> {{translate('option')}} ?','{{translate('Want_to_disable_the')}} <strong>{{translate('free_delivery')}}</strong> {{translate('option')}} ?',`<p>{{translate('If_enabled,_customers_can_order_food_for_free_delivery.')}}</p>`,`<p>{{translate('If_disabled,_the_free_delivery_option_will_be_hidden_from_your_restaurant.')}}</p>`)"
                                id="free_delivery" {{$restaurant->free_delivery?'checked':''}}>
                                <span class="toggle-switch-label">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                            <form action="{{route('vendor.business-settings.toggle-settings',[$restaurant->id,$restaurant->free_delivery?0:1, 'free_delivery'])}}" method="get" id="free_delivery_form">
                            </form>
                        </div>
                    </div>
                    @endif
                     @if ($restaurant_subscription && $restaurant_subscription->take_away === 1)
                    <div class="col-lg-4 col-sm-6">
                        <div class="form-group m-0">
                            <label class="toggle-switch toggle-switch-sm d-flex justify-content-between border rounded px-3 form-control" for="take_away">
                                <span class="pr-2 text-capitalize">
                                    {{translate('messages.Takeaway')}}:
                                    <span data-toggle="tooltip" data-placement="right" data-original-title="{{translate('If_enabled,_customers_can_pick_up_their_food_from_your_restaurant')}}" class="input-label-secondary"><img src="{{asset('public/assets/admin/img/info-circle.svg')}}" alt="i"></span>
                                </span>
                                <input type="checkbox" class="toggle-switch-input"
                                onclick="toogleStatusModal(event,'take_away','takeaway-on.png','takeaway-off.png','{{translate('Want_to_enable_the')}} <strong>{{translate('Takeaway')}}</strong> {{translate('option')}} ?','{{translate('Want_to_disable_the')}} <strong>{{translate('Takeaway')}}</strong> {{translate('option')}} ?',`<p>{{translate('If_enabled,_customers_can_place_takeaway/self-pickup_orders.')}}</p>`,`<p>{{translate('If_disabled,_the_takeaway_option_will_be_hidden_from_your_restaurant.')}}</p>`)"
                                id="take_away" {{$restaurant->take_away?'checked':''}}>
                                <span class="toggle-switch-label">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                            <form action="{{route('vendor.business-settings.toggle-settings',[$restaurant->id,$restaurant->take_away?0:1, 'take_away'])}}" method="get" id="take_away_form">
                            </form>
                        </div>
                    </div>
                      @endif
                      
                     @if ($restaurant_subscription && $restaurant_subscription->dine_in === 1)
                   
                    <div class="col-lg-4 col-sm-6">
                        <div class="form-group m-0">
                            <label class="toggle-switch toggle-switch-sm d-flex justify-content-between border rounded px-3 form-control" for="dine_in">
                                <span class="pr-2 text-capitalize">
                                    {{translate('messages.Dine_in')}}:
                                    <span data-toggle="tooltip" data-placement="right" data-original-title="{{translate('If_enabled,_customers_can_dine_in_their_food_from_your_restaurant')}}" class="input-label-secondary"><img src="{{asset('public/assets/admin/img/info-circle.svg')}}" alt="i"></span>
                                </span>
                                <input type="checkbox" class="toggle-switch-input"
                                onclick="toogleStatusModal(event,'dine_in','dinein-on.png','dinein-off.png','{{translate('Want_to_enable_the')}} <strong>{{translate('Dinein')}}</strong> {{translate('option')}} ?','{{translate('Want_to_disable_the')}} <strong>{{translate('Dinein')}}</strong> {{translate('option')}} ?',`<p>{{translate('If_enabled,_customers_can_place_dine_in.')}}</p>`,`<p>{{translate('If_disabled,_the_dine_in_option_will_be_hidden_from_your_restaurant.')}}</p>`)"
                                id="dine_in" {{$restaurant->dine_in?'checked':''}}>
                                <span class="toggle-switch-label">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                            <form action="{{route('vendor.business-settings.toggle-settings',[$restaurant->id,$restaurant->dine_in?0:1, 'dine_in'])}}" method="get" id="dine_in_form">
                            </form>
                        </div>
                    </div>
                    @endif
                    @if ($toggle_veg_non_veg)
                    <div class="col-lg-4 col-sm-6">
                        <div class="form-group m-0">
                            <label class="toggle-switch toggle-switch-sm d-flex justify-content-between border rounded px-3 form-control" for="veg">
                                <span class="pr-2 text-capitalize">
                                    {{translate('messages.veg')}}:
                                    <span data-toggle="tooltip" data-placement="right" data-original-title="{{translate('If_enabled,_your_restaurant_will_be_shown_on_the_Veg_Restaurant_section_of_the_User_App.')}}" class="input-label-secondary"><img src="{{asset('public/assets/admin/img/info-circle.svg')}}" alt="i"></span>
                                </span>
                                <input type="checkbox" class="toggle-switch-input"

                                onclick="toogleStatusModal(event,'veg','veg-on.png','veg-off.png','{{translate('Want_to_enable_the')}} <strong>{{translate('veg')}}</strong> {{translate('option')}} ?','{{translate('Want_to_disable_the')}} <strong>{{translate('veg')}}</strong> {{translate('option')}} ?',`<p>{{translate('If_enabled,_customers_can_find_your_restaurant_in_the_veg_restaurant_list.')}}</p>`,`<p>{{translate('If_disabled,_your_restaurant_will_be_hidden_from_the_veg_restaurant_list.')}}</p>`)"
                                id="veg" {{$restaurant->veg?'checked':''}}>
                                <span class="toggle-switch-label">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                            <form action="{{route('vendor.business-settings.toggle-settings',[$restaurant->id,$restaurant->veg?0:1, 'veg'])}}" method="get" id="veg_form">
                            </form>
                        </div>
                    </div>

                    <div class="col-lg-4 col-sm-6">
                        <div class="form-group m-0">
                            <label class="toggle-switch toggle-switch-sm d-flex justify-content-between border rounded px-3 form-control" for="non_veg">
                                <span class="pr-2 text-capitalize">
                                    {{translate('messages.non_veg')}}:
                                    <span data-toggle="tooltip" data-placement="right" data-original-title="{{translate('If_enabled,_your_restaurant_will_be_shown_on_the_Non_Veg_Restaurant_section_of_the_User_App.')}}" class="input-label-secondary"><img src="{{asset('public/assets/admin/img/info-circle.svg')}}" alt="i"></span>
                                </span>
                                <input type="checkbox" class="toggle-switch-input"
                                    onclick="toogleStatusModal(event,'non_veg','veg-on.png','veg-off.png','{{translate('Want_to_enable_the')}} <strong>{{translate('non_veg')}}</strong> {{translate('option')}} ?','{{translate('Want_to_disable_the')}} <strong>{{translate('non_veg')}}</strong> {{translate('option')}} ?',`<p>{{translate('If_enabled,_customers_can_find_your_restaurant_in_the_veg_restaurant_list.')}}</p>`,`<p>{{translate('If_disabled,_your_restaurant_will_be_hidden_from_the_veg_restaurant_list.')}}</p>`)"
                                id="non_veg" {{$restaurant->non_veg?'checked':''}}>
                                <span class="toggle-switch-label">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                            <form action="{{route('vendor.business-settings.toggle-settings',[$restaurant->id,$restaurant->non_veg?0:1, 'non_veg'])}}" method="get" id="non_veg_form">
                            </form>
                        </div>
                    </div>
                    @endif

                    @php($order_subscription = \App\Models\BusinessSetting::where('key', 'order_subscription')->first())
                    @if (isset($order_subscription) && $order_subscription->value == 1)
                    <div class="col-lg-4 col-sm-6">
                        <div class="form-group m-0">
                            <label class="toggle-switch toggle-switch-sm d-flex justify-content-between border rounded px-3 form-control" for="order_subscription_active">
                                <span class="pr-2 text-capitalize">
                                    {{translate('messages.Subscription_based_Order')}}:
                                    <span data-toggle="tooltip" data-placement="right" data-original-title="{{translate('If_enabled,_customers_can_place_subscription_based_orders_from_your_restaurant.')}}" class="input-label-secondary"><img src="{{asset('public/assets/admin/img/info-circle.svg')}}" alt="i"></span>
                                </span>
                                <input type="checkbox" class="toggle-switch-input"
                                     onclick="toogleStatusModal(event,'order_subscription_active','restaurant-reg-on.png','restaurant-reg-off.png','{{translate('Want_to_enable_the')}} <strong>{{translate('Subscription_based_Order')}}</strong> {{translate('option')}} ?','{{translate('Want_to_disable_the')}} <strong>{{translate('Subscription_based_Order')}}</strong> {{translate('option')}} ?',`<p>{{translate('If_enabled,_customers_can_order_food_on_a_subscription_basis_from_your_restaurant.')}}</p>`,`<p>{{translate('If_disabled,_the_subscription-based_order_option_will_be_hidden_from_your_restaurant.')}}</p>`)"

                                id="order_subscription_active" {{$restaurant->order_subscription_active?'checked':''}}>
                                <span class="toggle-switch-label">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                             <form action="{{route('vendor.business-settings.toggle-settings',[$restaurant->id,$restaurant->order_subscription_active?0:1, 'order_subscription_active'])}}" method="get" id="order_subscription_active_form">
                            </form>
                        </div>
                    </div>
                    @endif
                    <div class="col-lg-4 col-sm-6">
                        <div class="form-group m-0">
                            <label class="toggle-switch toggle-switch-sm d-flex justify-content-between border rounded px-3 form-control" for="cutlery">
                                <span class="pr-2 text-capitalize">
                                    {{translate('messages.cutlery')}}:
                                    <span data-toggle="tooltip" data-placement="right" data-original-title="{{translate('If this option is on , customer can choose cutlery in user app.')}}" class="input-label-secondary"><img src="{{asset('public/assets/admin/img/info-circle.svg')}}" alt="i"></span>
                                </span>
                                <input type="checkbox" class="toggle-switch-input"
                                onclick="toogleStatusModal(event,'cutlery','restaurant-reg-on.png','restaurant-reg-off.png','{{translate('Want_to_enable_the')}} <strong>{{translate('cutlery')}}</strong> {{translate('option')}} ?','{{translate('Want_to_disable_the')}} <strong>{{translate('cutlery')}}</strong> {{translate('option')}} ?',`<p>{{translate('If_enabled,_customers_can_order_food_with_or_without_cutlery_from_your_restaurant.')}}</p>`,`<p>{{translate('If_disabled,_the_cutlery_option_will_be_hidden_from_your_restaurant.')}}</p>`)"
                                id="cutlery" {{$restaurant->cutlery?'checked':''}}>
                                <span class="toggle-switch-label">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>

                                <form action="{{route('vendor.business-settings.toggle-settings',[$restaurant->id,$restaurant->cutlery?0:1, 'cutlery'])}}" method="get" id="cutlery_form">
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title">
                    <span class="card-header-icon"><i class="tio-tune"></i></span> &nbsp;
                    {{translate('messages.basic_settings')}}
                </h5>
            </div>
            <div class="card-body">
                <form action="{{route('vendor.business-settings.update-setup',[$restaurant['id']])}}" method="post"
                    enctype="multipart/form-data">
                    @csrf


                    <div class="row g-3">
                        <div class="col-sm-{{$data?'4':'6'}} col-12">
                            <div class="form-group m-0">
                                <label class="input-label text-capitalize" for="title">{{translate('messages.minimum_order_amount')}}
                                    <span data-toggle="tooltip" data-placement="right" data-original-title="{{translate('Specify_the_minimum_order_amount_required_for_customers_when_ordering_from_this_restaurant.')}}" class="input-label-secondary"><img src="{{asset('public/assets/admin/img/info-circle.svg')}}" alt="i"></span>

                                </label>
                                <input type="number" name="minimum_order" step="0.01" min="0" max="100000" class="form-control" placeholder="100" value="{{$restaurant->minimum_order??'0'}}">
                            </div>
                        </div>
                        @if($data)
                        <div class="col-sm-{{$data?'4':'6'}} col-12">
                            <div class="form-group m-0">
                                <label class="input-label text-capitalize" for="minimum_shipping_charge">{{translate('messages.minimum_delivery_charge')}} ({{\App\CentralLogics\Helpers::currency_symbol()}})
                                </label>
                                <input type="number" id="minimum_shipping_charge" min="0" max="99999999.99" step="0.01" name="minimum_delivery_charge" class="form-control shipping_input" value="{{isset($restaurant->minimum_shipping_charge) ? $restaurant->minimum_shipping_charge : ''}}">
                            </div>
                        </div>

                        <div class="col-sm-{{$data?'4':'6'}} col-12">
                            <div class="form-group m-0">
                                <label class="input-label text-capitalize" for="title">{{translate('messages.delivery_charge_per_km')}} ({{\App\CentralLogics\Helpers::currency_symbol()}})</label>
                                <input type="number" name="per_km_delivery_charge" step="0.01" min="0" max="100000" class="form-control" placeholder="100" value="{{$restaurant->per_km_shipping_charge??'0'}}">
                            </div>
                        </div>
                        <div class="col-sm-{{$data?'4':'6'}} col-12">
                            <div class="form-group m-0">
                                <label class="input-label text-capitalize" for="title">{{translate('messages.maximum_shipping_charge')}} ({{\App\CentralLogics\Helpers::currency_symbol()}})
                                    <span data-toggle="tooltip" data-placement="right" data-original-title="{{translate('It will add a limite on total delivery charge.') }}"
                                    class="input-label-secondary"><img
                                        src="{{ asset('/public/assets/admin/img/info-circle.svg') }}"
                                        alt="{{ translate('messages.maximum_shipping_charge') }}"></span>
                                </label>
                                <input type="number" name="maximum_shipping_charge" step="0.01" min="0" max="999999999" class="form-control" placeholder="10000" value="{{$restaurant->maximum_shipping_charge??''}}">
                            </div>
                        </div>
                        @endif

                        <div class="col-sm-{{$data?'4':'6'}} col-12">
                            <div class="form-group m-0">
                                <label class="toggle-switch toggle-switch-sm d-flex justify-content-between input-label mb-1" for="gst_status">
                                    <span class="form-check-label">{{translate('messages.gst')}} <span class="input-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{translate('messages.If_enabled,_the_GST_number_will_be_shown_in_the_invoice')}}"><img src="{{asset('/public/assets/admin/img/info-circle.svg')}}" alt="{{translate('messages.If_enabled,_the_GST_number_will_be_shown_in_the_invoice')}}"></span></span>
                                    <input type="checkbox" class="toggle-switch-input" name="gst_status" id="gst_status" value="1" {{$restaurant->gst_status?'checked':''}}>
                                    <span class="toggle-switch-label text">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>
                                <input type="number" id="gst" name="gst" class="form-control" value="{{$restaurant->gst_code}}" {{isset($restaurant->gst_status)?'':'readonly'}}>
                            </div>
                        </div>


                    <div class="col-sm-{{$data?'4':'6'}} col-12">
                        <div class="form-group m-0">
                            <label class="input-label" for="cuisine">{{ translate('messages.cuisine') }}
                                <span data-toggle="tooltip" data-placement="right" data-original-title="{{translate('Choose_your_preferred_cuisines_from_the_drop-down_menu,_and_customers_can_see_them_in_your_restaurant.')}}" class="input-label-secondary"><img src="{{asset('public/assets/admin/img/info-circle.svg')}}" alt="i"></span>
                                    </label>
                            <select name="cuisine_ids[]" id="cuisine"  multiple="multiple"
                                data-placeholder="{{ translate('messages.select_Cuisine') }}"
                                class="form-control h--45px min--45 js-select2-custom">
                                {{ translate('messages.Cuisine') }}</option>
                                {{-- <option value="" selected disabled>{{ translate('messages.select_Cuisine') }}</option> --}}
                                {{-- @foreach (\App\Models\Cuisine::where('status',1 )->get(['id','name']) as $cu)
                                        <option value="{{ $cu->id }}"
                                            {{in_array($cu->id, json_decode($cuisines,true))?'selected':''}}
                                            {{ $restaurant->cuisine_id == $cu->id ? 'selected' : '' }}
                                            >
                                            {{ $cu->name }}</option>
                                @endforeach --}}
                                @php($cuisine_array = \App\Models\Cuisine::where('status',1 )->get()->toArray())
                                @php($selected_cuisine = isset($restaurant->cuisine) ? $restaurant->cuisine->pluck('id')->toArray() : [])
                                @foreach ($cuisine_array as $cu)
                                    <option value="{{ $cu['id'] }}"
                                        {{ in_array($cu['id'], $selected_cuisine) ? 'selected' : '' }}>
                                        {{ $cu['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        </div>
                    </div>


                    <div class="btn--container justify-content-end mt-3">
                        <button type="reset" class="btn btn--reset">{{translate('messages.reset')}}</button>
                        <button type="submit" class="btn btn--primary">{{translate('messages.update')}}</button>
                    </div>
                </form>
            </div>
        </div>


        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title">
                    <span class="card-header-icon">
                        <img class="w--22" src="{{asset('public/assets/admin/img/restaurant.png')}}" alt="">
                    </span>
                    <span class="p-md-1"> {{translate('messages.restaurant_meta_data')}}</span>
                </h5>
            </div>
            @php($language=\App\Models\BusinessSetting::where('key','language')->first())
            @php($language = $language->value ?? null)
            @php($default_lang = 'en')
            <div class="card-body">
                <form action="{{route('vendor.business-settings.update-meta-data',[$restaurant['id']])}}" method="post"
                enctype="multipart/form-data" class="col-12">
                @csrf
                    <div class="row g-2">
                        <div class="col-lg-6">
                            <div class="card shadow--card-2">
                                <div class="card-body">
                                    @if($language)
                                    <ul class="nav nav-tabs mb-4">
                                        <li class="nav-item">
                                            <a class="nav-link lang_link active"
                                            href="#"
                                            id="default-link">{{ translate('Default') }}</a>
                                        </li>
                                        @foreach (json_decode($language) as $lang)
                                            <li class="nav-item">
                                                <a class="nav-link lang_link"
                                                    href="#"
                                                    id="{{ $lang }}-link">{{ \App\CentralLogics\Helpers::get_language_name($lang) . '(' . strtoupper($lang) . ')' }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                    @endif
                                    @if ($language)
                                    <div class="lang_form"
                                    id="default-form">
                                        <div class="form-group">
                                            <label class="input-label"
                                                for="default_title">{{ translate('messages.meta_title') }}
                                                ({{ translate('messages.Default') }})
                                            </label>
                                            <input type="text" name="meta_title[]" id="default_title"
                                                class="form-control" placeholder="{{ translate('messages.meta_title') }}" value="{{$restaurant->getRawOriginal('meta_title')}}"

                                                oninvalid="document.getElementById('en-link').click()">
                                        </div>
                                        <input type="hidden" name="lang[]" value="default">
                                        <div class="form-group mb-0">
                                            <label class="input-label"
                                                for="exampleFormControlInput1">{{ translate('messages.meta_description') }} ({{ translate('messages.default') }})</label>
                                            <textarea type="text" name="meta_description[]" placeholder="{{translate('messages.meta_description')}}" class="form-control min-h-90px ckeditor">{{$restaurant->getRawOriginal('meta_description')}}</textarea>
                                        </div>
                                    </div>
                                        @foreach (json_decode($language) as $lang)
                                        <?php
                                            if(count($restaurant['translations'])){
                                                $translate = [];
                                                foreach($restaurant['translations'] as $t)
                                                {
                                                    if($t->locale == $lang && $t->key=="meta_title"){
                                                        $translate[$lang]['meta_title'] = $t->value;
                                                    }
                                                    if($t->locale == $lang && $t->key=="meta_description"){
                                                        $translate[$lang]['meta_description'] = $t->value;
                                                    }
                                                }
                                            }
                                        ?>
                                            <div class="d-none lang_form"
                                                id="{{ $lang }}-form">
                                                <div class="form-group">
                                                    <label class="input-label"
                                                        for="{{ $lang }}_title">{{ translate('messages.meta_title') }}
                                                        ({{ strtoupper($lang) }})
                                                    </label>
                                                    <input type="text" name="meta_title[]" id="{{ $lang }}_title"
                                                        class="form-control" value="{{ $translate[$lang]['meta_title']??'' }}" placeholder="{{ translate('messages.meta_title') }}"
                                                        oninvalid="document.getElementById('en-link').click()">
                                                </div>
                                                <input type="hidden" name="lang[]" value="{{ $lang }}">
                                                <div class="form-group mb-0">
                                                    <label class="input-label"
                                                        for="exampleFormControlInput1">{{ translate('messages.meta_description') }} ({{ strtoupper($lang) }})</label>
                                                    <textarea type="text" name="meta_description[]" placeholder="{{translate('messages.meta_description')}}" class="form-control min-h-90px ckeditor">{{ $translate[$lang]['meta_description']??'' }}</textarea>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div id="default-form">
                                            <div class="form-group">
                                                <label class="input-label"
                                                    for="exampleFormControlInput1">{{ translate('messages.meta_title') }} ({{ translate('messages.default') }})</label>
                                                <input type="text" name="meta_title[]" class="form-control"
                                                    placeholder="{{ translate('messages.meta_title') }}" >
                                            </div>
                                            <input type="hidden" name="lang[]" value="default">
                                            <div class="form-group mb-0">
                                                <label class="input-label"
                                                    for="exampleFormControlInput1">{{ translate('messages.meta_description') }}
                                                </label>
                                                <textarea type="text" name="meta_description[]" placeholder="{{translate('messages.meta_description')}}" class="form-control min-h-90px ckeditor"></textarea>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="card shadow--card-2">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <span class="card-header-icon mr-1"><i class="tio-dashboard"></i></span>
                                        <span>{{translate('restaurant_meta_image')}}</span>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex flex-wrap flex-sm-nowrap __gap-12px">
                                        <label class="__custom-upload-img mr-lg-5">
                                            <label class="form-label">
                                                {{ translate('meta_image') }} <span class="text--primary">({{ translate('1:1') }})</span>
                                            </label>
                                            <center>
                                                <img class="img--110 min-height-170px min-width-170px" id="viewer"
                                                    onerror="this.src='{{ asset('public/assets/admin/img/upload.png') }}'"
                                                    src="{{asset('storage/app/public/restaurant').'/'.$restaurant->meta_image}}" alt="{{$restaurant->name}}"
                                                    alt="{{ translate('meta_image') }}" />
                                            </center>
                                            <input type="file" name="meta_image" id="customFileEg1" class="custom-file-input"
                                                accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="justify-content-end btn--container">
                                <button type="submit" class="btn btn--primary">{{translate('save_changes')}}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="tio-date-range"></i> &nbsp;
                    {{ translate('Restaurant_Opening_&_Closing_Schedules') }}
                </h5>
            </div>
            <div class="card-body" id="schedule">
                @include('vendor-views.business-settings.partials._schedule', $restaurant)
            </div>
        </div>
    </div>

    <!-- Create schedule modal -->

    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{translate('messages.Create Schedule For ')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="javascript:" method="post" id="add-schedule">
                        @csrf
                        <input type="hidden" name="day" id="day_id_input">
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">{{translate('messages.Start_time')}}:</label>
                            <input type="time" class="form-control" name="start_time" required>
                        </div>
                        <div class="form-group">
                            <label for="message-text" class="col-form-label">{{translate('messages.End_time')}}:</label>
                            <input type="time" class="form-control" name="end_time" required>
                        </div>
                        <div class="btn--container justify-content-end">
                            <button type="reset" class="btn btn--reset">{{translate('messages.reset')}}</button>
                            <button type="submit" class="btn btn--primary">{{translate('messages.Submit')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script>
        function restaurant_open_status(e) {
            Swal.fire({
                title: '{{ !$restaurant->active ? translate('messages.Want_to_make_your_restaurant_available_for_all') :  translate('messages.Want_to_close_your_restaurant_temporarily')}} ?',
                text: '{{!$restaurant->active ? translate('messages.If_yes_this_restaurant_will_be_available_for_customers_in_app_and_web') : translate('messages.If_yes_this_restaurant_will_be_unavailable_for_customers_in_apps_and_web') }}',
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: '#377dff',
                cancelButtonText: '{{translate('messages.no')}}',
                confirmButtonText: '{{translate('messages.yes')}}',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.get({
                        url: '{{route('vendor.business-settings.update-active-status')}}',
                        contentType: false,
                        processData: false,
                        beforeSend: function () {
                            $('#loading').show();
                        },
                        success: function (data) {
                            toastr.success(data.message);
                        },
                        complete: function () {
                            $('#loading').hide();
                            location.reload();
                        },
                    });
                } else {
                    e.checked = !e.checked;
                }
            })
        };

        function delete_schedule(route) {
            Swal.fire({
                title: '{{translate('messages.Want_to_delete_this_day’s_schedule')}}',
                text: '{{translate('messages.If_yes,_the_schedule_will_be_removed_from_here._However,_you_can_also_add_another_one.')}}',
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: '#377dff',
                cancelButtonText: '{{translate('messages.no')}}',
                confirmButtonText: '{{translate('messages.yes')}}',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.get({
                        url: route,
                        beforeSend: function () {
                            $('#loading').show();
                        },
                        success: function (data) {
                            if (data.errors) {
                                for (var i = 0; i < data.errors.length; i++) {
                                    toastr.error(data.errors[i].message, {
                                        CloseButton: true,
                                        ProgressBar: true
                                    });
                                }
                            } else {
                                $('#schedule').empty().html(data.view);
                                toastr.success('{{translate('messages.Schedule removed successfully')}}', {
                                    CloseButton: true,
                                    ProgressBar: true
                                });
                            }
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            toastr.error('{{translate('messages.Schedule not found')}}', {
                                CloseButton: true,
                                ProgressBar: true
                            });
                        },
                        complete: function () {
                            $('#loading').hide();
                        },
                    });
                }
            })
        };


        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileEg1").change(function () {
            readURL(this);
        });

        $(document).on('ready', function () {
            // INITIALIZATION OF SELECT2
            // =======================================================
            $('.js-select2-custom').each(function () {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });

            $("#gst_status").on('change', function(){
                if($("#gst_status").is(':checked')){
                    $('#gst').removeAttr('readonly');
                } else {
                    $('#gst').attr('readonly', true);
                }
            });
        });

        $('#exampleModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var day_name = button.data('day');
            var day_id = button.data('dayid');
            var modal = $(this);
            modal.find('.modal-title').text('{{translate('messages.Create Schedule For ')}} ' + day_name);
            modal.find('.modal-body input[name=day]').val(day_id);
        })

        $('#add-schedule').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('vendor.business-settings.add-schedule')}}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    if (data.errors) {
                        for (var i = 0; i < data.errors.length; i++) {
                            toastr.error(data.errors[i].message, {
                                CloseButton: true,
                                ProgressBar: true
                            });
                        }
                    } else {
                        $('#schedule').empty().html(data.view);
                        $('#exampleModal').modal('hide');
                        toastr.success('{{translate('messages.Schedule added successfully')}}', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    toastr.error(XMLHttpRequest.responseText, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        });

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
