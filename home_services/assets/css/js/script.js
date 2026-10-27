document.addEventListener("DOMContentLoaded", function () {
    const confirmForms = document.querySelectorAll(
        "form[data-confirm]"
    );

    confirmForms.forEach(function (form) {
        form.addEventListener("submit", function (event) {
            const message =
                form.getAttribute("data-confirm") ||
                "Are you sure you want to continue?";

            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });
    });

    const dateInputs = document.querySelectorAll(
        'input[type="date"][min]'
    );

    dateInputs.forEach(function (input) {
        input.addEventListener("change", function () {
            if (input.value && input.value < input.min) {
                alert("Please select a valid date.");
                input.value = "";
            }
        });
    });
});