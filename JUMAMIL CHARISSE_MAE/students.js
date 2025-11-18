// students.js - modal handling and add-student AJAX
document.addEventListener('DOMContentLoaded', function () {
    const addBtn = document.getElementById('addstudent');
    const modal = document.getElementById('addStudentModal');
    const closeBtn = document.getElementById('addStudentClose');
    const form = document.getElementById('addStudentForm');
    const tbody = document.getElementById('studentsBody');
    const editBtn = document.getElementById('editStudent');
    const deleteBtn = document.getElementById('deleteStudent');
    const editModal = document.getElementById('editStudentModal');
    const editClose = document.getElementById('editStudentClose');
    const editForm = document.getElementById('editStudentForm');

    let selectedRow = null;

    if (!addBtn || !modal) return;

    function openModal() {
        modal.classList.add('open');
        modal.querySelector('input[name="name"]').focus();
    }

    function closeModal() {
        modal.classList.remove('open');
        form.reset();
    }

    addBtn.addEventListener('click', openModal);
    closeBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', function (e) {
        if (e.target === modal) closeModal();
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const name = form.elements['name'].value.trim();
        const email = form.elements['email'].value.trim();

        if (!name || !email) {
            alert('Please enter both name and email.');
            return;
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            alert('Please enter a valid email address.');
            return;
        }

        // disable submit while in-flight
        const submit = form.querySelector('button[type="submit"]');
        submit.disabled = true;

        const data = new URLSearchParams();
        data.append('name', name);
        data.append('email', email);

        fetch('add_student.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: data.toString()
        }).then(r => r.json()).then(res => {
            if (res.success) {
                // append row to table
                const tbody = document.getElementById('studentsBody');
                if (tbody) {
                    const tr = document.createElement('tr');
                    tr.dataset.id = res.student.id;
                    tr.innerHTML = `
                        <td>${escapeHtml(res.student.id)}</td>
                        <td>${escapeHtml(res.student.name)}</td>
                        <td>${escapeHtml(res.student.email)}</td>
                    `;
                    tbody.appendChild(tr);
                }
                closeModal();
            } else {
                alert(res.error || 'Unable to add student');
            }
        }).catch(err => {
            console.error(err);
            alert('Server error while adding student');
        }).finally(() => submit.disabled = false);
    });

    function escapeHtml(s) {
        return String(s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // Row selection (event delegation)
    if (tbody) {
        tbody.addEventListener('click', function (e) {
            const tr = e.target.closest('tr');
            if (!tr) return;
            if (selectedRow) selectedRow.classList.remove('selected-row');
            selectedRow = tr;
            selectedRow.classList.add('selected-row');
            // enable edit button
            if (editBtn) editBtn.disabled = false;
            if (deleteBtn) deleteBtn.disabled = false;
        });
    }

    // Edit button behavior
    if (editBtn) {
        // disabled by default until a row is selected
        editBtn.disabled = true;
        editBtn.addEventListener('click', function () {
            if (!selectedRow) { alert('Please select a student row first.'); return; }
            // Prefill edit form
            const id = selectedRow.dataset.id;
            const cells = selectedRow.querySelectorAll('td');
            const name = cells[1] ? cells[1].textContent.trim() : '';
            const email = cells[2] ? cells[2].textContent.trim() : '';
            editForm.elements['id'].value = id;
            editForm.elements['name'].value = name;
            editForm.elements['email'].value = email;
            editModal.classList.add('open');
            editForm.elements['name'].focus();
        });
    }

    // Delete button behavior
    if (deleteBtn) {
        deleteBtn.addEventListener('click', function () {
            if (!selectedRow) { alert('Please select a student row first.'); return; }
            const id = selectedRow.dataset.id;
            if (!confirm('Are you sure you want to delete this student? This cannot be undone.')) return;
            const data = new URLSearchParams();
            data.append('id', id);
            deleteBtn.disabled = true;
            fetch('delete_student.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: data.toString() })
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        // remove row from DOM
                        if (selectedRow) {
                            selectedRow.remove();
                            selectedRow = null;
                        }
                        if (editBtn) editBtn.disabled = true;
                        if (deleteBtn) deleteBtn.disabled = true;
                    } else {
                        alert(res.error || 'Unable to delete student');
                    }
                }).catch(err => { console.error(err); alert('Server error while deleting student'); })
                .finally(() => { if (deleteBtn) deleteBtn.disabled = false; });
        });
    }

    function closeEdit() {
        if (editModal) editModal.classList.remove('open');
        if (editForm) editForm.reset();
    }

    if (editClose) editClose.addEventListener('click', closeEdit);
    if (editModal) editModal.addEventListener('click', function (e) { if (e.target === editModal) closeEdit(); });

    if (editForm) {
        editForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const id = editForm.elements['id'].value;
            const name = editForm.elements['name'].value.trim();
            const email = editForm.elements['email'].value.trim();
            if (!id || !name || !email) { alert('All fields required'); return; }
            const data = new URLSearchParams();
            data.append('id', id);
            data.append('name', name);
            data.append('email', email);
            const submit = editForm.querySelector('button[type="submit"]');
            submit.disabled = true;
            fetch('update_student.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: data.toString() })
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        // update row in table
                        if (selectedRow && selectedRow.dataset.id == res.student.id) {
                            const cells = selectedRow.querySelectorAll('td');
                            if (cells[1]) cells[1].textContent = res.student.name;
                            if (cells[2]) cells[2].textContent = res.student.email;
                        }
                        closeEdit();
                    } else {
                        alert(res.error || 'Unable to update student');
                    }
                }).catch(err => { console.error(err); alert('Server error while updating student'); })
                .finally(() => submit.disabled = false);
        });
    }
});
