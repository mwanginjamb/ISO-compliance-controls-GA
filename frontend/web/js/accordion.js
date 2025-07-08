/*Parent-Children accordion*/

$('tr.parent').find('span').text('+');
$('tr.parent').find('span').css({ "color": "red", "font-weight": "bolder" });
$('tr.parent').nextUntil('tr.parent').slideUp(1, function () { });
$('tr.parent').click(function () {
    $(this).find('span').text(function (_, value) { return value == '-' ? '+' : '-' }); //to disregard an argument -event- on a function use an underscore in the parameter               
    $(this).nextUntil('tr.parent').slideToggle(100, function () { });
});

/*Divs parenting*/

$('p.parent').find('span').text('+');
$('p.parent').find('span').css({ "color": "red", "font-weight": "bolder" });
$('p.parent').nextUntil('p.parent').slideUp(1, function () { });
$('p.parent').click(function () {
    $(this).find('span').text(function (_, value) { return value == '-' ? '+' : '-' }); //to disregard an argument -event- on a function use an underscore in the parameter               
    $(this).nextUntil('p.parent').slideToggle(100, function () { });
});