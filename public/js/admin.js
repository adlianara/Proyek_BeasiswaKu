(() => {
    const scholarshipDialog = document.querySelector('#scholarship-modal');
    const deleteDialog = document.querySelector('#delete-modal');
    const logoutDialog = document.querySelector('#logout-modal');
    const scholarshipForm = document.querySelector('#scholarship-form');
    const methodField = document.querySelector('#scholarship-method');
    const loginUrl = document.body.dataset.adminLoginUrl;
    let deleteUrl = null;

    if (!scholarshipDialog) return;

    const showDialog = (dialog) => dialog?.showModal();
    const closeDialogs = () => document.querySelectorAll('.admin-modal[open]').forEach((dialog) => dialog.close());
    const setError = (dialog, message = '') => {
        const box = dialog.querySelector('.modal-error');
        if (!box) return;
        box.textContent = message;
        box.hidden = !message;
    };
    const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const request = (url, method, body = null) => fetch(url, {
        method,
        body,
        credentials: 'same-origin',
        headers: {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken(),
        },
    });
    const formatErrors = (errors) => Object.values(errors ?? {}).flat().join('\n');

    document.querySelectorAll('[data-open-scholarship]').forEach((button) => {
        button.addEventListener('click', () => {
            scholarshipForm.reset();
            setError(scholarshipDialog);
            scholarshipForm.classList.remove('modal-saving');
            scholarshipForm.action = button.dataset.mode === 'edit' ? button.dataset.url : '/pengelola/beasiswa';
            const editing = button.dataset.mode === 'edit';
            methodField.disabled = !editing;
            methodField.value = editing ? 'PUT' : 'POST';
            scholarshipDialog.querySelector('#scholarship-modal-title').textContent = editing ? 'Ubah Beasiswa' : 'Tambah Beasiswa Baru';
            scholarshipForm.querySelector('[type="submit"]').textContent = editing ? 'Simpan Perubahan' : 'Simpan Beasiswa';

            if (editing) {
                try {
                    const bytes = Uint8Array.from(atob(button.dataset.scholarship), (character) => character.charCodeAt(0));
                    const data = JSON.parse(new TextDecoder().decode(bytes));
                    for (const field of ['name', 'provider', 'level', 'summary', 'description', 'application_url']) {
                        const input = scholarshipForm.elements.namedItem(field);
                        if (input) input.value = data[field] ?? '';
                    }
                    scholarshipForm.elements.namedItem('deadline').value = String(data.deadline ?? '').slice(0, 10);
                    scholarshipForm.elements.namedItem('requirements_text').value = (data.requirements ?? []).join('\n');
                    scholarshipForm.elements.namedItem('benefits_text').value = (data.benefits ?? []).join('\n');
                    scholarshipForm.elements.namedItem('is_active').checked = Boolean(data.is_active);
                } catch (error) {
                    setError(scholarshipDialog, 'Data beasiswa tidak dapat dibaca. Muat ulang halaman lalu coba lagi.');
                }
            } else {
                scholarshipForm.elements.namedItem('is_active').checked = true;
            }
            showDialog(scholarshipDialog);
        });
    });

    scholarshipForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        setError(scholarshipDialog);
        scholarshipForm.classList.add('modal-saving');
        const submitButton = scholarshipForm.querySelector('[type="submit"]');
        submitButton.disabled = true;
        try {
            const response = await request(scholarshipForm.action, 'POST', new FormData(scholarshipForm));
            const result = await response.json().catch(() => ({}));
            if (!response.ok) {
                setError(scholarshipDialog, formatErrors(result.errors) || result.message || 'Perubahan belum dapat disimpan.');
                return;
            }
            closeDialogs();
            window.location.reload();
        } catch (error) {
            setError(scholarshipDialog, 'Koneksi gagal. Periksa jaringan lalu coba kembali.');
        } finally {
            scholarshipForm.classList.remove('modal-saving');
            submitButton.disabled = false;
        }
    });

    document.querySelectorAll('[data-open-delete]').forEach((button) => {
        button.addEventListener('click', () => {
            deleteUrl = button.dataset.url;
            deleteDialog.querySelector('[data-delete-name]').textContent = button.dataset.name;
            setError(deleteDialog);
            showDialog(deleteDialog);
        });
    });

    deleteDialog.querySelector('[data-confirm-delete]').addEventListener('click', async (event) => {
        const button = event.currentTarget;
        setError(deleteDialog);
        button.disabled = true;
        try {
            const response = await request(deleteUrl, 'DELETE');
            const result = await response.json().catch(() => ({}));
            if (!response.ok) {
                setError(deleteDialog, result.message || 'Beasiswa belum dapat dihapus.');
                return;
            }
            closeDialogs();
            window.location.reload();
        } catch (error) {
            setError(deleteDialog, 'Koneksi gagal. Periksa jaringan lalu coba kembali.');
        } finally {
            button.disabled = false;
        }
    });

    document.querySelector('[data-open-logout]').addEventListener('click', () => {
        setError(logoutDialog);
        showDialog(logoutDialog);
    });

    logoutDialog.querySelector('[data-confirm-logout]').addEventListener('click', async (event) => {
        const button = event.currentTarget;
        setError(logoutDialog);
        button.disabled = true;
        try {
            const response = await request('/pengelola/keluar', 'POST');
            if (!response.ok) {
                setError(logoutDialog, 'Sesi belum dapat diakhiri. Coba kembali.');
                return;
            }
            window.location.replace(loginUrl);
        } catch (error) {
            setError(logoutDialog, 'Koneksi gagal. Periksa jaringan lalu coba kembali.');
        } finally {
            button.disabled = false;
        }
    });

    document.querySelectorAll('[data-close-modal]').forEach((button) => button.addEventListener('click', closeDialogs));
    document.querySelectorAll('.admin-modal').forEach((dialog) => {
        dialog.addEventListener('click', (event) => {
            if (event.target === dialog) dialog.close();
        });
    });
})();
