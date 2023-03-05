let counter = 0;
let salesData = [];

function inputHTML(counter) {
    return `<tr id="inputRow${counter}">
                <td><button class="btn btn-danger remove-row-btn" data-parent="inputRow${counter}"><i class="fa-solid fa-xmark"></i></button></td>
                <td>
                    <input type="text" class="w-100 form-control" value="" placeholder="Enter a Product">
                </td>
                <td>
                    <input type="text" placeholder="Quantity" class="form-control" value="">
                </td>
                <td>
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon1">$</span>
                        <input type="text" placeholder="Price" class="form-control" value="">
                    </div>
                </td>
                <td>
                    <input type="text" placeholder="Discount" class="form-control" value="">
                </td>
                <td>
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon1">$</span>
                        <input type="text" placeholder="Sub-Total" class="form-control" value="">
                    </div>
                </td>
            </tr>`;
}

function triggerRemoveEvent() {
    $(".remove-row-btn").on("click", function (e) {
        e.preventDefault();
        const parentId = $(this).data("parent");
        if (salesData.length <= 1) {
            alert("minimum i row required");
            return false;
        }
        $(`#${parentId}`).remove();
    });
}

function appendInputRow() {
    counter++;
    $("#invoiceTableBody").append(inputHTML(counter));
    salesData.push({
        id: counter,
        product: "",
        quantity: "",
        price: "",
        discount: "",
        subtotal: "",
    });
    triggerRemoveEvent();
}

$(window).on("load", function () {
    appendInputRow();
    $("#addRowBtn").on("click", function (e) {
        e.preventDefault();
        appendInputRow();
    });
});
