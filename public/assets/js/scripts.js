toastr.options = {
    "closeButton": false,
    "debug": false,
    "newestOnTop": false,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "1000",
    "hideDuration": "5000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
  }

Livewire.on('alert-success', function (data) {
    // Swal.fire({
    //     title: data.title,
    //     text: data.text,
    //     icon: data.icon,
    //     showConfirmButton: false,
    //     timer: 1500
    // })
    toastr.success(data.text, data.title);
});

Livewire.on('alert-error', function(data) {
    toastr.error(data.text, data.title);
});

Livewire.on('alert-warning', function(data) {
    toastr.warning(data.text, data.title);
});

Livewire.on('focus', function(data) {
    document.getElementById(data.id).focus();
});

Livewire.on('select2', function(data) {
    $(".select2").select2();
});

function update_notifications(){
    Livewire.emit('update-notifications');
}

setInterval(update_notifications, 5000);




