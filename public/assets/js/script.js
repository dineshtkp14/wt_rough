let counter = 0;
let salesData = [];
let finalData = [
    {
        subtotal: 0,
        discount: 0,
        total: 0,
        note: "",
    },
];

function inputHTML(counter) {
    return `<tr id="inputRow${counter}">
                <td><button class="btn btn-danger remove-row-btn" data-id="${counter}"><i class="fa-solid fa-xmark"></i></button></td>
                <td>
                    <select class="form-control sales-input" id="productInput" data-id="${counter}" data-name="product">
                        <option value="" selected disabled>Select a product</option>
                        <option value="1">Tin</option>
                        <option value="2">Steel</option>
                        <option value="3">Iron</option>
                    </select>
                </td>
                <td>
                    <input type="text" placeholder="Unstoked name" class="form-control sales-input" id="unstockedInput" value="" data-id="${counter}" data-name="unstocked">
                </td>
                <td>
                    <input type="text" placeholder="Quantity" class="form-control sales-input" id="quantityInput" value="" data-id="${counter}" data-name="quantity">
                </td>

                <td>
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon1">Rs.</span>
                        <input type="text" placeholder="Price" class="form-control sales-input" id="priceInput" value="" data-id="${counter}" data-name="price">
                    </div>
                </td>
                <td>
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon1">%</span>
                        <input type="text" placeholder="Discount" class="form-control sales-input" id="discountInput" value="" data-id="${counter}" data-name="discount">
                    </div>
                </td>
                <td>
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon1">Rs.</span>
                        <input type="text" placeholder="0.00" class="form-control" id="subTotalInput" value="" data-id="${counter}" data-name="subtotal" disabled>
                    </div>
                </td>
            </tr>`;
}

function appendInputRow() {
    if (salesData.length >= 10) {
        return false;
    }

    counter++;
    $("#invoiceTableBody").append(inputHTML(counter));
    salesData.push({
        id: counter,
        product: "",
        unstocked: "",
        quantity: "",
        price: "",
        discount: "",
        subtotal: "",
    });
    triggerRemoveEvent();
    handleInputChange();
    getFinalCalculations();
}

function removeObj(id) {
    let index = salesData.findIndex((obj) => obj.id === id);
    if (index !== -1) {
        salesData.splice(index, 1);
    }
}

function triggerRemoveEvent() {
    $(".remove-row-btn")
        .off("click")
        .on("click", function (e) {
            e.preventDefault();
            const dataId = $(this).data("id");
            const parentId = `inputRow${dataId}`;

            if (salesData.length <= 1) {
                return false;
            }

            $(`#${parentId}`).remove();
            removeObj(dataId);
            getFinalCalculations();
        });
}

function getRowCalculations(quantity, price, discount) {
    quantity = quantity.trim() === "" ? 0 : quantity;
    price = price.trim() === "" ? 0 : price;
    discount = discount.trim() === "" ? 0 : discount;

    let totalValue = parseFloat(quantity) * parseFloat(price);
    let discountAmount = (discount / 100) * totalValue;
    let finalDiscount = totalValue - discountAmount;
    return finalDiscount.toFixed(2);
}

function getFinalCalculations() {
    let finalSubTotal = 0;
    $.each(salesData, function (index, value) {
        let subTotalValue = value.subtotal.trim() === "" ? 0 : value.subtotal;
        finalSubTotal += parseFloat(subTotalValue);
    });
    finalData[0]["subtotal"] = finalSubTotal.toFixed(2);
    $("#subTotalInputFinal").val(finalData[0]["subtotal"]);

    let discountAmount =
        (finalData[0]["discount"] / 100) * finalData[0]["subtotal"];
    let totalAmount = finalData[0]["subtotal"] - discountAmount;
    finalData[0]["total"] = totalAmount.toFixed(2);
    $("#totalInputFinal").val(finalData[0]["total"]);

    if (finalData[0]["total"] > 0) {
        $("#totalAmountWords").text(
            numberToWords.toWords(finalData[0]["total"])
        );
    }
}

function addInputValue(index, inputId, dataId, dataName, value) {
    salesData[index][dataName] = value;

    // validation
    if (
        dataName === "quantity" ||
        dataName === "price" ||
        dataName === "discount"
    ) {
        $("#totalAmountWords").text("Calculating...");
        // trim
        salesData[index][dataName] = value.trim();
        $(`#inputRow${dataId} #${inputId}`).val(value.trim());

        const newValue = salesData[index][dataName].replace(/[^0-9\.]/g, "");
        if (newValue !== salesData[index][dataName]) {
            $(`#inputRow${dataId} #${inputId}`).val(newValue);
            salesData[index][dataName] = newValue;
        }

        if (dataName === "discount") {
            if (newValue > 100) {
                $(`#inputRow${dataId} #${inputId}`).val("100");
                salesData[index][dataName] = "100";
            }
        }
    }

    // calculation
    salesData[index]["subtotal"] = getRowCalculations(
        salesData[index]["quantity"],
        salesData[index]["price"],
        salesData[index]["discount"]
    );
    $(`#inputRow${dataId} #subTotalInput`).val(salesData[index]["subtotal"]);

    getFinalCalculations();
}

function handleInputChange() {
    $(".sales-input").on("input", function (e) {
        const target = e.target;
        const inputId = $(target).attr("id");
        const dataId = $(target).data("id");
        const dataName = $(target).data("name");

        const index = salesData.findIndex((obj) => obj.id === dataId);
        addInputValue(index, inputId, dataId, dataName, target.value.trim());
    });
}

// final total input change
$("#discountInputFinal").on("input", function () {
    const value = $(this).val().trim();
    finalData[0]["discount"] = value;
    // trim
    finalData[0]["discount"] = value.trim();
    $(this).val(value.trim());

    const newValue = finalData[0]["discount"].replace(/[^0-9\.]/g, "");
    if (newValue !== finalData[0]["discount"]) {
        $(this).val(newValue);
        finalData[0]["discount"] = newValue;
    }

    if (newValue > 100) {
        $(this).val("100");
        finalData[0]["discount"] = "100";
    }

    getFinalCalculations();
});

$(window).on("load", function () {
    appendInputRow();
    $("#addRowBtn").on("click", function (e) {
        e.preventDefault();
        appendInputRow();
    });

    $("#verifyBtn").on("click", function (e) {
        e.preventDefault();
        $.each(salesData, function (index, value) {
            if (value.product.trim() === "") {
                $("#errorText").attr("class", "text-danger fw-bold");
                $("#errorText").text("Please select product !");
                return false;
            }

            if (
                value.quantity.trim() === "" ||
                parseFloat(value.quantity) <= 0
            ) {
                $("#errorText").attr("class", "text-danger fw-bold");
                $("#errorText").text("Please enter valid quantity !");
                return false;
            }

            if (value.price.trim() === "") {
                $("#errorText").attr("class", "text-danger fw-bold");
                $("#errorText").text("Please enter valid price !");
                return false;
            }

            finalData[0]["note"] = $("#noteInput").val().trim();
            $("#salesArrInput").val(JSON.stringify(salesData));
            $("#finalArrInput").val(JSON.stringify(finalData));

            $("#errorText").attr("class", "text-success fw-bold");
            $("#errorText").text("Success, Now you can submit !");
            $("#submitBtn").removeAttr("disabled");
        });
    });
});
