<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Tasks;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;



class TasksController extends BaseController
{   
    /**
     * get tasks
     * return collection
     */
    public function getTasks(Request $request){

         $validator = Validator::make($request->all(), [
            'name' => 'sometimes',
            'status' => 'sometimes',
            'startDate' => 'required',
            'endDate' => 'required',
        ]);
        
        if($validator->fails()){
            return $this->sendError('Validation Error', $validator->errors(),403);       
        }

        $name = '';
        $status = '';
        $startDate = Carbon::today()->format("Y-m-d");
        $endDate = Carbon::today()->format("Y-m-d");
        if(!empty($request->startDate)){
            $startDate = Carbon::parse($request->startDate)->format("Y-m-d");
        }

        if(!empty($request->endDate)){
            $endDate = Carbon::parse($request->endDate)->format("Y-m-d");
        }
        
        if(!empty($request->name)){
            $name = $request->name;
        }

        if(!empty($request->status)){
            $status = $request->status;
        }
        
        $tasks = auth()->user()->tasks()
        ->where('name', 'like', $name.'%')
        ->where('date' ,'>=',$startDate)
        ->where('date','<=',$endDate)
        ->when($status, function ($query, $status) {
            return $query->where('status','=', $status);
        })
        ->get();

        return $this->sendResponse($tasks, []);                            
}
};

    