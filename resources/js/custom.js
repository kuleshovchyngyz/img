
import $ from "jquery";


function calcHeight() {
    let vh = $(".main__footer").outerHeight()
    vh += $(".main__top").outerHeight();
    vh += $(".header").outerHeight();
    document.documentElement.style.setProperty('--vh', `${vh}px`);
};

function num_img(value, img) {
    value = Math.abs(value) % 100;
    var num = value % 10;
    if (value > 10 && value < 20) return img[2];
    if (num > 1 && num < 5) return img[1];
    if (num == 1) return img[0];
    return img[2];
}
$(document).ready(function () {
    calcHeight()

});
Dropzone.autoDiscover = false;
var count = 0;
var stuff = [];
Dropzone.options.myDropzone = {
    maxFilesize: 500,
}
function fileType(filename){

    var extn = filename.substring(filename.lastIndexOf('.') + 1).toLowerCase();
    var img_type = 'image/jpeg';
    switch(extn) {
        case 'gif':
            img_type = 'image/gif';
            break;
        case 'png':
            img_type = 'image/png';
            break;
        case 'jpg':
        case 'jpeg':
        default:
            img_type = 'image/jpeg';
            break;
    }
    return img_type;
}

var myDropzone = new Dropzone("#dropzone", {

    maxFilesize: 10000,
    autoProcessQueue: true,
    parallelUploads:4,
    maxFiles: 50,
    thumbnailWidth: 350,
    thumbnailHeight: 350,
    acceptedFiles: ".jpeg,.jpg,.png,",
    addRemoveLinks: true,

    success: function(file, response) {

        const reader = new FileReader();
        reader.onload = (event) => {
            //console.log(event.target.result);
            $('.ibase64').append(`<input type='hidden' id='${file.name}' class='base64' value=${event.target.result}>`);
            if(!file.previewElement.querySelector("img").src){

                minifyImg(event.target.result, 350,350,null, fileType(file.name), (data)=> {
                    file.previewElement.querySelector("img").src = data;
                });
                console.log('empty')
            }
            // check_process(folder).then( v => {
            //     current_progress = v;
            // });
        };

        reader.readAsDataURL(file);

        stuff[file.upload.uuid] = response.changed_name;


        console.log("total: "+myDropzone.files.length);
        console.log("uploaded: "+response.number_of_images_uploaded);
        if(response.number_of_images_uploaded == myDropzone.files.length){
            $('.start-upload').removeClass('disable');
        }

    },
    complete: function(file) {
        //


    },
    removedfile: function (file) {
        var numItems = $('.dz-image-preview').length;

        if(numItems<=1){
            // location.reload();
        }

        var uuid = file.upload.uuid;
        var folder_id = $("#folder_id").val();
        $.ajax({
            type: 'POST',
            url: 'dropzone-delete',
            data: {
                _token: $('[name="_token"]').val(),
                name: stuff[uuid],
                folder_id:folder_id,
                request: 'delete'
            },
            sucess: function (data) {



            }
        })
            .done(function (data) {
                //


            })
            .fail(function (data) {

            });
        count = myDropzone.files.length;
        $('.main__top-text').html(`Загружено ${count} ${num_img(count, ['изображение', 'изображения',
            'Изображений'])} из 50`);
        var _ref;
        return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file
            .previewElement) : void 0;
    },
    addedfiles: function (file) {
        $('.main__footer-text').html('Укажите параметры и запустите сжатие');
        //$('.start-upload').removeClass('disable');
        count = myDropzone.files.length;

        $('.main__top-text').html(
            `Загружено ${count} ${num_img(count, ['изображение', 'изображения', 'Изображений'])} из 50`
        );
        $('body').toggleClass('mob-height');
        calcHeight();
    },
    reset: function (file) {
        $('.main__footer-text').html('Загрузите изображения');
        $('.start-upload').toggleClass('disable');
        $('.main__top-text').html('Не загружено ни одного файла :(');
        $('.main__wrap-file').removeClass('dz-started');
        $('body').removeClass('mob-height');
        calcHeight();
    }

});



//
function progressBar(folder) {

    var numItems = $('.dz-image-preview').length;

    $('.main__progress-wrap').toggleClass('start');
    $('.start-upload').toggleClass('disable');
    calcHeight();
    var current_progress = 0;
    let clearTimeoutid = setTimeout(frame  , 2000);
    function frame() {

        check_process(folder).then( v => {
            current_progress = v;
        });



        if (parseFloat(current_progress) > 99) {
            console.log("inside if")
            clearTimeout(clearTimeoutid);
            someafter();
        } else {
            $(".main__progress-line")
                .css("width", current_progress + "%")
                .attr("aria-valuenow", current_progress)
                .html(`<span class="main__progress-count">${current_progress}%</span>`);
            clearTimeoutid = setTimeout(frame  , 2000);
        }
    }
}
////777777



