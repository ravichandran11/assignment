@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12 pt-2">
                 <div class="row">
                    <div class="col-12">
                        <h1 class="display-one">Project Plan!</h1>
                    </div>
                </div> 
            </div>
        </div>
        <div class="row">
			<div class="col-12">				
				@if ($projectPlan['status'] === true)
					@foreach ($projectPlan['taskAssigned'] as $developer => $developerTask)
						<div class="row">
							<div class="col-12">
								Developer {{$developer}}
							</div>
						</div>	
						@foreach ($developerTask as $task)
							<div class="row">
								<div class="col-12">
									{{$task}}
								</div>
							</div>		
					   	@endforeach
					   	<div class="row">
							<div class="col-12">
								&nbsp;
							</div>
						</div>
				   	@endforeach
				@else
				    {{$projectPlan['msg']}}
				@endif
            </div>
		</div>
    </div>
@endsection