<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class UserController
 * @package App\Http\Controllers
 */
class UserController extends Controller
{

    function table(Request $request)
    {

        $searchText=$request->searchTerm;
        $status=$request->filterStatus;
        $data=DB::table('users')
            ->where(function ($query) use ($searchText)
            {
                if(empty($searchText) || trim($searchText)=="")
                {
                    return $query;
                }
                else
                {
                    return $query->where('users.name','like',"%{$searchText}%");
                }
            })
            ->where(function ($query) use ($status)
            {
                if($status=="-1")
                {
                    return $query;
                }
                else
                {
                    return $query->where('users.status',$status);
                }
            });
        $data_count=clone $data;
        $data=$data->simplePaginate($request->perPage);
        $data=$data->toArray();
        $data['total_records']=$data_count->count();
        return $data;

    }
    function update(Request $request)
    {

    }
    function store(Request $request)
    {
        $id=$request->id;
        $user=User::find($id);
        $user->name=$request->name;
        $user->status=$request->status;
        $user->save();
        return response()->json([
            "success"=>true
        ]);
    }
    function show($id)
    {
        return User::find($id);
    }
}
