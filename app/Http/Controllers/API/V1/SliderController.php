<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SliderRequest;
use App\Models\Slider;
use Illuminate\Http\Request;
use File;
class SliderController extends BaseController
{
    protected $slider = '';


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Slider $slider)
    {
        $this->middleware('auth:api');
        $this->slider = $slider;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Slider::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SliderRequest $request)
    {
        /**
         * converting base64 image string into jpg.
        */
        $imageName = time().".jpg";
        $imagePath = "images/sliders/".$imageName;
        \Image::make($request->image)->save(public_path($imagePath));

        $createSlide = new Slider();
        $createSlide->image = $imagePath;
        $createSlide->title = $request->title;
        $createSlide->action = $request->action;
        $createSlide->save();

        return $this->sendResponse($createSlide, 'Slide Created!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function is_base64($data)
    {
        if (preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $data)) {
        return TRUE;
        } else {
        return FALSE;
        }
    }

    public function update(SliderRequest $request, $id)
    {
        $updateSlide = Slider::find($id);

        if(base64_decode($request->image)){
            // delete old image from storage
            File::delete(public_path($updateSlide->image));

            // converting base64 image string into jpg.
            $imageName = time().".jpg";
            $imagePath = "images/sliders/".$imageName;
            \Image::make($request->image)->save(public_path($imagePath));
            $updateSlide->image = $imagePath;
        }
        $updateSlide->title = $request->title;
        $updateSlide->action = $request->action;
        $updateSlide->save();

        return $this->sendResponse($updateSlide, 'Slide Updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('isAdmin');
        $delete = $this->slider->findOrFail($id);
        // delete old image from storage
        File::delete(public_path($delete->image));
        $delete->delete();
        return $this->sendResponse($delete, 'Slider has been Deleted');
    }
}
