(function () {
    const ready = (callback) => {
        if (document.readyState != "loading") callback();
        else document.addEventListener("DOMContentLoaded", callback);
    }
    function showResult(container, resultClass) {
        container.classList.remove('tlc-progress');
        container.classList.add(resultClass);
        setTimeout(() => {
            container.classList.remove(resultClass);
            container.classList.remove('tlc-progress');
        }, 1000);

    }
    ready(() => {
        document.querySelector('.tx-timelog .tlc-sendmail').addEventListener('click', function (event) {
            event.stopPropagation();
            const button = this;
            const iconContainer = button.parentElement;
            iconContainer.classList.add('tlc-progress')
            fetch(button.dataset.uri)
                .then(data => {
                    showResult(iconContainer, 'tlc-success');
                }).catch(error => {
                    showResult(iconContainer, 'tlc-error');
                });
        })
    });
})();
