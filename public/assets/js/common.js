function convertNumberToWords(num) {
    var ones = [
        "",
        "One",
        "Two",
        "Three",
        "Four",
        "Five",
        "Six",
        "Seven",
        "Eight",
        "Nine",
        "Ten",
        "Eleven",
        "Twelve",
        "Thirteen",
        "Fourteen",
        "Fifteen",
        "Sixteen",
        "Seventeen",
        "Eighteen",
        "Nineteen",
    ];
    var tens = [
        "",
        "",
        "Twenty",
        "Thirty",
        "Forty",
        "Fifty",
        "Sixty",
        "Seventy",
        "Eighty",
        "Ninety",
    ];

    function convertToWordsLessThanThousand(number) {
        var words = "";
        if (number >= 100) {
            words += ones[Math.floor(number / 100)] + " Hundred";
            number %= 100;
        }
        if (number >= 20) {
            words += " " + tens[Math.floor(number / 10)];
            number %= 10;
        }
        if (number > 0) {
            words += " " + ones[number];
        }
        return words.trim();
    }

    if (num === 0) {
        return "Zero";
    }

    if (num < 0) {
        return "Minus " + convertNumberToWordsIndianFormat(Math.abs(num));
    }

    var words = "";
    var crore = Math.floor(num / 10000000);
    var lakh = Math.floor((num % 10000000) / 100000);
    var thousand = Math.floor((num % 100000) / 1000);
    var remaining = num % 1000;

    if (crore > 0) {
        words += convertToWordsLessThanThousand(crore) + " Crore";
    }
    if (lakh > 0) {
        words += " " + convertToWordsLessThanThousand(lakh) + " Lakh";
    }
    if (thousand > 0) {
        words += " " + convertToWordsLessThanThousand(thousand) + " Thousand";
    }
    if (remaining > 0) {
        words += " " + convertToWordsLessThanThousand(remaining);
    }

    return words.trim() + "  only /-";
}
