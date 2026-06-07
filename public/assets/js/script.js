function getData(url, successCb) {
    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: successCb,
    });
}

const BASE_URL = window.location.origin + "/api/";
const CUSTOMER_SEARCH_API_URL =
    window.location.origin + "/api/customer_search/";

const PRODUCT_SEARCH_API_URL = window.location.origin + "/api/items_search/";
const OLD_PRICE_SEARCH_URL = window.location.origin + "/itemsales/old-price-search";

let customerSearchQuery = "";
let productSearchQuery = "";
let customerCardAutoHideTimer = null;

let currentLink = null;
let currentIndex = null;
let currentID = null;

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

function scheduleCustomerCardAutoHide() {
    clearTimeout(customerCardAutoHideTimer);
    customerCardAutoHideTimer = setTimeout(function () {
        $("#customerCard").fadeOut(300);
        $("#toggleBox").addClass("animate");
        $("#toggleBox").data("toggle", "close");
    }, 7000);
}

const pathname = window.location.pathname;
const exactPathname = pathname.split("/")[1];
const quantityCase = exactPathname === "creditnotes" ? "all" : "limited";

function escapeHTML(value) {
    return `${value ?? ""}`
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function customerResultHTML(value) {
    const phone = [value.phoneno, value.alternate_phoneno]
        .filter((number) => number !== null && number !== undefined && `${number}`.trim() !== "")
        .join(", ");
    const totalDue = value.total_due_formatted || "0.00";

    return `
     <div class="result-box d-flex justify-content-start align-items-start customer-result-box customer-suggestion-box" data-value="${encodeURIComponent(JSON.stringify(value))}"> 
            <i class="fas fa-user"> </i>
            <div class="customer-suggestion-content px-2">
                <h1 class="m-0">${escapeHTML(value.name)}</h1>
                <div class="customer-suggestion-address">${escapeHTML(value.address || "No address")}</div>
                <div class="customer-suggestion-meta">
                    <span><i class="fas fa-phone"></i> ${escapeHTML(phone || "No contact no")}</span>
                    <span class="customer-suggestion-due">Due: Rs ${escapeHTML(totalDue)}</span>
                </div>
            </div>
     </div>`;
}

function productResultHTML(value) {
    return `
     <div class="result-box d-flex justify-content-start align-items-center product-result-box" data-value='${JSON.stringify(
         value
     )}'> 

     <span style="color: blue;font-size:22px; font-weight:bold;">${
         value.id
     }</span> <i class="fa-solid fa-boxes-stacked"> </i>

     <h1 class="m-0 px-2">${value.itemsname}   &nbsp; &nbsp; (
        <span style="color: red;font-size:24px;">${value.quantity}</span>
        )</h1> 
     </div>`;
}

$("#searchCustomerInput").on("keyup", function (e) {
    const target = e.target;
    customerSearchQuery = target.value.trim();

    $("#customerCard").hide();
    finalData[0]["customer"] = "";
    $("#customerIdInput").val("");
    $("#selectedCustomerInline").slideUp(100);
    $("#selectedCustomerAddress").text("-");
    $("#selectedCustomerPhone").text("-");
    const apiKey = $(this).data("api");

    if (customerSearchQuery.trim() === "") {
        $("#customerResultWrapper").slideUp();
    } else {
        $("#customerResultWrapper").slideDown();
        $("#customerLoadingResultBox").removeClass("d-none");
        $("#customerNotFoundResultBox").addClass("d-none");
        $(".customer-result-box").addClass("d-none");

        getData(
            BASE_URL + apiKey + "/" + customerSearchQuery,

            function (response) {
                if (response) {
                    if (response.length > 0) {
                        $("#customerResultList").empty();
                        $("#customerLoadingResultBox").addClass("d-none");
                        $("#customerNotFoundResultBox").addClass("d-none");
                        $.each(response, function (index, value) {
                            if (index < 20) {
                                $("#customerResultList").append(
                                    customerResultHTML(value)
                                );
                                triggerCustomerResultClick();
                            }
                        });
                    } else {
                        $("#customerResultList").empty();
                        $("#customerLoadingResultBox").addClass("d-none");
                        $("#customerNotFoundResultBox").removeClass("d-none");
                    }
                }
            }
        );
    }
});

function triggerCustomerResultClick() {
    $(".customer-result-box")
        .off()
        .on("click", function () {
            const json = $(this).attr("data-value");
            const data = JSON.parse(decodeURIComponent(json));

            $("#customerName").text(data.name);
            $("#customerId").text(data.id);

            $("#customerAddress").text(data.address);
            $("#customerEmail").text(data.email);
            // $("#customerPhone").text(data.phoneno);
            var phoneText = data.phoneno;

            if (
                data.alternate_phoneno !== null &&
                data.alternate_phoneno !== undefined
            ) {
                phoneText += ", " + data.alternate_phoneno;
            }

            $("#customerPhone").text(phoneText);
            $("#selectedCustomerAddress").text(data.address || "-");
            $("#selectedCustomerPhone").text(phoneText || "-");
            $("#selectedCustomerInline").slideDown(150);
            finalData[0]["customer"] = `${data.id}`;

            $("#customerCard").show();

            $("#customerCard").animate({
                right: "0",
            });

            $("#toggleBox").removeClass("animate");
            $("#toggleBox").data("toggle", "open");
            scheduleCustomerCardAutoHide();

            $("#searchCustomerInput").val(data.name);
            $("#customerIdInput").val(data.id);
            $("#customerResultWrapper").slideUp();
            $(document).trigger("customer:selected", [data]);
        });
}

$("#toggleBox")
    .off()
    .on("click", function () {
        clearTimeout(customerCardAutoHideTimer);
        const toggleStatus = $(this).data("toggle");
        if (toggleStatus === "close") {
            $("#customerCard").animate({
                right: "0",
            });
            $(this).removeClass("animate");
            $(this).data("toggle", "open");
        } else {
            $("#customerCard").animate({
                right: "-350px",
            });
            $(this).addClass("animate");
            $(this).data("toggle", "close");
        }
    });

function inputHTML(counter) {
    var serialNo = counter;

    return `<tr id="inputRow${counter}">

    <td>${serialNo}</td> <!-- Serial number column -->

                <td><button class="btn btn-danger remove-row-btn" data-id="${counter}"><i class="fa-solid fa-xmark"></i></button></td>
                <td>
                <a href="#" class="select-product-link" id="selectProductLink" style="text-decoration: none;" data-id="${counter}" data-query="">
                        <h6 class="m-0" style="font-size: 14px; color: #000000;"></h6>
                        <p class="m-0" style="font-size: 14px; font-weight: 500;">
                            or select Items
                        </p>
                    </a>
                </td>
                <td>
                    <div class="old-price-wrapper position-relative">
                        <input autocomplete="off" type="text" placeholder="Unstocked Name" class="form-control sales-input old-price-input" id="unstockedInput" value="" data-id="${counter}" data-name="unstocked">
                        <div class="old-price-result-box" data-id="${counter}" style="display:none;"></div>
                    </div>
                </td>
                <td>

                    <input  autocomplete="off" type="text" placeholder="Quantity  " class="form-control sales-input" id="quantityInput" value="" data-id="${counter}" data-name="quantity">
                </td>

            <td>

            <select class="form-select sales-input" id="unitInput" data-id="${counter}" data-name="unit">
                        <option value="choose" selected disabled>select</option>         
                        <option value="pcs">pcs</option>
                        <option value="kg" >kg</option>
                        <option value="feet">feet</option>
                        <option value="mtr">mtr</option>

                       

            </select>

             </td>


                

                <td>
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon1">Rs.</span>
                        <input autocomplete="off" type="text"  placeholder="Price" class="form-control sales-input" id="priceInput" value="" data-id="${counter}" data-name="price" >
                    </div>
                </td>
                







                <td>
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon1">Rs.</span>
                        <input autocomplete="off" type="text" placeholder="0.00" class="form-control" id="subTotalInput" value="" data-id="${counter}" data-name="subtotal" disabled>
                    </div>
                </td>
            </tr>`;
}

function appendInputRow(rowData = null) {
    if (salesData.length >= 13) {
        return false;
    }

    counter++;
    $("#invoiceTableBody").append(inputHTML(counter));
    const row = {
        id: counter,
        product: rowData && rowData.product ? `${rowData.product}` : "",
        unstocked: rowData && rowData.unstocked ? `${rowData.unstocked}` : "",
        quantity: rowData && rowData.quantity ? `${rowData.quantity}` : "",
        unit: rowData && rowData.unit ? `${rowData.unit}` : "",
        price: rowData && rowData.price ? `${rowData.price}` : "",
        discount: "",
        subtotal: rowData && rowData.subtotal ? `${rowData.subtotal}` : "",
    };

    salesData.push(row);

    if (rowData) {
        const rowEl = $(`#inputRow${counter}`);
        rowEl.find("#quantityInput").val(row.quantity);
        rowEl.find("#priceInput").val(row.price);
        rowEl.find("#subTotalInput").val(row.subtotal);
        rowEl.find("#unitInput").val(row.unit || "choose");

        if (row.product) {
            rowEl.find("#selectProductLink").data("query", rowData.item_name || "");
            rowEl.find("#selectProductLink h6").text(rowData.item_name || "");
            rowEl.find("#selectProductLink p").text("");
            rowEl.find("#unstockedInput").attr("disabled", "true");
            rowEl.find("#unitInput").attr("disabled", "true");

            if (rowData.max_quantity) {
                rowEl.find("#quantityInput").attr("placeholder", `(Max: ${rowData.max_quantity})`);
                rowEl.find("#quantityInput").attr("data-max", rowData.max_quantity);
            }
        } else {
            rowEl.find("#unstockedInput").val(row.unstocked);
        }
    }

    triggerRemoveEvent();
    handleInputChange();
    handleOldPriceSearch();
    selectProduct();
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
            convertNumberToWords(parseInt(finalData[0]["total"]))
        );
    }
}

