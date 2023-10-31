
function showModal(message) {
    const modalElement = document.querySelector('#myModal');
    modalElement.querySelector('#modalText').textContent = message;
    modalElement.style.display = 'block';
}

function closeModal() {
    const modalElement = document.querySelector('#myModal');
    modalElement.style.display = 'none';
}

const closeBtn = document.querySelector('.btn-close');
if (closeBtn) {
    closeBtn.addEventListener('click', closeModal);
}

$('#url-form').on('submit', function (e) {
    e.preventDefault();
    e.stopPropagation();

    let inputDate = new Date($('#expiryDate').val());
    $('#expiryDate').val(inputDate.toISOString().slice(0, 16));
    const now = new Date();
    now.setHours(0, 0, 0, 0);

    if (inputDate < now) {
        alert('Дата не может быть ранее сегодняшнего дня.');
        return false;
    }
    inputDate = $('#expiryDate').val();

    let inputUrl = $('#url').val();
    inputUrl = inputUrl.startsWith('/') ? inputUrl.slice(1) : inputUrl;
    const protocol = window.location.protocol;
    const host = window.location.host;

    const fullUrl = `${protocol}//${host}/${inputUrl}`;

    try {
        const url = new URL(fullUrl);

        if (/[а-яА-ЯЁёЇїІіЄєҐґ]/.test(decodeURIComponent(url.href))) {
            alert('URL содержит кириллические символы.');
            return false;
        }
    } catch {
        alert('Недействительный URL.');
        return false;
    }

    $.ajax({
        url: '/url/create',
        type: 'POST',
        data: {
            date: inputDate,
            url: fullUrl
        },
        success: function (response) {
            const protocol = window.location.protocol;
            const host = window.location.host;
            const fullShortUrl = `${protocol}//${host}/${response.short_url}`;
            showModal(`Коротка адресса: ${fullShortUrl}, ${response.message}`);
        },
        error: function (err) {
            console.log(err);
        }
    });
});