async function check_process(folder){

    const result = await $.ajax({
        type: 'GET',
        url: `check-progress/${folder}`,

    })
    return result;
}

async function uploadTiny1(data) {
    const result = await $.ajax({
        headers: {
            'X-CSRF-TOKEN': data._token
        },

        type: 'POST',
        url: 'upload-tiny',
        data: data,
        sucess: function (data) {
            console.log('response:')
            console.log(data);
        },
        error: function (xhr, status, error) {
            var errorMessage = xhr.status + ': ' + xhr.statusText
            console.log(errorMessage)
            console.log(error)
        }

    })
    return result;
}
async function uploadTiny(data){
    console.log("my"+data.folder_id)
    const result = await  $.ajax({
        url:'upload2.php',
        type:'POST',
        // contentType: 'application/octet-stream',
        // processData: false,
        data: data,
        success:function(response){
            //console.log(response);
        },

    });

    return result;

}
async function post_sizes(folder){
    const result = await $.ajax({
        type: 'GET',
        url: `shortpixel-file/${folder}`
    })
    return result;
}
async function get_image_sizes(folder){
    const result = await $.ajax({
        type: 'GET',
        url:`get-image-sizes/${folder}`
    })
    return result;
}
async function get_image_sizes_after_compressed(folder){
    const result = await $.ajax({
        type: 'GET',
        url: `get-image-sizes-after-compressed/${folder}`
    })
    return result;
}


$('body').on('click', '.dz-download-one', function () {
    let r = $(this).parent().find('.dz-filename').text();
    var folder_id = $("#folder_id").val();
    console.log(r)
    var getUrl = window.location;
    var baseUrl = getUrl .protocol + "//" + getUrl.host + "/dz-download-one";
    $('#frm111').remove();
    var token = $('[name="_token"]').val();
    let frm = `<form target="_blank" action="${baseUrl}" method="post" id="frm111">

            <input name="folder_id" value="${folder_id}">
            <input name="_token" value="${token}">
            <input name="name" value="${r}">
    </form>`;
    $('body').append(frm);
    setTimeout(() => {
        $('#frm111')[0].submit();
    }, 10);



});
let estimate_time = 50;
$('body').on('click', '.start-upload', function () {

    progressBar($("#folder_id").val());
    var numItems = $('.dz-image-preview').length;

    var compression = $("input[name='compression']:checked").val();
    var size = $("input[name='size']:checked").val();
    var width = 0;
    var height = 0;
    var folder_id = $("#folder_id").val();
    if(size=='random'){
        width = $("#width").val();
        height = $("#height").val();
    }
    let data =   {
        _token: $('[name="_token"]').val(),
        compression: compression,
        size:size,
        request: 'start-compression',
        width:width,
        height:height,
        folder_id:folder_id

    };
    if($('#shortpixel').is(':checked')){
        startCompress(data);
    }else{
        localCompress(data);
     }

})
function localCompress(config){
        var all = $(".base64").map(function() {
        var name = $(this).attr('id');
        var val = $(this).val();
        var dataToUpload =   {
              
                folder_id:config.folder_id,
                name:name

            };

       // console.log( config)
        var width = 1200;
        var width = 1200;
        var i = new Image();
        i.onload = function(){
            width = i.width;
            let height = i.height;

            var quality = 0.85;
            if(config.compression==0){
                quality = 0.9;
            }
            if(config.compression==2){
                quality = 0.80;
            }
            if(config.compression==1){
                quality = 0.70;
            }
            // console.log('kkkkkkkkkk')
            // console.log(config)

            if(config.size==0){
                minifyImg(val, width,height, null,fileType(name), (data)=> {

                    uploadTiny(config).then( v => {

                    });
                },quality);
            }
            if(config.size==1){
                console.log('inside 1')


                minifyImg(val, width,height, 1200,fileType(name) ,(data)=> {
                    dataToUpload.filedata = data;
                    // console.log(dataToUpload);
                    uploadTiny(dataToUpload).then( v => {
                        console.log(v);
                    });
                },quality);
            }
            if(config.size=='random'){

                width = $('#width').val();
                height = $('#height').val();
                minifyImg(val, width,height, null,fileType(name), (data)=> {
                    uploadTiny(config).then( v => {
                        console.log(v);
                    });
                },quality);
            }
        };
        i.src = $(this).val();
    }).get();
}
function startCompress(data){
    $.ajax({
        type: 'POST',
        url: 'start-compress',
        data: data,
        sucess: function (data) {


        }
    })
        .done(function (data) {
            //


        })
        .fail(function (data) {

        });
}
// $(".downLoadAll").on('click', function(event){

