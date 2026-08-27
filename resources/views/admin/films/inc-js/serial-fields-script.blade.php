<script>
    document.addEventListener('DOMContentLoaded', function () {
        const categorySelect = document.getElementById('category_id');
        const serialFields = document.getElementById('serial-fields');

        if (!categorySelect || !serialFields) {
            return;
        }

        const serialCategoryIds = @json(
        $formData['serial_category_ids'] ?? []
    );

        function toggleSerialFields() {
            const selectedId = Number(categorySelect.value);

            serialFields.style.display =
                serialCategoryIds.includes(selectedId)
                    ? 'block'
                    : 'none';
        }

        categorySelect.addEventListener(
            'change',
            toggleSerialFields
        );

        toggleSerialFields();
    });
</script>
