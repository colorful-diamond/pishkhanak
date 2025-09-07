// Comment Modal
document.addEventListener('DOMContentLoaded', () => {
    const openCommentModalBtn = document.getElementById('openCommentModal');
    const closeModalBtns = document.querySelectorAll('.closeModal');
    const submitCommentBtns = document.querySelectorAll('.submitComment');
    const openReplyModalBtns = document.querySelectorAll('.replyModalBtn');
    const modalContainer = document.querySelector('.modals');
    const modalOpenBtns = document.querySelectorAll('.modalOpener');
    const modalCloseBtns = document.querySelectorAll('.modalClose');
    const modals = document.querySelectorAll('.modal');

    if (modalOpenBtns) {
        modalOpenBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                modalContainer.classList.remove('hidden');
                overlay.classList.remove('hidden');
            });
        });
    }

    if (modalCloseBtns) {
        modalCloseBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                alert('close modal');
                modalContainer.classList.add('hidden');
                overlay.classList.add('hidden');
            });
        });
    }

    if (openCommentModalBtn) {
        openCommentModalBtn.addEventListener('click', (e) => {
            e.preventDefault();
            openCommentModal();
        });
    }

    function openCommentModal() {
        modalContainer.classList.remove('hidden');
        document.getElementById('commentModal').classList.remove('hidden');
        overlay.classList.remove('hidden');
    }

    if (closeModalBtns) {
        closeModalBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                closeModal();
            });
        });
    }

    function closeModal() {
        modalContainer.classList.add('hidden');
        modals.forEach(modal => {
            modal.classList.add('hidden');
        });
        overlay.classList.add('hidden');
    }

    if (submitCommentBtns) {
        submitCommentBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                submitComment();
            });
        });
    }

    function submitComment() {
        const comment = document.getElementById('newComment').value;
        // Here you would typically send the comment to your server
        console.log('New comment:', comment);
        closeCommentModal();
    }

    if (openReplyModalBtns) {
        openReplyModalBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                openReplyModal(btn.getAttribute('data-id') , btn.getAttribute('data-name'));
            });
        });
    }

    function openReplyModal(id, name) {
        document.getElementById('replyTo').value = id;
        document.getElementById('replyToName').textContent = name;
        document.getElementById('replyModal').classList.remove('hidden');
        modalContainer.classList.remove('hidden');
        overlay.classList.remove('hidden');
    }

    function submitReply() {
        const reply = document.getElementById('replyComment').value;
        const replyTo = document.getElementById('replyTo').textContent;
        // Here you would typically send the reply to your server
        console.log('Reply to', replyTo + ':', reply);
        closeReplyModal();
    }
});
