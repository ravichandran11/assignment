<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Expr\Print_;

class ProjectPlan extends Model
{
	private $sprintDays, $developer1DaysAssigned, $developer2DaysAssigned, $sprint;
	private $lastTaskPriority, $currentTaskPriority, $taskAssigned;
	
	public function getProjectPlan(){
		
		$this->sprintDays = 10;		
		$this->developer1DaysAssigned = 0;
		$this->developer2DaysAssigned = 0;
		$this->sprint = 1;
		
		$fileTask = storage_path('app/task.csv');
		$fileHandle = fopen($fileTask, "r");
		$taskDetails = array();
		while ( ($dataTask = fgetcsv($fileHandle, 200, ",")) !==FALSE) {
			$taskDetails[] = array( $dataTask[0], $dataTask[1], $dataTask[2]);
		}
		fclose($fileHandle);
		//echo '<pre>'; print_r($taskDetails); die;
		
		$taskDetailsPriority = array();
		foreach($taskDetails as $taskDetail) {
			if($taskDetail[1] > $this->sprintDays) {
				return array('status' => false, 'msg' => 'Task Effort should be less than or equal to sprint days('.$this->sprintDays.').');
			}
			$taskDetailsPriority[$taskDetail[2]][] = array($taskDetail[0],$taskDetail[1], 0);
		}
		ksort($taskDetailsPriority);
		
		$this->taskAssigned = array();
		$this->lastTaskPriority = 0;
		
		foreach($taskDetailsPriority as $taskPriority => $taskDetail) {
			$this->currentTaskPriority = $taskPriority;
			if($this->lastTaskPriority == 0) {
  				$this->lastTaskPriority = $this->currentTaskPriority;
  			}
  			$this->assignTasks($taskDetail);
		}
		
		$taskAssignedFinal = $this->formatTaskAssigned();
		
		return array('status' => true, 'msg' => 'data calculated', 'taskAssigned' => $taskAssignedFinal);
  	}
  	
  	private function assignTasks($taskDetail) {
  		
  		if($this->lastTaskPriority != $this->currentTaskPriority) {
  			$this->lastTaskPriority = $this->currentTaskPriority;

			if($this->developer1DaysAssigned < $this->developer2DaysAssigned) {
  				for($i=$this->developer1DaysAssigned;$i<$this->developer2DaysAssigned;$i++) {
  					$this->taskAssigned[1][$this->sprint][$i+1] = 'Worked on Miscellaneous tasks';
  				}
  				$this->developer1DaysAssigned = $this->developer2DaysAssigned;
  			}
  			else if($this->developer1DaysAssigned > $this->developer2DaysAssigned) {
  				for($i=$this->developer2DaysAssigned;$i<$this->developer1DaysAssigned;$i++) {
  					$this->taskAssigned[2][$this->sprint][$i+1] = 'Worked on Miscellaneous tasks';
  				}
  				$this->developer2DaysAssigned = $this->developer1DaysAssigned;
  			}
  		}
  		
  		foreach($taskDetail as $taskRow) {
  			
  			$developer1Remaining = $this->sprintDays-$this->developer1DaysAssigned;
  			$developer2Remaining = $this->sprintDays-$this->developer2DaysAssigned;
  				
  			if($taskRow[1] > $developer1Remaining && $taskRow[1] > $developer2Remaining) {
  							
  				for($i=$this->developer1DaysAssigned;$i<$this->sprintDays;$i++) {
  					$this->taskAssigned[1][$this->sprint][$i+1] = 'Worked on Miscellaneous tasks';
  				}
  				
  				for($i=$this->developer2DaysAssigned;$i<$this->sprintDays;$i++) {
  					$this->taskAssigned[2][$this->sprint][$i+1] = 'Worked on Miscellaneous tasks';
  				}
  				
  				$this->developer1DaysAssigned = 0;
  				$this->developer2DaysAssigned = 0;
  				
  				$developer1Remaining = 10;
  				$developer2Remaining = 10; 		
  				$this->sprint++;
  				
  			}

   			if($taskRow[1] <= $developer1Remaining && $developer1Remaining > $developer2Remaining) {
  				for($i=1;$i<=$taskRow[1];$i++) {
  					$this->developer1DaysAssigned++;
  					$this->taskAssigned[1][$this->sprint][$this->developer1DaysAssigned] = $taskRow[0];
  				}
  			}
  			else if($taskRow[1] <= $developer2Remaining) {
  				for($i=1;$i<=$taskRow[1];$i++) {
  					$this->developer2DaysAssigned++;
  					$this->taskAssigned[2][$this->sprint][$this->developer2DaysAssigned] = $taskRow[0];
  				}
  			} 			
  		}
  		
  		if($developer1Remaining > 0) {
  			for($i=$this->developer1DaysAssigned;$i<$this->sprintDays;$i++) {
  				$this->taskAssigned[1][$this->sprint][$i+1] = 'Worked on Miscellaneous tasks';
  			}	
  		}
  		
  		if($developer2Remaining > 0) {
  			for($i=$this->developer2DaysAssigned;$i<$this->sprintDays;$i++) {
  				$this->taskAssigned[2][$this->sprint][$i+1] = 'Worked on Miscellaneous tasks';
  			}
  		}
  		  
  	}  	
  	
