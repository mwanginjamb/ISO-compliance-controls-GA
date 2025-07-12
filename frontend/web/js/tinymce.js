function initializeTinyMCE(textareaId, initialContent, targetCell = null) {
    console.log(`Textarea ID: ${textareaId}`);
    console.log(`Initial Content: ${initialContent}`);
    tinymce.init({
        selector: '#' + textareaId,
        inline: false, // Using classic mode for the textarea
        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bulllist indent outdent | emoticons charmap | removeformat',
        height: 200,
        width: 600,
        menubar: false,
        statusbar: false,
        setup: function (editor) {
            // Set initial content after editor is ready (important for some cases)
            editor.on('init', function () {
                editor.setContent(initialContent);
            });

            editor.on('blur', function () {
                closeInput(editor.getElement());
            });
        },
        // Focus the editor immediately after initialization
        // `auto_focus` is a TinyMCE configuration option, often preferred over setTimeout
        auto_focus: textareaId
    });
    // Focus the editor immediately after initialization
    // This is often handled by auto_focus: true in init, but can be done manually
    // tinymce.get(uniqueId).focus(); // This might need a slight delay to ensure editor is fully ready
    setTimeout(() => {
        tinymce.get(uniqueId).focus();
    }, 50);
}