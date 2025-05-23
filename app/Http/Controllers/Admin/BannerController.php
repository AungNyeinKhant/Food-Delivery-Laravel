<?php

namespace App\Http\Controllers\Admin;

use App\Models\Banner;

use App\Models\DataSetting;
use App\Models\Translation;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BannerController extends Controller
{
    function index()
    {
        $banners = Banner::latest()->paginate(config('default_pagination'));
        return view('admin-views.banner.index', compact('banners'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:191',
            'image' => 'required|max:2048',
            'banner_type' => 'required',
            'zone_id' => 'required',
            'restaurant_id' => 'required_if:banner_type,restaurant_wise',
            'item_id' => 'required_if:banner_type,item_wise',
             'category_id' => 'required_if:banner_type,category_wise',
        ], [
            'zone_id.required' => translate('messages.select_a_zone'),
            'restaurant_id.required_if'=> translate('messages.Restaurant is required when banner type is restaurant wise'),
            'item_id.required_if'=> translate('messages.Food is required when banner type is food wise'),
            'category_id.required_if'=> translate('messages.Category is required when banner type is category wise'),
        ]);

        if($request->title[array_search('default', $request->lang)] == '' ){
            $validator->getMessageBag()->add('title', translate('messages.default_title_is_required'));
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
            }
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $banner = new Banner;
        $banner->title = $request->title[array_search('default', $request->lang)];
        $banner->type = $request->banner_type;
        $banner->zone_id = $request->zone_id;
        $banner->image = Helpers::upload(dir:'banner/',  format:'png', image: $request->file('image'));
         if ($request->banner_type == 'restaurant_wise') {
            $banner->data = $request->restaurant_id;
        } elseif ($request->banner_type == 'item_wise') {
            $banner->data = $request->item_id;
        } 
        else {
            $banner->data = $request->category_id;
        }
        $banner->save();
        $data=[];
        $default_lang = str_replace('_', '-', app()->getLocale());
        foreach ($request->lang as $index => $key) {
            if($default_lang == $key && !($request->title[$index])){
                if ($key != 'default') {
                    array_push($data, array(
                        'translationable_type' => 'App\Models\Banner',
                        'translationable_id' => $banner->id,
                        'locale' => $key,
                        'key' => 'title',
                        'value' => $banner->title,
                    ));
                }
            }else{
                if ($request->title[$index] && $key != 'default') {
                    array_push($data, array(
                        'translationable_type' => 'App\Models\Banner',
                        'translationable_id' => $banner->id,
                        'locale' => $key,
                        'key' => 'title',
                        'value' => $request->title[$index],
                    ));
                }
            }
        }
        Translation::insert($data);
        return response()->json([], 200);
    }

    public function edit(Banner $banner)
    {
        return view('admin-views.banner.edit', compact('banner'));
    }


    public function status(Request $request)
    {
        $banner = Banner::findOrFail($request->id);
        $banner->status = $request->status;
        $banner->save();
        Toastr::success(translate('messages.banner_status_updated'));
        return back();
    }

    public function update(Request $request, Banner $banner)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:191',
            'banner_type' => 'required',
            'zone_id' => 'required',
            'image' => 'nullable|max:2048',
            'restaurant_id' => 'required_if:banner_type,restaurant_wise',
            'item_id' => 'required_if:banner_type,item_wise',
            'category_id' => 'required_if:banner_type,category_wise',
        ], [
            'zone_id.required' => translate('messages.select_a_zone'),
            'restaurant_id.required_if'=> translate('messages.Restaurant is required when banner type is restaurant wise'),
            'item_id.required_if'=> translate('messages.Food is required when banner type is food wise'),
             'category_id.required_if'=> translate('messages.Category is required when banner type is category wise'),
        ]);


        if($request->title[array_search('default', $request->lang)] == '' ){
            $validator->getMessageBag()->add('title', translate('messages.default_title_is_required'));
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
            }

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $banner->title = $request->title[array_search('default', $request->lang)];;
        $banner->type = $request->banner_type;
        $banner->zone_id = $request->zone_id;
        $banner->image = $request->has('image') ? Helpers::update(dir:'banner/',old_image: $banner->image, format:'png', image: $request->file('image')) : $banner->image;
          //  category_wise update
        if ($request->banner_type == 'category_wise') {
            $banner->data = $request->category_id;
        } else {
            //  restaurant_wise and item_wise update
            $banner->data = $request->banner_type == 'restaurant_wise' ? $request->restaurant_id : $request->item_id;
        }
        $banner->save();
        $default_lang = str_replace('_', '-', app()->getLocale());
        foreach ($request->lang as $index => $key) {
            if($default_lang == $key && !($request->title[$index])){
                if ($key != 'default') {
                    Translation::updateOrInsert(
                        [
                            'translationable_type' => 'App\Models\Banner',
                            'translationable_id' => $banner->id,
                            'locale' => $key,
                            'key' => 'title'
                        ],
                        ['value' => $banner->title]
                    );
                }
            }else{

                if ($request->title[$index] && $key != 'default') {
                    Translation::updateOrInsert(
                        [
                            'translationable_type' => 'App\Models\Banner',
                            'translationable_id' => $banner->id,
                            'locale' => $key,
                            'key' => 'title'
                        ],
                        ['value' => $request->title[$index]]
                    );
                }
            }
        }
        return response()->json([], 200);
    }

    public function delete(Banner $banner)
    {
        if (Storage::disk('public')->exists('banner/' . $banner['image'])) {
            Storage::disk('public')->delete('banner/' . $banner['image']);
        }
        $banner?->translations()?->delete();
        $banner->delete();
        Toastr::success(translate('messages.banner_deleted_successfully'));
        return back();
    }

    public function search(Request $request){
        $key = explode(' ', $request['search']);
        $banners=Banner::where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('title', 'like', "%{$value}%");
            }
        })->limit(50)->get();
        return response()->json([
            'view'=>view('admin-views.banner.partials._table',compact('banners'))->render(),
            'count'=>$banners->count()
        ]);
    }





    public function promotional_banner(){
        $banner_title =  DataSetting::where('type','promotional_banner')->where('key' ,'promotional_banner_title')->withoutGlobalScope('translate')->with('translations')->first();
        $banner_image =  DataSetting::where('type','promotional_banner')->where('key', 'promotional_banner_image')->withoutGlobalScope('translate')->with('translations')->first();
        return view('admin-views.banner.promotional_banner', compact('banner_title','banner_image'));
    }

    public function promotional_banner_update(Request $request){

        $request->validate([
            'promotional_banner_title.*' => 'max:191',
            'promotional_banner_title.0'=>'required',
            'promotional_banner_image' => 'nullable|max:2048',
        ], [
            'promotional_banner_title.required' => translate('messages.Title is required!'),
            'promotional_banner_title.0.required'=>translate('default_Title_is_required'),
        ]);

        if( $request->has('promotional_banner_image')){
            $banner = DataSetting::firstOrNew(
                ['key' =>  'promotional_banner_image',
                'type' =>  'promotional_banner'],
            );
            $banner->value=   Helpers::update(dir:'banner/',old_image: $banner->value, format:'png', image: $request->file('promotional_banner_image'));
            $banner->save();
        }

        // dd($request->all());
        $this->update_data($request , 'promotional_banner_title','promotional_banner_title' );
        Toastr::success(translate('messages.banner_updated_successfully'));
        return back();

    }
    
    //popup Status by ANK(12.3.2024)
    public function popup_banner_status_update(Request $request){
        $banner_status =  DataSetting::where('type','popup_banner')->where('key', 'popup_banner_status')->first();
        $banner_status->value = $request->value;
        $banner_status?->save();
        
        Toastr::success(translate('messages.popup_banner_') . translate('messages.status_updated'));
        return back();
    }
    
    //popup Banner by ANK(31.1.2024)
    public function popup_banner(){
        $banner_link =  DataSetting::where('id' ,98)->first();// where('type','popup_banner')->where('key' ,'popup_banner_link') didn't work 
        $banner_image =  DataSetting::where('type','popup_banner')->where('key', 'popup_banner_image')->first();//->withoutGlobalScope('translate')->with('translations')
        $banner_status =  DataSetting::where('type','popup_banner')->where('key', 'popup_banner_status')->first();
        return view('admin-views.banner.popup_banner', compact('banner_link','banner_image','banner_status'));
    }

    public function popup_banner_update(Request $request){

        $request->validate([
            // 'popup_banner_link.*' => 'max:191',
            // 'popup_banner_link.0'=>'required',
            'popup_banner_image' => 'nullable|max:2048',
        ], [
            // 'popup_banner_link.required' => translate('messages.Link is required!'),
            // 'popup_banner_link.0.required'=>translate('default_Link_is_required'),
        ]);

        if( $request->has('popup_banner_image')){
            $banner = DataSetting::firstOrNew(
                ['key' =>  'popup_banner_image',
                'type' =>  'popup_banner'],
            );
            $banner->value=   Helpers::update(dir:'banner/',old_image: $banner->value, format:'png', image: $request->file('popup_banner_image'));
            $banner->save();
        }
        
        $banner_link_value = $request->input('popup_banner_link');
        
        // DB::update('UPDATE data_settings SET value = ? WHERE type = "popup_banner" AND `key` = "popup_banner_link" ',[$banner_link_value]);
        DB::update('UPDATE data_settings SET value = ? WHERE id = 98 AND `key` = "popup_banner_link" ',[$banner_link_value]);
        Toastr::success(translate('messages.popup_banner_updated_successfully'));
        return back();

    }

    //popup Banner End by ANK(31.1.2024)


    private function update_data($request, $key_data, $name_field , $type = 'promotional_banner' ){
        $data = DataSetting::firstOrNew(
            ['key' =>  $key_data,
            'type' =>  $type],
        );
// dd($request->{$name_field}[array_search('default', $request->lang)]);
        $data->value = $request->{$name_field}[array_search('default', $request->lang)];
        $data->save();
        $default_lang = str_replace('_', '-', app()->getLocale());
        foreach ($request->lang as $index => $key) {
            if ($default_lang == $key && !($request->{$name_field}[$index])) {
                if ($key != 'default') {
                    Translation::updateOrInsert(
                        [
                            'translationable_type' => 'App\Models\DataSetting',
                            'translationable_id' => $data->id,
                            'locale' => $key,
                            'key' => $key_data
                        ],
                        ['value' => $data->value]
                    );
                }
            } else {
                if ($request->{$name_field}[$index] && $key != 'default') {
                    Translation::updateOrInsert(
                        [
                            'translationable_type' => 'App\Models\DataSetting',
                            'translationable_id' => $data->id,
                            'locale' => $key,
                            'key' => $key_data
                        ],
                        ['value' => $request->{$name_field}[$index]]
                    );
                }
            }
        }

        return true;
    }
}
