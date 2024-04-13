<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DateConversionController extends Controller
{
    public function convertdate(Request $request)
    {
        // Get the English date from the request
        $englishDate = $request->input('english_date');

        // Create a DateTime object for the English date
        $dateTime = new \DateTime($englishDate);

        // Create an IntlDateFormatter instance for Nepali calendar
        $formatter = new \IntlDateFormatter(
            'ne_NP',
            \IntlDateFormatter::LONG,
            \IntlDateFormatter::NONE,
            'Asia/Kathmandu',
            \IntlDateFormatter::TRADITIONAL,
            'yyyy-MM-dd'
        );

        // Format the English date using the Nepali calendar
        $nepaliDate = $formatter->format($dateTime);

        // Return the view with the Nepali date
        return view('CHECKdata', ['nepali_date' => $nepaliDate]);
    }
}
