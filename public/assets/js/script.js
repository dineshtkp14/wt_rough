function getData(url, successCb) {
    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: successCb,
    });
}

const SEARCH_API_URL = window.location.origin + "/api/customer_search/";
let searchQuery = "";

let counter = 0;
let salesData = [];
let finalData = [
    {
        customer: "",
        subtotal: 0,
        discount: 0,
        total: 0,
        note: "",
    },
];

function resultHTML(value) {
    return `
     <div class="result-box d-flex justify-content-start align-items-center customer-result-box" data-value='${JSON.stringify(
         value
     )}'> 
            <i class="fas fa-user"> </i>
            <h1 class="m-0 px-2">${value.name}</h1>
     </div>`;
}

$("#searchCustomerInput").on("keyup", function (e) {
    const target = e.target;
    searchQuery = target.value.trim();

    $("#customerCard").hide();
    finalData[0]["customer"] = "";

    if (searchQuery.trim() === "") {
        $("#searchResult").slideUp();
    } else {
        $("#searchResult").slideDown();
        $("#loadingResultBox").removeClass("d-none");
        $("#notFoundResultBox").addClass("d-none");
        $(".customer-result-box").addClass("d-none");

        getData(SEARCH_API_URL + searchQuery, function (response) {
            if (response) {
                if (response.length > 0) {
                    $("#customerResult").empty();
                    $("#loadingResultBox").addClass("d-none");
                    $("#notFoundResultBox").addClass("d-none");
                    $.each(response, function (index, value) {
                        if (index < 20) {
                            $("#customerResult").append(resultHTML(value));
                            triggerResultClick();
                        }
                    });
                } else {
                    $("#customerResult").empty();
                    $("#loadingResultBox").addClass("d-none");
                    $("#notFoundResultBox").removeClass("d-none");
                }
            }
        });
    }
});

function triggerResultClick() {
    $(".customer-result-box")
        .off()
        .on("click", function () {
            const json = $(this).attr("data-value");
            const data = JSON.parse(json);

            $("#customerName").text(data.name);
            $("#customerId").text(data.id);
            $("#customerAddress").text(data.address);
            $("#customerEmail").text(data.email);
            $("#customerPhone").text(data.phoneno);
            finalData[0]["customer"] = `${data.id}`;

            $("#customerCard").show();

            $("#customerCard").animate({
                right: "0",
            });
            $("#toggleBox").removeClass("animate");
            $("#toggleBox").data("toggle", "open");

            $("#searchCustomerInput").val(data.name);
            $("#searchResult").slideUp();
        });

    $("#toggleBox")
        .off()
        .on("click", function () {
            const toggleStatus = $(this).data("toggle");
            if (toggleStatus === "close") {
                $("#customerCard").animate({
                    right: "0",
                });
                $(this).removeClass("animate");
                $(this).data("toggle", "open");
            } else {
                $("#customerCard").animate({
                    right: "-300px",
                });
                $(this).addClass("animate");
                $(this).data("toggle", "close");
            }
        });
}

function inputHTML(counter) {
    return `<tr id="inputRow${counter}">
                <td><button class="btn btn-danger remove-row-btn" data-id="${counter}"><i class="fa-solid fa-xmark"></i></button></td>
                <td>
                    <select class="form-control sales-input product-input" id="productInput" data-id="${counter}" data-name="product">
                        <option value="" selected disabled>Select a product</option>
                      
                        ${ITEMS_DATA.map(function (data) {
                            return `<option value="${data.id}" data-price="${data.mrp}" >${data.itemsname}</option>`;
                        })}   
                    </select>
                </td>
                <td>
                    <input type="text" placeholder="Unstocked Name" class="form-control sales-input" id="unstockedInput" value="" data-id="${counter}" data-name="unstocked">
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
                        <span class="input-group-text" id="basic-addon1">Rs.</span>
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
    $(".product-input").select2();
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
    let discountAmount = parseFloat(discount);
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

    let discountAmount = finalData[0]["discount"];
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

    if (dataName === "product") {
        const priceValue = $(
            "option:selected",
            $(`#inputRow${dataId} #productInput`)
        ).attr("data-price");
        $(`#inputRow${dataId} #priceInput`).val(priceValue);
        salesData[index]["price"] = priceValue;
    }

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
            console.log(salesData[index]["subtotal"]);
            if (
                newValue >
                parseFloat(
                    salesData[index]["quantity"] * salesData[index]["price"]
                )
            ) {
                $(`#inputRow${dataId} #${inputId}`).val("0");
                salesData[index][dataName] = "0";
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
$(".sales-input-final").on("input", function () {
    const value = $(this).val().trim();
    const dataName = $(this).data("name");
    finalData[0][dataName] = value;

    if (dataName === "discount") {
        // trim
        finalData[0]["discount"] = value.trim();
        $(this).val(value.trim());

        const newValue = finalData[0]["discount"].replace(/[^0-9\.]/g, "");
        if (newValue !== finalData[0]["discount"]) {
            $(this).val(newValue);
            finalData[0]["discount"] = newValue;
        }

        if (newValue > parseFloat(finalData[0]["subtotal"])) {
            $(this).val("");
            finalData[0]["discount"] = "0";
        }
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

        if ($("#salesDate").val().trim() === "") {
            $("#errorText").attr("class", "text-danger fw-bold");
            $("#errorText").text("Please select Date !");
            return false;
        }

        if (finalData[0]["customer"].trim() === "") {
            $("#errorText").attr("class", "text-danger fw-bold");
            $("#errorText").text("Please select customer !");
            return false;
        }

        $.each(salesData, function (index, value) {
            if (value.product.trim() === "" && value.unstocked.trim() === "") {
                $("#errorText").attr("class", "text-danger fw-bold");
                $("#errorText").text("Please enter or select product !");
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
