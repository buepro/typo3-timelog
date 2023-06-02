setTimeout(function () {
    document.querySelector('[data-local-table="tx_timelog_domain_model_task"][data-local-field="intervals"] button')
        .dispatchEvent(new MouseEvent("click", {
            view: window,
            bubbles: true,
            cancelable: true,
        }));
}, 1000);
