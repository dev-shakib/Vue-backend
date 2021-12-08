<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\TopBar;
use Illuminate\Http\Request;

class TopBarController extends Controller
{
    protected $topbar = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(TopBar $topbar)
    {
        $this->middleware('auth:api');
        $this->topbar = $topbar;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = $this->topbar->latest()->paginate(10);

        return $this->sendResponse($categories, 'topbar list');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        $categories = $this->topbar->pluck('name', 'id');

        return $this->sendResponse($categories, 'topbar list');
    }


    /**
     * Store a newly created resource in storage.
     *
     *
     * @param $id
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $tag = $this->topbar->create([
            'name' => $request->get('name'),
            'description' => $request->get('description'),
        ]);

        return $this->sendResponse($tag, 'topbar Created Successfully');
    }

    /**
     * Update the resource in storage
     *
     * @param $id
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, $id)
    {
        $tag = $this->topbar->findOrFail($id);

        $tag->update($request->all());

        return $this->sendResponse($tag, 'topbar Information has been updated');
    }
}
