<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use DB;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $empleyees = Employee::select('employees.*','departments.name as department')
        ->join('departments','departments.id','=', 'employees.department_id')
        ->paginate(10);

        return response()->json($empleyees);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|min:1|max:100',
            'email' => 'required|string|email',
            'phone' => 'required|string',
            'department_id' => 'required|numeric'
        ];

        $validator = \Validator::make($request->input(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $employee = new Employee($request->input());

        $employee->save();

        return response()->json([
                'status' => true,
                'message'=> 'The employee created successfully',
                'data' => $employee
            ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        return response()->json([
            'data' => $employee
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $rules = [
            'name' => 'required|string|min:1|max:100',
            'email' => 'required|string|email',
            'phone' => 'required|string',
            'department_id' => 'required|numeric'
        ];

        $validator = \Validator::make($request->input(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $employee->update($request->input());

        return response()->json([
                'status' => true,
                'message'=> 'The employee updated successfully',
                'data' => $employee
            ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();

        return response()->json([
            'status' => true,
            'message'=> 'The employee deleted successfully',
        ], 204);
    }

    public function employeesByDepartment(){
        $employees = Employee::select(DB::raw('count(employees.id) as count, departments.name'))
                    ->rightJoin('departments','departments.id','=', 'employees.department_id')
                    ->groupBy('departments.name')
                    ->get();

        return response()->json($employees);
    }
}
