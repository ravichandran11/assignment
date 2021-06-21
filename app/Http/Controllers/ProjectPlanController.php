<?php

namespace App\Http\Controllers;

use App\ProjectPlan;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\Print_;

class ProjectPlanController extends Controller
{
    /**
     * Display a listing of the tasks assigned.
     *
     */
    public function index()
    {
    	$projectPlan = (new ProjectPlan)->getProjectPlan();
    	
    	return view('projectplan.index', [
    		'projectPlan' => $projectPlan
    	]); 
    }
    
    /**
     * Display a listing of the tasks assigned.
     *
     */
    public function ballassignment()
    {
    	$ballDetails = (new ProjectPlan)->getBallDetails();
    	 
    	return view('projectplan.ballassignment', [
    			'ballDetails' => $ballDetails
    	]); 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ProjectPlan  $projectPlan
     * @return \Illuminate\Http\Response
     */
    public function show(ProjectPlan $projectPlan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ProjectPlan  $projectPlan
     * @return \Illuminate\Http\Response
     */
    public function edit(ProjectPlan $projectPlan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ProjectPlan  $projectPlan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProjectPlan $projectPlan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ProjectPlan  $projectPlan
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProjectPlan $projectPlan)
    {
        //
    }
}
