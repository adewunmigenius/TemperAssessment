<?php


namespace App\Services;

use App\Repositories\UserInsightRepository;
use Carbon\Carbon;
use App\Interfaces\Services\UserInsightInterface;

class UserInsightService implements UserInsightInterface
{
    private $userInsightRepository;

    public function __construct(UserInsightRepository $userInsightRepository)
    {
        $this->userInsightRepository = $userInsightRepository;
    }

    public function load_user_data(){
        $result = $this->userInsightRepository->loadData();

        // get first date and last date from the data
        $first_date = $result[0]['created_at'];
        $last_date = $result[count($result)-1]['created_at'];

        // get the weeks between the first date and last date
        $diffByday = Carbon::parse($first_date)->floatDiffInDays($last_date);
        $weeksBtw = intVal($diffByday/7);

        $all_date = [$first_date];
        for($i=1;$i <= $weeksBtw; $i++){
            $all_date[] = Carbon::parse($first_date)->addWeek($i)->toDateString();
        }
        
        // using the weekly dates as key and onboarding_perentage as values
        $date_week_onboarding = [];
        for($i=0; $i < count($all_date); $i++){
            $date_week_onboarding[$all_date[$i]] = [];
        }

        foreach ($result as $item){
            for($i=0; $i < count($all_date); $i++){
                if($i !== count($all_date)-1 && $item['created_at'] >= $all_date[$i] && $item['created_at'] < $all_date[$i+1]){
                    array_push($date_week_onboarding[$all_date[$i]], $item['onboarding_perentage']);
                }elseif ($i === count($all_date)-1 && $item['created_at'] >= $all_date[$i]){
                    array_push($date_week_onboarding[$all_date[$i]], $item['onboarding_perentage']);
                }
            }
        }

        $groupUser = $this->groupUsersWeekly($all_date, $date_week_onboarding);
        $outputHighChartData = $this->highChartDataFormat($groupUser);
        return $outputHighChartData;
    }

    // converting weekly data to onboarding_perentage[keys] and number of occurence (percentage of total user per week) [values]
    public function groupUsersWeekly($all_date, $date_week_onboarding){
        for($i=0; $i < count($all_date); $i++){
            $user = $date_week_onboarding[$all_date[$i]];
            $totalUserPerWeek = count($user);
            $data = [];
            foreach($user as $item){
                if(array_key_exists($item,$data)){
                    $data[$item] += (1/$totalUserPerWeek)*100;
                }else{
                    $data[$item] = (1/$totalUserPerWeek)*100;
                }
            }
            $date_week_onboarding[$all_date[$i]] = $this->splitandRoundDataTwoDecimal($data);
        }
        return $date_week_onboarding;
    }

    // round to two decimal places and return the required expected data for highcharts [xaxis = onboarding percentage, yAxis = percentage of user per week]
    public function splitandRoundDataTwoDecimal($data){
        $result = [];
        ksort($data);
        foreach ($data as $key => $item){
           array_push($result, [$key ? $key : 0, round($item,2)]);
        }

        return $result;
    }

    public function highChartDataFormat($data){
        $allKeys = array_keys($data);
        $allValues = array_values($data);

        $result = [];
        for($i=0; $i < count($allKeys); $i++){
            // add 100% of users starts at onboarding value of 0%
            array_unshift($allValues[$i],[0,100]);

            $output = [
                'name' => $allKeys[$i],
                'data' => $allValues[$i],
                'color' => $this->generateRandomColor()
            ];
            array_push($result, $output);
        }
        return $result;
    }

    //generate colors
    public function generateRandomColor(){
        $color = "";
        $possible = "ABCDEFabcdef0123456789";
        for ($i = 0; $i <= 5; $i++) {
            $color .= $possible[rand(0, strlen($possible)-1)];
        }

        return "#".$color;
    }
}