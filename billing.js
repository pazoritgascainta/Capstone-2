
document.getElementById('toggleFormBtn').addEventListener('click', function() {
    const formContainer = document.getElementById('formContainer');
    formContainer.classList.toggle('hidden');
    this.textContent = formContainer.classList.contains('hidden') ? 'Show Form' : 'Hide Form';
});

