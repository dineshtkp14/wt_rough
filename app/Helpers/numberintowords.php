<?php

// app/Helpers/numberintowords.php//notworking

if (!function_exists('convertNumberToWords')) {
    function convertNumberToWords($num) {
        $ones = array(
            "", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine", "Ten",
            "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eighteen", "Nineteen"
        );
        $tens = array(
            "", "", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", "Seventy", "Eighty", "Ninety"
        );

        if ($num == 0) {
            return "Zero";
        }

        $words = "";

        if ($num >= 10000000) {
            $words .= convertNumberToWords(floor($num / 10000000)) . " Crore ";
            $num %= 10000000;
        }

        if ($num >= 100000) {
            $words .= convertNumberToWords(floor($num / 100000)) . " Lakh ";
            $num %= 100000;
        }

        if ($num >= 1000) {
            $words .= convertNumberToWords(floor($num / 1000)) . " Thousand ";
            $num %= 1000;
        }

        if ($num >= 100) {
            $words .= convertNumberToWords(floor($num / 100)) . " Hundred ";
            $num %= 100;
        }

        if ($num >= 20) {
            $words .= $tens[floor($num / 10)] . " ";
            $num %= 10;
        }

        if ($num > 0) {
            $words .= $ones[$num] . " ";
        }

        return $words;
    }
}
