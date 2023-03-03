const fields = [
    {
        label: "Unstocked Name",
        name: "unstockedname",
        width: "half",
    },
    {
        label: "Quantity",
        name: "quantity",
        width: "half",
    },
    {
        label: "Price",
        name: "price",
        width: "half",
    },
    {
        label: "Discount",
        name: "discount",
        width: "half",
    },
    {
        label: "Sub Total",
        name: "subtotal",
        width: "full",
    },
];

function inputHTML(field) {
    return (
        '<div class="' +
        (field.width === "full" ? "col-md-12" : "col-md-6") +
        '" style="' +
        (field.width === "full"
            ? "border-bottom: 1px solid #888888; padding-bottom: 30px;"
            : "") +
        '"' +
        ">" +
        '<label for="inputPassword4" class="form-label">' +
        field.label +
        "</label>" +
        '<input type="text" class="form-control" name="' +
        field.name +
        '">' +
        "</div>"
    );
}

function appendInputFields() {
    $.each(fields, function (index, field) {
        $("#formWrapper").append(inputHTML(field));
    });
}

$(window).on("load", function () {
    appendInputFields();
    $("#addFieldBtn").on("click", function (e) {
        e.preventDefault();
        appendInputFields();
    });
});
