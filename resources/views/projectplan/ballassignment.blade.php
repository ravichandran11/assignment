@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12 pt-2">
                 <div class="row">
                    <div class="col-12">
                        <h1 class="display-one">Ball Assignment!</h1>
                    </div>
                </div> 
            </div>
        </div>
        <div class="row">
			<div class="col-12">
				@if ($ballDetails['status'] === true)
					@foreach ($ballDetails['ballDetails'] as $key => $ball)
						<div class="row">
							<div class="col-12">
								{{$ball}}
							</div>
						</div>		
					@endforeach
				@else
				    Issue in input!
				@endif
            </div>
		</div>
    </div>
@endsection