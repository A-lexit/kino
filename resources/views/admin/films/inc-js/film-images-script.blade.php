<script>
    document.addEventListener('DOMContentLoaded', function () {

        /*
         * Видалення зображення
         */
        document.querySelectorAll('.remove-image-btn').forEach(function (button) {

            button.addEventListener('click', function () {
                const target = this.dataset.target;
                const preview = document.querySelector(this.dataset.preview);

                const input = document.getElementById(target);
                const deleteInput = document.getElementById(`delete-${target}`);
                const filename = document.getElementById(`filename-${target}`);

                if (deleteInput) {
                    deleteInput.value = '1';
                }

                if (input) {
                    input.value = '';
                }

                if (filename) {
                    filename.textContent = '';
                    filename.hidden = true;
                }

                if (preview) {
                    preview.src = '';
                    preview.hidden = true;
                }

                this.hidden = true;
            });

        });


        /*
         * Preview нового зображення
         */
        document.querySelectorAll('.image-upload-preview').forEach(function (input) {

            input.addEventListener('change', function () {

                const target = this.name;
                const preview = document.querySelector(this.dataset.preview);

                const deleteInput = document.getElementById(`delete-${target}`);
                const removeButton = document.getElementById(`remove-${target}`);
                const filename = document.getElementById(`filename-${target}`);

                if (!this.files || !this.files[0]) {
                    return;
                }

                const file = this.files[0];

                if (!file.type.startsWith('image/')) {
                    this.value = '';

                    if (removeButton) {
                        removeButton.hidden = true;
                    }

                    return;
                }

                if (deleteInput) {
                    deleteInput.value = '0';
                }

                /*
                 * Показуємо назву файла.
                 */
                if (filename) {
                    filename.textContent = file.name;
                    filename.hidden = false;
                }

                /*
                 * Показуємо preview.
                 */
                if (preview) {
                    preview.src = URL.createObjectURL(file);
                    preview.hidden = false;
                }

                /*
                 * Показуємо кнопку видалення.
                 */
                if (removeButton) {
                    removeButton.hidden = false;
                }

            });

        });

    });
</script>