// });
// get_image_sizes_after_compressed('60e423e3c94de').then(data => {
//     ;
//
//
// });
function someafter(){



    $('.main__footer-text').html('Идёт сжатие');
    $('.main__footer .btn').html('Сжатие...');
    $('.main__footer .btn').toggleClass('disable');

    $('.dz-preview').addClass('dz-download-wrap');
    $('.show-sidebar').css('display', 'none');
    $('.main__upload-btn').css('display', 'none');
    $('.dz-image-preview').append(`<div class="dz-download-one"></div>`);
    $('.side-bar').toggleClass('no-active');
    $('.side-bar').removeClass('show');
    $('.show-sidebar').text('Настроить сжатие');
    calcHeight();
    var folder_id = $("#folder_id").val();
    $('.main__progress-wrap').removeClass('start');
    get_image_sizes_after_compressed(folder_id).then(data => {

        $('.main__footer-text').html(`Всего ${(data['new_size_sum']/1024/1024).toPrecision(2)} Мб (${count} изображения)`);
        $('.main__footer-progress').html(`Сжатие ${data['percentage']}%`);
    });
    $('.main__footer .btn').removeClass('disable');
    $(".main__progress-line").css("width", "0");

    get_image_sizes(folder_id).then(data => {


        $('div.dz-details').each(function( index , obj) {

            $(obj).prepend(`<div class="dz-image-info-per">${data[$(obj).find('.dz-filename span').text()]['percent']}%</div>`);
            $(obj).append(`<div class="dz-image-new-size">${(data[$(obj).find('.dz-filename span').text()]['new_size']/1024/1024).toPrecision(2)}MB</div>`);

        });

    });
    $('.dz-details').toggleClass('active');
    $('.start-upload').removeClass('disable start-upload').toggleClass('downLoadAll').html(
        'Скачать все');
    calcHeight();

}


$('body').on('click', '.downLoadAll', function () {

    count = 0;


    $('.dz-download-one').remove();
    $('.main__footer .btn').html('Загрузка...');
    $('.dz-details').removeClass('active');
    $('.dz-complete').removeClass('dz-download-wrap');

    calcHeight();
    var getUrl = window.location;
    var baseUrl = getUrl .protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1];

    var folder_id = $("#folder_id").val();
    window.location.href = baseUrl+'/'+folder_id;
    setTimeout(function () {
        myDropzone.removeAllFiles(true);
        $('.main__upload-btn').removeAttr('style');
        $('.main__progress-wrap').removeClass('start');
        $('.main__footer-text').html('Загрузите изображения');
        $('.main__footer-progress').html('');
        $('.main__dz-info').css('display', 'none');
        $('.downLoadAll').removeClass('downLoadAll').toggleClass('disable start-upload').html(
            'Начать');
        $('.main__upload-btn').prepend(
            `<span class="main__upload-title">Изображения загружены</span>`);
        $('.show-sidebar').removeAttr('style');
        calcHeight();
        $('.side-bar').removeClass('no-active');
        $('.main__top-input').val('');
    }, 6000);
})
$('.show-sidebar').on('click', function () {
    $('.side-bar').toggleClass('show');
    var text = $('.show-sidebar').text();
    $('.show-sidebar').text(text == "Загрузить ещё фото" ? "Настроить сжатие" : "Загрузить ещё фото");
});
$('input[name="size"]').on('change', function () {
    if ($('#randome-size').prop('checked')) {
        $('.side-bar-random').addClass('active');
    } else {
        $('.side-bar-random').removeClass('active');
    }
})

// minifyImg($(this).val(), width,height, 1200,fileType(name) ,(data)=> {
var minifyImg = function(dataUrl,newWidth=null,newHeight=null,def,imageType="image/jpeg",resolve,imageArguments=0.85){
    var image, oldWidth, oldHeight, newHeight, canvas, ctx, newDataUrl;
    (new Promise(function(resolve){
        image = new Image(); image.src = dataUrl;


        //
        setTimeout(() => {
            resolve('Done : ');
        }, 1000);

    })).then((d)=>{
        oldWidth = image.width; oldHeight = image.height;
        if(def!==null){
            if(newHeight>newWidth||newHeight==newWidth){
                newHeight = def;
                newWidth=null;
            }
            if(newHeight<newWidth){
                newWidth = def;
                newHeight = null;
            }
        }
        if(newHeight===null)newHeight = Math.floor(oldHeight / oldWidth * newWidth);
        if(newWidth===null)newWidth = Math.floor(oldWidth / oldHeight * newHeight);


        canvas = document.createElement("canvas");
        canvas.width = newWidth; canvas.height = newHeight;
        //
        ctx = canvas.getContext("2d");
        ctx.drawImage(image, 0, 0, newWidth, newHeight);
        // 

        newDataUrl = canvas.toDataURL(imageType, imageArguments);
        resolve(newDataUrl);
        // 
        //
    });
};
