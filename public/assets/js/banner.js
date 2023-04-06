function bannerSwitcher() {
    let next = $('.sec-1-input').filter(':checked').next('.sec-1-input');
    if (next.length) next.prop('checked', true);
    else $('.sec-1-input').first().prop('checked', true);
}

let bannerTimer = setInterval(bannerSwitcher, 5000);

$('nav .controls label').click(function () {
    clearInterval(bannerTimer);
    bannerTimer = setInterval(bannerSwitcher, 5000)
});