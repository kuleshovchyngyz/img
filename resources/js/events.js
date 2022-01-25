
$('input[name=width]').keyup(function(){
    $('input[name=height]').val('')
});

$('input[name=height]').keyup(function(){
    $('input[name=width]').val('')
});
