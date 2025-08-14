<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\WareHouse;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WareHouseController extends Controller
{
    public $house;

    public function __construct(WareHouse $house)
    {
        $this->house = $house;
    }

    public function index(){
        $warehouse = $this->house->orderBy('id', 'desc')->paginate(10);
        return view('admin.ware-house.warehouse', compact('warehouse'));
    }

    public function create(){
        $countries = Country::orderBy('name', 'asc')->get();
        return view('admin.ware-house.add_warehouse', compact('countries'));
    }

    public function store(Request $request){
        $data = $this->house->insertId($request);

        if ($data != 1) {
            return response()->json(['success' => false, 'message' => 'Record Added Failed']);
        }
        return response()->json(['success' => true, 'message' => 'Record Added Successfully']);
    }

    public function getStates(Request $request)
    {
        $country_id = $request->country_id;
        $states = DB::table('states')->where('country_id', $country_id)->orderBy('name', 'asc')->get();

        return response()->json($states);
    }

    public function getCities(Request $request)
    {
        $state_id = $request->state_id;
        $cities = DB::table('cities')->where('state_id', $state_id)->orderBy('name', 'asc')->get();

        return response()->json($cities);
    }

    public function edit(string $id){
        $warehouse = $this->house->find($id);
        $data['warehouse'] = $warehouse;
        $data['countries'] = Country::orderBy('name', 'asc')->get();
        $country = Country::where('name', $warehouse->country)->first();
        $data['states'] = DB::table('states')->where('country_id', $country->id)->orderBy('name', 'asc')->get();
        $state = DB::table('states')->where('name', $warehouse->state)->first();
        $data['cities'] = DB::table('cities')->where('state_id', $state->id)->orderBy('name', 'asc')->get();
        return view('admin.ware-house.edit_warehouse', $data);
    }

    public function update(Request $request, string $id)
    {
        $house = $this->house->find($id);

        $data = $this->house->InsertId($request, $house);

        if ($data != 1) {
            return response()->json(['success' => false, 'message' => 'Record Updated Failed']);
        }
        return response()->json(['success' => true, 'message' => 'Record Updated Successfully']);
    }

    public function destroy(string $id)
    {
        $data = $this->house->find($id);

        if(!$data){
            return back()->with('error','Record Delete Failed');
        }
        $data->delete();
        return back()->withSuccess('Record Delete Successfully');
    }
}