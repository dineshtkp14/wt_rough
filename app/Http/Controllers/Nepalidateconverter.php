<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\YourModel;

class YourController extends Controller
{
    public function showNepaliDate()
    {
        $englishDate = '2024-03-11';
        $nepaliDate = $this->convertToNepaliDate($englishDate);
        
        return view('nepali_date', ['nepaliDate' => $nepaliDate]);
    }

    private function convertToNepaliDate($englishDate)
    {
        $timestamp = strtotime($englishDate);
        $year = date('Y', $timestamp);
        $month = date('m', $timestamp);
        $day = date('d', $timestamp);

        $nepaliDate = NepaliDate::convertEnglishToNepali($year, $month, $day);

        $formattedDate = $nepaliDate['year'] . '-' . $nepaliDate['month'] . '-' . $nepaliDate['day'];

        return $formattedDate;
    }
}
