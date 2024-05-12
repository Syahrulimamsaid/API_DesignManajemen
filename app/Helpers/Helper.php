<?php

namespace App\Helpers;

class Helper
{
    // public static function IDGenerator($model, $prefix)
    // {
    //     $today = today()->format('ymd');
    //     $data = $model::orderBy('kode', 'desc')->first();

    //     if (!$data) {
    //         $id = $prefix . '-' . $today . '0001'; 
    //     } else {
    //         $date = substr($data->kode, 4, 8); 
    //         if ($date != $today) {
    //             $id = $prefix . '-' . $today . '0001';
    //         } else {
    //             $last_number = (int)substr($data->id, -4);
    //             $last_number++;

    //             $number = sprintf('%04d', $last_number); 
    //             $id = $prefix . '-' . $date . $number;
    //         }
    //     }

    //     return $id;
    // }
    public static function IDGenerator($model, $prefix)
    {
        $today = today()->format('ym');
        $data = $model::orderBy('kode', 'desc')->first();
    
        if (!$data) {
            $id = $prefix . '-' . $today . '0001'; 
        } else {
            $last_date = substr($data->kode, 4, 4); 
            if ($last_date == $today) {
                $last_number = (int)substr($data->kode, -2);
                $last_number++;
                $number = sprintf('%04d', $last_number); 
                $id = $prefix . '-' . $today . $number;
            } else {
                $id = $prefix . '-' . $today . '0001';
            }
        }
    
        return $id;
    }
    
}
