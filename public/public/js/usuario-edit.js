var resize = $('#upload-demo').croppie({

    enableExif: true,

    enableOrientation: true,

    viewport: {

        width: 200,

        height: 200,

        type: 'circle'

    },

    boundary: {

        width: 300,

        height: 300

    }

});



$('#image').on('change', function () {

    var reader = new FileReader();

    reader.onload = function (e) {

        resize.croppie('bind', {

            url: e.target.result

        }).then(function () {

            console.log('jQuery bind complete');

        });

    }

    reader.readAsDataURL(this.files[0]);

});



$('.upload-image').on('click', function (ev) {

    resize.croppie('result', {

        type: 'canvas',

        size: 'viewport'

    }).then(function (img) {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({

            url: "/crop-image",

            type: "POST",

            data: {
                "image": img
            },

            success: function (data) {

                location.reload(true);
            }

        });

    });

});
