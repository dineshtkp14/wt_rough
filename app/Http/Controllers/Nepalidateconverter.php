<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use NepaliDate\NepaliDate;

class Nepalidateconverter extends Controller
{
  

    // Convert English date to Nepali date
    function convertToNepaliDate($englishDate)
    {
        // Convert English date to timestamp
        $timestamp = strtotime($englishDate);
    
        // Extract year, month, and day
        $year = date('Y', $timestamp);
        $month = date('m', $timestamp);
        $day = date('d', $timestamp);
    
        // Convert to Nepali date
        $nepaliDate = NepaliDate::convertEnglishToNepali($year, $month, $day);
    
        // Format the Nepali date
        $formattedDate = $nepaliDate['year'] . '-' . $nepaliDate['month'] . '-' . $nepaliDate['day'];
    
        return $formattedDate;
    }
    
    // Usage
    $englishDate = '2024-03-11';
    $nepaliDate = convertToNepaliDate($englishDate);
    echo $nepaliDate; // Output: २०८०-११-२७
}
