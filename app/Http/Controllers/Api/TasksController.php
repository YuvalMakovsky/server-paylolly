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
     * fetch tasks for the table
     * mendatory params - start date,end date
     * optional params - name,status
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

        //format dates
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
        ->orderBy('date', 'ASC')
        ->orderByRaw("FIELD(status , 'not started', 'in progress', 'completed') ASC")
        ->get();

        return $this->sendResponse($tasks, []);                            
}

/**
 * create/update single task
 * mendatory params - date,status,name
 * optional params - id
 * return collection
 */
public function createTask(Request $request){

    $validator = Validator::make($request->all(), [
       'name' => 'required',
       'status' => 'required',
       'date' => 'required',
       'id' => 'sometimes',
   ]);
   
   if($validator->fails()){
       return $this->sendError('Validation Error', $validator->errors(),403);       
   }

   if(!empty($request->date)){
       $date = Carbon::parse($request->date)->format("Y-m-d");
   }
   
   if(!empty($request->name)){
       $name = $request->name;
   }

   if(!empty($request->status)){
       $status = $request->status;
   }

   if(!empty($request->id)){
    $task = auth()->user()->tasks()->find($request->id);
    $success = $task->update([
        'name'=>$name,
        'status'=>$status,
        'date'=>$date
    ]);
   }else{
    $success = auth()->user()->tasks()->create([
        'name'=>$name,
        'status'=>$status,
        'date'=>$date
    ]);
   }
  
   if($success){
    return $this->sendResponse($success, []);  
   }

    return $this->sendError('Failed', ['Failed'],500);                              
}

/**
 * mendatory params - id
 * return collection
 */
public function deleteTask(Request $request){

    $validator = Validator::make($request->all(), [
        'id' => 'required',
    ]);

    if($validator->fails()){
        return $this->sendError('Validation Error', $validator->errors(),403);       
    }

    $success = auth()->user()->tasks()->find($request->id)->delete();

    if($success){
        return $this->sendResponse($success, []);  
     }

    return $this->sendError('Failed', ['Failed'],500);    
    

}

};

    