  	private function formatTaskAssigned() {
  		$taskAssignedFinal = array();
  		//echo '<pre>'; print_r($this->taskAssigned); die;
  		foreach($this->taskAssigned as $developer => $taskAssigned) {
  			foreach($taskAssigned as $sprint => $tasksSprint) {
	  			$taskName = '';
	  			$taskStartDays = 0;
	  			$taskEndDays = 0;
	  			$taskStatus = 0;
	  			$taskCount = 0;
	  			foreach($tasksSprint as $day => $task) {
	  				$taskCount++;
	  				if($taskName == '') {
	  					$taskName = $task;
	  					$taskStartDays = $day;
	  				}
		  			if($taskName == $task) {
		  				$taskEndDays++;
		  				if($taskCount == count($tasksSprint)) {
		  					$taskStatus = 1;
		  				}
		  			}
		  			else {
		  				if($taskStartDays != $taskEndDays) {
		  					$taskAssignedFinal[$developer][] = 'Sprint '.$sprint . ' - Days  ' . $taskStartDays . ' - ' . $taskEndDays . ' ' .$taskName;
		  				}
		  				else {
		  					$taskAssignedFinal[$developer][] = 'Sprint '.$sprint . ' - Days - ' . $taskEndDays . ' ' .$taskName;
		  				}
		  				$taskName = $task;
		  				$taskEndDays = $day;
	  					$taskStartDays = $day;
	  					if($taskCount == count($tasksSprint)) {
	  						$taskStatus = 1;
	  					}
		  			}
	  			}
	  			if($taskStatus == 1) {
	  				if($taskStartDays != $taskEndDays) {
	  					$taskAssignedFinal[$developer][] = 'Sprint '.$sprint . ' - Days  ' . $taskStartDays . ' - ' . $taskEndDays . ' ' .$taskName;
	  				}
	  				else {
	  					$taskAssignedFinal[$developer][] = 'Sprint '.$sprint . ' - Days - ' . $taskEndDays . ' ' .$taskName;
	  				}
	  				$taskStatus = 0;
	  			}
	  			//echo '<pre>'; Print_r($task);
	  		}  	
  		}
  		//die;
  		ksort($taskAssignedFinal);
  		return $taskAssignedFinal;	
  	}
  	
  	public function getBallDetails() {
  		//$balls = 'red,red,green,yellow,red,green,yellow,yellow,yellow,yellow';//file_get_contents("balls.txt");
  		$balls = Storage::get('balls.txt');
  		$ballDetails = array();
  		$ballDetails[] = 'Balls: '.$balls;
  		
  		$redBallsCount = preg_match_all('/red/', $balls);
  		$greenBallsCount = preg_match_all('/green/', $balls);
  		$yellowBallsCount = preg_match_all('/yellow/', $balls);
  		
  		$totalBalls = $redBallsCount + $greenBallsCount + $yellowBallsCount;
  		
  		$ballDetails[] = 'Number of red balls: '.$redBallsCount;
  		$ballDetails[] = 'Number of green balls: '.$greenBallsCount;
  		$ballDetails[] = 'Number of yellow balls: '.$yellowBallsCount;
  		
  		$ballDetails[] = 'Average of red balls: '.($redBallsCount/$totalBalls);
  		$ballDetails[] = 'Average of green balls: '.($greenBallsCount/$totalBalls);
  		$ballDetails[] = 'Average of yellow balls: '.($yellowBallsCount/$totalBalls);
  		
  		$redWeight = 1;
  		$yellowWeight = 0.5;
  		$greenWeight = 0.25;
  		
  		$productWeight = ($redBallsCount*$redWeight) + ($greenBallsCount*$greenWeight) + ($yellowBallsCount*$yellowWeight);
  		$sumWeight = $redWeight + $greenWeight + $yellowWeight;
  		
  		$weightedAverage = $productWeight / $sumWeight;
  		
  		$ballDetails[] = 'Weighted average of red balls: '.round((($weightedAverage*$redWeight)/$productWeight),2);
  		$ballDetails[] = 'Weighted average of green balls: '.round((($weightedAverage*$greenWeight)/$productWeight),2);
  		$ballDetails[] = 'Weighted average of yellow balls: '.round((($weightedAverage*$yellowWeight)/$productWeight),2);
  		//$ba[] = $ballDetails;
  		return array('status' => true, 'msg' => 'data calculated', 'ballDetails' => $ballDetails);
  		 
  	}
  	
}
