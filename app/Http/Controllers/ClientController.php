<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use DataTables;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request,$page=0)
    {
        // dd($request->all());
        if ($request->ajax()) {
            $data = Client::select('*');

            if(isset($request->start_date) && !empty($request->start_date) && isset($request->end_date) && !empty($request->end_date)){
                    $start_date = date('Y-m-d 00:00:00',strtotime($request->start_date));
                    $end_date = date('Y-m-d  23:59:59',strtotime($request->end_date));
                    $data->whereBetween('created_at', [$start_date,$end_date]);
            }
            if(isset($request->stateId) && !empty($request->stateId)){
                $statestr = $request->stateId;
                $explodeState = explode("-", $statestr);
                $stateId = $explodeState[1];
                $data->where("state",$stateId);
            }
            $btn = '';
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('id', function($row){
                        return $row->id;
                    })
                    ->addColumn('created_at', function($row){
                        return date('Y-m-d',strtotime($row->created_at));
                    })
                    ->addColumn('address', function($row){
                        return "<div id='copytext'>$row->address</div><a href='javascript:void(0);' class='copy'><i class='fa-solid fa-copy'></i></a>";
                    })
                    ->addColumn('action', function($row){
                           $btn = '<a href="clients/'.$row->id.'/edit" class="edit-btn btn btn-primary btn-sm">Edit</a>&nbsp;';
                           $btn .= '<a href="clients/'.$row->id.'" class="view btn btn-info btn-sm">View</a>&nbsp';
                           $btn .= '<form action="clients/' . $row->id.'" method="POST">
                           '.csrf_field().'
                           '.method_field("DELETE").'
                           <button type="submit" class="btn btn-danger">Delete</a>
                           </form>';

                            return $btn;
                    })
                    ->rawColumns(['action','address'])
                    ->make(true);
        }

        return view('client.index',compact('page'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('client.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = [];
        $rules = [
            'name'          => 'required',
            'mobile_number' => 'required|numeric|unique:clients,mobile_number|min:10',
            'gender'        => 'required|in:male,female,other',
            'password'      => 'required',
            'cpassword'     => 'same:password',
            'state'         =>'required',
            'city'          =>'required',
        ];
        if(isset($request->email) && !empty($request->email)) {
            $rules['email'] = 'email';
        }
        $request->validate($rules);
            $statestr = $request->state;
            $explodeState = explode("-", $statestr);
            $state = $explodeState[1];

        $data = [
            'name' => $request->name,
            'mobile_number' => $request->mobile_number,
            'email' => $request->email,
            'state' => $state,
            'city' => $request->city,
            'gender' => $request->gender,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'video_url' => $request->video_url
        ];

        $client = Client::create($data);

        if ($client) {
            return  redirect()->route('clients.index')->with('success', "Client has been added successfully.");
        } else {
            return  redirect()->back();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        return view('client.show', compact('client'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        return view('client.create', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        $rules = [
            'name'          => 'required',
            'cpassword'     => 'same:password',
        ];

        if(isset($request->email) && !empty($request->email)) {
            $rules['email'] = 'email';
        }
        $request->validate($rules);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            // 'password' => Hash::make($request->password)
        ];
        $client->update($data);

        if($client) {
            return redirect('/clients?page=' . $request->page)->with('success','Edited Successfull');
        } else {
            return redirect()->back()->with('error','Somthing went wrong');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        $client->delete();
        if($client) {
            return redirect()->route('clients.index')->with('success','Data deleted successfulll');
        } else {
            return redirect()->back()->with('error','Somthing went wrong');
        }
    }
}