function addInputValue(index, inputId, dataId, dataName, value) {
    salesData[index][dataName] = value;

    const unitInput = $(`#inputRow${dataId} #unitInput`);

    // validation
    if (dataName === "unstocked") {
        if (value.trim() !== "") {
            $(`#inputRow${dataId} #selectProductLink`).css({
                "pointer-events": "none",
                color: "#afafaf",
            });
            unitInput.prop("disabled", false); // Enable unit input
        } else {
            unitInput.prop("disabled", true); // Enable unit input
            $(`#inputRow${dataId} #selectProductLink`).css({
                "pointer-events": "all",
                color: "#0d6efd",
            });
        }
    }

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

        // // Add validation for the "unit" field
        // if (dataName === "unit") {
        //     if (value.trim() === "") {
        //         $("#errorText").attr("class", "text-danger fw-bold");
        //         $("#errorText").text("Please enter unit !");
        //         // You can add additional logic here if needed, such as disabling the submit button.
        //         return; // Exit the function to prevent further execution.
        //     }
        // }

        if (dataName === "quantity") {
            const dataMax = $(`#inputRow${dataId} #${inputId}`).attr(
                "data-max"
            );
            const dataCurrent = $(`#inputRow${dataId} #${inputId}`).val();

            if (dataMax) {
                if (parseInt(dataCurrent) > parseInt(dataMax)) {
                    $(`#inputRow${dataId} #${inputId}`).val(dataMax);
                    salesData[index]["quantity"] = dataMax;
                }
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
    $(".sales-input").on("change", function (e) {
        // Change event instead of input event for select dropdowns
        const target = e.target;
        const inputId = $(target).attr("id");
        const dataId = $(target).data("id");
        const dataName = $(target).data("name");
        const value = target.value.trim(); // Trim the selected value

        const index = salesData.findIndex((obj) => obj.id === dataId);

        if (inputId !== "unstockedInput") {
            hideOldPriceBoxes();
        }

        if (inputId === "unitInput") {
            // Check if a unit is selected
            if (value === "select") {
                // If no unit is selected, show the validation error
                $("#errorText").attr("class", "text-danger fw-bold");
                $("#errorText").text("Please select a unit!");
                $("#submitBtn").attr("disabled", "disabled");
            } else {
                // If a unit is selected, clear the validation error and update the salesData
                $("#errorText").text(""); // Clear the error message
                salesData[index][dataName] = value; // Update the salesData
                if ($(`#${inputId}`).val() !== "select") {
                    // Check if the previous value was "select"
                    $("#submitBtn").removeAttr("disabled"); // Enable the submit button
                }
            }
        } else {
            // For other input types, handle the input value as before
            addInputValue(index, inputId, dataId, dataName, value);
        }
    });
}

let oldPriceTimers = {};

function hideOldPriceBoxes() {
    $(".old-price-result-box").hide().empty();
}

function oldPriceResultHTML(value) {
    const encodedValue = encodeURIComponent(JSON.stringify(value));

    return `
        <button type="button" class="old-price-result-item" data-value="${encodedValue}">
            <span><b>${value.item_name || ""}</b></span>
            <span>Rs. ${value.price || "0"}</span>
            <small>${value.date || ""} | Bill: ${value.invoiceid || ""}</small>
        </button>`;
}

function handleOldPriceSearch() {
    $(".old-price-input")
        .off("input.oldprice")
        .off("focus.oldprice")
        .off("blur.oldprice")
        .on("input.oldprice", function () {
            const input = this;
            const dataId = $(input).data("id");
            const search = input.value.trim();
            const customerId = finalData[0]["customer"];
            const customerName = $("#searchCustomerInput").val().trim();
            const resultBox = $(`.old-price-result-box[data-id="${dataId}"]`);

            clearTimeout(oldPriceTimers[dataId]);

            if ((!customerId && !customerName) || search.length < 2) {
                resultBox.hide().empty();
                return;
            }

            oldPriceTimers[dataId] = setTimeout(function () {
                $.ajax({
                    url: OLD_PRICE_SEARCH_URL,
                    type: "GET",
                    dataType: "json",
                    data: {
                        customerid: customerId,
                        customer_name: customerName,
                        search: search,
                    },
                    success: function (response) {
                        resultBox.empty();

                        if (!response || response.length === 0) {
                            positionOldPriceBox(input, resultBox);
                            resultBox
                                .append(
                                    '<div class="old-price-empty">No old price found for this customer.</div>'
                                )
                                .show();
                            return;
                        }

                        $.each(response, function (index, value) {
                            resultBox.append(oldPriceResultHTML(value));
                        });

                        positionOldPriceBox(input, resultBox);
                        resultBox.show();
                        triggerOldPriceClick();
                    },
                });
            }, 250);
        })
        .on("focus.oldprice", function () {
            const resultBox = $(`.old-price-result-box[data-id="${$(this).data("id")}"]`);
            if (resultBox.children().length > 0) {
                positionOldPriceBox(this, resultBox);
                resultBox.show();
            }
        })
        .on("blur.oldprice", function () {
            const dataId = $(this).data("id");
            setTimeout(function () {
                $(`.old-price-result-box[data-id="${dataId}"]`).hide().empty();
            }, 180);
        });
}

function positionOldPriceBox(input, resultBox) {
    const rect = input.getBoundingClientRect();
    resultBox.css({
        position: "fixed",
        left: rect.left + "px",
        top: rect.bottom + 4 + "px",
        width: Math.max(rect.width, 340) + "px",
        right: "auto",
        zIndex: 99999,
    });
}

function triggerOldPriceClick() {
    $(".old-price-result-item")
        .off("mousedown.oldprice touchstart.oldprice")
        .on("mousedown.oldprice touchstart.oldprice", function (e) {
            e.preventDefault();

            const data = JSON.parse(decodeURIComponent($(this).attr("data-value")));
            const row = $(this).closest("tr");
            const dataId = row.attr("id").replace("inputRow", "");
            const numericDataId = parseInt(dataId);
            const index = salesData.findIndex((obj) => obj.id === numericDataId);

            if (index === -1) return;

            const itemName = data.item_name || "";
            const price = data.price ? String(data.price) : "";
            const unit = data.unit || "";

            row.find("#unstockedInput").val(itemName);
            row.find("#priceInput").val(price);

            addInputValue(index, "unstockedInput", numericDataId, "unstocked", itemName);
            addInputValue(index, "priceInput", numericDataId, "price", price);

            if (unit) {
                row.find("#unitInput").val(unit);
                salesData[index]["unit"] = unit;
                $("#errorText").text("");
            }

            row.find(".old-price-result-box").hide().empty();
        });
}

function selectProduct() {
    $(".select-product-link").on("click", function (e) {
        e.preventDefault();

        currentLink = this;
        currentID = $(this).data("id");
        currentIndex = salesData.findIndex((obj) => obj.id === currentID);

        productSearchQuery = $(this).data("query");
        $("#searchProductInput").val($(this).data("query"));
        getProductData();

        $("#modalWrapper").show();
    });
}

$("#searchProductInput").on("keyup", function (e) {
    const target = e.target;
    productSearchQuery = target.value.trim();
    $(currentLink).data("query", productSearchQuery);

    getProductData();
});

function getProductData() {
    if (productSearchQuery.trim() === "") {
        $("#productResultWrapper").slideUp();
    } else {
        $("#productResultWrapper").slideDown();
        $("#productLoadingResultBox").removeClass("d-none");
        $("#productNotFoundResultBox").addClass("d-none");
        $(".product-result-box").addClass("d-none");

        getData(
            PRODUCT_SEARCH_API_URL +
                productSearchQuery +
                `?quantity=${quantityCase}`,
            function (response) {
                if (response) {
                    if (response.length > 0) {
                        $("#productResultList").empty();
                        $("#productLoadingResultBox").addClass("d-none");
                        $("#productNotFoundResultBox").addClass("d-none");
                        $.each(response, function (index, value) {
                            if (index < 20) {
                                $("#productResultList").append(
                                    productResultHTML(value)
                                );
                                triggerProductResultClick();
                            }
                        });
                    } else {
                        $("#productResultList").empty();
                        $("#productLoadingResultBox").addClass("d-none");
                        $("#productNotFoundResultBox").removeClass("d-none");
                    }
                }
            }
        );
    }
}

function triggerProductResultClick() {
    //forunit

    //forcreditnotes
    const currentUrl = window.location.href;
    $(".product-result-box")
        .off()
        .on("click", function () {
            const json = $(this).attr("data-value");
            const data = JSON.parse(json);

            $(currentLink).data("query", data.itemsname);
            $(currentLink).find("h6").text(data.itemsname);
            salesData[currentIndex]["product"] = `${data.id}`;

            $(`#inputRow${currentID} #priceInput`).val(data.mrp);
            salesData[currentIndex]["price"] = `${data.mrp}`;

            //mywork
            $(`#inputRow${currentID} #unitInput`).val(data.unit);
            salesData[currentIndex]["unit"] = `${data.unit}`;

            if (currentUrl.indexOf("creditnotes/create") === -1) {
                $(`#inputRow${currentID} #quantityInput`).attr(
                    "placeholder",
                    `(Max: ${data.quantity})`
                );
                $(`#inputRow${currentID} #quantityInput`).attr(
                    "data-max",
                    data.quantity
                );
            }

            $(`#inputRow${currentID} #quantityInput`).val("");
            salesData[currentIndex]["quantity"] = "";

            $(`#inputRow${currentID} #unstockedInput`).attr("disabled", "true");

            $(`#inputRow${currentID} #unitInput`).attr("disabled", "true");

            $("#searchProductInput").val(data.itemsname);
            $("#productResultWrapper").slideUp();

            $("#modalWrapper").hide();
        });
}

$("#modalContainer").on("click", function (e) {
    const targetEl = e.target;
    const elId = $(targetEl).attr("id");
    const closeStatus = $(targetEl).data("close");

    if (closeStatus) {
        if (elId === "modalContainer") {
            $("#modalWrapper").hide();
        }
    }
});

// final total input change
$(".sales-input-final").on("input", function () {
    hideOldPriceBoxes();

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
    const currentUrl = window.location.href;

    if (window.INVOICE_EDIT_DATA) {
        finalData[0] = {
            customer: `${window.INVOICE_EDIT_DATA.customer || ""}`,
            subtotal: `${window.INVOICE_EDIT_DATA.subtotal || 0}`,
            discount: `${window.INVOICE_EDIT_DATA.discount || 0}`,
            total: `${window.INVOICE_EDIT_DATA.total || 0}`,
            note: window.INVOICE_EDIT_DATA.note || "",
        };

        $("#searchCustomerInput").val(window.INVOICE_EDIT_DATA.customer_name || "");
        $("#customerIdInput").val(window.INVOICE_EDIT_DATA.customer || "");
        $("#selectedCustomerAddress").text(window.INVOICE_EDIT_DATA.customer_address || "-");
        $("#selectedCustomerPhone").text(window.INVOICE_EDIT_DATA.customer_phone || "-");
        $("#selectedCustomerInline").show();
        $("#invoice_type").val(window.INVOICE_EDIT_DATA.invoice_type || "");
        if (typeof changeBackgroundColor === "function") {
            changeBackgroundColor(document.querySelector('select[name="invoice_type"]'));
        }
        $("#salesDate").val(window.INVOICE_EDIT_DATA.date || "");
        $("#noteInput").val(window.INVOICE_EDIT_DATA.note || "");
        $("#discountInputFinal").val(finalData[0]["discount"]);

        $.each(window.INVOICE_EDIT_DATA.rows || [], function (index, row) {
            appendInputRow(row);
        });

        if (salesData.length === 0) {
            appendInputRow();
        }

        getFinalCalculations();
    } else {
        appendInputRow();
    }

    $("#addRowBtn").on("click", function (e) {
        e.preventDefault();
        appendInputRow();
    });

    $("#verifyBtn").on("click", function (e) {
        e.preventDefault();
        hideOldPriceBoxes();

        let hasError = false; // Flag to track if any error occurs

        if ($("#salesDate").val().trim() === "") {
            $("#errorText").attr("class", "text-danger fw-bold");
            $("#errorText").text("Please select Date !");
            hasError = true;
        } else if (finalData[0]["customer"].trim() === "") {
            $("#errorText").attr("class", "text-danger fw-bold");
            $("#errorText").text("Please select customer Name !");
            hasError = true;
        } else if (
            currentUrl.indexOf("creditnotes/create") === -1 &&
            $("#invoice_type").val().trim() === ""
        ) {
            // Check if invoice type is selected, but only if it's not a credit note creation page
            $("#errorText").attr("class", "text-danger fw-bold");
            $("#errorText").text("Please select invoice type !");
            hasError = true;
        } else {
            $.each(salesData, function (index, value) {
                if (
                    value.product.trim() === "" &&
                    value.unstocked.trim() === ""
                ) {
                    $("#errorText").attr("class", "text-danger fw-bold");
                    $("#errorText").text(
                        "Please enter Unstocked  or select Item !"
                    );
                    hasError = true;
                    return false; // Exit the loop early since there's an error
                } else if (
                    value.quantity.trim() === "" ||
                    parseFloat(value.quantity) <= 0
                ) {
                    $("#errorText").attr("class", "text-danger fw-bold");
                    $("#errorText").text("Please enter valid quantity !");
                    hasError = true;
                    return false; // Exit the loop early since there's an error
                } else if (value.price.trim() === "") {
                    $("#errorText").attr("class", "text-danger fw-bold");
                    $("#errorText").text("Please enter valid price !");
                    hasError = true;
                    return false; // Exit the loop early since there's an error
                } else if (value.unit.trim() === "") {
                    $("#errorText").attr("class", "text-danger fw-bold");
                    $("#errorText").text("Please Select unit !");
                    hasError = true;
                    return false; // Exit the loop early since there's an error
                }
            });

            // If there are no errors, set success message and enable submit button
            if (!hasError) {
                finalData[0]["note"] = $("#noteInput").val().trim();
                $("#salesArrInput").val(JSON.stringify(salesData));
                $("#finalArrInput").val(JSON.stringify(finalData));

                $("#errorText").attr("class", "text-success fw-bold");
                $("#errorText").text("Success, Now you can submit !");
                $("#submitBtn").removeAttr("disabled");

                if (
                    currentUrl.indexOf("creditnotes/create") !== -1 ||
                    currentUrl.indexOf("itemsales/create") !== -1 ||
                    currentUrl.indexOf("/invoice/") !== -1
                ) {
                    $("#verifyBtn").hide();
                    $("#submitBtn").text("Save").show();
                }
            }
        }

        // If there are any errors, disable the submit button
        if (hasError) {
            $("#submitBtn").attr("disabled", "disabled");
            if (
                currentUrl.indexOf("creditnotes/create") !== -1 ||
                currentUrl.indexOf("itemsales/create") !== -1 ||
                currentUrl.indexOf("/invoice/") !== -1
            ) {
                $("#submitBtn").hide();
                $("#verifyBtn").show();
            }
        }
    });
});
