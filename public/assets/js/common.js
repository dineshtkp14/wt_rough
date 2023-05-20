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
    var scales = ["", "Thousand", "Lakh", "Crore", "Arab"];

    if (num === 0) {
        return "Zero";
    }

    var words = "";

    if (num < 0) {
        words += "Minus ";
        num = Math.abs(num);
    }

    var scaleIndex = 0;

    while (num > 0) {
        var chunk = num % 1000;

        if (chunk !== 0) {
            var chunkWords = "";

            if (chunk < 20) {
                chunkWords = ones[chunk];
            } else if (chunk < 100) {
                chunkWords =
                    tens[Math.floor(chunk / 10)] + " " + ones[chunk % 10];
            } else {
                chunkWords =
                    ones[Math.floor(chunk / 100)] +
                    " Hundred " +
                    tens[Math.floor((chunk % 100) / 10)] +
                    " " +
                    ones[chunk % 10];
            }

            words = chunkWords + " " + scales[scaleIndex] + " " + words;
        }

        num = Math.floor(num / 1000);
        scaleIndex++;
    }

    return words.trim();
}
