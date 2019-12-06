<?php


namespace App\Repositories;


class UserInsightRepository
{
    private static $filename = 'export.csv';

    public function loadData(){
        $file = public_path().'/userData/'.self::$filename;
        $data_array = [];

        if (($h = fopen($file, "r")) !== FALSE)
        {
            // Each line in the file is converted into an individual array that we call $data
            while (($data = fgetcsv($h, 1000, ";")) !== FALSE)
            {
                $data_array[] = $data;
            }
            // Close the file
            fclose($h);
        }

        // get column header
        $header = array_shift($data_array);

        // convert array data to associative array using header as key
        $response_data = [];
        foreach($data_array as $item){
            $data = [];
            for($i =0; $i < count($header); $i++){
                $data[$header[$i]] = $item[$i];
            }
            $response_data[] = $data;
        }

        return $response_data;
    }

}