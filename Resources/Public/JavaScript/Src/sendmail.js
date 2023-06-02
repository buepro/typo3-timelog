(function () {
    const ready = (callback) => {
        if (document.readyState != "loading") callback();
        else document.addEventListener("DOMContentLoaded", callback);
    }
    ready(() => {
        document.querySelector('.tx-timelog .tlc-sendmail').addEventListener('click', function (event) {
            event.stopPropagation();
            console.log(this);
            const button = this;
            fetch(button.dataset.uri)
                .then(data => {
                    console.log('result: ', data);
                }).catch(error => {
                    console.log('error: ', error);
                });
        })
    });
})();


// ;+function ( $ ) {
//   $(function () {
//     $('.tx-timelog .tlc-sendmail').click(function (event) {
//       event.stopPropagation();
//       console.log('click');
//       var $this = $(this);
//       var $panel = $this.parent();
//       var uri = $this.attr('data-uri');
//       $panel.addClass('tlc-progress')
//       $.ajax( uri )
//         .done(function() {
//           $panel.removeClass('tlc-progress').addClass('tlc-success');
//           setTimeout(function () {
//             $panel.removeClass('tlc-success');
//           }, 3000);
//         })
//         .fail(function() {
//           $panel.removeClass('tlc-progress').addClass('tlc-error');
//           $panel.parent().append('<p class="alert alert-danger my-2">' + arguments[2] +'</p>');
//         })
//     })
//   })
// }(jQuery);
