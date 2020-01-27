<?php

namespace App\Http\Controllers\Employee;

use App\Employee;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

class EmployeeController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employees = Employee::with('info')->get();
        return $this->showAll($employees);
    }

    public function show(Employee $employee)
    {
        return $this->showOne($employee);
    }

    
}
