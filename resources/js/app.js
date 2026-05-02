document.addEventListener('submit', (event) => {
    const form = event.target;

    if (!(form instanceof HTMLFormElement)) {
        return;
    }

    if (form.dataset.submitOnce === 'false') {
        return;
    }

    if ((form.getAttribute('method') || 'get').toLowerCase() === 'get') {
        return;
    }

    if (form.dataset.submitting === 'true') {
        event.preventDefault();
        event.stopImmediatePropagation();

        return;
    }

    form.dataset.submitting = 'true';

    form.querySelectorAll('button, input[type="submit"]').forEach((submitter) => {
        submitter.setAttribute('aria-busy', 'true');
        submitter.classList.add('is-submitting');
    });
}, true);
