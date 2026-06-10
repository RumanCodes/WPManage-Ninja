window.addEventListener('message', function(event) {
if (event.origin !== 'https://wpf.s6-tastewp.com') {
return;
}

if (!event.data || event.data.type !== 'fluentform_redirect') {
return;
}

if (!event.data.redirectUrl) {
return;
}

window.location.href = event.data.redirectUrl;
});