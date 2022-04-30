<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Tasks;
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

        $tasks = [];

        if(!empty($request->startDate)){
            $startDate = Carbon::createFromFormat('m/d/Y', $request->startDate)->format('Y-m-d');
        }

        if(!empty($request->endDate)){
            $endDate = Carbon::createFromFormat('m/d/Y', $request->endDate)->format('Y-m-d');
        }
        
        if(!empty($request->name)){
            $name = $request->name;
        }

        if(!empty($request->status)){
            $status = $request->status;
        }
 
        $totals = auth()->user()->tasks;

        return $this->sendResponse($totals, []);                            
}
};

    