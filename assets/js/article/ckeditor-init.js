// ckeditor-init.js
$(document).ready(function() {
    ClassicEditor
        .create(document.querySelector('.article-content-area'), {
            // CKEditor 설정 옵션
            language: 'ko', // 언어 설정
        })
        .then(editor => {
            window.editor = editor; // 필요시 전역 변수로 에디터 인스턴스를 저장
        })
        .catch(error => {
            console.error('CKEditor initialization failed: ', error);
        });
});
